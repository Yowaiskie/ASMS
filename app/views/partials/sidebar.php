<?php
// Get current path to set active state
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$root = URLROOT; // /ASMS/public

// Check for unread announcements (for Users)
$unreadAnnouncements = 0;
$pendingExcuses = 0;

try {
    $db = \App\Core\Database::getInstance();
    $role = $_SESSION['role'] ?? 'User';

    // 1. Unread Announcements Count
    if (isset($_SESSION['user_id'])) {
        $db->query("SELECT last_read_announcements FROM users WHERE id = :id");
        $db->bind(':id', $_SESSION['user_id']);
        $res = $db->single();
        if ($res) {
            $lastRead = $res->last_read_announcements;
            if (!$lastRead) {
                 $db->query("SELECT COUNT(*) as count FROM announcements");
            } else {
                 $db->query("SELECT COUNT(*) as count FROM announcements WHERE created_at > :last_read");
                 $db->bind(':last_read', $lastRead);
            }
            $unreadAnnouncements = $db->single()->count;
        }
    }

    // 2. Pending Excuses Count (For Admins/Superadmins)
    if ($role === 'Admin' || $role === 'Superadmin') {
        $db->query("SELECT last_viewed_excuses FROM users WHERE id = :id");
        $db->bind(':id', $_SESSION['user_id']);
        $user = $db->single();
        
        if ($user && $user->last_viewed_excuses) {
            $db->query("SELECT COUNT(*) as count FROM excuses WHERE status = 'Pending' AND created_at > :last_viewed");
            $db->bind(':last_viewed', $user->last_viewed_excuses);
        } else {
            $db->query("SELECT COUNT(*) as count FROM excuses WHERE status = 'Pending'");
        }
        $pendingExcuses = $db->single()->count;
    }
} catch (Exception $e) {}

// Navigation
$nav_items = [
    [
        'label' => 'Main',
        'type' => 'flat',
        'items' => [
            [
                'label' => 'Dashboard',
                'url' => $root . '/dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6zM14 6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2V6zM4 16a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2zM14 16a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-2z" />'
            ],
            [
                'label' => 'Announcements',
                'url' => $root . '/announcements',
                'badge' => $unreadAnnouncements,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />'
            ],
        ]
    ],
    [
        'label' => 'Operations',
        'type' => 'flat',
        'items' => [
            [
                'label' => 'Schedules',
                'url' => $root . '/schedules',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
            ],
            [
                'label' => 'Attendance',
                'url' => $root . '/attendance',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />'
            ],
            [
                'label' => 'Excuse Letters',
                'url' => $root . '/excuses',
                'badge' => $pendingExcuses,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'
            ],
        ]
    ],
    [
        'label' => 'Directory',
        'type' => 'flat',
        'items' => [
            [
                'label' => 'Server Profiles',
                'url' => $root . '/servers',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />'
            ],
            [
                'label' => 'User Accounts',
                'url' => $root . '/users',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />'
            ],
        ]
    ],
    [
        'label' => 'System',
        'type' => 'dropdown',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        'items' => [
            [
                'label' => 'Reports',
                'url' => $root . '/reports',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'
            ],
            [
                'label' => 'Audit Logs',
                'url' => $root . '/logs',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'
            ],
            [
                'label' => 'Archive Center',
                'url' => $root . '/archives',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />'
            ],
        ]
    ],
    [
        'label' => 'Setup',
        'type' => 'flat',
        'items' => [
            [
                'label' => 'Settings',
                'url' => $root . '/settings',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'
            ]
        ]
    ]
];
?>

