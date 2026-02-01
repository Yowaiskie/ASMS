<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ScheduleRepository;

class ScheduleController extends Controller {
    private $scheduleRepo;

    public function __construct() {
        $this->requireLogin();
        $this->scheduleRepo = new ScheduleRepository();
    }

    public function index() {
        $schedules = $this->scheduleRepo->getAll();
        
        $data = [
            'pageTitle' => 'Mass Schedules',
            'title' => 'Schedules | ASMS',
            'schedules' => $schedules
        ];
        
        $this->view('schedules/index', $data);
    }

    public function create() {
        $this->view('schedules/create', [
            'pageTitle' => 'New Schedule',
            'title' => 'Create Schedule | ASMS'
        ]);
    }

    public function store() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'mass_type' => trim($_POST['mass_type']),
                'mass_date' => $_POST['mass_date'],
                'mass_time' => $_POST['mass_time'],
                'status' => $_POST['status']
            ];

            if ($this->scheduleRepo->create($data)) {
                setFlash('msg_success', 'Schedule created successfully!');
            } else {
                setFlash('msg_error', 'Failed to create schedule.');
            }
            redirect('schedules');
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id && $this->scheduleRepo->delete($id)) {
            setFlash('msg_success', 'Schedule deleted.');
        } else {
            setFlash('msg_error', 'Failed to delete schedule.');
        }
        redirect('schedules');
    }
}