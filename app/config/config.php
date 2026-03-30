<?php

// Load composer autoloader and .env
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (file_exists(dirname(__DIR__, 2) . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
    $dotenv->load();
}

define('DB_HOST', $_ENV['DB_HOST'] ?? 'sql310.infinityfree.com');
define('DB_USER', $_ENV['DB_USER'] ?? 'if0_41112521');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'AMFKISAsg8n');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'if0_41112521_asms_db');

define('DEFAULT_USER_PASSWORD', $_ENV['DEFAULT_USER_PASSWORD'] ?? '12345');

define('APPROOT', dirname(dirname(__FILE__)));

// Dynamic URLROOT
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$urlRoot = str_replace('/index.php', '', $scriptName);

// If we are in the root (htdocs), urlRoot might be empty or just a slash.
// We want it to be empty if it's the root to avoid double slashes like //login
$urlRoot = rtrim($urlRoot, '/');
define('URLROOT', $urlRoot);

define('SITENAME', 'ASMS');

// Set Timezone
date_default_timezone_set('Asia/Manila');

// SMTP Config
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? 587);
define('SMTP_USER', $_ENV['SMTP_USER'] ?? '');
define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? '');
define('SMTP_FROM', $_ENV['SMTP_FROM'] ?? '');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'ASMS Notifications');
