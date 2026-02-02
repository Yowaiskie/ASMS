<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $cols = [
        'nickname' => "VARCHAR(100) DEFAULT NULL",
        'dob' => "DATE DEFAULT NULL",
        'month_joined' => "VARCHAR(20) DEFAULT NULL",
        'investiture_date' => "DATE DEFAULT NULL",
        'order_name' => "VARCHAR(100) DEFAULT NULL", // 'order' is reserved keyword
        'position' => "VARCHAR(100) DEFAULT NULL"
    ];

    foreach ($cols as $col => $def) {
        $sql = "SHOW COLUMNS FROM servers LIKE '$col'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE servers ADD COLUMN $col $def";
            $pdo->exec($sql);
            echo "Column '$col' added to 'servers' table.\n";
        } else {
            echo "Column '$col' already exists.\n";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
