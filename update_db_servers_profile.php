<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $cols = [
        'age' => "INT DEFAULT NULL",
        'address' => "TEXT DEFAULT NULL",
        'profile_image' => "VARCHAR(255) DEFAULT NULL"
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
