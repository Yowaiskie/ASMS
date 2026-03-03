<?php

namespace App\Controllers\Api;

class TrainingController extends ApiController {
    public function __construct() {
        $this->requireLoginApi();
    }

    public function index() {
        $this->ok(['message' => 'Training modules available.']);
    }
}