<aside class="w-64 bg-white border-r border-slate-100 flex flex-col justify-between hidden md:flex shrink-0 h-full overflow-hidden">
    <div class="flex flex-col h-full overflow-hidden">
        <!-- Logo -->
        <div class="p-4 flex items-center gap-3 shrink-0">
            <div class="w-10 h-10 flex items-center justify-center">
                <img src="<?= URLROOT ?>/images/logo.png" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="font-bold text-slate-900 leading-tight text-sm">Altar Servers</h1>
                <p class="text-[9px] text-slate-400 font-medium uppercase tracking-wide">Management System</p>
            </div>
        </div>

        <!-- Role Badge -->
        <div class="px-6 mb-4 shrink-0">
            <span class="inline-block bg-fuchsia-100 text-fuchsia-600 text-[10px] font-bold px-3 py-1 rounded-full w-fit uppercase tracking-wider">
                <?php 
                    $displayRole = $_SESSION['role'] ?? 'Server';
                    echo ($displayRole === 'User') ? 'Server' : $displayRole;
                ?>
            </span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 space-y-6 overflow-y-auto custom-scrollbar pb-8">
            <?php foreach ($nav_items as $group): ?>
                <?php 
                    $role = $_SESSION['role'] ?? 'User';
                    $visible_items = array_filter($group['items'], function($item) use ($role) {
                        if ($role === 'User') {
                            if (in_array($item['label'], ['Server Profiles', 'User Accounts', 'Reports', 'Audit Logs', 'Archive Center'])) return false;
                        }
                        if ($role === 'Admin') {
                            if (in_array($item['label'], ['User Accounts', 'Audit Logs', 'Archive Center'])) return false;
                        }
                        return true;
                    });

                    if (empty($visible_items)) continue;
                ?>

                <div>
                    <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-3">
                        <?= $group['label'] ?>
                    </p>
                    
                    <div class="space-y-1">
                        <?php if ($group['type'] === 'flat'): ?>
                            <?php foreach ($visible_items as $item): 
                                $isActive = (strpos($current_path, parse_url($item['url'], PHP_URL_PATH)) === 0);
                                if ($item['url'] === $root . '/dashboard' && !($current_path === $root . '/' || $current_path === $root . '/dashboard')) $isActive = false;
                            ?>
                                <a href="<?= $item['url'] ?>" 
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all <?= $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-slate-50' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $item['icon'] ?></svg>
                                    <span class="font-bold text-xs flex-1"><?= $item['label'] ?></span>
                                    <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                                        <span class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $item['badge'] ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: 
                            // Dropdown Logic
                            $hasActiveChild = false;
                            foreach ($visible_items as $item) {
                                if (strpos($current_path, parse_url($item['url'], PHP_URL_PATH)) === 0) { $hasActiveChild = true; break; }
                            }
                        ?>
                            <div class="space-y-1 dropdown-group">
                                <button onclick="toggleSidebarDropdown(this)" 
                                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all text-slate-600 hover:bg-slate-50 <?= $hasActiveChild ? 'bg-slate-50/50' : '' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $group['icon'] ?></svg>
                                    <span class="font-bold text-xs flex-1 text-left"><?= $group['label'] ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-200 dropdown-arrow <?= $hasActiveChild ? 'rotate-180' : '' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                
                                <div class="dropdown-content pl-4 space-y-1 mt-1 border-l-2 border-slate-100 ml-6 <?= $hasActiveChild ? '' : 'hidden' ?>">
                                    <?php foreach ($visible_items as $item): 
                                        $isActive = (strpos($current_path, parse_url($item['url'], PHP_URL_PATH)) === 0);
                                    ?>
                                        <a href="<?= $item['url'] ?>" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all <?= $isActive ? 'text-blue-600 font-extrabold bg-blue-50' : 'text-slate-500 hover:text-slate-800' ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $item['icon'] ?></svg>
                                            <span class="font-bold text-[11px] flex-1"><?= $item['label'] ?></span>
                                            <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                                                <span class="bg-red-500 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full"><?= $item['badge'] ?></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="p-4 border-t border-slate-50 shrink-0 bg-white">
        <a href="<?= URLROOT ?>/logout" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="font-bold text-xs">Logout</span>
        </a>
    </div>
</aside>

<script>
    function toggleSidebarDropdown(btn) {
        const group = btn.closest('.dropdown-group');
        const content = group.querySelector('.dropdown-content');
        const arrow = btn.querySelector('.dropdown-arrow');
        
        content.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }
</script>