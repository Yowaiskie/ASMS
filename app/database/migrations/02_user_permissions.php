<?php

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'asms_db');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();

    echo "Creating user_permissions table...\n";

    $db->query("CREATE TABLE IF NOT EXISTS user_permissions (
        user_id INT NOT NULL,
        permission_id INT NOT NULL,
        PRIMARY KEY (user_id, permission_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
    )");
    
    if ($db->execute()) {
        echo "Table 'user_permissions' created successfully.\n";
    } else {
        echo "Failed to create table.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
