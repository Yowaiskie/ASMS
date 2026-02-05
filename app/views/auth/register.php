<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ASMS</title>
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
        body { font-family: 'Inter', sans-serif; }
        .bg-mesh {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.15) 0px, transparent 50%);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade { animation: fadeIn 0.8s ease-out forwards; }
        
        .input-group:focus-within label { color: #2563eb; }
        .input-group:focus-within .input-icon { color: #2563eb; transform: scale(1.1); }
        .custom-input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .custom-input:focus {
            background-color: #fff;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.1), 0 8px 10px -6px rgba(37, 99, 235, 0.1);
        }
    </style>
</head>
<body class="bg-white min-h-screen flex overflow-hidden">

    <!-- Left Side: Visual/Branding -->
    <div class="hidden lg:flex lg:w-5/12 bg-mesh relative items-center justify-center p-8 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
        
        <div class="relative z-10 text-center max-w-sm animate-fade">
            <div class="inline-flex p-4 bg-white/10 rounded-3xl backdrop-blur-2xl mb-6 shadow-2xl ring-1 ring-white/20">
                <img src="<?= URLROOT ?>/images/logo.png" alt="Logo" class="h-16 w-auto drop-shadow-2xl">
            </div>
            <h1 class="text-3xl font-black text-white tracking-tighter mb-4 leading-tight">
                Begin Your <br><span class="text-blue-500 text-2xl">Service Today.</span>
            </h1>
            <p class="text-slate-400 text-sm font-medium leading-relaxed italic">
                Join the Ministry of Altar Servers and be part of our community.
            </p>
        </div>
    </div>

    <!-- Right Side: Enhanced Register Form -->
    <div class="w-full lg:w-7/12 flex items-center justify-center p-6 md:p-12 bg-slate-50/30 relative overflow-y-auto">
        <div class="w-full max-w-sm animate-fade" style="animation-delay: 0.1s">
            
            <div class="mb-10 text-center lg:text-left">
                <div class="lg:hidden flex justify-center mb-6">
                    <img src="<?= URLROOT ?>/images/logo.png" alt="Logo" class="h-12 w-auto">
                </div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Create Account</h2>
                <p class="text-slate-500 text-sm font-medium">Start your journey as an Altar Server.</p>
            </div>

            <?php if(isset($error)): ?>
                <div class="bg-rose-50 border border-rose-100 text-rose-600 px-4 py-3 rounded-2xl mb-8 text-xs font-bold flex items-center gap-3 animate-fade">
                    <i class="ph-bold ph-warning-circle text-lg"></i>
                    <p><?= $error ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= URLROOT ?>/auth/register" method="POST" class="space-y-6">
                <?php csrf_field(); ?>
                
                <div class="space-y-2 input-group">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 transition-colors">Username</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 input-icon transition-all">
                            <i class="ph-bold ph-user-circle text-xl"></i>
                        </span>
                        <input type="text" name="username" required 
                            class="custom-input w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-sm font-bold text-slate-800 placeholder:text-slate-300 placeholder:font-medium"
                            placeholder="Pick a unique username">
                    </div>
                </div>

                <div class="space-y-2 input-group">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 transition-colors">Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 input-icon transition-all">
                            <i class="ph-bold ph-lock-key text-xl"></i>
                        </span>
                        <input type="password" name="password" required 
                            class="custom-input w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-sm font-bold text-slate-800 placeholder:text-slate-300"
                            placeholder="Create a password">
                    </div>
                </div>

                <div class="space-y-2 input-group">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 transition-colors">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 input-icon transition-all">
                            <i class="ph-bold ph-lock-key text-xl"></i>
                        </span>
                        <input type="password" name="confirm_password" required 
                            class="custom-input w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-sm font-bold text-slate-800 placeholder:text-slate-300"
                            placeholder="Repeat password">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="group w-full bg-slate-900 hover:bg-blue-600 text-white font-black py-4 rounded-2xl transition-all shadow-xl shadow-slate-200 hover:shadow-blue-200 transform active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-widest text-xs">
                        <span>Register Now</span>
                        <i class="ph-bold ph-user-plus text-base group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 lg:left-12 lg:translate-x-0">
            <p class="text-slate-300 text-[9px] font-black uppercase tracking-[0.3em]">
                ASMS &bull; SHJP MBS &bull; <?= date('Y') ?>
            </p>
        </div>
    </div>

</body>
</html>