<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SHOW COLUMNS FROM excuses LIKE 'absence_time'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE excuses ADD COLUMN absence_time VARCHAR(50) DEFAULT NULL AFTER absence_date";
        $pdo->exec($sql);
        echo "Column 'absence_time' added to 'excuses' table.\n";
    } else {
        echo "Column 'absence_time' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
