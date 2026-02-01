<?php
require_once __DIR__ . '/app/config/config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if column exists
    $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'server_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN server_id INT AFTER role");
        echo "Column 'server_id' added successfully.\n";
    } else {
        echo "Column 'server_id' already exists.\n";
    }

    // Link 'user' account to Juan Dela Cruz (server_id = 1)
    $pdo->exec("UPDATE users SET server_id = 1 WHERE username = 'user'");
    echo "User 'user' linked to server_id 1.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

