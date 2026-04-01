<?php
require_once 'includes/db.php';

try {
  // Books
  $pdo->exec("CREATE TABLE IF NOT EXISTS `books` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `author` varchar(255) NOT NULL,
      `isbn` varchar(50) DEFAULT NULL,
      `category` varchar(100) DEFAULT NULL,
      `quantity` int(11) DEFAULT 1,
      `available` int(11) DEFAULT 1,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB");

  // Issued Books
  $pdo->exec("CREATE TABLE IF NOT EXISTS `issued_books` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `book_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `issue_date` date NOT NULL,
      `return_date` date DEFAULT NULL,
      `status` enum('Issued','Returned','Overdue') DEFAULT 'Issued',
      `fine_amount` decimal(10,2) DEFAULT 0.00,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");

  // Events
  $pdo->exec("CREATE TABLE IF NOT EXISTS `events` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text DEFAULT NULL,
      `event_date` date NOT NULL,
      `location` varchar(255) DEFAULT NULL,
      `posted_by` int(11) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");

  // Complaints
  $pdo->exec("CREATE TABLE IF NOT EXISTS `complaints` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `subject` varchar(255) NOT NULL,
      `message` text NOT NULL,
      `status` enum('Pending','Resolved','Closed') DEFAULT 'Pending',
      `admin_reply` text DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");

  // Messages (direct messages)
  $pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `sender_id` int(11) NOT NULL,
      `receiver_id` int(11) NOT NULL,
      `subject` varchar(255) DEFAULT NULL,
      `message` text NOT NULL,
      `is_read` tinyint(1) DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");

  // Timetable
  $pdo->exec("CREATE TABLE IF NOT EXISTS `timetable` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `course_id` int(11) NOT NULL,
      `semester` int(11) NOT NULL,
      `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
      `subject_id` int(11) NOT NULL,
      `teacher_id` int(11) NOT NULL,
      `start_time` time NOT NULL,
      `end_time` time NOT NULL,
      `room_no` varchar(50) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");

  // Attendance uniqueness (one row per student/subject/date)
  try {
    $pdo->exec("CREATE UNIQUE INDEX attendance_unique ON attendance (student_id, subject_id, date)");
  } catch (PDOException $e) {
    // ignore if already exists
  }

  // Teacher Subjects Mapping
  $pdo->exec("CREATE TABLE IF NOT EXISTS `teacher_subjects` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `teacher_id` int(11) NOT NULL,
      `subject_id` int(11) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");

  echo "Database tables updated/verified successfully!";
} catch (PDOException $e) {
  echo "Update failed: " . $e->getMessage();
}
?>