<?php
require_once __DIR__ . '/../app/config/config.php';
header('Location: ' . URLROOT . '/logs');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Super Admin</title>
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
                <span class="inline-block bg-pink-100 text-pink-600 text-xs font-bold px-3 py-1 rounded-full">Super Admin</span>
            </div>

            <nav class="px-4 space-y-1">
                <a href="super_admin_dashboard.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
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

                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-medium text-sm">Logs</span>
                </a>

                <a href="super_reports.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
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
            
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Audit Logs</h2>
                    <p class="text-slate-500 text-sm mt-1">Monitor system activities and user actions</p>
                </div>
                
                <button class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl shadow-lg transition-all flex items-center gap-2 font-semibold text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Log
                </button>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h3 class="font-bold text-slate-700">Filter Logs</h3>
                </div>
                
                <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Search</label>
                        <input type="text" placeholder="Search user, IP, or description..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Role</label>
                        <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer text-sm">
                            <option>All Roles</option>
                            <option>Super Admin</option>
                            <option>Admin</option>
                            <option>User</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Action Type</label>
                        <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer text-sm">
                            <option>All Actions</option>
                            <option>Login</option>
                            <option>Create</option>
                            <option>Update</option>
                            <option>Delete</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Timestamp</th>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Action</th>
                                <th class="px-6 py-4">Module</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm">
                            
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">Feb 01, 2026<br>09:45:12 AM</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">JD</div>
                                        <div>
                                            <p class="font-bold text-slate-700">John Doe</p>
                                            <p class="text-[10px] text-slate-400">Admin</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">Create</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">Schedules</td>
                                <td class="px-6 py-4 text-slate-500">Created new schedule "Sunday Mass" (ID: 84)</td>
                                <td class="px-6 py-4 text-slate-400 font-mono text-xs">192.168.1.45</td>
                            </tr>

                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">Feb 01, 2026<br>08:30:00 AM</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-pink-500 text-white flex items-center justify-center font-bold text-xs">SA</div>
                                        <div>
                                            <p class="font-bold text-slate-700">Super Admin</p>
                                            <p class="text-[10px] text-slate-400">Super Admin</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">Login</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">Auth</td>
                                <td class="px-6 py-4 text-slate-500">User logged in successfully</td>
                                <td class="px-6 py-4 text-slate-400 font-mono text-xs">10.0.0.12</td>
                            </tr>

                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">Jan 31, 2026<br>04:15:22 PM</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center font-bold text-xs">JS</div>
                                        <div>
                                            <p class="font-bold text-slate-700">Jane Smith</p>
                                            <p class="text-[10px] text-slate-400">Admin</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">Delete</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">Announcements</td>
                                <td class="px-6 py-4 text-slate-500">Deleted announcement "Meeting Cancelled"</td>
                                <td class="px-6 py-4 text-slate-400 font-mono text-xs">192.168.1.50</td>
                            </tr>

                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">Jan 31, 2026<br>02:10:05 PM</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">JD</div>
                                        <div>
                                            <p class="font-bold text-slate-700">John Doe</p>
                                            <p class="text-[10px] text-slate-400">Admin</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700">Update</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">Users</td>
                                <td class="px-6 py-4 text-slate-500">Updated profile information for User #22</td>
                                <td class="px-6 py-4 text-slate-400 font-mono text-xs">192.168.1.45</td>
                            </tr>

                             <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">Jan 30, 2026<br>11:59:00 PM</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs">?</div>
                                        <div>
                                            <p class="font-bold text-slate-700">Unknown</p>
                                            <p class="text-[10px] text-slate-400">Guest</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-800 text-white">Warning</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">Security</td>
                                <td class="px-6 py-4 text-slate-500">Failed login attempt (Invalid credentials)</td>
                                <td class="px-6 py-4 text-slate-400 font-mono text-xs">45.22.19.112</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t border-slate-50 flex items-center justify-between">
                    <p class="text-xs text-slate-500">Showing 1 to 5 of 142 entries</p>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 text-xs border border-slate-200 rounded-lg hover:bg-slate-50">Previous</button>
                        <button class="px-3 py-1 text-xs bg-primary text-white rounded-lg">1</button>
                        <button class="px-3 py-1 text-xs border border-slate-200 rounded-lg hover:bg-slate-50">2</button>
                        <button class="px-3 py-1 text-xs border border-slate-200 rounded-lg hover:bg-slate-50">3</button>
                        <button class="px-3 py-1 text-xs border border-slate-200 rounded-lg hover:bg-slate-50">Next</button>
                    </div>
                </div>

            </div>

        </div>
    </main>

</body>
</html>