<?php
require_once 'includes/db.php';

try {
    $stmt = $pdo->query("SELECT username, role, full_name FROM users ORDER BY role, username");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "VIDYA SETU ERP - INSTITUTIONAL ACCESS CREDENTIALS\n";
    echo "================================================\n\n";
    echo "Note: Passwords are encrypted in the database for security protocols.\n";
    echo "Standard development password for demo accounts: password123\n\n";

    foreach ($users as $user) {
        echo "Role: " . strtoupper($user['role']) . "\n";
        echo "Name: " . $user['full_name'] . "\n";
        echo "User: " . $user['username'] . "\n";
        echo "Pass: password123 (Institutional Default)\n";
        echo "------------------------------------------------\n";
    }
} catch (PDOException $e) {
    echo "Access Denied: " . $e->getMessage();
}
