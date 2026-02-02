<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - ASMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 h-screen flex items-center justify-center font-sans p-6">
    <div class="max-w-md w-full text-center">
        <div class="mb-8 flex justify-center">
            <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-3xl flex items-center justify-center shadow-xl shadow-amber-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                </svg>
            </div>
        </div>
        
        <h1 class="text-3xl font-bold text-slate-900 mb-4">Under Maintenance</h1>
        <p class="text-slate-500 mb-8 leading-relaxed">
            We are currently performing some system updates to improve your experience. 
            The system will be back online shortly.
        </p>
        
        <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider mb-2">Notice</p>
            <p class="text-sm text-slate-600">Only Superadmins can access the system at this time.</p>
        </div>

        <div class="mt-8">
            <a href="<?= URLROOT ?>/logout" class="text-primary hover:underline font-medium text-sm">Go back to login</a>
        </div>
    </div>
</body>
</html>