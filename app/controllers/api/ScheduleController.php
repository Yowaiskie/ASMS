<?php

namespace App\Controllers\Api;

use App\Repositories\ScheduleRepository;
use App\Repositories\ServerRepository;
use App\Repositories\AnnouncementRepository;
use App\Repositories\ScheduleTemplateRepository;
use App\Repositories\UserRepository;

class ScheduleController extends ApiController {
    private $scheduleRepo;
    private $serverRepo;
    private $announcementRepo;
    private $templateRepo;
    private $userRepo;
    private $db;

    public function __construct() {
        $this->requireLoginApi();
        $this->scheduleRepo = new ScheduleRepository();
        $this->serverRepo = new ServerRepository();
        $this->announcementRepo = new AnnouncementRepository();
        $this->templateRepo = new ScheduleTemplateRepository();
        $this->userRepo = new UserRepository();
        $this->db = \App\Core\Database::getInstance();
    }

    public function index() {
        if (($_SESSION['role'] ?? '') === 'User') {
            $allSchedules = $this->scheduleRepo->getAll();
            $assignedIds = $this->scheduleRepo->getAssignedScheduleIds($_SESSION['user_id']);
            $this->ok([
                'schedules' => $allSchedules,
                'assignedIds' => $assignedIds
            ]);
        }

        $schedules = $this->scheduleRepo->getAll();
        $servers = $this->serverRepo->getAll();
        $activityTypes = (new \App\Repositories\SystemSettingRepository())->getActivityTypes();

        foreach ($schedules as $s) {
            $assignments = $this->scheduleRepo->getAssignments($s->id);
            $s->assigned_servers = $assignments;
            $s->assigned_ids = array_map(function($a) { return (int)$a->id; }, $assignments);
        }

        $this->db->query("SELECT server_id FROM users WHERE id = :uid");
        $this->db->bind(':uid', $_SESSION['user_id']);
        $cu = $this->db->single();
        $currentServerId = $cu ? $cu->server_id : null;

        $this->ok([
            'schedules' => $schedules,
            'servers' => $servers,
            'activityTypes' => $activityTypes,
            'currentServerId' => $currentServerId
        ]);
    }

    public function show($id) {
        $schedule = $this->scheduleRepo->getById($id);
        if (!$schedule) {
            $this->error('Schedule not found.', 404);
        }
        $schedule->assigned_servers = $this->scheduleRepo->getAssignments($id);
        $this->ok($schedule);
    }

    public function getServers($id) {
        $servers = $this->scheduleRepo->getFullAssignments($id);
        $this->ok($servers);
    }

    public function selfAssign() {
        $this->requireRoleApi('User');
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $scheduleId = $data['schedule_id'] ?? null;
        if (!$scheduleId) {
            $this->error('Schedule ID is required.', 422);
        }

        $this->db->query("SELECT status, suspension_until FROM servers WHERE id = (SELECT server_id FROM users WHERE id = :uid)");
        $this->db->bind(':uid', $_SESSION['user_id']);
        $server = $this->db->single();

        if ($server && $server->status === 'Suspended') {
            $today = date('Y-m-d');
            if ($server->suspension_until >= $today) {
                $this->error('Account is suspended until ' . date('M d, Y', strtotime($server->suspension_until)) . '.', 403);
            } else {
                $this->db->query("UPDATE servers SET status = 'Active', suspension_until = NULL WHERE id = (SELECT server_id FROM users WHERE id = :uid)");
                $this->db->bind(':uid', $_SESSION['user_id']);
                $this->db->execute();
            }
        }

        if (!($_SESSION['is_verified'] ?? 0)) {
            $this->error('Your account must be verified before joining schedules.', 403);
        }

        $schedule = $this->scheduleRepo->getById($scheduleId);
        if (!$schedule) {
            $this->error('Schedule not found.', 404);
        }

        $scheduleDateTime = new \DateTime($schedule->mass_date . ' ' . $schedule->mass_time);
        $now = new \DateTime();
        if ($scheduleDateTime < $now) {
            $this->error('This schedule has already passed and cannot be joined.', 422);
        }

        if ($this->scheduleRepo->selfAssign($_SESSION['user_id'], $scheduleId)) {
            if (empty($_SESSION['full_name'])) {
                $this->db->query("SELECT CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name FROM users u JOIN servers s ON u.server_id = s.id WHERE u.id = :id");
                $this->db->bind(':id', $_SESSION['user_id']);
                $res = $this->db->single();
                $_SESSION['full_name'] = $res ? $res->name : $_SESSION['username'];
            }

            $this->announcementRepo->create([
                'title' => 'New Slot Filled',
                'category' => 'System',
                'message' => $_SESSION['full_name'] . ' has joined the schedule for ' . $schedule->mass_type . ' on ' . date('M d, Y', strtotime($schedule->mass_date)) . '.',
                'author' => 'System'
            ]);

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
            $this->ok(['message' => 'Successfully joined the schedule!']);
        }

        $this->error('Failed to join schedule.', 500);
    }

