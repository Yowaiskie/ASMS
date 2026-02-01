<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ASMS</title>
    <link rel="icon" type="image/png" href="<?= URLROOT ?>/images/logo.png">
    <link href="<?= URLROOT ?>/output.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600 tracking-wider">ASMS</h1>
            <p class="text-slate-500 text-sm mt-2">Create your account</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="<?= URLROOT ?>/auth/register" method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400">
                        <i class="ph ph-user text-xl"></i>
                    </span>
                    <input type="text" name="username" required 
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                        placeholder="Choose a username">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400">
                        <i class="ph ph-lock-key text-xl"></i>
                    </span>
                    <input type="password" name="password" required 
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                        placeholder="Create a password">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400">
                        <i class="ph ph-lock-key text-xl"></i>
                    </span>
                    <input type="password" name="confirm_password" required 
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                        placeholder="Confirm password">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors shadow-lg shadow-blue-200">
                Register
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-500">
            Already have an account? 
            <a href="<?= URLROOT ?>/login" class="text-blue-600 hover:underline font-medium">Sign In</a>
        </div>
    </div>

</body>
</html>