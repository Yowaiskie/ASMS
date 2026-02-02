<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create excuses table
    $sql = "CREATE TABLE IF NOT EXISTS excuses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        server_id INT NOT NULL,
        type VARCHAR(50) NOT NULL, -- 'Mass', 'Meeting', 'Both'
        absence_date DATE NOT NULL,
        reason TEXT NOT NULL,
        image_path VARCHAR(255) DEFAULT NULL,
        status VARCHAR(20) DEFAULT 'Pending', -- Pending, Approved, Rejected
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Table 'excuses' created successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
