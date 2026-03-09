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
use App\Controllers\UserController;
use App\Controllers\Api\AuthController as ApiAuthController;
use App\Controllers\Api\DashboardController as ApiDashboardController;
use App\Controllers\Api\ScheduleController as ApiScheduleController;
use App\Controllers\Api\ServerController as ApiServerController;
use App\Controllers\Api\UserController as ApiUserController;
use App\Controllers\Api\AttendanceController as ApiAttendanceController;
use App\Controllers\Api\AnnouncementController as ApiAnnouncementController;
use App\Controllers\Api\ExcuseController as ApiExcuseController;
use App\Controllers\Api\ReportController as ApiReportController;
use App\Controllers\Api\SettingsController as ApiSettingsController;
use App\Controllers\Api\LogController as ApiLogController;
use App\Controllers\Api\ArchiveController as ApiArchiveController;
use App\Controllers\Api\TrainingController as ApiTrainingController;

use App\Controllers\NotificationController;

// Auth Routes
$router->get('/login', [AuthController::class, 'login']);
$router->get('/maintenance', [AuthController::class, 'maintenance']);
$router->post('/auth/login', [AuthController::class, 'authenticate']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/auth/register', [AuthController::class, 'store']);
$router->get('/logout', [AuthController::class, 'logout']);

// Protected Routes
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

// Schedules
$router->get('/schedules', [ScheduleController::class, 'index']);
$router->get('/schedules/get-servers', [ScheduleController::class, 'getServers']);
$router->post('/schedules/self-assign', [ScheduleController::class, 'selfAssign']);
$router->get('/schedules/create', [ScheduleController::class, 'create']);
$router->post('/schedules/store', [ScheduleController::class, 'store']);
$router->get('/schedules/delete', [ScheduleController::class, 'delete']);
$router->post('/schedules/bulk-delete', [ScheduleController::class, 'bulkDelete']);
$router->post('/schedules/bulk-update', [ScheduleController::class, 'bulkUpdate']);
$router->get('/schedules/generate', [ScheduleController::class, 'generate']);
$router->get('/schedules/templates', [ScheduleController::class, 'templates']);
$router->post('/schedules/store-template', [ScheduleController::class, 'storeTemplate']);
$router->post('/schedules/delete-template', [ScheduleController::class, 'deleteTemplate']);
$router->post('/schedules/copy-template', [ScheduleController::class, 'copyTemplate']);
$router->post('/schedules/clear-templates', [ScheduleController::class, 'clearTemplates']);
$router->post('/schedules/apply-presets', [ScheduleController::class, 'applyPresets']);
$router->post('/schedules/store-preset', [ScheduleController::class, 'storePreset']);
$router->post('/schedules/delete-preset', [ScheduleController::class, 'deletePreset']);
$router->post('/schedules/import', [ScheduleController::class, 'import']);
$router->post('/schedules/store-season', [ScheduleController::class, 'storeSeason']);
$router->post('/schedules/delete-season', [ScheduleController::class, 'deleteSeason']);

// Servers
$router->get('/servers', [ServerController::class, 'index']);
$router->post('/servers/store', [ServerController::class, 'store']);
$router->post('/servers/import', [ServerController::class, 'import']);
$router->post('/servers/delete', [ServerController::class, 'delete']);
$router->post('/servers/bulk-delete', [ServerController::class, 'bulkDelete']);
$router->post('/servers/update-status', [ServerController::class, 'updateStatus']);
$router->get('/servers/download', [ServerController::class, 'download_pdf']);

// Users (Superadmin only)
$router->get('/users', [UserController::class, 'index']);
$router->post('/users/store', [UserController::class, 'store']);
$router->post('/users/update', [UserController::class, 'update']);
$router->post('/users/delete', [UserController::class, 'delete']);
$router->post('/users/bulk-delete', [UserController::class, 'bulkDelete']);
$router->post('/users/allow-late-excuse', [UserController::class, 'allowLateExcuse']);
$router->post('/users/import', [UserController::class, 'import']);

// Attendance
$router->get('/attendance', [AttendanceController::class, 'index']);
$router->post('/attendance/update', [AttendanceController::class, 'update']);
$router->get('/attendance/downloadReport', [AttendanceController::class, 'downloadReport']);

// Announcements
$router->get('/announcements', [AnnouncementController::class, 'index']);
$router->post('/announcements/store', [AnnouncementController::class, 'store']);
$router->get('/announcements/delete', [AnnouncementController::class, 'delete']);

// Excuses
$router->get('/excuses', [ExcuseController::class, 'index']);
$router->post('/excuses/store', [ExcuseController::class, 'store']);
$router->post('/excuses/update-status', [ExcuseController::class, 'updateStatus']);
$router->post('/excuses/bulk-delete', [ExcuseController::class, 'bulkDelete']);

// Reports
$router->get('/reports', [ReportController::class, 'index']);
$router->get('/reports/download', [ReportController::class, 'download']);

// Logs
$router->get('/logs', [LogController::class, 'index']);

// Trainings
$router->get('/trainings', [TrainingController::class, 'index']);

// Notifications
$router->get('/notifications', [NotificationController::class, 'index']);
$router->post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);
$router->post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
$router->get('/notifications/latest', [NotificationController::class, 'getLatest']);

