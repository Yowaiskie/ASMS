<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Admin</title>
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
                <a href="admin_dashboard.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
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
                <h2 class="text-2xl font-bold text-slate-800">Attendance Management</h2>
                <p class="text-slate-500 text-sm mt-1">Mark and manage altar server attendance</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
                <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Select Activity</label>
                        <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer">
                            <option>Sunday Mass</option>
                            <option>Formation Training</option>
                            <option>Special Event</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Date</label>
                        <input type="date" value="2026-02-01" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Time</label>
                        <input type="time" value="08:00" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-green-50/50 border border-green-200 p-6 rounded-2xl">
                    <p class="text-xs font-medium text-slate-500 mb-1">Present</p>
                    <p class="text-3xl font-bold text-green-600">5</p>
                </div>
                <div class="bg-yellow-50/50 border border-yellow-200 p-6 rounded-2xl">
                    <p class="text-xs font-medium text-slate-500 mb-1">Late</p>
                    <p class="text-3xl font-bold text-yellow-600">1</p>
                </div>
                <div class="bg-red-50/50 border border-red-200 p-6 rounded-2xl">
                    <p class="text-xs font-medium text-slate-500 mb-1">Absent</p>
                    <p class="text-3xl font-bold text-red-600">2</p>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-slate-700 mb-4">Mark Attendance</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">

                    <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-green-500 transition-all">
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-800 text-lg">John Doe</h4>
                            <p class="text-slate-400 text-xs">Altar Server 1</p>
                        </div>
                        
                        <div class="flex gap-2">
                            <button class="flex-1 py-2 rounded-lg text-sm font-bold bg-green-500 text-white shadow-md shadow-green-200 transition-transform active:scale-95">Present</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">Late</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">Absent</button>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-green-500 transition-all">
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-800 text-lg">Jane Smith</h4>
                            <p class="text-slate-400 text-xs">Altar Server 2</p>
                        </div>
                        
                        <div class="flex gap-2">
                            <button class="flex-1 py-2 rounded-lg text-sm font-bold bg-green-500 text-white shadow-md shadow-green-200 transition-transform active:scale-95">Present</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">Late</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">Absent</button>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-red-500 transition-all">
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-800 text-lg">Michael Brown</h4>
                            <p class="text-slate-400 text-xs">Altar Server 1</p>
                        </div>
                        
                        <div class="flex gap-2">
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">Present</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">Late</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-bold bg-red-500 text-white shadow-md shadow-red-200 transition-transform active:scale-95">Absent</button>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-green-500 transition-all">
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-800 text-lg">Sarah Johnson</h4>
                            <p class="text-slate-400 text-xs">Altar Server 2</p>
                        </div>
                        
                        <div class="flex gap-2">
                            <button class="flex-1 py-2 rounded-lg text-sm font-bold bg-green-500 text-white shadow-md shadow-green-200 transition-transform active:scale-95">Present</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">Late</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">Absent</button>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 transition-all">
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-800 text-lg">David Wilson</h4>
                            <p class="text-slate-400 text-xs">Altar Server 3</p>
                        </div>
                        
                        <div class="flex gap-2">
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-green-50 hover:text-green-600 hover:border-green-200 transition-colors">Present</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-yellow-50 hover:text-yellow-600 hover:border-yellow-200 transition-colors">Late</button>
                            <button class="flex-1 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors">Absent</button>
                        </div>
                    </div>

                    </div>
            </div>

        </div>
    </main>
    
    <div class="fixed bottom-6 right-6 z-40">
        <button class="bg-primary hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow-lg hover:scale-105 transition-all flex items-center gap-2 font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            Save Changes
        </button>
    </div>

</body>
</html>