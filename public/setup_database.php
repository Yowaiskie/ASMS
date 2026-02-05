<?php
// setup_database.php
// Run this file in your browser: http://localhost/ASMS/public/setup_database.php

require_once __DIR__ . '/../app/config/config.php';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "Database '" . DB_NAME . "' checked/created successfully.<br>";

    // Connect to the specific database
    $pdo->exec("USE " . DB_NAME);

    // Function to safely add a column if it doesn't exist
    function addColumn($pdo, $table, $column, $definition) {
        $check = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'")->fetch();
        if (!$check) {
            $pdo->exec("ALTER TABLE `$table` ADD `$column` $definition");
            echo "Added column '$column' to table '$table'.<br>";
        }
    }

    // --- Table: servers ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS servers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Add all missing columns to 'servers'
    addColumn($pdo, 'servers', 'middle_name', "VARCHAR(100) AFTER first_name");
    addColumn($pdo, 'servers', 'nickname', "VARCHAR(50) AFTER last_name");
    addColumn($pdo, 'servers', 'dob', "DATE AFTER nickname");
    addColumn($pdo, 'servers', 'age', "INT AFTER dob");
    addColumn($pdo, 'servers', 'address', "TEXT AFTER age");
    addColumn($pdo, 'servers', 'phone', "VARCHAR(20) AFTER address");
    addColumn($pdo, 'servers', 'email', "VARCHAR(100) AFTER phone");
    addColumn($pdo, 'servers', 'rank', "VARCHAR(50) DEFAULT 'Server' AFTER email");
    addColumn($pdo, 'servers', 'team', "VARCHAR(50) DEFAULT 'Unassigned' AFTER rank");
    addColumn($pdo, 'servers', 'status', "VARCHAR(20) DEFAULT 'Active' AFTER team");
    addColumn($pdo, 'servers', 'month_joined', "VARCHAR(20) AFTER status");
    addColumn($pdo, 'servers', 'investiture_date', "DATE AFTER month_joined");
    addColumn($pdo, 'servers', 'order_name', "VARCHAR(100) AFTER investiture_date");
    addColumn($pdo, 'servers', 'position', "VARCHAR(100) AFTER order_name");
    addColumn($pdo, 'servers', 'profile_image', "VARCHAR(255) AFTER position");
    addColumn($pdo, 'servers', 'suspension_until', "DATE NULL AFTER profile_image");
    addColumn($pdo, 'servers', 'deleted_at', "TIMESTAMP NULL DEFAULT NULL");

    // Check if 'name' column exists and migration is needed
    $checkName = $pdo->query("SHOW COLUMNS FROM servers LIKE 'name'")->fetch();
    if ($checkName) {
        // Migration: If 'name' exists, try to split it into first and last name
        $pdo->exec("UPDATE servers SET first_name = SUBSTRING_INDEX(name, ' ', 1), last_name = SUBSTRING_INDEX(name, ' ', -1) WHERE first_name = '' OR first_name IS NULL");
        echo "Migrated 'name' data to 'first_name' and 'last_name'.<br>";
    }

    // --- Table: users ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    addColumn($pdo, 'users', 'is_verified', "TINYINT(1) DEFAULT 0");
    addColumn($pdo, 'users', 'can_edit_profile', "TINYINT(1) DEFAULT 0");
    addColumn($pdo, 'users', 'has_edited_profile', "TINYINT(1) DEFAULT 0");
    addColumn($pdo, 'users', 'force_password_reset', "TINYINT(1) DEFAULT 1");
    addColumn($pdo, 'users', 'last_read_announcements', "TIMESTAMP NULL");
    addColumn($pdo, 'users', 'server_id', "INT");
    addColumn($pdo, 'users', 'deleted_at', "TIMESTAMP NULL DEFAULT NULL");

    // --- Other tables ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mass_type VARCHAR(100) NOT NULL,
        mass_date DATE NOT NULL,
        mass_time VARCHAR(20) NOT NULL,
        status VARCHAR(20) DEFAULT 'Confirmed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        server_id INT,
        schedule_id INT,
        status VARCHAR(20) NOT NULL,
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(50),
        module VARCHAR(50),
        description TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "<hr><strong>Database schema updated successfully!</strong> All columns are now synchronized with the code.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
