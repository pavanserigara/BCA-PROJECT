<?php
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: students-list.php");
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
    $stmt->execute([$id]);
} catch (PDOException $e) {
    // Avoid exposing DB errors; redirect back
}

header("Location: students-list.php");
exit();

