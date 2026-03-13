<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | ASMS</title>
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
        }
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0;
            padding: 0;
        }
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(163, 59, 57, 0.4) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(0, 89, 156, 0.3) 0px, transparent 50%),
                url('<?= URLROOT ?>/images/MAS.png');
            background-size: cover;
            background-position: center;
            padding: 20px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 32px;
            width: 100%;
            max-width: 1000px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .form-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .custom-input {
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }
        .custom-input:focus {
            box-shadow: 0 0 0 4px rgba(163, 59, 57, 0.2);
            border-color: var(--primary);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-up { animation: fadeInUp 0.8s ease-out forwards; }
    </style>
</head>
<body class="bg-slate-900">

    <div class="login-wrapper">
        <div class="glass-card animate-up">
            
            <!-- Left Side: Visual Content -->
            <div class="hidden lg:flex flex-col justify-end p-16 w-1/2 relative">
                <div class="relative z-10">
                    <div class="mb-8">
                        <img src="<?= URLROOT ?>/images/logo.png" alt="SHJP Logo" class="h-20 w-auto drop-shadow-2xl">
                    </div>
                    <h1 class="text-6xl font-black text-white leading-tight tracking-tighter mb-4">
                        FINAL <br><span class="text-primary-400">STEP.</span>
                    </h1>
                    <p class="text-white/70 text-lg font-medium leading-relaxed max-w-md">
                        Verification successful! Now set your new secure password to regain access to your account.
                    </p>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12">
                <div class="form-glass w-full">
                    <div class="mb-8">
                        <h2 class="text-2xl font-black text-white tracking-tight">New Password</h2>
                        <p class="text-white/60 text-sm mt-1">Set a strong password for your account.</p>
                    </div>

                    <?php if(isset($error)): ?>
                        <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-2xl mb-6 text-xs font-bold flex items-center gap-3">
                            <i class="ph-bold ph-warning-circle text-lg"></i>
                            <p><?= $error ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="<?= URLROOT ?>/auth/update-password" method="POST" class="space-y-5">
                        <?php csrf_field(); ?>
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/50 uppercase tracking-[0.2em] ml-1">New Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required autofocus
                                    class="custom-input w-full px-5 py-4 rounded-2xl focus:outline-none text-sm font-bold text-slate-800 placeholder:text-slate-400"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePassword('password', 'eye-1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                                    <i id="eye-1" class="ph-bold ph-eye text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/50 uppercase tracking-[0.2em] ml-1">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="confirm_password" name="confirm_password" required 
                                    class="custom-input w-full px-5 py-4 rounded-2xl focus:outline-none text-sm font-bold text-slate-800 placeholder:text-slate-400"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePassword('confirm_password', 'eye-2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                                    <i id="eye-2" class="ph-bold ph-eye text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-primary hover:bg-primary-700 text-white font-black py-4 rounded-2xl transition-all shadow-xl shadow-primary/20 transform active:scale-[0.98] uppercase tracking-widest text-xs">
                                Update Password
                            </button>
                        </div>

                        <div class="text-center pt-4">
                            <a href="<?= URLROOT ?>/login" class="text-xs font-bold text-white/30 hover:text-white transition-colors flex items-center justify-center gap-2">
                                <i class="ph-bold ph-arrow-left"></i>
                                Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        function togglePassword(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.replace('ph-eye', 'ph-eye-closed');
            } else {
                input.type = 'password';
                eye.classList.replace('ph-eye-closed', 'ph-eye');
            }
        }
    </script>

</body>
</html>
