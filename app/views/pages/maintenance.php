<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance | ASMS</title>
    <link rel="icon" type="image/png" href="<?= URLROOT ?>/images/logo.png">
    
    <!-- CSS -->
    <link href="<?= URLROOT ?>/output.css" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .bg-mesh {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 99, 212, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 99, 212, 0.15) 0px, transparent 50%);
        }
        .glass-card {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.9);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
    </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md animate-fade-up text-center">
        <!-- Icon -->
        <div class="inline-flex p-6 bg-amber-500/10 rounded-[2.5rem] backdrop-blur-xl mb-8 shadow-xl ring-1 ring-amber-500/20 text-amber-500">
            <i class="ph-fill ph-wrench text-5xl"></i>
        </div>

        <!-- Card -->
        <div class="glass-card p-10 rounded-[3rem] shadow-2xl border border-white/20">
            <h1 class="text-3xl font-black text-slate-800 mb-4 tracking-tight">System Update</h1>
            <p class="text-slate-500 text-sm leading-relaxed mb-8">
                We are currently performing some essential maintenance to improve your experience. 
                The system will be back online shortly.
            </p>
            
            <div class="p-5 bg-amber-50/50 rounded-2xl border border-amber-100 mb-8">
                <p class="text-[10px] text-amber-600 uppercase font-black tracking-widest mb-1">Status Notice</p>
                <p class="text-xs text-amber-800 font-bold">Only Superadmins can access the system at this time.</p>
            </div>

            <a href="<?= URLROOT ?>/logout" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-bold text-sm transition-all group">
                <i class="ph-bold ph-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Go back to login
            </a>
        </div>
        
        <p class="mt-12 text-slate-500 text-[10px] font-bold uppercase tracking-widest">
            Altar Servers Management System &bull; SHJP MBS
        </p>
    </div>

</body>
</html>