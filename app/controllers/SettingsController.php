<?php

namespace App\Controllers;

use App\Core\Controller;

class SettingsController extends Controller {
    public function __construct() {
        $this->requireLogin();
    }

    public function index() {
        $this->view('settings/index', [
            'pageTitle' => 'System Settings',
            'title' => 'Settings | ASMS'
        ]);
    }

    public function store() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // In a real app, you'd save these to a 'settings' table in DB
            // For now, we'll mock success
            setFlash('msg_success', 'System settings updated successfully!');
            redirect('settings');
        }
    }
}