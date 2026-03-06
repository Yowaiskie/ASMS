<?php
require 'app/config/config.php';
require 'app/core/Database.php';
$db = App\Core\Database::getInstance();
try {
    $db->query("ALTER TABLE servers ADD COLUMN suspended_at DATE NULL AFTER suspension_until");
    $db->execute();
    echo "Column 'suspended_at' added successfully.\n";
} catch (Exception $e) {
    echo "Error or column already exists: " . $e->getMessage() . "\n";
}
