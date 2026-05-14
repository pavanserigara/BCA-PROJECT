<?php
require_once '../db.php';
require_once '../functions.php';

header('Content-Type: application/json');

if (!is_logged_in() || !has_role('admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

if ($student_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid student identifier.']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM student_documents WHERE student_id = ? ORDER BY uploaded_at DESC");
    $stmt->execute([$student_id]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'documents' => $documents]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
