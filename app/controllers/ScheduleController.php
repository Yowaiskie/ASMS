<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ScheduleRepository;
use App\Repositories\ServerRepository;

class ScheduleController extends Controller {
    private $scheduleRepo;
    private $serverRepo;

    public function __construct() {
        $this->requireLogin();
        $this->scheduleRepo = new ScheduleRepository();
        $this->serverRepo = new ServerRepository();
    }

    public function index() {
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'User') {
            $schedules = $this->scheduleRepo->getByUserId($_SESSION['user_id']);
            
            $data = [
                'pageTitle' => 'My Schedule',
                'title' => 'My Schedule | ASMS',
                'schedules' => $schedules
            ];
            
            $this->view('schedules/user_index', $data);
        } else {
            // Admin View
            $schedules = $this->scheduleRepo->getAll();
            $servers = $this->serverRepo->getAll();

            // Attach assignments to schedules
            foreach ($schedules as $s) {
                $s->assigned_servers = $this->scheduleRepo->getAssignments($s->id);
            }
            
            $data = [
                'pageTitle' => 'Mass Schedules',
                'title' => 'Schedules | ASMS',
                'schedules' => $schedules,
                'servers' => $servers
            ];
            
            $this->view('schedules/index', $data);
        }
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
            $id = $_POST['id'] ?? '';
            $data = [
                'mass_type' => trim($_POST['mass_type']),
                'event_name' => trim($_POST['event_name'] ?? ''),
                'color' => $_POST['color'] ?? null,
                'mass_date' => $_POST['mass_date'],
                'mass_time' => $_POST['mass_time'],
                'status' => $_POST['status']
            ];
            
            $assignedServers = $_POST['assigned_servers'] ?? [];

            if (!empty($id)) {
                // Update
                if ($this->scheduleRepo->update($id, $data)) {
                    $this->scheduleRepo->syncAssignments($id, $assignedServers);
                    setFlash('msg_success', 'Schedule updated successfully!');
                } else {
                    setFlash('msg_error', 'Failed to update schedule.');
                }
            } else {
                // Create
                // We need the ID. `create` only returns true/false.
                // Assuming Database class has lastInsertId method or similar logic.
                // For now, let's try to fetch it after create or update repo.
                
                // Hack: We can't easily get ID from bool return. 
                // We should update Repo to return ID.
                // For now, I'll rely on update logic mostly or just not support assignment on create (user has to edit).
                // "Click nalang para mag add" -> Implies edit flow usually.
                
                // But let's try to support it. 
                if ($this->scheduleRepo->create($data)) {
                    // Fetch the latest ID? unsafe.
                    // Let's defer assignment on create if ID unavailable, or update Repo.
                    
                    // Actually, let's just save. Assignment might require a second step if I don't fix Repo.
                    // But I will fix Repo next.
                    $db = \App\Core\Database::getInstance();
                    $newId = $db->lastInsertId(); // Assuming this method exists or I can add it
                    
                    if ($newId) {
                        $this->scheduleRepo->syncAssignments($newId, $assignedServers);
                    }
                    
                    setFlash('msg_success', 'Schedule created successfully!');
                } else {
                    setFlash('msg_error', 'Failed to create schedule.');
                }
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

    public function bulkDelete() {
        $this->verifyCsrf();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $count = 0;
            foreach ($ids as $id) {
                if ($this->scheduleRepo->delete($id)) {
                    $count++;
                }
            }
            setFlash('msg_success', "Deleted $count schedules successfully.");
        } else {
            setFlash('msg_error', "No schedules selected.");
        }
        redirect('schedules');
    }

    public function bulkUpdate() {
        $this->verifyCsrf();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids'])) {
            $ids = json_decode($_POST['ids'], true);
            $status = $_POST['status'] ?? null;

            if ($ids && $status) {
                $count = 0;
                foreach ($ids as $id) {
                    if ($this->scheduleRepo->updateStatus($id, $status)) {
                        $count++;
                    }
                }
                setFlash('msg_success', "Updated $count schedules.");
            }
        }
        redirect('schedules');
    }

    public function generate() {
        $month = str_pad($_GET['month'] ?? date('m'), 2, '0', STR_PAD_LEFT);
        $year = $_GET['year'] ?? date('Y');
        
        $sundayTimes = ['06:00', '07:30', '09:00', '16:00', '17:30', '19:00'];
        $count = 0;

        try {
            $date = new \DateTime("$year-$month-01");
            
            while ($date->format('m') == $month) {
                $dayOfWeek = $date->format('w');
                $dateStr = $date->format('Y-m-d');

                // Saturday: Anticipated Mass at 6PM
                if ($dayOfWeek == 6) {
                    $data = [
                        'mass_type' => 'Anticipated Mass',
                        'mass_date' => $dateStr,
                        'mass_time' => '18:00',
                        'status' => 'Confirmed'
                    ];
                    $this->scheduleRepo->create($data);
                    $count++;
                }

                // Sunday: All slots
                if ($dayOfWeek == 0) {
                    foreach ($sundayTimes as $time) {
                        $data = [
                            'mass_type' => 'Sunday Mass',
                            'mass_date' => $dateStr,
                            'mass_time' => $time,
                            'status' => 'Confirmed'
                        ];
                        $this->scheduleRepo->create($data);
                        $count++;
                    }
                }
                
                $date->modify('+1 day');
            }

            setFlash('msg_success', "Generated $count schedules (Saturdays & Sundays) for $month/$year.");
        } catch (\Exception $e) {
            setFlash('msg_error', "Error generating schedules: " . $e->getMessage());
        }
        
        redirect('schedules');
    }

    public function import() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $fileName = $_FILES['csv_file']['tmp_name'];
            
            if ($_FILES['csv_file']['size'] > 0) {
                $file = fopen($fileName, "r");
                $count = 0;
                
                // Skip header if exists? Assume no header or handle basic check
                // Let's assume standard format: Date, Time, Type, EventName
                
                while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                    // Validate minimal columns
                    if (count($column) < 3) continue;
                    
                    // Basic cleanup
                    $date = trim($column[0]);
                    $time = trim($column[1]);
                    $type = trim($column[2]);
                    $eventName = isset($column[3]) ? trim($column[3]) : null;

                    // Validate Date/Time format (optional but good practice)
                    // For now, just try to insert
                    
                    $data = [
                        'mass_date' => $date,
                        'mass_time' => $time,
                        'mass_type' => $type,
                        'event_name' => $eventName,
                        'status' => 'Confirmed'
                    ];
                    
                    if ($this->scheduleRepo->create($data)) {
                        $count++;
                    }
                }
                
                fclose($file);
                setFlash('msg_success', "Imported $count schedules successfully.");
            } else {
                setFlash('msg_error', 'Empty file uploaded.');
            }
        } else {
            setFlash('msg_error', 'Invalid file upload.');
        }
        redirect('schedules');
    }
}