    public function store() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $id = $data['id'] ?? '';
        $payload = [
            'mass_type' => trim($data['mass_type'] ?? ''),
            'event_name' => trim($data['event_name'] ?? ''),
            'color' => $data['color'] ?? null,
            'mass_date' => $data['mass_date'] ?? '',
            'mass_time' => $data['mass_time'] ?? '',
            'status' => $data['status'] ?? ''
        ];
        $assignedServers = $data['assigned_servers'] ?? [];
        if (is_string($assignedServers)) {
            $decoded = json_decode($assignedServers, true);
            if (is_array($decoded)) {
                $assignedServers = $decoded;
            } else {
                $assignedServers = array_filter(array_map('trim', explode(',', $assignedServers)));
            }
        }
        $assignedServers = array_map('intval', $assignedServers);
        $isRecurring = !empty($data['is_recurring']);

        if (!empty($id)) {
            if ($this->scheduleRepo->update($id, $payload)) {
                $this->scheduleRepo->syncAssignments($id, $assignedServers);
                $this->notifyAssignedServers($id, $payload, $assignedServers);
                logAction('Update', 'Schedules', "Updated schedule ID: $id (" . $payload['mass_type'] . ")");
                $this->ok(['message' => 'Schedule updated successfully!']);
            }
            $this->error('Failed to update schedule. A schedule already exists for this date and time.', 409);
        } elseif ($isRecurring) {
            $frequency = $data['frequency'] ?? 'daily';
            $interval = (int)($data['interval'] ?? 1);
            $endDate = $data['end_date'] ?? '';
            $recurringDays = $data['recurring_days'] ?? [];
            if (is_string($recurringDays)) {
                $decoded = json_decode($recurringDays, true);
                if (is_array($decoded)) {
                    $recurringDays = $decoded;
                } else {
                    $recurringDays = array_filter(array_map('trim', explode(',', $recurringDays)));
                }
            }

            if (empty($endDate)) {
                $this->error('End date is required for recurring schedules.', 422);
            }

            $this->db->beginTransaction();
            try {
                $startDate = new \DateTime($payload['mass_date']);
                $end = new \DateTime($endDate);
                $current = clone $startDate;
                $count = 0;
                $skipped = 0;

                while ($current <= $end) {
                    $shouldCreate = false;

                    if ($frequency === 'daily') {
                        $shouldCreate = true;
                    } elseif ($frequency === 'weekly') {
                        if (empty($recurringDays)) {
                            $shouldCreate = true;
                        } else {
                            if (in_array($current->format('w'), $recurringDays)) {
                                $shouldCreate = true;
                            }
                        }
                    } elseif ($frequency === 'monthly') {
                        $shouldCreate = true;
                    }

                    if ($shouldCreate) {
                        $payload['mass_date'] = $current->format('Y-m-d');
                        if (!$this->scheduleRepo->hasConflict($payload['mass_date'], $payload['mass_time'])) {
                            if ($this->scheduleRepo->create($payload)) {
                                $newId = $this->db->lastInsertId();
                                if ($newId) {
                                    $this->scheduleRepo->syncAssignments($newId, $assignedServers);
                                    $this->notifyAssignedServers($newId, $payload, $assignedServers);
                                }
                                $count++;
                            }
                        } else {
                            $skipped++;
                        }
                    }

                    if ($frequency === 'daily') {
                        $current->modify("+$interval day");
                    } elseif ($frequency === 'weekly') {
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
                $this->ok([
                    'message' => 'Recurring schedules created.',
                    'created' => $count,
                    'skipped' => $skipped
                ]);
            } catch (\Exception $e) {
                $this->db->rollBack();
                $this->error('Error creating recurring schedules: ' . $e->getMessage(), 500);
            }
        } else {
            if ($this->scheduleRepo->create($payload)) {
                $newId = $this->db->lastInsertId();
                if ($newId) {
                    $this->scheduleRepo->syncAssignments($newId, $assignedServers);
                    $this->notifyAssignedServers($newId, $payload, $assignedServers);
                }
                $this->ok(['message' => 'Schedule created successfully!', 'id' => $newId]);
            }
            $this->error('Failed to create schedule. A schedule already exists for this date and time.', 409);
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
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $id = $data['id'] ?? null;
        if ($id && $this->scheduleRepo->delete($id)) {
            logAction('Delete', 'Schedules', "Deleted schedule ID: $id");
            $this->ok(['message' => 'Schedule deleted.']);
        }
        $this->error('Failed to delete schedule.', 500);
    }

    public function bulkDelete() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $ids = $data['ids'] ?? [];
        if (empty($ids)) {
            $this->error('No schedules selected.', 422);
        }

        $count = 0;
        foreach ($ids as $id) {
            if ($this->scheduleRepo->delete($id)) {
                $count++;
            }
        }
        logAction('Delete', 'Schedules', "Bulk deleted $count schedules.");
        $this->ok(['message' => "Deleted $count schedules successfully."]);
    }

    public function bulkUpdate() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $ids = $data['ids'] ?? [];
        $status = $data['status'] ?? null;

        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            if (is_array($decoded)) {
                $ids = $decoded;
            }
        }

