<?php
session_start();
require_once '../app/config/config.php';
require_once '../app/helpers/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch User
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    $nextSchedule = null;
    $stats = ['Present' => 0, 'Late' => 0, 'Absent' => 0, 'Total' => 0, 'Rate' => 0];
    $announcements = [];

    if ($user && $user->server_id) {
        // Next Schedule
        $stmt = $pdo->prepare("
            SELECT s.* FROM schedules s
            JOIN attendance a ON s.id = a.schedule_id
            WHERE a.server_id = ? AND s.mass_date >= CURDATE()
            ORDER BY s.mass_date ASC, s.mass_time ASC LIMIT 1
        ");
        $stmt->execute([$user->server_id]);
        $nextSchedule = $stmt->fetch(PDO::FETCH_OBJ);

        // Attendance Stats
        $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM attendance WHERE server_id = ? GROUP BY status");
        $stmt->execute([$user->server_id]);
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($res as $r) {
            $stats[$r->status] = (int)$r->count;
            $stats['Total'] += (int)$r->count;
        }
        if ($stats['Total'] > 0) {
            $stats['Rate'] = round(($stats['Present'] / $stats['Total']) * 100);
        }

        // Announcements
        $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
        $announcements = $stmt->fetchAll(PDO::FETCH_OBJ);
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Altar Servers System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e63d4',
                        success: '#dcfce7',
                        successText: '#166534',
                        warning: '#fef9c3',
                        warningText: '#854d0e',
                        danger: '#fee2e2',
                        dangerText: '#991b1b',
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            'from': { opacity: '0', transform: 'translateY(20px)' },
                            'to': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes progress {
            from { stroke-dashoffset: 351.86; }
            to { stroke-dashoffset: <?= 351.86 - (351.86 * ($stats['Rate'] / 100)) ?>; }
        }
        .progress-circle {
            animation: progress 2s ease-out forwards;
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans text-slate-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col justify-between hidden md:flex shrink-0">
        <div>
            <div class="p-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-blue-100 shadow-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 leading-tight">Altar Servers</h1>
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">Management System</p>
                </div>
            </div>

            <div class="px-6 mb-6">
                <span class="inline-block bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">User</span>
            </div>

            <nav class="px-4 space-y-1">
                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>

                <a href="attendance.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="font-medium text-sm">My Attendance</span>
                </a>

                <a href="schedule.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-sm">Schedule</span>
                </a>

                <a href="announcements.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="font-medium text-sm">Announcements</span>
                </a>

                <a href="profile.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium text-sm">Profile</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-50">
            <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium text-sm">Logout</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <div class="p-8">
            
            <div class="mb-8 animate-fade-in-up">
                <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    Welcome Back, <?= h($user->username) ?>! 
                    <span class="text-2xl">👋</span>
                </h2>
                <p class="text-slate-500 text-sm mt-1">Here's your schedule and updates</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col animate-fade-in-up delay-100">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-700">Next Schedule</h3>
                    </div>

                    <?php if($nextSchedule): ?>
                    <div class="bg-blue-600 rounded-2xl p-6 text-white relative overflow-hidden flex-1 flex flex-col justify-center">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-white opacity-5 rounded-full"></div>

                        <div class="flex justify-between items-start mb-2">
                            <p class="text-blue-100 text-xs font-medium"><?= date('l, F d, Y', strtotime($nextSchedule->mass_date)) ?></p>
                            <span class="bg-blue-500/50 backdrop-blur-sm px-3 py-1 rounded-lg text-xs font-medium border border-blue-400/30"><?= h($nextSchedule->mass_time) ?></span>
                        </div>
                        
                        <h2 class="text-2xl font-bold mb-4"><?= h($nextSchedule->mass_type) ?></h2>

                        <div class="flex items-center gap-6 text-sm text-blue-100">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Main Parish</span>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="bg-slate-100 rounded-2xl p-6 text-slate-400 flex-1 flex items-center justify-center border-2 border-dashed border-slate-200">
                        No upcoming schedules assigned.
                    </div>
                    <?php endif; ?>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 animate-fade-in-up delay-200">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-700">Attendance</h3>
                    </div>

                    <div class="flex flex-col items-center mb-6">
                        <div class="relative w-32 h-32 flex items-center justify-center">
                            <svg class="transform -rotate-90 w-32 h-32">
                                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-100" />
                                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="351.86" stroke-dashoffset="351.86" class="text-blue-600 progress-circle" stroke-linecap="round" />
                            </svg>
                            <div class="absolute flex flex-col items-center">
                                <span class="text-3xl font-bold text-slate-800"><?= $stats['Rate'] ?>%</span>
                                <span class="text-[10px] text-slate-400">Attendance Rate</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-green-50 px-4 py-2 rounded-xl">
                            <span class="text-xs font-semibold text-green-800">Present</span>
                            <span class="text-xs font-bold text-green-800"><?= $stats['Present'] ?></span>
                        </div>
                        <div class="flex justify-between items-center bg-yellow-50 px-4 py-2 rounded-xl">
                            <span class="text-xs font-semibold text-yellow-800">Late</span>
                            <span class="text-xs font-bold text-yellow-800"><?= $stats['Late'] ?></span>
                        </div>
                        <div class="flex justify-between items-center bg-red-50 px-4 py-2 rounded-xl">
                            <span class="text-xs font-semibold text-red-800">Absent</span>
                            <span class="text-xs font-bold text-red-800"><?= $stats['Absent'] ?></span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 animate-fade-in-up delay-300">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-700">Recent Announcements</h3>
                </div>

                <div class="space-y-4">
                    
                    <?php if(!empty($announcements)): ?>
                        <?php foreach($announcements as $ann): ?>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex justify-between items-start group hover:bg-slate-100 transition-colors cursor-pointer">
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm mb-1"><?= h($ann->title) ?></h4>
                                <p class="text-slate-500 text-xs line-clamp-1"><?= h($ann->message) ?></p>
                            </div>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap ml-4"><?= date('M d, Y', strtotime($ann->created_at)) ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-slate-400 text-sm text-center py-4">No announcements yet.</p>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </main>

    <div class="fixed bottom-6 right-6">
        <button class="bg-slate-900 text-white p-3 rounded-full shadow-lg hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>

</body>
</html>