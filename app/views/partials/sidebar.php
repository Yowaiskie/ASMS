<?php
// Get current path to set active state
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$root = URLROOT; // /ASMS/public

// Check for unread announcements (for Users)
$unreadAnnouncements = 0;
$pendingExcuses = 0;
$notifCount = 0;

try {
    $db = \App\Core\Database::getInstance();
    $role = $_SESSION['role'] ?? 'User';

    // 1. Unread Activity Count (Announcements + Notifications)
    $totalUnread = 0;
    if (isset($_SESSION['user_id'])) {
        // Announcements
        $db->query("SELECT last_read_announcements FROM users WHERE id = :id");
        $db->bind(':id', $_SESSION['user_id']);
        $res = $db->single();
        $lastRead = $res ? $res->last_read_announcements : null;
        
        if (!$lastRead) {
             $db->query("SELECT COUNT(*) as count FROM announcements");
        } else {
             $db->query("SELECT COUNT(*) as count FROM announcements WHERE created_at > :last_read");
             $db->bind(':last_read', $lastRead);
        }
        $totalUnread += $db->single()->count;

        // Notifications
        $db->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = :id AND is_read = 0");
        $db->bind(':id', $_SESSION['user_id']);
        $totalUnread += $db->single()->count;
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

// Navigation Structure
$nav_items = [
    [
        'label' => 'Overview',
        'type' => 'dropdown',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
        'items' => [
            [
                'label' => 'Dashboard',
                'url' => $root . '/dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6zM14 6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2V6zM4 16a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2zM14 16a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-2z" />'
            ],
            [
                'label' => 'Activity Center',
                'url' => $root . '/notifications',
                'badge' => $totalUnread,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />'
            ],
        ]
    ],
    [
        'label' => 'Service',
        'type' => 'dropdown',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-1 1H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
        'items' => [
            [
                'label' => 'Schedules',
                'url' => $root . '/schedules',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
            ],
            [
                'label' => 'Master Plan',
                'url' => $root . '/schedules/templates',
                'role' => ['Admin', 'Superadmin'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />'
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
        'label' => 'Administration',
        'type' => 'dropdown',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
        'items' => [
            [
                'label' => 'Server Profiles',
                'url' => $root . '/servers',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />'
            ],
            [
                'label' => 'Server Accounts',
                'url' => $root . '/users',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />'
            ],
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
        'label' => 'System Settings',
        'type' => 'dropdown',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
        'items' => [
            [
                'label' => 'Settings',
                'url' => $root . '/settings',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'
            ],
            [
                'label' => 'System Configuration',
                'url' => $root . '/settings/system',
                'role' => ['Admin', 'Superadmin'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />'
            ],
            [
                'label' => 'Database Management',
                'url' => $root . '/settings/database',
                'role' => 'Superadmin',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 1.1.9 2 2 2h12a2 2 0 002-2V7M4 7c0-1.1.9-2 2-2h12a2 2 0 002 2M4 7l8 5 8-5M12 12l8 5" />'
            ],
        ]
    ],
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
                <h1 class="font-bold text-slate-900 leading-tight text-sm tracking-tight"><?= h($system_name ?? 'Altar Servers') ?></h1>
                <p class="text-[9px] text-slate-400 font-medium uppercase tracking-wide">Management System</p>
            </div>
        </div>

        <!-- Role Badge -->
        <div class="px-6 mb-4 shrink-0">
            <span class="inline-block bg-fuchsia-100 text-fuchsia-600 text-[10px] font-bold px-3 py-1 rounded-full w-fit uppercase tracking-wider">
                <?php 
                    $role = $_SESSION['role'] ?? 'Server';
                    echo ($role === 'User') ? 'Server' : h($role);
                ?>
            </span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 space-y-6 overflow-y-auto custom-scrollbar pb-8">
            <?php foreach ($nav_items as $group): ?>
                <?php 
                    $role = $_SESSION['role'] ?? 'User';
                    
                    // 1. If it's a direct link (no dropdown), handle visibility directly
                    if (!isset($group['type']) || $group['type'] !== 'dropdown') {
                        if (isset($group['role'])) {
                            if (is_array($group['role'])) {
                                if (!in_array($role, $group['role'])) continue;
                            } else {
                                if ($group['role'] !== $role) continue;
                            }
                        }
                        $visible_items = []; 
                    } else {
                        // 2. If it's a dropdown, filter the sub-items
                        $visible_items = array_filter($group['items'] ?? [], function($item) use ($role) {
                            if (isset($item['role'])) {
                                if (is_array($item['role'])) {
                                    if (!in_array($role, $item['role'])) return false;
                                } else {
                                    if ($item['role'] !== $role) return false;
                                }
                            }
                            
                            if ($role === 'User') {
                                if (in_array($item['label'], ['Server Profiles', 'Server Accounts', 'Reports', 'Audit Logs', 'Archive Center', 'Database Management'])) return false;
                            }
                            if ($role === 'Admin') {
                                if (in_array($item['label'], ['Server Accounts', 'Audit Logs', 'Archive Center', 'Database Management'])) return false;
                            }
                            return true;
                        });

                        if (empty($visible_items)) continue;
                    }
                ?>

                <div>
                    <div class="space-y-1">
                        <?php 
                            $isDropdown = (isset($group['type']) && $group['type'] === 'dropdown');
                            $visibleCount = count($visible_items);
                            
                            // If it's a dropdown but only has 1 item, treat it as a single link
                            if ($isDropdown && $visibleCount === 1): 
                                $singleItem = reset($visible_items);
                                $itemPath = parse_url($singleItem['url'], PHP_URL_PATH);
                                $isActive = ($current_path === $itemPath);
                        ?>
                            <a href="<?= $singleItem['url'] ?>" 
                               class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all <?= $isActive ? 'bg-primary-50 text-primary font-bold shadow-sm shadow-primary/5' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $singleItem['icon'] ?></svg>
                                <span class="font-bold text-xs flex-1 text-left"><?= $singleItem['label'] ?></span>
                            </a>

                        <?php elseif ($isDropdown && $visibleCount > 1): ?>
                            <?php 
                                // Standard Dropdown Logic
                                $hasActiveChild = false;
                                foreach ($visible_items as $item) {
                                    $itemPath = parse_url($item['url'], PHP_URL_PATH);
                                    if ($current_path === $itemPath) { $hasActiveChild = true; break; }
                                    if ($item['label'] !== 'Schedules' && strpos($current_path, $itemPath) === 0) { $hasActiveChild = true; break; }
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
                                        $itemPath = parse_url($item['url'], PHP_URL_PATH);
                                        $isActive = ($current_path === $itemPath);
                                        if ($item['label'] === 'Schedules' && $current_path !== $itemPath) { $isActive = false; }
                                    ?>
                                        <a href="<?= $item['url'] ?>" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-xl transition-all <?= $isActive ? 'bg-primary-50 text-primary font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $item['icon'] ?></svg>
                                            <span class="font-bold text-[11px] flex-1"><?= $item['label'] ?></span>
                                            <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                                                <span class="bg-red-500 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full unread-badge-count"><?= $item['badge'] ?></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php 
                                // Direct Link Logic
                                $groupPath = parse_url($group['url'], PHP_URL_PATH);
                                $isActive = ($current_path === $groupPath);
                            ?>
                            <a href="<?= $group['url'] ?>" 
                               class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all <?= $isActive ? 'bg-primary-50 text-primary font-bold shadow-sm shadow-primary/5' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $group['icon'] ?></svg>
                                <span class="font-bold text-xs flex-1 text-left"><?= $group['label'] ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Logout (Visible in Nav for Cloning) -->
            <div class="pt-4 mt-4 border-t border-slate-50">
                <a href="<?= URLROOT ?>/logout" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="font-bold text-xs">Logout</span>
                </a>
            </div>
        </nav>
    </div>
</aside>

<script>
    function toggleSidebarDropdown(btn) {
        const content = btn.nextElementSibling;
        const arrow = btn.querySelector('.dropdown-arrow');
        if (content) {
            content.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }
</script>