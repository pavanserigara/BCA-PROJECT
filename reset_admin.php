<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$username = 'admin';
$email = 'admin@vidyasetu.com';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET password = ?, email = ?, full_name = 'System Administrator', role = 'admin', status = 'active' WHERE id = ?");
        $stmt->execute([$hash, $email, $user['id']]);
        echo "Admin password has been reset to: $password\n";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name, status) VALUES (?, ?, ?, 'admin', 'System Administrator', 'active')");
        $stmt->execute([$username, $email, $hash]);
        echo "Admin user created with password: $password\n";
    }
} catch (PDOException $e) {
    echo "Error resetting admin user: " . $e->getMessage() . "\n";
}
?>