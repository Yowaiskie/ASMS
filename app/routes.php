<?php

use App\Controllers\DashboardController;
use App\Controllers\ScheduleController;
use App\Controllers\ServerController;
use App\Controllers\AttendanceController;
use App\Controllers\TrainingController;
use App\Controllers\SettingsController;
use App\Controllers\AuthController;
use App\Controllers\AnnouncementController;
use App\Controllers\ReportController;
use App\Controllers\LogController;
use App\Controllers\ExcuseController;

// Auth Routes
$router->get('/login', [AuthController::class, 'login']);
$router->post('/auth/login', [AuthController::class, 'authenticate']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/auth/register', [AuthController::class, 'store']);
$router->get('/logout', [AuthController::class, 'logout']);

// Protected Routes (Session check logic should be inside Controllers or Middleware)
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

// Schedules
$router->get('/schedules', [ScheduleController::class, 'index']);
$router->get('/schedules/create', [ScheduleController::class, 'create']);
$router->post('/schedules/store', [ScheduleController::class, 'store']);
$router->get('/schedules/delete', [ScheduleController::class, 'delete']);
$router->post('/schedules/bulk-delete', [ScheduleController::class, 'bulkDelete']);
$router->post('/schedules/bulk-update', [ScheduleController::class, 'bulkUpdate']);
$router->get('/schedules/generate', [ScheduleController::class, 'generate']);
$router->post('/schedules/import', [ScheduleController::class, 'import']);

// Servers
$router->get('/servers', [ServerController::class, 'index']);
$router->post('/servers/store', [ServerController::class, 'store']);
$router->get('/servers/delete', [ServerController::class, 'delete']); // Usage: /servers/delete?id=1

// Attendance
$router->get('/attendance', [AttendanceController::class, 'index']);
$router->post('/attendance/update', [AttendanceController::class, 'update']);

// Announcements
$router->get('/announcements', [AnnouncementController::class, 'index']);
$router->post('/announcements/store', [AnnouncementController::class, 'store']);
$router->get('/announcements/delete', [AnnouncementController::class, 'delete']);

// Excuses
$router->get('/excuses', [ExcuseController::class, 'index']);
$router->post('/excuses/store', [ExcuseController::class, 'store']);
$router->post('/excuses/update-status', [ExcuseController::class, 'updateStatus']);

// Reports
$router->get('/reports', [ReportController::class, 'index']);
$router->get('/reports/download', [ReportController::class, 'download']);

// Logs
$router->get('/logs', [LogController::class, 'index']);

// Trainings
$router->get('/trainings', [TrainingController::class, 'index']);

// Settings
$router->get('/settings', [SettingsController::class, 'index']);
$router->post('/settings/store', [SettingsController::class, 'store']);

// Logout (Mock) - Removed
// $router->get('/logout', function() { ... });