        if (!$ids || !$status) {
            $this->error('IDs and status are required.', 422);
        }

        $count = 0;
        foreach ($ids as $id) {
            if ($this->scheduleRepo->updateStatus($id, $status)) {
                $count++;
            }
        }
        logAction('Update', 'Schedules', "Bulk updated status to $status for $count schedules.");
        $this->ok(['message' => "Updated $count schedules."]);
    }

    public function generate() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);

        $startMonth = (int)($_GET['month'] ?? date('m'));
        $startYear = (int)($_GET['year'] ?? date('Y'));
        $endMonth = isset($_GET['end_month']) && $_GET['end_month'] !== '' ? (int)$_GET['end_month'] : $startMonth;
        $endYear = isset($_GET['end_year']) && $_GET['end_year'] !== '' ? (int)$_GET['end_year'] : $startYear;

        $templates = $this->templateRepo->getAll();
        if (empty($templates)) {
            $this->error('No Auto-Fill templates found. Please configure them first.', 422);
        }

        try {
            $startDate = new \DateTime("$startYear-$startMonth-01");
            $endDate = new \DateTime("$endYear-$endMonth-01");
            $endDate->modify('last day of this month');

            $current = clone $startDate;
            $count = 0;
            $monthsProcessed = [];

            while ($current <= $endDate) {
                $monthsProcessed[] = $current->format('F Y');

                $monthStart = clone $current;
                $monthEnd = clone $current;
                $monthEnd->modify('last day of this month');

                $dayRunner = clone $monthStart;
                while ($dayRunner <= $monthEnd) {
                    $dayOfWeek = (int)$dayRunner->format('w');
                    $dateStr = $dayRunner->format('Y-m-d');

                    $dayTemplates = array_filter($templates, function($t) use ($dayOfWeek) {
                        return (int)$t->day_of_week === $dayOfWeek;
                    });

                    foreach ($dayTemplates as $tpl) {
                        if (!$this->scheduleRepo->hasConflict($dateStr, $tpl->mass_time)) {
                            $payload = [
                                'mass_type' => $tpl->mass_type,
                                'event_name' => $tpl->event_name,
                                'color' => $tpl->color,
                                'mass_date' => $dateStr,
                                'mass_time' => $tpl->mass_time,
                                'status' => 'Confirmed'
                            ];
                            $this->scheduleRepo->create($payload);
                            $count++;
                        }
                    }
                    $dayRunner->modify('+1 day');
                }

                $current->modify('first day of next month');
            }

            $monthRangeStr = count($monthsProcessed) > 1
                ? $monthsProcessed[0] . " to " . end($monthsProcessed)
                : $monthsProcessed[0];

            logAction('Create', 'Schedules', "Generated $count schedules for $monthRangeStr based on templates.");
            $this->announcementRepo->create([
                'title' => 'Schedules Generated',
                'category' => 'System',
                'message' => "Schedules for $monthRangeStr have been automatically generated based on the Master Plan.",
                'author' => 'System'
            ]);

            $this->ok(['message' => "Successfully generated $count slots for $monthRangeStr.", 'count' => $count]);
        } catch (\Exception $e) {
            $this->error("Error generating schedules: " . $e->getMessage(), 500);
        }
    }

    public function templates() {
        $this->requireRoleApi('Superadmin');
        $templates = $this->templateRepo->getAll();
        $this->ok(['templates' => $templates]);
    }

    public function storeTemplate() {
        $this->requireRoleApi('Superadmin');
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $payload = [
            'day_of_week' => $data['day_of_week'] ?? null,
            'mass_time' => $data['mass_time'] ?? null,
            'mass_type' => $data['mass_type'] ?? null,
            'event_name' => $data['event_name'] ?? null,
            'color' => $data['color'] ?? 'blue'
        ];

        if ($this->templateRepo->create($payload)) {
            $this->ok(['message' => 'Template slot added.']);
        }
        $this->error('Failed to add template. A slot already exists for this time.', 409);
    }

    public function deleteTemplate() {
        $this->requireRoleApi('Superadmin');
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $id = $data['id'] ?? null;
        if ($id && $this->templateRepo->delete($id)) {
            $this->ok(['message' => 'Template slot removed.']);
        }
        $this->error('Failed to remove template.', 500);
    }

    public function copyTemplate() {
        $this->requireRoleApi('Superadmin');
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $fromDay = $data['from_day'] ?? null;
        $toDay = $data['to_day'] ?? null;

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
            $this->ok(['message' => 'Patterns copied successfully!']);
        }
        $this->error('Source day has no templates to copy.', 422);
    }

    public function clearTemplates() {
        $this->requireRoleApi('Superadmin');
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $day = $data['day_of_week'] ?? null;
        if ($day !== null) {
            $this->templateRepo->clearDay($day);
            $this->ok(['message' => 'Day cleared.']);
        }
        $this->error('Day is required.', 422);
    }

    public function import() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->verifyCsrfApi();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $fileName = $_FILES['csv_file']['tmp_name'];

            if ($_FILES['csv_file']['size'] > 0) {
                $file = fopen($fileName, "r");
                $count = 0;
                $firstRow = true;

                while (($line = fgetcsv($file, 10000, ",")) !== false) {
                    $column = $line;
                    if (count($column) == 1 && strpos($column[0], ';') !== false) {
                        $column = str_getcsv($column[0], ';');
                    }

                    if ($firstRow) { $firstRow = false; continue; }
                    if (count($column) < 3) continue;

                    $column[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $column[0]);
                    $date = trim($column[0]);
                    $time = trim($column[1]);
                    $type = trim($column[2]);
                    $eventName = isset($column[3]) ? trim($column[3]) : null;

                    if (empty($date) || empty($time)) continue;

                    $payload = [
                        'mass_date' => $date,
                        'mass_time' => $time,
                        'mass_type' => $type,
                        'event_name' => $eventName,
                        'status' => 'Confirmed'
                    ];

                    if ($this->scheduleRepo->create($payload)) {
                        $count++;
                    }
                }

                fclose($file);
                logAction('Create', 'Schedules', "Imported $count schedules via CSV.");
                $this->ok(['message' => "Imported $count schedules successfully.", 'count' => $count]);
            }
            $this->error('Empty file uploaded.', 422);
        }
        $this->error('Invalid file upload.', 422);
    }
}