// Settings
$router->get('/settings', [SettingsController::class, 'index']);
$router->get('/settings/database', [SettingsController::class, 'database']);
$router->get('/settings/system', [SettingsController::class, 'system']);
$router->post('/settings/system/update', [SettingsController::class, 'storeSystem']);
$router->post('/settings/activity-type/store', [SettingsController::class, 'storeActivityType']);
$router->get('/settings/activity-type/delete/:id', [SettingsController::class, 'deleteActivityType']);
$router->post('/settings/rank/store', [SettingsController::class, 'storeRank']);
$router->get('/settings/rank/delete/:id', [SettingsController::class, 'deleteRank']);
$router->post('/settings/category/store', [SettingsController::class, 'storeCategory']);
$router->get('/settings/category/delete/:id', [SettingsController::class, 'deleteCategory']);
$router->post('/settings/store', [SettingsController::class, 'store']);
$router->get('/settings/backup', [SettingsController::class, 'backup']);
$router->post('/settings/restore', [SettingsController::class, 'restore']);
$router->get('/settings/toggle_edit/:id', [SettingsController::class, 'toggle_edit']);

// Archives (Superadmin only)
$router->get('/archives', [\App\Controllers\ArchiveController::class, 'index']);
$router->get('/archives/restore/:id', [\App\Controllers\ArchiveController::class, 'restore']);
$router->get('/archives/delete/:id', [\App\Controllers\ArchiveController::class, 'delete']);
$router->post('/archives/bulk-delete', [\App\Controllers\ArchiveController::class, 'bulkDelete']);

// API Routes
// Auth
$router->post('/api/auth/login', [ApiAuthController::class, 'login']);
$router->post('/api/auth/register', [ApiAuthController::class, 'register']);
$router->post('/api/auth/logout', [ApiAuthController::class, 'logout']);
$router->get('/api/auth/me', [ApiAuthController::class, 'me']);

// Dashboard
$router->get('/api/dashboard', [ApiDashboardController::class, 'index']);

// Schedules
$router->get('/api/schedules', [ApiScheduleController::class, 'index']);
$router->get('/api/schedules/:id', [ApiScheduleController::class, 'show']);
$router->get('/api/schedules/:id/servers', [ApiScheduleController::class, 'getServers']);
$router->post('/api/schedules/self-assign', [ApiScheduleController::class, 'selfAssign']);
$router->post('/api/schedules/store', [ApiScheduleController::class, 'store']);
$router->post('/api/schedules/delete', [ApiScheduleController::class, 'delete']);
$router->post('/api/schedules/bulk-delete', [ApiScheduleController::class, 'bulkDelete']);
$router->post('/api/schedules/bulk-update', [ApiScheduleController::class, 'bulkUpdate']);
$router->get('/api/schedules/generate', [ApiScheduleController::class, 'generate']);
$router->get('/api/schedules/templates', [ApiScheduleController::class, 'templates']);
$router->post('/api/schedules/templates/store', [ApiScheduleController::class, 'storeTemplate']);
$router->post('/api/schedules/templates/delete', [ApiScheduleController::class, 'deleteTemplate']);
$router->post('/api/schedules/templates/copy', [ApiScheduleController::class, 'copyTemplate']);
$router->post('/api/schedules/templates/clear', [ApiScheduleController::class, 'clearTemplates']);
$router->post('/api/schedules/import', [ApiScheduleController::class, 'import']);

