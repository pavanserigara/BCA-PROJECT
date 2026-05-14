<?php
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

// Security Gate
csrf_guard();
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];
    
    // Validation
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and WEBP allowed.']);
        exit();
    }

    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 2MB limit.']);
        exit();
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
    $upload_path = '../../uploads/profiles/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        try {
            $pdo->beginTransaction();

            // Fetch old profile pic to delete it
            $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $old_pic = $stmt->fetchColumn();

            if ($old_pic && $old_pic != 'default_profile.png' && $old_pic != 'default_profile.svg') {
                $old_path = '../../uploads/profiles/' . $old_pic;
                if (file_exists($old_path)) unlink($old_path);
            }

            // Update DB
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->execute([$filename, $user_id]);

            // Update session
            $_SESSION['profile_pic'] = $filename;

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully.', 'filename' => $filename]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
}
