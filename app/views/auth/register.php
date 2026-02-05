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
        .login-card {
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

    <div class="w-full max-w-md animate-fade-up">
        <!-- Branding Top -->
        <div class="text-center mb-8">
            <div class="inline-flex p-4 bg-white/10 rounded-3xl backdrop-blur-xl mb-4 shadow-xl ring-1 ring-white/20">
                <img src="<?= URLROOT ?>/images/logo.png" alt="Logo" class="h-16 w-auto drop-shadow-2xl">
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Join the Ministry</h1>
            <p class="text-slate-400 text-sm mt-2 font-medium">Create your server account today</p>
        </div>

        <!-- Register Card -->
        <div class="login-card p-8 md:p-10 rounded-[2.5rem] shadow-2xl border border-white/20">
            <div class="mb-8">
                <h2 class="text-xl font-bold text-slate-800">Create Account</h2>
                <p class="text-slate-500 text-xs mt-1">Start your journey as an Altar Server.</p>
            </div>

            <?php if(isset($error)): ?>
                <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-2xl mb-6 text-xs font-bold flex items-center gap-3">
                    <i class="ph-bold ph-warning-circle text-lg"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="<?= URLROOT ?>/auth/register" method="POST" class="space-y-5">
                <?php csrf_field(); ?>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                    <div class="relative group">
                        <span class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <i class="ph ph-user text-xl"></i>
                        </span>
                        <input type="text" name="username" required 
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-medium text-slate-700 placeholder:text-slate-400"
                            placeholder="Pick a unique username">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                    <div class="relative group">
                        <span class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <i class="ph ph-lock-key text-xl"></i>
                        </span>
                        <input type="password" name="password" required 
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-medium text-slate-700 placeholder:text-slate-400"
                            placeholder="Create a strong password">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Confirm Password</label>
                    <div class="relative group">
                        <span class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <i class="ph ph-lock-key text-xl"></i>
                        </span>
                        <input type="password" name="confirm_password" required 
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-medium text-slate-700 placeholder:text-slate-400"
                            placeholder="Repeat password">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-blue-200 transform active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>Register Now</span>
                        <i class="ph-bold ph-user-plus"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center text-sm text-slate-500">
                Already have an account? 
                <a href="<?= URLROOT ?>/login" class="text-blue-600 hover:underline font-bold">Sign In</a>
            </div>
        </div>
        
        <!-- Footer Info -->
        <p class="text-center mt-8 text-slate-500 text-[10px] font-bold uppercase tracking-widest">
            &copy; <?= date('Y') ?> Sacred Heart of Jesus Parish. All rights reserved.
        </p>
    </div>

</body>
</html>