<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? h($system_name) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= URLROOT ?>/images/logo.png">
    
    <!-- CSS -->
    <link href="<?= URLROOT ?>/output.css" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #a33b39;
            --color-primary: #a33b39;
            --secondary: #00599c;
            --accent: #f9c402;
        }
        .bg-primary { background-color: #a33b39 !important; }
        .text-primary { color: #a33b39 !important; }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Fix for mobile height issues */
        .h-screen-safe {
            height: 100vh;
            height: 100dvh;
        }

        .bg-mesh-dark {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(163, 59, 57, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(163, 59, 57, 0.15) 0px, transparent 50%);
        }

        /* Global Loading Overlay */
        #global-loader {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            z-index: 200;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
        }
        .loader-spinner {
            width: 48px;
            height: 48px;
            border: 5px solid #fff;
            border-bottom-color: var(--primary);
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
            margin-bottom: 1rem;
        }
        @keyframes rotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Button Loading Spinner */
        .btn-loading {
            position: relative !important;
            color: transparent !important;
            pointer-events: none !important;
        }
        .btn-loading > * {
            opacity: 0 !important;
        }
        .btn-loading::after {
            content: "";
            position: absolute;
            width: 1.25rem;
            height: 1.25rem;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            border: 2px solid #fff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: btn-spinner 0.6s linear infinite;
        }
        /* For light background buttons */
        .bg-white.btn-loading::after, .bg-slate-50.btn-loading::after, .bg-slate-100.btn-loading::after {
            border-color: #64748b;
            border-top-color: transparent;
        }
        @keyframes btn-spinner {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 flex h-screen-safe overflow-hidden">

    <!-- Mobile Sidebar Backdrop (Overlay) -->
    <div id="mobile-sidebar-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden transition-opacity duration-300 opacity-0 md:hidden" onclick="closeMobileSidebar()"></div>

    <!-- Mobile Sidebar Container -->
    <div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white z-[60] transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden shadow-2xl">
        <div class="h-full flex flex-col">
            <div class="p-4 flex items-center justify-between border-b border-slate-50">
                <div class="flex items-center gap-2">
                    <img src="<?= URLROOT ?>/images/logo.png" class="h-8 w-auto">
                    <span class="font-bold text-slate-800"><?= h($system_name) ?></span>
                </div>
                <button onclick="closeMobileSidebar()" class="p-2 text-slate-400 hover:text-slate-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto" id="mobile-sidebar-content">
                <!-- Sidebar content will be cloned here via JS to avoid duplication -->
            </div>
        </div>
    </div>

    <!-- Sidebar (Desktop) -->
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Loading Overlay -->
    <div id="global-loader">
        <span class="loader-spinner"></span>
        <p class="font-bold text-sm tracking-widest uppercase animate-pulse">Generating PDF...</p>
        <p class="text-[10px] text-slate-300 mt-2">Please wait a moment</p>
    </div>

    <!-- Main Content Wrapper -->
    <main class="flex-1 flex flex-col transition-all duration-300 w-full overflow-hidden">
        
        <!-- Mobile Header -->
        <header class="md:hidden h-16 bg-white border-b border-slate-100 flex items-center justify-between px-4 shrink-0 z-40">
            <div class="flex items-center gap-3">
                <button onclick="openMobileSidebar()" class="p-2 -ml-2 text-slate-500 hover:text-primary transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <img src="<?= URLROOT ?>/images/logo.png" class="h-8 w-auto">
                    <span class="font-black text-slate-800 tracking-tight"><?= h($system_name) ?></span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Mobile Notification Bell -->
                <div class="relative">
                    <button onclick="toggleNotifications('mobile')" class="p-2 text-slate-400 hover:text-primary transition-colors relative">
                        <i class="ph-bold ph-bell text-2xl"></i>
                        <span id="notif-badge-mobile" class="hidden absolute top-1.5 right-1.5 w-4 h-4 bg-primary text-white text-[10px] font-black flex items-center justify-center rounded-full border-2 border-white animate-bounce">0</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Desktop Header (Internal Pages) -->
        <header class="hidden md:flex h-16 bg-white/80 backdrop-blur-md border-b border-slate-100 items-center justify-end px-8 shrink-0 z-40 sticky top-0">
            <div class="flex items-center gap-4">
                <!-- Notification Bell -->
                <div class="relative" id="notif-dropdown-container">
                    <button onclick="toggleNotifications('desktop')" class="w-10 h-10 bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary rounded-xl flex items-center justify-center transition-all relative group">
                        <i class="ph-bold ph-bell text-xl group-hover:animate-swing"></i>
                        <span id="notif-badge-desktop" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-[10px] font-black flex items-center justify-center rounded-full border-2 border-white animate-bounce">0</span>
                    </button>

                    <!-- Dropdown -->
                    <div id="notif-dropdown" class="absolute right-0 mt-3 w-80 bg-white rounded-3xl shadow-2xl border border-slate-100 hidden flex-col overflow-hidden animate-fade-in-up origin-top-right z-50">
                        <div class="p-4 border-b border-slate-50 flex items-center justify-between">
                            <h5 class="font-bold text-slate-800">Notifications</h5>
                            <a href="<?= URLROOT ?>/notifications" class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline">View All</a>
                        </div>
                        <div id="notif-items" class="max-h-96 overflow-y-auto custom-scrollbar">
                            <!-- Items populated via JS -->
                            <div class="p-8 text-center">
                                <i class="ph-bold ph-bell-slash text-3xl text-slate-200 mb-2"></i>
                                <p class="text-xs text-slate-400 font-medium">No new notifications</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-8 w-px bg-slate-100 mx-2"></div>

                <div class="flex items-center gap-3 px-3 py-1.5 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-8 h-8 bg-primary/10 text-primary rounded-xl flex items-center justify-center font-bold text-xs uppercase">
                        <?= substr($_SESSION['username'] ?? 'U', 0, 1) ?>
                    </div>
                    <div class="hidden lg:block text-left">
                        <p class="text-xs font-bold text-slate-800 truncate max-w-[100px]"><?= h($_SESSION['username'] ?? 'User') ?></p>
                        <p class="text-[9px] font-medium text-slate-400 uppercase tracking-tighter"><?= h($_SESSION['role'] ?? 'Member') ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            <?php flash('msg_success'); ?>
            <?php flash('msg_error'); ?>
            
            <?php 
                // Render the main content
                if (isset($content)) {
                    echo $content;
                }
            ?>
        </div>
        
    </main>

    <script src="<?= URLROOT ?>/js/app.js"></script>

    <!-- Global Confirmation Modal -->
    <div id="globalConfirmModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[150] hidden items-center justify-center p-4 transition-opacity duration-200 opacity-0">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform scale-95 transition-transform duration-200" id="globalConfirmContent">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ph-bold ph-warning text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2" id="globalConfirmTitle">Confirm Action</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6" id="globalConfirmMessage">
                    Are you sure you want to proceed?
                </p>
                <div class="flex gap-3">
                    <button id="globalConfirmYesBtn" class="flex-1 bg-primary hover:opacity-90 text-white font-bold py-3 rounded-xl shadow-lg shadow-primary-200 transition-all active:scale-[0.98]">Yes, Proceed</button>
                    <button onclick="closeGlobalConfirm()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 rounded-xl transition-all">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Alert Modal -->
    <div id="globalAlertModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[150] hidden items-center justify-center p-4 transition-opacity duration-200 opacity-0">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform scale-95 transition-transform duration-200" id="globalAlertContent">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ph-bold ph-info text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2" id="globalAlertTitle">Notice</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6" id="globalAlertMessage">
                    This is an alert message.
                </p>
                <button onclick="closeGlobalAlert()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 rounded-xl transition-all">Okay, Got it</button>
            </div>
        </div>
    </div>

    <script>
        // Global Modal Helpers
        const globalConfirmModal = document.getElementById('globalConfirmModal');
        const globalConfirmContent = document.getElementById('globalConfirmContent');
        const globalConfirmTitle = document.getElementById('globalConfirmTitle');
        const globalConfirmMessage = document.getElementById('globalConfirmMessage');
        const globalConfirmYesBtn = document.getElementById('globalConfirmYesBtn');

        const globalAlertModal = document.getElementById('globalAlertModal');
        const globalAlertContent = document.getElementById('globalAlertContent');
        const globalAlertTitle = document.getElementById('globalAlertTitle');
        const globalAlertMessage = document.getElementById('globalAlertMessage');

        let confirmCallback = null;

        window.showConfirm = function(message, title = 'Confirm Action', callback) {
            globalConfirmMessage.innerText = message;
            globalConfirmTitle.innerText = title;
            confirmCallback = callback;
            
            globalConfirmModal.classList.remove('hidden');
            globalConfirmModal.classList.add('flex');
            // Animate in
            setTimeout(() => {
                globalConfirmModal.classList.remove('opacity-0');
                globalConfirmContent.classList.remove('scale-95');
                globalConfirmContent.classList.add('scale-100');
            }, 10);
        };

        window.closeGlobalConfirm = function() {
            globalConfirmModal.classList.add('opacity-0');
            globalConfirmContent.classList.remove('scale-100');
            globalConfirmContent.classList.add('scale-95');
            setTimeout(() => {
                globalConfirmModal.classList.add('hidden');
                globalConfirmModal.classList.remove('flex');
                confirmCallback = null;
            }, 200);
        };

        globalConfirmYesBtn.onclick = function() {
            if (confirmCallback) confirmCallback();
            closeGlobalConfirm();
        };

        window.showAlert = function(message, title = 'Notice') {
            globalAlertMessage.innerText = message;
            globalAlertTitle.innerText = title;
            
            globalAlertModal.classList.remove('hidden');
            globalAlertModal.classList.add('flex');
            // Animate in
            setTimeout(() => {
                globalAlertModal.classList.remove('opacity-0');
                globalAlertContent.classList.remove('scale-95');
                globalAlertContent.classList.add('scale-100');
            }, 10);
        };

        window.closeGlobalAlert = function() {
            globalAlertModal.classList.add('opacity-0');
            globalAlertContent.classList.remove('scale-100');
            globalAlertContent.classList.add('scale-95');
            setTimeout(() => {
                globalAlertModal.classList.add('hidden');
                globalAlertModal.classList.remove('flex');
            }, 200);
        };

        // Loading Handler
        document.addEventListener('click', function(e) {
            const loadingEl = e.target.closest('[data-loading]');
            if (loadingEl) {
                const loader = document.getElementById('global-loader');
                loader.style.display = 'flex';
                
                // Also add button spinner if it's a button or link
                loadingEl.classList.add('btn-loading');
                
                // Hide after 3.5 seconds (fallback since download finished is hard to detect)
                setTimeout(() => {
                    loader.style.display = 'none';
                    loadingEl.classList.remove('btn-loading');
                }, 3500);
            }
        });

        // Mobile Sidebar Controls
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileOverlay = document.getElementById('mobile-sidebar-overlay');
        const desktopSidebarNav = document.querySelector('aside nav');
        const mobileSidebarContent = document.getElementById('mobile-sidebar-content');

        function openMobileSidebar() {
            // Clone nav if not already cloned
            if (mobileSidebarContent.children.length === 0) {
                const navClone = desktopSidebarNav.cloneNode(true);
                mobileSidebarContent.appendChild(navClone);
            }
            
            mobileSidebar.classList.remove('-translate-x-full');
            mobileOverlay.classList.remove('hidden');
            setTimeout(() => {
                mobileOverlay.classList.remove('opacity-0');
            }, 10);
        }

        function closeMobileSidebar() {
            mobileSidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('opacity-0');
            setTimeout(() => {
                mobileOverlay.classList.add('hidden');
            }, 300);
        }

        // Notification System Logic
        const notifDropdown = document.getElementById('notif-dropdown');
        const notifItems = document.getElementById('notif-items');
        const notifBadgeDesktop = document.getElementById('notif-badge-desktop');
        const notifBadgeMobile = document.getElementById('notif-badge-mobile');

        function toggleNotifications(type) {
            if (notifDropdown.classList.contains('hidden')) {
                fetchNotifications();
                notifDropdown.classList.remove('hidden');
                notifDropdown.classList.add('flex');
            } else {
                notifDropdown.classList.add('hidden');
                notifDropdown.classList.remove('flex');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('notif-dropdown-container');
            if (container && !container.contains(e.target)) {
                notifDropdown.classList.add('hidden');
                notifDropdown.classList.remove('flex');
            }
        });

        function fetchNotifications() {
            fetch('<?= URLROOT ?>/notifications/latest')
                .then(response => response.json())
                .then(data => {
                    updateNotificationUI(data);
                });
        }

        function updateNotificationUI(data) {
            // Update Badges
            if (data.count > 0) {
                notifBadgeDesktop.innerText = data.count;
                notifBadgeDesktop.classList.remove('hidden');
                notifBadgeMobile.innerText = data.count;
                notifBadgeMobile.classList.remove('hidden');
            } else {
                notifBadgeDesktop.classList.add('hidden');
                notifBadgeMobile.classList.add('hidden');
            }

            // Update Dropdown Items
            if (data.notifications && data.notifications.length > 0) {
                let html = '';
                data.notifications.forEach(n => {
                    let iconClass = 'ph-info';
                    let iconBg = 'bg-blue-50 text-blue-500';
                    if (n.type === 'success') { iconClass = 'ph-check-circle'; iconBg = 'bg-emerald-50 text-emerald-500'; }
                    if (n.type === 'warning') { iconClass = 'ph-warning'; iconBg = 'bg-amber-50 text-amber-500'; }
                    if (n.type === 'danger') { iconClass = 'ph-warning-octagon'; iconBg = 'bg-red-50 text-red-500'; }
                    if (n.type === 'schedule') { iconClass = 'ph-calendar'; iconBg = 'bg-indigo-50 text-indigo-500'; }

                    html += `
                        <a href="<?= URLROOT ?>${n.link || '/notifications'}" class="p-4 border-b border-slate-50 flex gap-4 hover:bg-slate-50 transition-colors group">
                            <div class="shrink-0 w-10 h-10 ${iconBg} rounded-xl flex items-center justify-center text-xl">
                                <i class="ph-bold ${iconClass}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">${n.title}</p>
                                <p class="text-[10px] text-slate-500 line-clamp-2 mt-0.5">${n.message}</p>
                                <p class="text-[9px] font-medium text-slate-400 mt-1 uppercase tracking-tighter">${timeAgo(n.created_at)}</p>
                            </div>
                        </a>
                    `;
                });
                notifItems.innerHTML = html;
            }
        }

        function timeAgo(date) {
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + "y ago";
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + "mo ago";
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + "d ago";
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + "h ago";
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + "m ago";
            return "just now";
        }

        // Poll every 30 seconds
        if (<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>) {
            fetchNotifications();
            setInterval(fetchNotifications, 30000);
        }
    </script>
</body>
</html>
