<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ScheduleRepository;
use App\Repositories\ServerRepository;
use App\Repositories\AnnouncementRepository;

class ScheduleController extends Controller {
    private $scheduleRepo;
    private $serverRepo;
    private $announcementRepo;
    private $userRepo;
    private $db;

    public function __construct() {
        $this->requireLogin();
        $this->scheduleRepo = new ScheduleRepository();
        $this->serverRepo = new ServerRepository();
        $this->announcementRepo = new AnnouncementRepository();
        $this->userRepo = new \App\Repositories\UserRepository();
        $this->db = \App\Core\Database::getInstance();
    }

    public function index() {
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'User') {
            // Get ALL schedules for the general calendar
            $allSchedules = $this->scheduleRepo->getAll();
            
            // Get user's assigned IDs
            $assignedIds = $this->scheduleRepo->getAssignedScheduleIds($_SESSION['user_id']);
            
            $data = [
                'pageTitle' => 'Mass Schedules',
                'title' => 'Schedules | ASMS',
                'schedules' => $allSchedules,
                'assignedIds' => $assignedIds
            ];
            
            $this->view('schedules/user_index', $data);
        } else {
            // Admin View
            $schedules = $this->scheduleRepo->getAll();
            $servers = $this->serverRepo->getAll();

            // Attach assignments to schedules
            foreach ($schedules as $s) {
                $assignments = $this->scheduleRepo->getAssignments($s->id);
                $s->assigned_servers = $assignments;
                $s->assigned_ids = array_map(function($a) { return (int)$a->id; }, $assignments);
            }

            // Get Current Admin's Server ID
            $this->db->query("SELECT server_id FROM users WHERE id = :uid");
            $this->db->bind(':uid', $_SESSION['user_id']);
            $cu = $this->db->single();
            $currentServerId = $cu ? $cu->server_id : null;
            
            $data = [
                'pageTitle' => 'Mass Schedules',
                'title' => 'Schedules | ASMS',
                'schedules' => $schedules,
                'servers' => $servers,
                'currentServerId' => $currentServerId
            ];
            
            $this->view('schedules/index', $data);
        }
    }

    public function getServers() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $servers = $this->scheduleRepo->getFullAssignments($id);
            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json');
            echo json_encode($servers);
        }
        exit;
    }

    public function create() {
        $this->view('schedules/create', [
            'pageTitle' => 'New Schedule',
            'title' => 'Create Schedule | ASMS'
        ]);
    }

    public function selfAssign() {
        $this->verifyCsrf();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Fetch Server Status
            $this->db->query("SELECT status, suspension_until FROM servers WHERE id = (SELECT server_id FROM users WHERE id = :uid)");
            $this->db->bind(':uid', $_SESSION['user_id']);
            $server = $this->db->single();

            if ($server && $server->status === 'Suspended') {
                $today = date('Y-m-d');
                if ($server->suspension_until >= $today) {
                    setFlash('msg_error', 'Your account is currently suspended until ' . date('M d, Y', strtotime($server->suspension_until)) . '.', 'bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm font-bold');
                    redirect('schedules');
                    return;
                } else {
                    // Auto-unsuspend if date passed
                    $this->db->query("UPDATE servers SET status = 'Active', suspension_until = NULL WHERE id = (SELECT server_id FROM users WHERE id = :uid)");
                    $this->db->bind(':uid', $_SESSION['user_id']);
                    $this->db->execute();
                }
            }

            // 2. Existing verification check
            if (($_SESSION['role'] ?? '') === 'User' && !($_SESSION['is_verified'] ?? 0)) {
                setFlash('msg_error', 'Your account must be verified before joining schedules.');
                redirect('settings');
                return;
            }

            $scheduleId = $_POST['schedule_id'];
            $schedule = $this->scheduleRepo->getById($scheduleId);

            if (!$schedule) {
                setFlash('msg_error', 'Schedule not found.');
                redirect('schedules');
                return;
            }

            // Check if schedule is in the past
            $scheduleDateTime = new \DateTime($schedule->mass_date . ' ' . $schedule->mass_time);
            $now = new \DateTime();

            if ($scheduleDateTime < $now) {
                setFlash('msg_error', 'This schedule has already passed and cannot be joined.', 'bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm font-bold');
                redirect('schedules');
                return;
            }

            if ($this->scheduleRepo->selfAssign($_SESSION['user_id'], $scheduleId)) {
                
                // Ensure full_name is available
                if (empty($_SESSION['full_name'])) {
                    $this->db->query("SELECT CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name FROM users u JOIN servers s ON u.server_id = s.id WHERE u.id = :id");
                    $this->db->bind(':id', $_SESSION['user_id']);
                    $res = $this->db->single();
                    $_SESSION['full_name'] = $res ? $res->name : $_SESSION['username'];
                }

                // Trigger Announcement
                $this->announcementRepo->create([
                    'title' => 'New Slot Filled',
                    'category' => 'System',
                    'message' => $_SESSION['full_name'] . ' has joined the schedule for ' . $schedule->mass_type . ' on ' . date('M d, Y', strtotime($schedule->mass_date)) . '.',
                    'author' => 'System'
                ]);

                // Trigger Email to User
                $this->db = \App\Core\Database::getInstance();
                $this->db->query("SELECT email, CONCAT_WS(' ', first_name, middle_name, last_name) as name FROM servers WHERE id = (SELECT server_id FROM users WHERE id = :uid)");
                $this->db->bind(':uid', $_SESSION['user_id']);
                $srv = $this->db->single();
                
                if ($srv && $srv->email) {
                    sendEmailNotification(
                        $srv->email,
                        'Schedule Confirmation',
                        'You Joined a New Schedule!',
                        "Hi {$srv->name}, you have successfully joined the schedule for <b>{$schedule->mass_type}</b> on <b>" . date('M d, Y', strtotime($schedule->mass_date)) . "</b> at <b>" . date('h:i A', strtotime($schedule->mass_time)) . "</b>. See you there!"
                    );
                }

                // Notify Admins
                $admins = $this->userRepo->getAdmins();
                foreach ($admins as $admin) {
                    sendEmailNotification(
                        $admin->email,
                        'System Alert: New Volunteer',
                        'A slot has been filled!',
                        "User <b>{$_SESSION['username']}</b> has self-assigned to the schedule for <b>{$schedule->mass_type}</b> on <b>" . date('M d, Y', strtotime($schedule->mass_date)) . "</b>."
                    );
                }

                logAction('Update', 'Schedules', "User self-assigned to schedule ID: $scheduleId");
                setFlash('msg_success', 'Successfully joined the schedule!');
            } else {
                setFlash('msg_error', 'Failed to join schedule.');
            }
            redirect('schedules');
        }
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
            $isRecurring = isset($_POST['is_recurring']);

            if (!empty($id)) {
                // Update (Single)
                if ($this->scheduleRepo->update($id, $data)) {
                    $this->scheduleRepo->syncAssignments($id, $assignedServers);
                    
                    // Email newly assigned servers
                    $this->notifyAssignedServers($id, $data, $assignedServers);

                    logAction('Update', 'Schedules', "Updated schedule ID: $id (" . $data['mass_type'] . ")");
                    setFlash('msg_success', 'Schedule updated successfully!');
                } else {
                    setFlash('msg_error', 'Failed to update schedule.');
                }
            } elseif ($isRecurring) {
                // Create Recurring
                $frequency = $_POST['frequency'] ?? 'daily';
                $interval = (int)($_POST['interval'] ?? 1);
                $endDate = $_POST['end_date'] ?? '';
                $recurringDays = $_POST['recurring_days'] ?? []; // Indices 0-6

                if (empty($endDate)) {
                    setFlash('msg_error', 'End date is required for recurring schedules.');
                    redirect('schedules');
                    return;
                }

                $this->db->beginTransaction();
                try {
                    $startDate = new \DateTime($data['mass_date']);
                    $end = new \DateTime($endDate);
                    $current = clone $startDate;
                    $count = 0;

                    while ($current <= $end) {
                        $shouldCreate = false;
                        
                        if ($frequency === 'daily') {
                            $shouldCreate = true;
                        } elseif ($frequency === 'weekly') {
                            if (empty($recurringDays)) {
                                $shouldCreate = true; // If no days selected, just repeat the start day
                            } else {
                                if (in_array($current->format('w'), $recurringDays)) {
                                    $shouldCreate = true;
                                }
                            }
                        } elseif ($frequency === 'monthly') {
                            $shouldCreate = true;
                        }

                        if ($shouldCreate) {
                            $data['mass_date'] = $current->format('Y-m-d');
                            if ($this->scheduleRepo->create($data)) {
                                $newId = $this->db->lastInsertId();
                                if ($newId) {
                                    $this->scheduleRepo->syncAssignments($newId, $assignedServers);
                                    $this->notifyAssignedServers($newId, $data, $assignedServers);
                                }
                                $count++;
                            }
                        }

                        // Increment based on frequency
                        if ($frequency === 'daily') {
                            $current->modify("+$interval day");
                        } elseif ($frequency === 'weekly') {
                            // If we are checking specific days, we go day by day, 
                            // but if we hit the end of the week, we jump by interval weeks
                            if (empty($recurringDays)) {
                                $current->modify("+$interval week");
                            } else {
                                $current->modify("+1 day");
                                if ($current->format('w') == $startDate->format('w')) {
                                    if ($interval > 1) {
                                        $skip = $interval - 1;
                                        $current->modify("+$skip week");
                                    }
                                }
                            }
                        } elseif ($frequency === 'monthly') {
                            $current->modify("+$interval month");
                        }
                    }

                    $this->db->commit();
                    
                    // Trigger Announcement
                    $this->announcementRepo->create([
                        'title' => 'Recurring Schedules Added',
                        'category' => 'Mass Schedule',
                        'message' => $count . ' new schedules for ' . $data['mass_type'] . ' have been generated until ' . date('M d, Y', strtotime($endDate)) . '.',
                        'author' => $_SESSION['full_name'] ?? 'Admin'
                    ]);

                    logAction('Create', 'Schedules', "Created $count recurring schedules for " . $data['mass_type']);
                    setFlash('msg_success', "Successfully created $count recurring schedules!");
                } catch (\Exception $e) {
                    $this->db->rollBack();
                    setFlash('msg_error', 'Error creating recurring schedules: ' . $e->getMessage());
                }
            } else {
                // Create (Single)
                if ($this->scheduleRepo->create($data)) {
                    $newId = $this->db->lastInsertId();
                    
                    if ($newId) {
                        $this->scheduleRepo->syncAssignments($newId, $assignedServers);
                        $this->notifyAssignedServers($newId, $data, $assignedServers);
                    }
                    
                    // Trigger Announcement
                    $this->announcementRepo->create([
                        'title' => 'New Schedule Added',
                        'category' => 'Mass Schedule',
                        'message' => 'A new ' . $data['mass_type'] . ' has been scheduled for ' . date('M d, Y', strtotime($data['mass_date'])) . ' at ' . date('h:i A', strtotime($data['mass_time'])) . '.',
                        'author' => $_SESSION['full_name'] ?? 'Admin'
                    ]);

                    logAction('Create', 'Schedules', "Created new schedule: " . $data['mass_type'] . " on " . $data['mass_date']);
                    setFlash('msg_success', 'Schedule created successfully!');
                } else {
                    setFlash('msg_error', 'Failed to create schedule.');
                }
            }
            redirect('schedules');
        }
    }

    private function notifyAssignedServers($scheduleId, $data, $assignedServers) {
        if (empty($assignedServers)) return;
        
        foreach ($assignedServers as $svid) {
            $this->db->query("SELECT email, CONCAT_WS(' ', first_name, middle_name, last_name) as name FROM servers WHERE id = :sid");
            $this->db->bind(':sid', $svid);
            $srv = $this->db->single();
            if ($srv && $srv->email) {
                sendEmailNotification(
                    $srv->email,
                    'Schedule Assignment',
                    'You Have a New Assignment!',
                    "Hi {$srv->name}, you have been assigned to: <b>{$data['mass_type']}</b> on <b>" . date('M d, Y', strtotime($data['mass_date'])) . "</b> at <b>" . date('h:i A', strtotime($data['mass_time'])) . "</b>."
                );
            }
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id && $this->scheduleRepo->delete($id)) {
            logAction('Delete', 'Schedules', "Deleted schedule ID: $id");
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
            logAction('Delete', 'Schedules', "Bulk deleted $count schedules.");
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
                logAction('Update', 'Schedules', "Bulk updated status to $status for $count schedules.");
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

                // Sunday: All slots + Fixed Meeting
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

                    // Add Fixed Meeting (1:00 PM - 3:00 PM)
                    $this->scheduleRepo->create([
                        'mass_type' => 'Meeting',
                        'mass_date' => $dateStr,
                        'mass_time' => '13:00',
                        'status' => 'Confirmed',
                        'color' => 'gray'
                    ]);
                    $count++;
                }
                
                $date->modify('+1 day');
            }

            logAction('Create', 'Schedules', "Generated $count schedules for $month/$year.");
            
            // Bulk Announcement
            $this->announcementRepo->create([
                'title' => 'Monthly Schedules Generated',
                'category' => 'System',
                'message' => "Schedules for " . date('F Y', strtotime("$year-$month-01")) . " have been automatically generated. Please check the calendar for your assignments.",
                'author' => 'System'
            ]);

            setFlash('msg_success', "Generated $count schedules (Saturdays & Sundays) for $month/$year.");
        } catch (\Exception $e) {
            setFlash('msg_error', "Error generating schedules: " . $e->getMessage());
        }
        
        redirect('schedules');
    }

    public function import() {
        $this->verifyCsrf();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $fileName = $_FILES['csv_file']['tmp_name'];
            
            if ($_FILES['csv_file']['size'] > 0) {
                $file = fopen($fileName, "r");
                $count = 0;
                $firstRow = true;
                
                while (($line = fgetcsv($file, 10000, ",")) !== FALSE) {
                    $column = $line;

                    // Delimiter Detection: If only 1 column found, try semicolon
                    if (count($column) == 1 && strpos($column[0], ';') !== false) {
                        $column = str_getcsv($column[0], ';');
                    }

                    if ($firstRow) { $firstRow = false; continue; } // Skip Header row

                    if (count($column) < 3) continue;
                    
                    // Clean BOM or weird chars from first column
                    $column[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $column[0]);

                    $date = trim($column[0]);
                    $time = trim($column[1]);
                    $type = trim($column[2]);
                    $eventName = isset($column[3]) ? trim($column[3]) : null;

                    if (empty($date) || empty($time)) continue;

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
                logAction('Create', 'Schedules', "Imported $count schedules via CSV.");
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