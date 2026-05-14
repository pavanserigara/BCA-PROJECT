<?php
require_once '../db.php';
require_once '../functions.php';

header('Content-Type: application/json');

if (!is_logged_in() || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
        exit();
    }

    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        $user_id = $_SESSION['user_id'];
        $doc_type = sanitize($_POST['doc_type']);
        $file = $_FILES['document'];
        
        $allowed_types = ['aadhaar', 'sslc', 'puc', 'tc', 'photo'];
        if (!in_array($doc_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid document type.']);
            exit();
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array(strtolower($ext), $allowed_exts)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file extension. Only PDF, JPG, PNG allowed.']);
            exit();
        }

        $filename = 'doc_' . $doc_type . '_' . $user_id . '_' . time() . '.' . $ext;
        $upload_path = '../../uploads/documents/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Check if document already exists
            $stmt_check = $pdo->prepare("SELECT id FROM student_documents WHERE student_id = ? AND document_type = ?");
            $stmt_check->execute([$user_id, $doc_type]);
            $existing = $stmt_check->fetch();

            if ($existing) {
                $stmt = $pdo->prepare("UPDATE student_documents SET file_path = ?, verification_status = 'pending', uploaded_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$filename, $existing['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO student_documents (student_id, document_type, file_path) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $doc_type, $filename]);
            }

            echo json_encode(['success' => true]);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
            exit();
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
