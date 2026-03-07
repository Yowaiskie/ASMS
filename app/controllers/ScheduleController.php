<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ScheduleRepository;
use App\Repositories\ServerRepository;
use App\Repositories\AnnouncementRepository;
use App\Repositories\ScheduleTemplateRepository;

class ScheduleController extends Controller {
    private $scheduleRepo;
    private $serverRepo;
    private $announcementRepo;
    private $templateRepo;
    private $seasonRepo;
    private $presetRepo;
    private $userRepo;
    private $db;

    public function __construct() {
        $this->requireLogin();
        $this->scheduleRepo = new ScheduleRepository();
        $this->serverRepo = new ServerRepository();
        $this->announcementRepo = new AnnouncementRepository();
        $this->templateRepo = new ScheduleTemplateRepository();
        $this->seasonRepo = new \App\Repositories\LiturgicalSeasonRepository();
        $this->presetRepo = new \App\Repositories\SchedulePresetRepository();
        $this->userRepo = new \App\Repositories\UserRepository();
        $this->db = \App\Core\Database::getInstance();
    }

    public function index() {
        $seasons = $this->seasonRepo->getAll();

        if (isset($_SESSION['role']) && $_SESSION['role'] === 'User') {
            // Get ALL schedules for the general calendar
            $allSchedules = $this->scheduleRepo->getAll();
            
            // Dynamic Liturgical Override for Users
            foreach ($allSchedules as $s) {
                foreach ($seasons as $season) {
                    if ($s->mass_date >= $season->start_date && $s->mass_date <= $season->end_date) {
                        $exempted = !empty($season->exempted_types) ? json_decode($season->exempted_types, true) : [];
                        if (in_array($s->mass_type, $exempted)) continue;

                        $s->color = $season->color;
                        if ($s->mass_type === 'Sunday Mass' || $s->mass_type === 'Weekday Mass') {
                            $s->event_name = ($s->event_name ? $s->event_name . " (" . $season->name . ")" : $season->name);
                        }
                        break;
                    }
                }
            }

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
            $activityTypes = $this->templateRepo instanceof \App\Repositories\ScheduleTemplateRepository 
                ? (new \App\Repositories\SystemSettingRepository())->getActivityTypes() 
                : [];

            // Dynamic Liturgical Override for Admins
            foreach ($schedules as $s) {
                foreach ($seasons as $season) {
                    if ($s->mass_date >= $season->start_date && $s->mass_date <= $season->end_date) {
                        $exempted = !empty($season->exempted_types) ? json_decode($season->exempted_types, true) : [];
                        if (in_array($s->mass_type, $exempted)) continue;

                        $s->color = $season->color;
                        if ($s->mass_type === 'Sunday Mass' || $s->mass_type === 'Weekday Mass') {
                            $s->event_name = ($s->event_name ? $s->event_name . " (" . $season->name . ")" : $season->name);
                        }
                        break;
                    }
                }
            }

            // Get Current Admin's Server ID
            $this->db->query("SELECT server_id FROM users WHERE id = :uid");
            $this->db->bind(':uid', $_SESSION['user_id']);
            $cu = $this->db->single();
            $currentServerId = $cu ? $cu->server_id : null;
            
            $systemRepo = new \App\Repositories\SystemSettingRepository();
            $data = [
                'pageTitle' => 'Mass Schedules',
                'title' => 'Schedules | ASMS',
                'schedules' => $schedules,
                'servers' => $servers,
                'activityTypes' => $activityTypes,
                'currentServerId' => $currentServerId,
                'policy_schedule_start_date' => $systemRepo->get('policy_schedule_start_date', date('Y-m-01')),
                'policy_schedule_end_date' => $systemRepo->get('policy_schedule_end_date', date('Y-m-t', strtotime('+2 months')))
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
                
                // In-App Notification (Admins)
                $admins = $this->userRepo->getAdmins();
                $notifRepo = new \App\Repositories\NotificationRepository();
                foreach ($admins as $admin) {
                    $notifRepo->create([
                        'user_id' => $admin->id,
                        'title' => 'New Self-Assignment',
                        'message' => "{$_SESSION['username']} has self-assigned to the {$schedule->mass_type} on " . date('M d', strtotime($schedule->mass_date)) . ".",
                        'link' => '/attendance',
                        'type' => 'schedule'
                    ]);
                }

                // In-App Notification (The User itself)
                $notifRepo->create([
                    'user_id' => $_SESSION['user_id'],
                    'title' => 'Schedule Confirmed',
                    'message' => "You have successfully joined the {$schedule->mass_type} on " . date('M d', strtotime($schedule->mass_date)) . ".",
                    'link' => '/notifications',
                    'type' => 'success'
                ]);

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
            $isRecurring = !empty($_POST['is_recurring']);

            if (!empty($id)) {
                // Update (Single or Propagate)
                if ($this->scheduleRepo->update($id, $data)) {
                    $this->scheduleRepo->syncAssignments($id, $assignedServers);
                    $this->notifyAssignedServers($id, $data, $assignedServers);

                    // Propagate to future schedules if Master Plan (isRecurring) is checked
                    if ($isRecurring) {
                        $startDate = new \DateTime($data['mass_date']);
                        $dayOfWeek = (int)$startDate->format('w');
                        $massTime = $data['mass_time'];
                        
                        // Get all future schedules with same day and time
                        $futureSchedules = $this->scheduleRepo->getFutureMatchingSchedules($data['mass_date'], $dayOfWeek, $massTime);
                        
                        foreach ($futureSchedules as $fs) {
                            $this->scheduleRepo->syncAssignments($fs->id, $assignedServers);
                            // Optional: notify them for each? (might be too many emails, maybe just one summary)
                        }
                        
                        logAction('Update', 'Schedules', "Propagated assignments for $id to " . count($futureSchedules) . " future schedules.");
                        setFlash('msg_success', 'Assignments propagated to all future ' . $startDate->format('l') . ' ' . date('h:i A', strtotime($massTime)) . ' slots!');
                    } else {
                        logAction('Update', 'Schedules', "Updated schedule ID: $id (" . $data['mass_type'] . ")");
                        setFlash('msg_success', 'Schedule updated successfully!');
                    }
                } else {
                    setFlash('msg_error', 'Failed to update schedule. A schedule already exists for this date and time.');
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
                    $skipped = 0;

                    // If weekly and no specific days selected, use the start date's day of week
                    if ($frequency === 'weekly' && empty($recurringDays)) {
                        $recurringDays = [(int)$startDate->format('w')];
                    }

                    while ($current <= $end) {
                        $shouldCreate = false;
                        
                        if ($frequency === 'daily') {
                            $shouldCreate = true;
                        } elseif ($frequency === 'weekly') {
                            if (in_array((int)$current->format('w'), $recurringDays)) {
                                $shouldCreate = true;
                            }
                        } elseif ($frequency === 'monthly') {
                            $shouldCreate = true;
                        }

                        if ($shouldCreate) {
                            $data['mass_date'] = $current->format('Y-m-d');
                            // Check for conflict before creating in loop
                            if (!$this->scheduleRepo->hasConflict($data['mass_date'], $data['mass_time'])) {
                                if ($this->scheduleRepo->create($data)) {
                                    $newId = $this->db->lastInsertId();
                                    if ($newId) {
                                        $this->scheduleRepo->syncAssignments($newId, $assignedServers);
                                        $this->notifyAssignedServers($newId, $data, $assignedServers);
                                    }
                                    $count++;
                                }
                            } else {
                                $skipped++;
                            }
                        }

                        // Simply move day by day
                        $current->modify("+1 day");
                    }

                    $this->db->commit();
                    
                    $msg = "Successfully created $count recurring schedules!";
                    if ($skipped > 0) $msg .= " ($skipped slots were skipped due to conflicts).";
                    
                    setFlash('msg_success', $msg);
                    logAction('Create', 'Schedules', "Created $count recurring schedules for " . $data['mass_type']);
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
                    
                    setFlash('msg_success', 'Schedule created successfully!');
                } else {
                    setFlash('msg_error', 'Failed to create schedule. A schedule already exists for this date and time.');
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
        $this->verifyCsrf();
        $id = $_POST['id'] ?? null;
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
            $color = $_POST['color'] ?? null;

            if ($ids && ($status || $color)) {
                $count = 0;
                foreach ($ids as $id) {
                    if ($status) {
                        $this->scheduleRepo->updateStatus($id, $status);
                    }
                    if ($color) {
                        $this->scheduleRepo->updateColor($id, $color);
                    }
                    $count++;
                }
                $msg = "Updated $count schedules.";
                if ($status && $color) $msg = "Updated status and color for $count schedules.";
                elseif ($color) $msg = "Updated color for $count schedules.";
                
                logAction('Update', 'Schedules', "Bulk updated schedules: " . implode(',', $ids));
                setFlash('msg_success', $msg);
            }
        }
        redirect('schedules');
    }

    public function generate() {
        $startMonth = (int)($_GET['month'] ?? date('m'));
        $startYear = (int)($_GET['year'] ?? date('Y'));
        
        // Support Weeks or Months
        $durationType = $_GET['duration_type'] ?? 'months';
        $durationValue = (int)($_GET['duration'] ?? 1);
        
        $templates = $this->templateRepo->getAll();

        if (empty($templates)) {
            setFlash('msg_error', "No Auto-Fill templates found. Please configure them first in Auto-Fill Settings.");
            redirect('schedules');
            return;
        }

        try {
            $startDate = new \DateTime("$startYear-$startMonth-01");
            $endDate = clone $startDate;
            
            if ($durationType === 'weeks') {
                $endDate->modify("+" . ($durationValue * 7 - 1) . " days");
            } else {
                $endDate->modify("+" . ($durationValue - 1) . " months");
                $endDate->modify('last day of this month');
            }

            $current = clone $startDate;
            $count = 0;
            $dateRangeStr = $startDate->format('M d, Y') . " to " . $endDate->format('M d, Y');

            while ($current <= $endDate) {
                $dayOfWeek = (int)$current->format('w');
                $dateStr = $current->format('Y-m-d');

                $dayTemplates = array_filter($templates, function($t) use ($dayOfWeek) {
                    return (int)$t->day_of_week === $dayOfWeek;
                });

                foreach ($dayTemplates as $tpl) {
                    // Check for conflict
                    if (!$this->scheduleRepo->hasConflict($dateStr, $tpl->mass_time)) {
                        // Liturgical Season Override
                        $color = $tpl->color;
                        $eventName = $tpl->event_name;
                        
                        $season = $this->seasonRepo->getSeasonByDate($dateStr);
                        if ($season) {
                            $exempted = !empty($season->exempted_types) ? json_decode($season->exempted_types, true) : [];
                            
                            if (!in_array($tpl->mass_type, $exempted)) {
                                $color = $season->color;
                                if ($tpl->mass_type === 'Sunday Mass' || $tpl->mass_type === 'Weekday Mass') {
                                    $eventName = ($eventName ? $eventName . " (" . $season->name . ")" : $season->name);
                                }
                            }
                        }

                        $data = [
                            'mass_type' => $tpl->mass_type,
                            'event_name' => $eventName,
                            'color' => $color,
                            'mass_date' => $dateStr,
                            'mass_time' => $tpl->mass_time,
                            'status' => 'Confirmed'
                        ];
                        $this->scheduleRepo->create($data);
                        $count++;
                    }
                }
                $current->modify('+1 day');
            }

            logAction('Create', 'Schedules', "Generated $count schedules for $dateRangeStr based on templates.");
            
            $this->announcementRepo->create([
                'title' => 'Schedules Generated',
                'category' => 'System',
                'message' => "Schedules from $dateRangeStr have been automatically generated based on the Master Plan.",
                'author' => 'System'
            ]);

            setFlash('msg_success', "Successfully generated $count slots for $dateRangeStr.");
        } catch (\Exception $e) {
            setFlash('msg_error', "Error generating schedules: " . $e->getMessage());
        }
        
        redirect('schedules');
    }

    // --- Template Management ---

    public function templates() {
        $this->requireRole('Superadmin');
        
        $templates = $this->templateRepo->getAll();
        $activityTypes = (new \App\Repositories\SystemSettingRepository())->getActivityTypes();
        $seasons = $this->seasonRepo->getAll();
        $presets = $this->presetRepo->getAll();
        
        $data = [
            'pageTitle' => 'Auto-Fill Settings',
            'title' => 'Auto-Fill Config | ASMS',
            'templates' => $templates,
            'activityTypes' => $activityTypes,
            'seasons' => $seasons,
            'presets' => $presets
        ];
        
        $this->view('schedules/templates', $data);
    }

    public function storeSeason() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'name' => $_POST['name'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'color' => $_POST['color'],
                'exempted_types' => isset($_POST['exempted_types']) ? json_encode($_POST['exempted_types']) : null
            ];

            if ($id) {
                if ($this->seasonRepo->update($id, $data)) {
                    setFlash('msg_success', 'Liturgical event updated.');
                } else {
                    setFlash('msg_error', 'Failed to update liturgical event.');
                }
            } else {
                if ($this->seasonRepo->create($data)) {
                    setFlash('msg_success', 'Liturgical event/season added.');
                } else {
                    setFlash('msg_error', 'Failed to add liturgical season.');
                }
            }
        }
        redirect('schedules/templates');
    }

    public function deleteSeason() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        $id = $_POST['id'] ?? null;
        if ($id && $this->seasonRepo->delete($id)) {
            setFlash('msg_success', 'Liturgical event removed.');
        } else {
            setFlash('msg_error', 'Failed to remove liturgical event.');
        }
        redirect('schedules/templates');
    }