// Servers
$router->get('/api/servers', [ApiServerController::class, 'index']);
$router->post('/api/servers/store', [ApiServerController::class, 'store']);
$router->post('/api/servers/update-status', [ApiServerController::class, 'updateStatus']);
$router->get('/api/servers/download', [ApiServerController::class, 'downloadPdf']);
$router->post('/api/servers/import', [ApiServerController::class, 'import']);
$router->post('/api/servers/delete', [ApiServerController::class, 'delete']);
$router->post('/api/servers/bulk-delete', [ApiServerController::class, 'bulkDelete']);

// Users
$router->get('/api/users', [ApiUserController::class, 'index']);
$router->post('/api/users/store', [ApiUserController::class, 'store']);
$router->post('/api/users/update', [ApiUserController::class, 'update']);
$router->post('/api/users/delete', [ApiUserController::class, 'delete']);
$router->post('/api/users/bulk-delete', [ApiUserController::class, 'bulkDelete']);
$router->post('/api/users/import', [ApiUserController::class, 'import']);

// Attendance
$router->get('/api/attendance', [ApiAttendanceController::class, 'index']);
$router->post('/api/attendance/update', [ApiAttendanceController::class, 'update']);
$router->get('/api/attendance/report', [ApiAttendanceController::class, 'downloadReport']);

// Announcements
$router->get('/api/announcements', [ApiAnnouncementController::class, 'index']);
$router->post('/api/announcements/store', [ApiAnnouncementController::class, 'store']);
$router->post('/api/announcements/delete', [ApiAnnouncementController::class, 'delete']);
$router->post('/api/announcements/mark-read', [ApiAnnouncementController::class, 'markRead']);

// Excuses
$router->get('/api/excuses', [ApiExcuseController::class, 'index']);
$router->post('/api/excuses/store', [ApiExcuseController::class, 'store']);
$router->post('/api/excuses/update-status', [ApiExcuseController::class, 'updateStatus']);
$router->post('/api/excuses/bulk-delete', [ApiExcuseController::class, 'bulkDelete']);
$router->post('/api/excuses/mark-seen', [ApiExcuseController::class, 'markSeen']);

// Reports
$router->get('/api/reports', [ApiReportController::class, 'index']);
$router->get('/api/reports/download', [ApiReportController::class, 'download']);

// Settings
$router->get('/api/settings', [ApiSettingsController::class, 'index']);
$router->get('/api/settings/system', [ApiSettingsController::class, 'system']);
$router->post('/api/settings/system/store', [ApiSettingsController::class, 'storeSystem']);
$router->post('/api/settings/activity-type/store', [ApiSettingsController::class, 'storeActivityType']);
$router->get('/api/settings/activity-type/delete/:id', [ApiSettingsController::class, 'deleteActivityType']);
$router->post('/api/settings/rank/store', [ApiSettingsController::class, 'storeRank']);
$router->get('/api/settings/rank/delete/:id', [ApiSettingsController::class, 'deleteRank']);
$router->post('/api/settings/category/store', [ApiSettingsController::class, 'storeCategory']);
$router->get('/api/settings/category/delete/:id', [ApiSettingsController::class, 'deleteCategory']);
$router->post('/api/settings/store', [ApiSettingsController::class, 'store']);
$router->get('/api/settings/toggle-edit/:id', [ApiSettingsController::class, 'toggle_edit']);
$router->get('/api/settings/backup', [ApiSettingsController::class, 'backup']);

// Logs
$router->get('/api/logs', [ApiLogController::class, 'index']);

// Archives
$router->get('/api/archives', [ApiArchiveController::class, 'index']);
$router->get('/api/archives/restore/:id', [ApiArchiveController::class, 'restore']);
$router->get('/api/archives/delete/:id', [ApiArchiveController::class, 'delete']);

// Trainings
$router->get('/api/trainings', [ApiTrainingController::class, 'index']);
