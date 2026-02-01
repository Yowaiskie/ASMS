<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule - Altar Servers System</title>
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
                <span class="inline-block bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">User</span>
            </div>

            <nav class="px-4 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>

                <a href="attendance.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="font-medium text-sm">My Attendance</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
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
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">My Schedule</h2>
                    <p class="text-slate-500 text-sm mt-1">View your upcoming assignments</p>
                </div>
                
                <div class="bg-white p-1 rounded-xl shadow-sm border border-slate-200 flex">
                    <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium shadow-sm transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Calendar
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2 text-slate-500 hover:text-slate-800 rounded-lg text-sm font-medium transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        List
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-slate-800">February 2026</h3>
                    <div class="flex items-center gap-2">
                        <button class="p-2 hover:bg-slate-100 rounded-lg text-slate-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="p-2 hover:bg-slate-100 rounded-lg text-slate-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-7 mb-4 text-center">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Sun</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase">Mon</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase">Tue</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase">Wed</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase">Thu</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase">Fri</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase">Sat</div>
                </div>

                <div class="grid grid-cols-7 gap-4">
                    
                    <div class="h-28 rounded-xl bg-blue-50 border-2 border-blue-200 flex items-center justify-center cursor-pointer transition-all hover:shadow-md relative">
                        <span class="text-blue-600 font-bold text-lg">1</span>
                    </div>

                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">2</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">3</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">4</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">5</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">6</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">7</span></div>

                    <div class="h-28 rounded-xl bg-blue-600 shadow-lg shadow-blue-200 flex items-center justify-center cursor-pointer transition-all hover:bg-blue-700 hover:-translate-y-1">
                        <span class="text-white font-bold text-lg">8</span>
                    </div>

                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">9</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">10</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">11</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">12</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">13</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">14</span></div>

                    <div class="h-28 rounded-xl bg-blue-600 shadow-lg shadow-blue-200 flex items-center justify-center cursor-pointer transition-all hover:bg-blue-700 hover:-translate-y-1">
                        <span class="text-white font-bold text-lg">15</span>
                    </div>

                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">16</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">17</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">18</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">19</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">20</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">21</span></div>

                    <div class="h-28 rounded-xl bg-blue-600 shadow-lg shadow-blue-200 flex items-center justify-center cursor-pointer transition-all hover:bg-blue-700 hover:-translate-y-1">
                        <span class="text-white font-bold text-lg">22</span>
                    </div>

                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">23</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">24</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">25</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">26</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">27</span></div>
                    <div class="h-28 rounded-xl bg-white hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-all"><span class="text-slate-600 font-medium">28</span></div>

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