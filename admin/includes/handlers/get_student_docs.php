<?php
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';

if (!has_role('admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$student_id = $_GET['student_id'] ?? 0;

if (!$student_id) {
    echo json_encode(['success' => false, 'message' => 'Student ID required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM student_documents WHERE student_id = ? ORDER BY uploaded_at DESC");
    $stmt->execute([$student_id]);
    $docs = $stmt->fetchAll();

    echo json_encode(['success' => true, 'documents' => $docs]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
