<?php
$host = '127.0.0.1'; // Use IP instead of localhost
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS cms_db");
    echo "Database 'cms_db' created/verified successfully!";
} catch (PDOException $e) {
    echo "Connection failing on IP: " . $e->getMessage();
}

echo "\n--- Testing Localhost ---\n";
try {
    $pdo = new PDO("mysql:host=localhost", $user, $pass);
    echo "Connection to localhost success!";
} catch (PDOException $e) {
    echo "Connection failing on localhost: " . $e->getMessage();
}
?>