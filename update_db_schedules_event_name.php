<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SHOW COLUMNS FROM schedules LIKE 'event_name'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE schedules ADD COLUMN event_name VARCHAR(255) DEFAULT NULL AFTER mass_type";
        $pdo->exec($sql);
        echo "Column 'event_name' added to 'schedules' table.\n";
    } else {
        echo "Column 'event_name' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>