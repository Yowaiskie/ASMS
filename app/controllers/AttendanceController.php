<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AttendanceRepository;

class AttendanceController extends Controller {
    private $attendanceRepo;

    public function __construct() {
        $this->requireLogin();
        $this->attendanceRepo = new AttendanceRepository();
    }

    public function index() {
        $logs = $this->attendanceRepo->getAll();
        $this->view('attendance/index', [
            'pageTitle' => 'Attendance Management',
            'title' => 'Attendance | ASMS',
            'logs' => $logs
        ]);
    }

    public function update() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['attendance_id'];
            $status = $_POST['status'];

            if ($this->attendanceRepo->updateStatus($id, $status)) {
                setFlash('msg_success', 'Attendance updated.');
            } else {
                setFlash('msg_error', 'Update failed.');
            }
            redirect('attendance');
        }
    }
}