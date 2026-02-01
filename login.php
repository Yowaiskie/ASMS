<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Altar Servers System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4 font-sans">

    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        
        <div class="pt-8 pb-6 px-8 flex flex-col items-center">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200 mb-4 transform rotate-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white -rotate-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 text-center">Altar Servers</h2>
            <p class="text-sm text-gray-500 font-medium text-center mt-1">Management System</p>
        </div>

        <div class="p-8 pt-0">
            <form action="login.php" method="POST" class="space-y-5">
                
                <div class="space-y-1">
                    <label for="username" class="text-sm font-semibold text-gray-700 block">Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        required 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 text-gray-800"
                        placeholder="Enter username"
                    >
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-sm font-semibold text-gray-700 block">Password</label>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 text-gray-800"
                        placeholder="••••••••"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform active:scale-[0.98] flex items-center justify-center gap-2"
                >
                    <span>Sign In</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>

            </form>
        </div>
        
        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400 font-medium">© 2026 Church Ministry</p>
        </div>

    </div>

</body>
</html>