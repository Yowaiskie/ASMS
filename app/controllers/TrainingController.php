<?php

namespace App\Controllers;

use App\Core\Controller;

class TrainingController extends Controller {
    public function index() {
        $this->requireLogin();
        $this->view('trainings/index', [
            'pageTitle' => 'Training Modules',
            'title' => 'Trainings | ASMS'
        ]);
    }
}