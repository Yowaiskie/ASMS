<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add last_read_announcements column to users table if it doesn't exist
    $sql = "SHOW COLUMNS FROM users LIKE 'last_read_announcements'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE users ADD COLUMN last_read_announcements DATETIME DEFAULT NULL";
        $pdo->exec($sql);
        echo "Column 'last_read_announcements' added to 'users' table.\n";
    } else {
        echo "Column 'last_read_announcements' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>