<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Altar Servers System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e63d4',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#f8fafc] font-sans text-slate-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col justify-between hidden md:flex shrink-0">
        <div>
            <div class="p-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-blue-100 shadow-lg text-white">
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
                <span class="inline-block bg-fuchsia-100 text-fuchsia-600 text-xs font-bold px-3 py-1 rounded-full">Admin</span>
            </div>

            <nav class="px-4 space-y-1">
                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>

                <a href="admin_attendance.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="font-medium text-sm">Attendance</span>
                </a>

                <a href="admin_schedules.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-sm">Schedules</span>
                </a>

                <a href="admin_announcements.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="font-medium text-sm">Announcements</span>
                </a>

                <a href="admin_reports.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-medium text-sm">Reports</span>
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
                    Admin Dashboard 
                    <span class="text-red-500 text-xl">🎯</span>
                </h2>
                <p class="text-slate-500 text-sm mt-1">Overview and quick stats</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-200">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium opacity-90">Total Servers</h3>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-4xl font-bold mb-1">24</span>
                        <span class="text-xs opacity-70">Active members</span>
                    </div>
                </div>

                <div class="bg-emerald-500 rounded-3xl p-6 text-white shadow-lg shadow-emerald-200">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium opacity-90">Present Today</h3>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-4xl font-bold mb-1">18</span>
                        <span class="text-xs opacity-70">Out of 24</span>
                    </div>
                </div>

                <div class="bg-purple-600 rounded-3xl p-6 text-white shadow-lg shadow-purple-200">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium opacity-90">Schedules</h3>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-4xl font-bold mb-1">12</span>
                        <span class="text-xs opacity-70">This month</span>
                    </div>
                </div>

                <div class="bg-orange-500 rounded-3xl p-6 text-white shadow-lg shadow-orange-200">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium opacity-90">Attendance</h3>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-4xl font-bold mb-1">92%</span>
                        <span class="text-xs opacity-70">Overall rate</span>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-700 mb-6 text-lg">Today's Attendance</h3>
                    
                    <div class="space-y-4">
                        
                        <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-sm">
                                    JD
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">John Doe</h4>
                                    <p class="text-slate-400 text-xs">7:45 AM</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600">Present</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-sm">
                                    JS
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Jane Smith</h4>
                                    <p class="text-slate-400 text-xs">7:50 AM</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600">Present</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-sm">
                                    MB
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Michael Brown</h4>
                                    <p class="text-slate-400 text-xs">-</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600">Absent</span>
                        </div>

                         <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-sm">
                                    SJ
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Sarah Johnson</h4>
                                    <p class="text-slate-400 text-xs">7:48 AM</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600">Present</span>
                        </div>
                        </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-700 mb-6 text-lg">Upcoming Schedules</h3>
                    
                    <div class="space-y-4">
                        
                        <div class="p-4 rounded-2xl border border-slate-100 hover:shadow-md transition-shadow bg-slate-50/50">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-slate-800">Sunday Mass</h4>
                                <span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-lg">4 servers</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-slate-500">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Feb 8, 2026
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    8:00 AM
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-100 hover:shadow-md transition-shadow bg-slate-50/50">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-slate-800">Family Mass</h4>
                                <span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-lg">3 servers</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-slate-500">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Feb 15, 2026
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    10:00 AM
                                </div>
                            </div>
                        </div>
                        </div>
                </div>

            </div>

        </div>
    </main>

    <div class="fixed bottom-6 right-6 z-40">
        <button class="bg-slate-900 text-white p-3 rounded-full shadow-lg hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>

</body>
</html>