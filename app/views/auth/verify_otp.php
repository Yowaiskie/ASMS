<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | ASMS</title>
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
                        SECURE <br><span class="text-[#fca5a5]">ACCESS.</span>
                    </h1>
                    <p class="text-white/70 text-lg font-medium leading-relaxed max-w-md">
                        Please enter the 6-digit code sent to <b><?= h($_SESSION['reset_email'] ?? '') ?></b> to verify your identity.
                    </p>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12">
                <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-3xl p-10 w-full max-w-[420px] text-center">
                    <div class="mb-8 text-left">
                        <h2 class="text-2xl font-black text-white tracking-tight">Verify Code</h2>
                        <p class="text-white/60 text-sm mt-1">Enter the 6-digit OTP from your email.</p>
                    </div>

                    <?php if(isset($error)): ?>
                        <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-2xl mb-6 text-xs font-bold flex items-center gap-3">
                            <i class="ph-bold ph-warning-circle text-lg"></i>
                            <p><?= $error ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($_SESSION['msg_success'])): ?>
                         <div class="bg-emerald-500/20 border border-emerald-500/50 text-emerald-200 px-4 py-3 rounded-2xl mb-6 text-[10px] font-bold flex items-center gap-3">
                            <i class="ph-bold ph-check-circle text-lg"></i>
                            <p><?= $_SESSION['msg_success']; unset($_SESSION['msg_success']); ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="<?= URLROOT ?>/auth/verify-otp" method="POST" class="space-y-6">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="email" value="<?= h($_SESSION['reset_email'] ?? '') ?>">
                        
                        <div class="space-y-2 text-left">
                            <label class="text-[10px] font-black text-white/50 uppercase tracking-[0.2em] ml-1">6-Digit OTP</label>
                            <input type="text" name="otp" required maxlength="6" autofocus
                                class="w-full px-5 py-5 rounded-2xl bg-white/95 focus:outline-none text-center text-3xl font-black tracking-[0.6em] text-slate-800 placeholder:text-slate-200"
                                placeholder="000000">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-[#a33b39] hover:bg-[#8a2f2d] text-white font-black py-4 rounded-2xl transition-all shadow-xl shadow-[#a33b39]/20 transform active:scale-[0.98] uppercase tracking-widest text-xs">
                                Verify Code
                            </button>
                        </div>

                        <div class="flex flex-col gap-4 mt-6">
                            <div class="text-xs font-bold text-white/40">
                                Didn't receive a code? 
                                <span id="timer-container" class="text-[#fca5a5]">
                                    Resend in <span id="timer">60</span>s
                                </span>
                                <a href="<?= URLROOT ?>/forgot-password" id="resend-link" class="text-[#fca5a5] hover:text-[#f87171] hidden">
                                    Resend Code
                                </a>
                            </div>

                            <a href="<?= URLROOT ?>/login" class="text-xs font-bold text-white/30 hover:text-white transition-colors flex items-center justify-center gap-2">
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

    <script>
        let timeLeft = 60;
        const timerEl = document.getElementById('timer');
        const timerContainer = document.getElementById('timer-container');
        const resendLink = document.getElementById('resend-link');

        const countdown = setInterval(() => {
            timeLeft--;
            if (timerEl) timerEl.innerText = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(countdown);
                if (timerContainer) timerContainer.classList.add('hidden');
                if (resendLink) resendLink.classList.remove('hidden');
            }
        }, 1000);
    </script>

</body>
</html>
