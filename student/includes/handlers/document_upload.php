<?php
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';

// Security Gate
csrf_guard();
if (!has_role('student')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document_file'])) {
    $file = $_FILES['document_file'];
    $doc_type = $_POST['document_type'] ?? 'other';
    
    // Validation
    $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF, JPG, PNG, and WEBP allowed.']);
        exit();
    }

    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
        exit();
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'doc_' . $student_id . '_' . $doc_type . '_' . time() . '.' . $ext;
    $upload_path = '../../../uploads/documents/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        try {
            // Insert or Update document record
            $stmt = $pdo->prepare("SELECT id, file_path FROM student_documents WHERE student_id = ? AND document_type = ?");
            $stmt->execute([$student_id, $doc_type]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Delete old file
                $old_path = '../../../uploads/documents/' . $existing['file_path'];
                if (file_exists($old_path)) unlink($old_path);

                // Update
                $stmt = $pdo->prepare("UPDATE student_documents SET file_path = ?, verification_status = 'pending', uploaded_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$filename, $existing['id']]);
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO student_documents (student_id, document_type, file_path) VALUES (?, ?, ?)");
                $stmt->execute([$student_id, $doc_type, $filename]);
            }

            echo json_encode(['success' => true, 'message' => 'Document uploaded and awaiting verification.', 'filename' => $filename]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
}
