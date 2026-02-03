<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Add new columns
    $pdo->exec("ALTER TABLE servers 
                ADD COLUMN first_name VARCHAR(100) AFTER id,
                ADD COLUMN middle_name VARCHAR(100) AFTER first_name,
                ADD COLUMN last_name VARCHAR(100) AFTER middle_name");

    // 2. Migrate data (Simple split by space for existing records)
    $stmt = $pdo->query("SELECT id, name FROM servers");
    $servers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($servers as $server) {
        $parts = explode(' ', trim($server['name']));
        $lastName = array_pop($parts);
        $firstName = array_shift($parts) ?? $lastName; // Fallback if only one name exists
        $middleName = implode(' ', $parts);

        $upd = $pdo->prepare("UPDATE servers SET first_name = ?, middle_name = ?, last_name = ? WHERE id = ?");
        $upd->execute([$firstName, $middleName, $lastName, $server['id']]);
    }

    // 3. Drop old name column (Optional, but let's keep it for a bit or just rename it to full_name_legacy)
    // For safety, I'll just leave it or rename it.
    $pdo->exec("ALTER TABLE servers CHANGE name full_name_old VARCHAR(255)");

    echo "Database schema updated and data migrated successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>