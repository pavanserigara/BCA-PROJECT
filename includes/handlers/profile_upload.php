<?php
require_once '../db.php';
require_once '../functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
        exit();
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $user_id = $_SESSION['user_id'];
        $file = $_FILES['profile_pic'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];

        if (!in_array(strtolower($ext), $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
            exit();
        }

        $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
        $upload_path = '../../uploads/profiles/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Update DB
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->execute([$filename, $user_id]);
            
            $_SESSION['profile_pic'] = $filename;

            echo json_encode(['success' => true, 'filename' => $filename]);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
            exit();
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