    private function normalizeDayOfWeek($day) {
        if ($day === null || $day === '') {
            return null;
        }
        $day = (int)$day;
        if ($day < 0) {
            return 0;
        }
        if ($day > 6) {
            $day = $day % 7;
        }
        return $day;
    }

    private function normalizeTime($time) {
        if (!$time) {
            return null;
        }
        $formats = ['H:i:s', 'H:i', 'g:i A', 'g:i a'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $time);
            if ($dt) {
                return $dt->format('H:i:s');
            }
        }
        $ts = strtotime($time);
        return $ts === false ? $time : date('H:i:s', $ts);
    }

    public function storePreset() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $dayOfWeek = $this->normalizeDayOfWeek($_POST['day_of_week'] ?? null);
            $massTime = $this->normalizeTime($_POST['mass_time'] ?? null);
            $data = [
                'name' => $_POST['name'],
                'preset_group' => $_POST['preset_group'],
                'day_of_week' => $dayOfWeek ?? $_POST['day_of_week'],
                'mass_time' => $massTime ?? $_POST['mass_time'],
                'mass_type' => $_POST['mass_type'],
                'event_name' => $_POST['event_name'] ?? null,
                'color' => $_POST['color'] ?? 'blue'
            ];

            if ($id) {
                $this->presetRepo->update($id, $data);
                setFlash('msg_success', 'Preset updated.');
            } else {
                $this->presetRepo->create($data);
                setFlash('msg_success', 'New preset added.');
            }
        }
        redirect('schedules/templates');
    }

    public function deletePreset() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        $id = $_POST['id'] ?? null;
        if ($id && $this->presetRepo->delete($id)) {
            setFlash('msg_success', 'Preset removed.');
        }
        redirect('schedules/templates');
    }

    public function storeTemplate() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'day_of_week' => $_POST['day_of_week'],
                'mass_time' => $_POST['mass_time'],
                'mass_type' => $_POST['mass_type'],
                'event_name' => $_POST['event_name'] ?? null,
                'color' => $_POST['color'] ?? 'blue'
            ];

            if ($id) {
                if ($this->templateRepo->update($id, $data)) {
                    setFlash('msg_success', 'Template slot updated.');
                } else {
                    setFlash('msg_error', 'Failed to update template.');
                }
            } else {
                if ($this->templateRepo->create($data)) {
                    setFlash('msg_success', 'Template slot added.');
                } else {
                    setFlash('msg_error', 'Failed to add template. A slot already exists for this time.');
                }
            }
        }
        redirect('schedules/templates');
    }

    public function deleteTemplate() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        $id = $_POST['id'] ?? null;
        if ($id && $this->templateRepo->delete($id)) {
            setFlash('msg_success', 'Template slot removed.');
        } else {
            setFlash('msg_error', 'Failed to remove template.');
        }
        redirect('schedules/templates');
    }

    public function copyTemplate() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        $fromDay = $_POST['from_day'];
        $toDay = $_POST['to_day'];

        $sourceTemplates = $this->templateRepo->getByDay($fromDay);
        if ($sourceTemplates) {
            foreach ($sourceTemplates as $tpl) {
                $this->templateRepo->create([
                    'day_of_week' => $toDay,
                    'mass_time' => $tpl->mass_time,
                    'mass_type' => $tpl->mass_type,
                    'event_name' => $tpl->event_name,
                    'color' => $tpl->color
                ]);
            }
            setFlash('msg_success', 'Patterns copied successfully!');
        } else {
            setFlash('msg_error', 'Source day has no templates to copy.');
        }
        redirect('schedules/templates');
    }

    public function clearTemplates() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        $day = $_POST['day_of_week'] ?? null;
        if ($day !== null) {
            $this->templateRepo->clearDay($day);
            setFlash('msg_success', 'Day cleared.');
        } else {
            // Clear ALL if no day specified (for reset)
            for($i=0; $i<=6; $i++) $this->templateRepo->clearDay($i);
            setFlash('msg_success', 'Master Plan has been reset.');
        }
        redirect('schedules/templates');
    }

    public function applyPresets() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedIds = $_POST['presets'] ?? [];
            $alsoGenerate = isset($_POST['also_generate']);
            
            // 1. Clear Master Plan
            for($i=0; $i<=6; $i++) $this->templateRepo->clearDay($i);

            // 2. Apply dynamic presets from DB
            foreach ($selectedIds as $id) {
                $p = $this->presetRepo->getById($id);
                if ($p) {
                    $dayOfWeek = $this->normalizeDayOfWeek($p->day_of_week);
                    $massTime = $this->normalizeTime($p->mass_time);
                    $this->templateRepo->create([
                        'day_of_week' => $dayOfWeek ?? $p->day_of_week,
                        'mass_time' => $massTime ?? $p->mass_time,
                        'mass_type' => $p->mass_type,
                        'event_name' => $p->event_name,
                        'color' => $p->color
                    ]);
                }
            }

            setFlash('msg_success', 'Master Plan updated with selected presets.');

            // 3. Optional Immediate Generation
            if ($alsoGenerate) {
                $month = (int)$_POST['gen_month'];
                $year = (int)($_POST['gen_year'] ?? date('Y'));
                $durationValue = (int)$_POST['gen_duration'];
                $durationType = $_POST['gen_duration_type'] ?? 'months';
                
                $_GET['month'] = $month;
                $_GET['year'] = $year;
                $_GET['duration'] = $durationValue;
                $_GET['duration_type'] = $durationType;
                
                $this->generate();
                return;
            }
        }
        redirect('schedules/templates');
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
