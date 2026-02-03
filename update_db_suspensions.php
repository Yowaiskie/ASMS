<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add suspension fields to servers table
    $sql = "ALTER TABLE servers 
            ADD COLUMN suspension_until DATE DEFAULT NULL,
            ADD COLUMN last_warning_month VARCHAR(7) DEFAULT NULL";
    
    $pdo->exec($sql);
    echo "Server table updated with suspension fields.\n";

} catch (PDOException $e) {
    echo "Note: Fields might already exist or: " . $e->getMessage() . "\n";
}
?>
