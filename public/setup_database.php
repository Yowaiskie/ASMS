<?php
// setup_database.php
// Run this file in your browser: http://localhost/[your-path]/public/setup_database.php

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
    addColumn($pdo, 'users', 'last_viewed_excuses', "TIMESTAMP NULL");
    addColumn($pdo, 'users', 'excuse_override_until', "TIMESTAMP NULL");
    addColumn($pdo, 'users', 'server_id', "INT");
    addColumn($pdo, 'users', 'deleted_at', "TIMESTAMP NULL DEFAULT NULL");

    // --- Table: schedules ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mass_type VARCHAR(100) NOT NULL,
        mass_date DATE NOT NULL,
        mass_time VARCHAR(20) NOT NULL,
        status VARCHAR(20) DEFAULT 'Confirmed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    addColumn($pdo, 'schedules', 'event_name', "VARCHAR(255) AFTER mass_type");
    addColumn($pdo, 'schedules', 'color', "VARCHAR(50) AFTER event_name");

    // --- Table: attendance ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        server_id INT,
        schedule_id INT,
        status VARCHAR(20) NOT NULL,
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // --- Table: logs ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(50),
        module VARCHAR(50),
        description TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // --- Table: announcements ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(50),
        message TEXT NOT NULL,
        author VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // --- Table: excuses ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS excuses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        server_id INT NOT NULL,
        type VARCHAR(50) NOT NULL,
        absence_date DATE NOT NULL,
        absence_time VARCHAR(50),
        reason TEXT NOT NULL,
        image_path VARCHAR(255),
        status VARCHAR(20) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // --- Table: system_settings ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS system_settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT
    )");

    // --- Table: schedule_templates ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS schedule_templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        day_of_week INT NOT NULL,
        mass_time TIME NOT NULL,
        mass_type VARCHAR(100) NOT NULL,
        event_name VARCHAR(255) DEFAULT NULL,
        color VARCHAR(50) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // --- Table: activity_types ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS activity_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        default_color VARCHAR(50) DEFAULT 'blue',
        is_active BOOLEAN DEFAULT 1
    )");

    // --- Table: server_ranks ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS server_ranks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) UNIQUE NOT NULL,
        is_active BOOLEAN DEFAULT 1
    )");

    // --- Table: announcement_categories ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS announcement_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) UNIQUE NOT NULL,
        is_active BOOLEAN DEFAULT 1
    )");

    // --- Table: notifications ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        link VARCHAR(255) DEFAULT NULL,
        type VARCHAR(50) DEFAULT 'info',
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "Notifications table checked/created.<br>";

    // --- Table: liturgical_seasons ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS liturgical_seasons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        color VARCHAR(20) NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    addColumn($pdo, 'liturgical_seasons', 'exempted_types', "TEXT AFTER color");

    // --- Table: schedule_presets ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS schedule_presets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        preset_group VARCHAR(50) DEFAULT 'General',
        day_of_week TINYINT NOT NULL,
        mass_time TIME NOT NULL,
        mass_type VARCHAR(50) NOT NULL,
        event_name VARCHAR(100),
        color VARCHAR(20) DEFAULT 'blue',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_preset (name, day_of_week, mass_time)
    )");

    // Clean up duplicate presets before adding constraint
    $pdo->exec("DELETE t1 FROM schedule_presets t1 INNER JOIN schedule_presets t2 WHERE t1.id > t2.id AND t1.name = t2.name AND t1.day_of_week = t2.day_of_week AND t1.mass_time = t2.mass_time");
    
    // Safely add UNIQUE constraint to schedule_presets if not exists
    $checkIndex = $pdo->query("SHOW INDEX FROM schedule_presets WHERE Key_name = 'unique_preset'")->fetch();
    if (!$checkIndex) {
        $pdo->exec("ALTER TABLE schedule_presets ADD UNIQUE KEY unique_preset (name, day_of_week, mass_time)");
        echo "Added UNIQUE constraint to 'schedule_presets'.<br>";
    }

    // --- Seed Activity Types ---
    $activityTypes = ['Sunday Mass', 'Anticipated Mass', 'Weekday Mass', 'Wedding', 'Funeral', 'Baptism', 'Special Event', 'Meeting'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO activity_types (name, default_color) VALUES (?, 'blue')");
    foreach($activityTypes as $type) $stmt->execute([$type]);

    // --- Seed Ranks ---
    $ranks = ['Senior', 'Junior', 'Aspirant'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO server_ranks (name) VALUES (?)");
    foreach($ranks as $rank) $stmt->execute([$rank]);

    // --- Seed Announcement Categories ---
    $categories = ['General', 'Training', 'Schedule', 'Reminder'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO announcement_categories (name) VALUES (?)");
    foreach($categories as $cat) $stmt->execute([$cat]);

    // --- Seed Schedule Presets ---
    $presets = [
        // Sunday
        ['Sunday 6:00 AM', 'Sunday', 0, '06:00:00', 'Sunday Mass', 'green'],
        ['Sunday 7:30 AM', 'Sunday', 0, '07:30:00', 'Sunday Mass', 'green'],
        ['Sunday 9:00 AM', 'Sunday', 0, '09:00:00', 'Sunday Mass', 'green'],
        ['Sunday 4:00 PM', 'Sunday', 0, '16:00:00', 'Sunday Mass', 'green'],
        ['Sunday 5:00 PM (Holy Hour)', 'Sunday', 0, '17:00:00', 'Meeting', 'yellow'],
        ['Sunday 5:30 PM', 'Sunday', 0, '17:30:00', 'Sunday Mass', 'green'],
        ['Sunday 7:00 PM', 'Sunday', 0, '19:00:00', 'Sunday Mass', 'green'],

        // Saturday
        ['Saturday 6:00 AM', 'Saturday', 6, '06:00:00', 'Weekday Mass', 'blue'],
        ['Saturday 6:00 PM (Anticipated)', 'Saturday', 6, '18:00:00', 'Anticipated Mass', 'teal'],

        // Weekdays 6AM
        ['Monday 6:00 AM', 'Weekday Morning', 1, '06:00:00', 'Weekday Mass', 'blue'],
        ['Tuesday 6:00 AM', 'Weekday Morning', 2, '06:00:00', 'Weekday Mass', 'blue'],
        ['Wednesday 6:00 AM', 'Weekday Morning', 3, '06:00:00', 'Weekday Mass', 'blue'],
        ['Thursday 6:00 AM', 'Weekday Morning', 4, '06:00:00', 'Weekday Mass', 'blue'],
        ['Friday 6:00 AM', 'Weekday Morning', 5, '06:00:00', 'Weekday Mass', 'blue'],

        // Weekdays 6PM
        ['Wednesday 6:00 PM', 'Weekday Evening', 3, '18:00:00', 'Weekday Mass', 'blue'],
        ['Friday 6:00 PM', 'Weekday Evening', 5, '18:00:00', 'Weekday Mass', 'blue'],
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO schedule_presets (name, preset_group, day_of_week, mass_time, mass_type, color) VALUES (?, ?, ?, ?, ?, ?)");
    foreach($presets as $p) $stmt->execute($p);

    // Default System Settings
    $pdo->exec("INSERT IGNORE INTO system_settings (setting_key, setting_value) VALUES 
        ('system_name', 'Altar Servers Management System'),
        ('allow_registration', 'on'),
        ('maintenance_mode', 'off')");

    // --- CREATE DEFAULT SUPERADMIN ---
    $adminUsername = 'superadmin';
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $checkAdmin = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $checkAdmin->execute(['username' => $adminUsername]);
    if (!$checkAdmin->fetch()) {
        $pdo->prepare("INSERT INTO users (username, password, role, force_password_reset) VALUES (:u, :p, 'Superadmin', 0)")
            ->execute(['u' => $adminUsername, 'p' => $adminPassword]);
        echo "Default Superadmin created (Username: superadmin, Password: admin123).<br>";
    }

    echo "<hr><strong>Database schema updated successfully!</strong> All columns and tables are now synchronized with the code.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}