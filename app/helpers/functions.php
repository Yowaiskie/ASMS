<?php

// XSS Protection: Escape String
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Redirect Helper
function redirect($page) {
    header('Location: ' . URLROOT . '/' . $page);
    exit;
}

// Session Flash Message Helper
// Usage: setFlash('register_success', 'You are now registered');
// Display: flash('register_success');
function setFlash($name = '', $message = '', $class = 'bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm') {
    if(!empty($name)){
        if(!empty($message) && empty($_SESSION[$name])){
            if(!empty($_SESSION[$name])){
                unset($_SESSION[$name]);
            }
            if(!empty($_SESSION[$name. '_class'])){
                unset($_SESSION[$name. '_class']);
            }
            $_SESSION[$name] = $message;
            $_SESSION[$name. '_class'] = $class;
        }
    }
}

function flash($name = ''){
    if(!empty($name)){
        if(!empty($_SESSION[$name])){
            $class = !empty($_SESSION[$name. '_class']) ? $_SESSION[$name. '_class'] : '';
            echo '<div class="'.$class.'" id="msg-flash">'.$_SESSION[$name].'</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name. '_class']);
        }
    }
}

// CSRF Protection Helpers
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    echo '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF Validation Failed: Invalid Request');
        }
    }
    return true;
}

function logAction($action, $module, $description) {
    $db = \App\Core\Database::getInstance();
    $db->query("INSERT INTO logs (user_id, action, module, description, ip_address) VALUES (:u, :a, :m, :d, :ip)");
    $db->bind(':u', $_SESSION['user_id'] ?? null);
    $db->bind(':a', $action);
    $db->bind(':m', $module);
    $db->bind(':d', $description);
    $db->bind(':ip', $_SERVER['REMOTE_ADDR']);
    $db->execute();
}

function dd($value) {
    echo "<pre>";
    print_r($value);
    echo "</pre>";
    die();
}

/**
 * Build URL with preserved query parameters
 */
function build_url($base, $new_params = []) {
    $current_params = $_GET;
    // Remove page from current params if it's being replaced or we want to reset it
    // Usually when filtering we reset to page 1
    $merged_params = array_merge($current_params, $new_params);
    return URLROOT . '/' . $base . '?' . http_build_query($merged_params);
}
