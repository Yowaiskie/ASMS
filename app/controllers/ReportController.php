<?php

namespace App\Controllers;

use App\Core\Controller;

class ReportController extends Controller {
    public function __construct() {
        $this->requireLogin();
    }

    public function index() {
        $this->view('reports/index', [
            'pageTitle' => 'Reports & Analytics',
            'title' => 'Reports | ASMS'
        ]);
    }
}