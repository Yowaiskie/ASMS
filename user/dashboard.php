<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Altar Servers System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e63d4', // Yung blue sa login at dashboard mo
                        success: '#dcfce7', // Green bg for present
                        successText: '#166534',
                        warning: '#fef9c3', // Yellow bg for late
                        warningText: '#854d0e',
                        danger: '#fee2e2', // Red bg for absent
                        dangerText: '#991b1b',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#f8fafc] font-sans text-slate-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col justify-between hidden md:flex">
        <div>
            <div class="p-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-blue-100 shadow-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 leading-tight">Altar Servers</h1>
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">Management System</p>
                </div>
            </div>

            <div class="px-6 mb-6">
                <span class="inline-block bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">User</span>
            </div>

            <nav class="px-4 space-y-1">
                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="font-medium text-sm">My Attendance</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-sm">Schedule</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="font-medium text-sm">Announcements</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium text-sm">Profile</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-50">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium text-sm">Logout</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <div class="p-8">
            
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    Welcome Back! 
                    <span class="text-2xl">👋</span>
                </h2>
                <p class="text-slate-500 text-sm mt-1">Here's your schedule and updates</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-700">Next Schedule</h3>
                    </div>

                    <div class="bg-blue-600 rounded-2xl p-6 text-white relative overflow-hidden flex-1 flex flex-col justify-center">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-white opacity-5 rounded-full"></div>

                        <div class="flex justify-between items-start mb-2">
                            <p class="text-blue-100 text-xs font-medium">Sunday, February 8, 2026</p>
                            <span class="bg-blue-500/50 backdrop-blur-sm px-3 py-1 rounded-lg text-xs font-medium border border-blue-400/30">8:00 AM</span>
                        </div>
                        
                        <h2 class="text-2xl font-bold mb-4">Sunday Mass</h2>

                        <div class="flex items-center gap-6 text-sm text-blue-100">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Main Church</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Altar Server 1</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-700">Attendance</h3>
                    </div>

                    <div class="flex flex-col items-center mb-6">
                        <div class="relative w-32 h-32 flex items-center justify-center">
                            <svg class="transform -rotate-90 w-32 h-32">
                                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-100" />
                                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="351.86" stroke-dashoffset="49.26" class="text-blue-600" stroke-linecap="round" />
                            </svg>
                            <div class="absolute flex flex-col items-center">
                                <span class="text-3xl font-bold text-slate-800">86%</span>
                                <span class="text-[10px] text-slate-400">Attendance Rate</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-green-50 px-4 py-2 rounded-xl">
                            <span class="text-xs font-semibold text-green-800">Present</span>
                            <span class="text-xs font-bold text-green-800">18</span>
                        </div>
                        <div class="flex justify-between items-center bg-yellow-50 px-4 py-2 rounded-xl">
                            <span class="text-xs font-semibold text-yellow-800">Late</span>
                            <span class="text-xs font-bold text-yellow-800">1</span>
                        </div>
                        <div class="flex justify-between items-center bg-red-50 px-4 py-2 rounded-xl">
                            <span class="text-xs font-semibold text-red-800">Absent</span>
                            <span class="text-xs font-bold text-red-800">2</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-700">Recent Announcements</h3>
                </div>

                <div class="space-y-4">
                    
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex justify-between items-start group hover:bg-slate-100 transition-colors cursor-pointer">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm mb-1">Training Session Next Week</h4>
                            <p class="text-slate-500 text-xs line-clamp-1">Mandatory training for all altar servers on February 12 regarding new protocols.</p>
                        </div>
                        <span class="text-[10px] text-slate-400 whitespace-nowrap ml-4">Feb 5, 2026</span>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex justify-between items-start group hover:bg-slate-100 transition-colors cursor-pointer">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm mb-1">Easter Schedule Posted</h4>
                            <p class="text-slate-500 text-xs line-clamp-1">The Easter week schedule has been posted. Please check your assignments immediately.</p>
                        </div>
                        <span class="text-[10px] text-slate-400 whitespace-nowrap ml-4">Feb 2, 2026</span>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <div class="fixed bottom-6 right-6">
        <button class="bg-slate-900 text-white p-3 rounded-full shadow-lg hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>

</body>
</html>