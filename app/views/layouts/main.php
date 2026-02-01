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
</body>
</html>