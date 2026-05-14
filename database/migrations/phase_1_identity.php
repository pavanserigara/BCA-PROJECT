<?php
require_once __DIR__ . '/../../includes/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS student_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        document_type ENUM('aadhaar', 'sslc', 'puc', 'tc', 'photo', 'other') NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
        admin_remarks TEXT,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Migration Phase 1 successful: student_documents table created.\n";

    // Initialize directories
    $dirs = [
        __DIR__ . '/../../uploads/profiles',
        __DIR__ . '/../../uploads/documents'
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            echo "Created directory: $dir\n";
        }
    }

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
