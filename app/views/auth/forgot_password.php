<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | ASMS</title>
    <link rel="icon" type="image/png" href="<?= URLROOT ?>/images/logo.png">
    
    <!-- CSS -->
    <link href="<?= URLROOT ?>/output.css" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-900 font-sans antialiased">

    <div class="min-h-screen flex items-center justify-center p-5 relative overflow-hidden bg-[#0f172a]"
         style="background-image: radial-gradient(at 0% 0%, rgba(163, 59, 57, 0.4) 0px, transparent 50%), radial-gradient(at 100% 100%, rgba(0, 89, 156, 0.3) 0px, transparent 50%), url('<?= URLROOT ?>/images/MAS.png'); background-size: cover; background-position: center;">
        
        <div class="bg-white/10 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] w-full max-w-[1000px] flex flex-col lg:flex-row overflow-hidden shadow-2xl animate-fade-in-up">
            
            <!-- Left Side: Visual Content -->
            <div class="hidden lg:flex flex-col justify-end p-16 lg:w-1/2 relative">
                <div class="relative z-10">
                    <div class="mb-8">
                        <img src="<?= URLROOT ?>/images/logo.png" alt="SHJP Logo" class="h-20 w-auto drop-shadow-2xl">
                    </div>
                    <h1 class="text-6xl font-black text-white leading-tight tracking-tighter mb-4">
                        RECOVER <br><span class="text-[#fca5a5]">YOUR ACCOUNT.</span>
                    </h1>
                    <p class="text-white/70 text-lg font-medium leading-relaxed max-w-md">
                        Please enter your registered email address to receive a 6-digit verification code.
                    </p>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12">
                <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-3xl p-10 w-full max-w-[420px]">
                    <div class="mb-8 text-left">
                        <h2 class="text-2xl font-black text-white tracking-tight">Forgot Password</h2>
                        <p class="text-white/60 text-sm mt-1">Enter your email to receive an OTP.</p>
                    </div>

                    <?php if(isset($error)): ?>
                        <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-2xl mb-6 text-xs font-bold flex items-center gap-3">
                            <i class="ph-bold ph-warning-circle text-lg"></i>
                            <p><?= $error ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="<?= URLROOT ?>/auth/send-otp" method="POST" class="space-y-5">
                        <?php csrf_field(); ?>
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/50 uppercase tracking-[0.2em] ml-1">Email Address</label>
                            <input type="email" name="email" required 
                                class="w-full px-5 py-4 rounded-2xl bg-white/95 focus:outline-none focus:ring-4 focus:ring-[#a33b39]/20 focus:border-[#a33b39] border border-transparent transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400"
                                placeholder="example@email.com">
                        </div>

                        <div class="pt-2 text-left">
                            <button type="submit" class="w-full bg-[#a33b39] hover:bg-[#8a2f2d] text-white font-black py-4 rounded-2xl transition-all shadow-xl shadow-[#a33b39]/20 transform active:scale-[0.98] uppercase tracking-widest text-xs">
                                Send OTP Code
                            </button>
                        </div>

                        <div class="text-center pt-4 text-left">
                            <a href="<?= URLROOT ?>/login" class="text-xs font-bold text-white/40 hover:text-white transition-colors flex items-center justify-center gap-2">
                                <i class="ph-bold ph-arrow-left"></i>
                                Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="fixed bottom-8 text-center w-full">
            <p class="text-white/20 text-[10px] font-black uppercase tracking-[0.4em]">
                SHJP MBS &bull; ASMS &bull; <?= date('Y') ?>
            </p>
        </div>
    </div>

</body>
</html>
