<?php
// setup_database.php
// Run this file in your browser: http://localhost/ASMS/public/setup_database.php

require_once __DIR__ . '/../app/config/config.php';

try {
    // Connect to MySQL server (without selecting DB first to ensure it exists)
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "Database '" . DB_NAME . "' checked/created successfully.<br>";

    // Connect to the specific database
    $pdo->exec("USE " . DB_NAME);

    // --- Table: servers ---
    $sql = "CREATE TABLE IF NOT EXISTS servers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        rank VARCHAR(50) NOT NULL, -- Senior, Junior, Aspirant
        team VARCHAR(50) NOT NULL, -- Team A, Team B
        status VARCHAR(20) DEFAULT 'Active',
        email VARCHAR(100),
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'servers' created.<br>";

    // Seed Servers
    $check = $pdo->query("SELECT count(*) FROM servers")->fetchColumn();
    if ($check == 0) {
        $stmt = $pdo->prepare("INSERT INTO servers (name, rank, team, status, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Juan Dela Cruz', 'Senior Server', 'Team A', 'Active', 'juan@example.com']);
        $stmt->execute(['Pedro Penduko', 'Junior Server', 'Team B', 'Active', 'pedro@example.com']);
        $stmt->execute(['Cardo Dalisay', 'Aspirant', 'Team A', 'Inactive', 'cardo@example.com']);
        $stmt->execute(['Jose Rizal', 'Head Server', 'Team A', 'Active', 'jose@example.com']);
        echo "Sample data inserted into 'servers'.<br>";
    }

    // --- Table: schedules ---
    $sql = "CREATE TABLE IF NOT EXISTS schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mass_type VARCHAR(100) NOT NULL,
        mass_date DATE NOT NULL,
        mass_time VARCHAR(20) NOT NULL,
        status VARCHAR(20) DEFAULT 'Confirmed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'schedules' created.<br>";

    // Seed Schedules
    $check = $pdo->query("SELECT count(*) FROM schedules")->fetchColumn();
    if ($check == 0) {
        $stmt = $pdo->prepare("INSERT INTO schedules (mass_type, mass_date, mass_time, status) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Sunday Mass (Early)', '2026-02-08', '06:00 AM', 'Confirmed']);
        $stmt->execute(['Sunday Mass (Regular)', '2026-02-08', '08:00 AM', 'Confirmed']);
        $stmt->execute(['Wedding Mass', '2026-02-10', '10:00 AM', 'Pending']);
        $stmt->execute(['Funeral Mass', '2026-02-11', '01:00 PM', 'Confirmed']);
        echo "Sample data inserted into 'schedules'.<br>";
    }

    // --- Table: attendance ---
    $sql = "CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        server_id INT,
        schedule_id INT,
        status VARCHAR(20) NOT NULL, -- Present, Late, Absent
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'attendance' created.<br>";

    // Seed Attendance
    $check = $pdo->query("SELECT count(*) FROM attendance")->fetchColumn();
    if ($check == 0) {
        $stmt = $pdo->prepare("INSERT INTO attendance (server_id, schedule_id, status) VALUES (?, ?, ?)");
        $stmt->execute([1, 1, 'Present']);
        $stmt->execute([2, 1, 'Late']);
        $stmt->execute([4, 1, 'Present']);
        echo "Sample data inserted into 'attendance'.<br>";
    }

    // --- Table: users ---
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) NOT NULL, -- User, Admin, Superadmin
        is_verified TINYINT(1) DEFAULT 0,
        last_read_announcements TIMESTAMP NULL,
        server_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'users' created.<br>";

    // Seed Users
    $check = $pdo->query("SELECT count(*) FROM users")->fetchColumn();
    if ($check == 0) {
        $password = password_hash('123', PASSWORD_DEFAULT); // Hash the password '123'
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        
        // Create the requested accounts
        $stmt->execute(['user', $password, 'User']);
        $stmt->execute(['admin', $password, 'Admin']);
        $stmt->execute(['superadmin', $password, 'Superadmin']);
        
        echo "Sample accounts inserted (Pass: 123).<br>";
    }

    // --- Table: announcements ---
    $sql = "CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        author VARCHAR(100) DEFAULT 'Admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'announcements' created.<br>";

    // --- Table: logs ---
    $sql = "CREATE TABLE IF NOT EXISTS logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(50), -- Create, Update, Delete, Login
        module VARCHAR(50), -- Servers, Schedules, etc.
        description TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'logs' created.<br>";

    // --- Table: system_settings ---
    $sql = "CREATE TABLE IF NOT EXISTS system_settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'system_settings' created.<br>";

    // --- Table: excuses ---
    $sql = "CREATE TABLE IF NOT EXISTS excuses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        server_id INT NOT NULL,
        type VARCHAR(50), -- Mass, Meeting, Event
        absence_date DATE,
        absence_time TIME,
        reason TEXT,
        image_path VARCHAR(255),
        status VARCHAR(20) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'excuses' created.<br>";

    // Seed System Settings
    $check = $pdo->query("SELECT count(*) FROM system_settings")->fetchColumn();
    if ($check == 0) {
        $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute(['system_name', 'Altar Servers Management System']);
        $stmt->execute(['admin_email', 'admin@church.org']);
        $stmt->execute(['contact_phone', '+63 912 345 6789']);
        $stmt->execute(['maintenance_mode', 'off']);
        $stmt->execute(['allow_registration', 'on']);
        echo "Default system settings seeded.<br>";
    }

    echo "<hr><strong>Database setup completed successfully!</strong> <a href='" . URLROOT . "'>Go to Dashboard</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
