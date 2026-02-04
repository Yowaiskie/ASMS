<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ASMS Dashboard' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= URLROOT ?>/images/logo.png">
    
    <!-- CSS -->
    <link href="<?= URLROOT ?>/output.css" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e63d4;
            --color-primary: #1e63d4;
        }
        .bg-primary { background-color: #1e63d4 !important; }

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
<body class="bg-[#f8fafc] font-sans text-slate-800 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Main Content Wrapper -->
    <main class="flex-1 flex flex-col transition-all duration-300 w-full overflow-hidden">
        
        <!-- Mobile Header (Optional) -->
        <header class="md:hidden h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 shadow-sm z-40 shrink-0">
            <span class="font-bold text-lg text-primary">ASMS</span>
            <button class="p-2 text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
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
                    <button id="globalConfirmYesBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-[0.98]">Yes, Proceed</button>
                    <button onclick="closeGlobalConfirm()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 rounded-xl transition-all">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Alert Modal -->
    <div id="globalAlertModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[150] hidden items-center justify-center p-4 transition-opacity duration-200 opacity-0">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform scale-95 transition-transform duration-200" id="globalAlertContent">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
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
    </script>
</body>
</html>