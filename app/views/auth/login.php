<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ASMS</title>
    <link rel="icon" type="image/png" href="<?= URLROOT ?>/images/logo.png">
    <link href="<?= URLROOT ?>/output.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <img src="<?= URLROOT ?>/images/logo.png" alt="Ministry Logo" class="h-24 w-auto">
            </div>
            <h1 class="text-2xl font-bold text-blue-600 tracking-wider">ASMS</h1>
            <p class="text-slate-500 text-sm mt-1">Altar Servers Management System</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['registered'])): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                Registration successful! Please login.
            </div>
        <?php endif; ?>

        <form action="<?= URLROOT ?>/auth/login" method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400">
                        <i class="ph ph-user text-xl"></i>
                    </span>
                    <input type="text" name="username" required 
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                        placeholder="Enter your username">
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
                        placeholder="Enter your password">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors shadow-lg shadow-blue-200">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-500">
            Don't have an account? 
            <a href="<?= URLROOT ?>/register" class="text-blue-600 hover:underline font-medium">Create one</a>
        </div>
    </div>

</body>
</html>