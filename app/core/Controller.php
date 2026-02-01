<?php

namespace App\Core;

class Controller {
    // Load Model
    public function model($model) {
        // Require model file
        require_once '../app/models/' . $model . '.php';
        
        // Instantiate model
        $modelClass = "App\\Models\\" . $model;
        return new $modelClass();
    }

    // Load View
    public function view($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        // Check for view file
        if (file_exists('../app/views/pages/' . $view . '.php')) {
            // Start buffering
            ob_start();
            require_once '../app/views/pages/' . $view . '.php';
            $content = ob_get_clean();
            
            // Require main layout
            require_once '../app/views/layouts/main.php';
        } else {
            // View does not exist
            die('View does not exist');
        }
    }

    protected function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/login');
            exit;
        }
    }

    protected function verifyCsrf() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            verify_csrf();
        }
    }
}
