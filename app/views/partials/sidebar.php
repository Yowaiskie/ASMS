<?php
// Get current path to set active state
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$root = URLROOT; // /ASMS/public

// Define Menu Items
$menu_items = [
    [
        'label' => 'Dashboard',
        'url' => $root . '/dashboard',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />'
    ],
    [
        'label' => 'Attendance',
        'url' => $root . '/attendance',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />'
    ],
    [
        'label' => 'Schedules',
        'url' => $root . '/schedules',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
    ],
    [
        'label' => 'Servers',
        'url' => $root . '/servers',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />'
    ],
    [
        'label' => 'Announcements',
        'url' => $root . '/announcements',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />'
    ],
    [
        'label' => 'Reports',
        'url' => $root . '/reports',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'
    ],
    [
        'label' => 'Logs',
        'url' => $root . '/logs',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'
    ],
    [
        'label' => 'Settings',
        'url' => $root . '/settings',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'
    ]
];
?>

<aside class="w-64 bg-white border-r border-slate-100 flex flex-col justify-between hidden md:flex shrink-0 h-full">
    <div>
        <!-- Logo -->
        <div class="p-4 flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center">
                <img src="<?= URLROOT ?>/images/logo.png" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="font-bold text-slate-900 leading-tight text-sm">Altar Servers</h1>
                <p class="text-[9px] text-slate-400 font-medium uppercase tracking-wide">Management System</p>
            </div>
        </div>

        <!-- Role Badge -->
        <div class="px-6 mb-6">
            <span class="inline-block bg-fuchsia-100 text-fuchsia-600 text-xs font-bold px-3 py-1 rounded-full">
                <?= $_SESSION['role'] ?? 'User' ?>
            </span>
        </div>

        <!-- Navigation -->
        <nav class="px-4 space-y-1">
            <?php foreach ($menu_items as $item): ?>
                <?php 
                    $isActive = false;
                    
                    // Simple logic to check if URL matches start of path (e.g., /schedules/create matches /schedules)
                    // Or exact match for Dashboard
                    if ($item['url'] === $root . '/dashboard') {
                        if ($current_path === $root . '/' || $current_path === $root . '/dashboard') {
                            $isActive = true;
                        }
                    } else {
                        if (strpos($current_path, parse_url($item['url'], PHP_URL_PATH)) === 0) {
                            $isActive = true;
                        }
                    }

                    // Active: Blue background, White text
                    // Inactive: Gray text, Gray hover
                    $activeClass = "bg-blue-600 text-white shadow-md shadow-blue-200"; 
                    $inactiveClass = "text-slate-600 hover:bg-slate-50 hover:text-slate-900";
                ?>
                <a href="<?= $item['url'] ?>" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= $isActive ? $activeClass : $inactiveClass ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <?= $item['icon'] ?>
                    </svg>
                    <span class="font-medium text-sm"><?= $item['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Logout -->
    <div class="p-4 border-t border-slate-50">
        <a href="<?= URLROOT ?>/logout" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="font-medium text-sm">Logout</span>
        </a>
    </div>
</aside>