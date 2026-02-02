<?php

session_start();

require_once '../app/config/config.php';
require_once '../app/helpers/functions.php';
require_once '../app/helpers/mail_helper.php';

// Simple Autoloader
spl_autoload_register(function ($className) {
    // Convert namespace to full file path
    // App\Core\Router -> ../app/core/Router.php
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }

    $relative_class = substr($className, $len);
    
    // Replace namespace separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Fix case sensitivity for directories if necessary (basic mapping)
    // For Windows it's fine, for Linux casing matters. 
    // This simple replace assumes folder structure matches namespace casing exactly 
    // or is lowercased where appropriate.
    // Our structure: App\Core -> app/core
    
    // Let's try to handle the lowercase folder convention vs PascalCase classes
    // App\Core\Router -> app/core/Router.php
    // App\Controllers\DashboardController -> app/controllers/DashboardController.php
    
    // Split the path parts
    $parts = explode('\\', $relative_class);
    // Lowercase all parts except the last one (the file name)
    $fileName = array_pop($parts);
    $path = implode('/', array_map('strtolower', $parts));
    
    $file = $base_dir . $path . '/' . $fileName . '.php';

    if (file_exists($file)) {
        require $file;
    } else {
        // Fallback or debug
        // echo "Autoload error: $file not found<br>";
    }
});

use App\Core\Router;

// Init Router
$router = new Router();

// Load Routes
require_once '../app/routes.php';

// Resolve Route
$router->resolve();
