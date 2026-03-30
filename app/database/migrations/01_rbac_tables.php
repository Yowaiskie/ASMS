<?php

// Local overrides for migration
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'asms_db');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();

    echo "Starting RBAC tables migration...\n";

    // 1. Create roles table
    $db->query("CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) UNIQUE NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $db->execute();
    echo "Created 'roles' table.\n";

    // 2. Create permissions table
    $db->query("CREATE TABLE IF NOT EXISTS permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        module VARCHAR(50) NOT NULL,
        action VARCHAR(50) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $db->execute();
    echo "Created 'permissions' table.\n";

    // 3. Create role_permissions table
    $db->query("CREATE TABLE IF NOT EXISTS role_permissions (
        role_id INT NOT NULL,
        permission_id INT NOT NULL,
        PRIMARY KEY (role_id, permission_id),
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
        FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
    )");
    $db->execute();
    echo "Created 'role_permissions' table.\n";

    // 4. Update users table (add role_id if not exists)
    $db->query("SHOW COLUMNS FROM users LIKE 'role_id'");
    if (!$db->single()) {
        $db->query("ALTER TABLE users ADD COLUMN role_id INT AFTER role");
        $db->execute();
        $db->query("ALTER TABLE users ADD CONSTRAINT fk_user_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL");
        $db->execute();
        echo "Added 'role_id' to 'users' table.\n";
    }

    // 5. Seed Roles
    $roles = [
        ['name' => 'Superadmin', 'description' => 'System Owner with full access.'],
        ['name' => 'Admin', 'description' => 'Ministry Leader/Coordinator.'],
        ['name' => 'User', 'description' => 'Altar Server with basic access.']
    ];

    foreach ($roles as $role) {
        $db->query("INSERT IGNORE INTO roles (name, description) VALUES (:name, :description)");
        $db->bind(':name', $role['name']);
        $db->bind(':description', $role['description']);
        $db->execute();
    }
    echo "Seeded initial roles.\n";

    // 6. Seed Permissions
    $modules = [
        'Dashboard' => ['view'],
        'Activity Center' => ['view'],
        'Schedules' => ['view', 'create', 'edit', 'delete', 'self_assign'],
        'Master Plan' => ['view', 'manage'],
        'Attendance' => ['view', 'track'],
        'Excuse Letters' => ['view', 'submit', 'approve'],
        'Server Profiles' => ['view', 'create', 'edit', 'delete'],
        'Server Accounts' => ['view', 'create', 'edit', 'delete'],
        'Reports' => ['view', 'generate'],
        'Audit Logs' => ['view'],
        'Archive Center' => ['view', 'restore', 'delete'],
        'System Settings' => ['view', 'edit'],
        'System Configuration' => ['view', 'edit'],
        'Database Management' => ['view', 'backup', 'restore'],
        'Roles Management' => ['view', 'create', 'edit', 'delete']
    ];

    foreach ($modules as $module => $actions) {
        foreach ($actions as $action) {
            $permissionName = ucwords(str_replace('_', ' ', $action)) . " " . $module;
            $db->query("INSERT IGNORE INTO permissions (name, module, action) VALUES (:name, :module, :action)");
            $db->bind(':name', $permissionName);
            $db->bind(':module', $module);
            $db->bind(':action', $action);
            $db->execute();
        }
    }
    echo "Seeded module-based permissions.\n";

    // 7. Map Role Permissions (Initial Mapping to mimic current hardcoded logic)
    
    // Get role IDs
    $db->query("SELECT id, name FROM roles");
    $rolesMap = [];
    foreach ($db->resultSet() as $r) {
        $rolesMap[$r->name] = $r->id;
    }

    // Get all permissions
    $db->query("SELECT id, module, action FROM permissions");
    $allPerms = $db->resultSet();

    // Mapping logic
    foreach ($allPerms as $p) {
        // Superadmin gets everything
        $db->query("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)");
        $db->bind(':role_id', $rolesMap['Superadmin']);
        $db->bind(':permission_id', $p->id);
        $db->execute();

        // Admin mapping
        $adminModules = ['Dashboard', 'Activity Center', 'Schedules', 'Master Plan', 'Attendance', 'Excuse Letters', 'Server Profiles', 'Reports', 'System Settings', 'System Configuration'];
        if (in_array($p->module, $adminModules)) {
            $db->query("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)");
            $db->bind(':role_id', $rolesMap['Admin']);
            $db->bind(':permission_id', $p->id);
            $db->execute();
        }

        // User mapping
        $userModules = ['Dashboard', 'Activity Center', 'Schedules', 'Attendance', 'Excuse Letters'];
        if (in_array($p->module, $userModules)) {
            // Limited actions for User
            $userAllowedActions = ['view', 'submit', 'self_assign'];
            if (in_array($p->action, $userAllowedActions)) {
                $db->query("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)");
                $db->bind(':role_id', $rolesMap['User']);
                $db->bind(':permission_id', $p->id);
                $db->execute();
            }
        }
    }
    echo "Mapped role permissions.\n";

    // 8. Migrate existing users to role_id
    foreach ($rolesMap as $name => $id) {
        $db->query("UPDATE users SET role_id = :role_id WHERE role = :role_name");
        $db->bind(':role_id', $id);
        $db->bind(':role_name', $name);
        $db->execute();
    }
    echo "Migrated users to 'role_id'.\n";

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
