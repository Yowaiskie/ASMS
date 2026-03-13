<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success | ASMS</title>
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
        
        <div class="bg-white/10 backdrop-blur-xl border border-white/10 rounded-[2rem] p-10 md:p-16 w-full max-text-center max-w-[400px] text-center shadow-2xl animate-fade-in-up">
            <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-emerald-500/20">
                <i class="ph-bold ph-check text-3xl"></i>
            </div>
            
            <h2 class="text-2xl font-black text-white tracking-tight mb-2">Password Reset</h2>
            <p class="text-white/60 text-sm mb-12">Your account is now secure.</p>

            <div class="pt-8 border-t border-white/5">
                <p class="text-[10px] font-bold text-white/30 uppercase tracking-[0.2em]">
                    Redirecting to login in <span id="countdown" class="text-white/60">3</span>...
                </p>
            </div>
        </div>

        <div class="fixed bottom-8 text-center w-full">
            <p class="text-white/20 text-[10px] font-black uppercase tracking-[0.4em]">
                SHJP MBS &bull; ASMS &bull; <?= date('Y') ?>
            </p>
        </div>
    </div>

    <script>
        let timeLeft = 3;
        const countdownEl = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            timeLeft--;
            if (countdownEl) countdownEl.innerText = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(timer);
                window.location.href = '<?= URLROOT ?>/login';
            }
        }, 1000);
    </script>

</body>
</html>
