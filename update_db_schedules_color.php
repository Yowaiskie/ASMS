<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SHOW COLUMNS FROM schedules LIKE 'color'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE schedules ADD COLUMN color VARCHAR(20) DEFAULT NULL AFTER event_name";
        $pdo->exec($sql);
        echo "Column 'color' added to 'schedules' table.\n";
    } else {
        echo "Column 'color' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
