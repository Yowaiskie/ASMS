<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

try {
    $db->query("SET FOREIGN_KEY_CHECKS = 0;");
    $db->execute();

    echo "Clearing attendance...<br>";
    $db->query("TRUNCATE attendance");
    $db->execute();

    echo "Clearing excuses...<br>";
    $db->query("TRUNCATE excuses");
    $db->execute();

    echo "Clearing logs...<br>";
    $db->query("TRUNCATE logs");
    $db->execute();

    echo "Clearing users (except Superadmin)...<br>";
    $db->query("DELETE FROM users WHERE role != 'Superadmin'");
    $db->execute();
    $db->query("ALTER TABLE users AUTO_INCREMENT = 2");
    $db->execute();

    echo "Clearing servers...<br>";
    $db->query("TRUNCATE servers");
    $db->execute();

    $db->query("SET FOREIGN_KEY_CHECKS = 1;");
    $db->execute();

    echo "<strong>Database cleared successfully (Servers, Users, Attendance, Excuses, Logs).</strong><br>";
    echo "You can now try importing your CSV again.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
