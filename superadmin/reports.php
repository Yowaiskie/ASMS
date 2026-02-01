<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Reports - Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <span class="inline-block bg-pink-100 text-pink-600 text-xs font-bold px-3 py-1 rounded-full">Super Admin</span>
            </div>

            <nav class="px-4 space-y-1">
                <a href="super_admin_dashboard.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>

                <a href="super_users.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="font-medium text-sm">Users</span>
                </a>

                <a href="super_settings.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="font-medium text-sm">Settings</span>
                </a>

                <a href="super_logs.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-medium text-sm">Logs</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
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
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">System Analytics</h2>
                    <p class="text-slate-500 text-sm mt-1">Global system performance and usage statistics</p>
                </div>
                
                <div class="flex gap-3">
                    <button class="bg-white border border-slate-200 text-slate-600 px-4 py-2.5 rounded-xl shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2 text-sm font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Last 30 Days
                    </button>
                    <button class="bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center gap-2 text-sm font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Report
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Users</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1">128</h3>
                        </div>
                        <span class="bg-green-100 text-green-600 text-xs font-bold px-2 py-1 rounded-lg">+12%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: 70%"></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">System Health</p>
                            <h3 class="text-3xl font-bold text-emerald-600 mt-1">99.9%</h3>
                        </div>
                        <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400">All systems operational</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Database Size</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1">45 MB</h3>
                        </div>
                        <span class="bg-slate-100 text-slate-500 text-xs font-bold px-2 py-1 rounded-lg">Safe</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                        <div class="bg-purple-600 h-1.5 rounded-full" style="width: 15%"></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Login Failures</p>
                            <h3 class="text-3xl font-bold text-red-500 mt-1">3</h3>
                        </div>
                        <span class="bg-red-50 text-red-500 text-xs font-bold px-2 py-1 rounded-lg">Today</span>
                    </div>
                    <p class="text-xs text-slate-400">Requires attention</p>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-700 mb-6">User Registration Trends</h3>
                    <div class="h-72">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-700 mb-6">Activity By Module</h3>
                    <div class="h-72 flex justify-center">
                        <canvas id="moduleActivityChart"></canvas>
                    </div>
                </div>

            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-700">Admin Activity Summary</h3>
                    <button class="text-xs font-bold text-blue-600 hover:text-blue-700">View All</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 text-xs font-bold text-slate-500 uppercase">
                            <tr>
                                <th class="px-6 py-4">Admin Name</th>
                                <th class="px-6 py-4">Schedules Created</th>
                                <th class="px-6 py-4">Attendance Marked</th>
                                <th class="px-6 py-4">Last Active</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm">
                            
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-700">John Doe</td>
                                <td class="px-6 py-4 text-slate-600">12</td>
                                <td class="px-6 py-4 text-slate-600">45</td>
                                <td class="px-6 py-4 text-slate-400 text-xs">2 mins ago</td>
                                <td class="px-6 py-4"><span class="w-2 h-2 rounded-full bg-green-500 inline-block mr-2"></span> Online</td>
                            </tr>

                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-700">Jane Smith</td>
                                <td class="px-6 py-4 text-slate-600">8</td>
                                <td class="px-6 py-4 text-slate-600">32</td>
                                <td class="px-6 py-4 text-slate-400 text-xs">1 hour ago</td>
                                <td class="px-6 py-4"><span class="w-2 h-2 rounded-full bg-slate-300 inline-block mr-2"></span> Offline</td>
                            </tr>

                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-700">Michael Brown</td>
                                <td class="px-6 py-4 text-slate-600">2</td>
                                <td class="px-6 py-4 text-slate-600">10</td>
                                <td class="px-6 py-4 text-slate-400 text-xs">3 days ago</td>
                                <td class="px-6 py-4"><span class="w-2 h-2 rounded-full bg-slate-300 inline-block mr-2"></span> Offline</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        // User Growth Chart
        const ctxGrowth = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(ctxGrowth, {
            type: 'line',
            data: {
                labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
                datasets: [{
                    label: 'New Users',
                    data: [5, 8, 12, 15, 20, 24],
                    borderColor: '#1e63d4',
                    backgroundColor: 'rgba(30, 99, 212, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart',
                    delay: (context) => context.dataIndex * 150
                },
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        grid: { 
                            color: '#f1f5f9',
                            drawBorder: false
                        } 
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // Module Activity Chart
        const ctxModule = document.getElementById('moduleActivityChart').getContext('2d');
        new Chart(ctxModule, {
            type: 'polarArea',
            data: {
                labels: ['Attendance', 'Schedules', 'Announcements', 'Users'],
                datasets: [{
                    data: [45, 30, 15, 10],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.7)', // Emerald
                        'rgba(59, 130, 246, 0.7)', // Blue
                        'rgba(245, 158, 11, 0.7)', // Amber
                        'rgba(236, 72, 153, 0.7)'  // Pink
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 2500,
                    easing: 'easeOutElastic'
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>

</body>
</html>