<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Altar Servers System</title>
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

                <a href="schedule.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-sm">Schedule</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
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
                <h2 class="text-2xl font-bold text-slate-800">Announcements</h2>
                <p class="text-slate-500 text-sm mt-1">Stay updated with parish news and reminders</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 min-h-[500px]">
                
                <div class="mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">All Announcements</h3>
                </div>

                <div class="space-y-4">
                    
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-600">Training</span>
                            <span class="text-slate-400 text-xs">Feb 5, 2026</span>
                        </div>
                        
                        <h4 class="font-bold text-slate-800 text-lg mb-2">Training Session Next Week</h4>
                        
                        <p class="text-slate-500 text-sm leading-relaxed mb-4 line-clamp-2">
                            Mandatory training for all altar servers on February 12, 2026 at 3:00 PM in the parish hall. We will cover proper liturgical procedures and new guidelines. Attendance is required for all active servers.
                        </p>

                        <div class="flex items-center justify-between border-t border-slate-50 pt-4 mt-2">
                            <p class="text-xs text-slate-400 font-medium">Posted by Fr. John Smith</p>
                            
                            <button 
                                onclick="openModal(this)"
                                data-category="Training"
                                data-color="purple" 
                                data-date="Feb 5, 2026"
                                data-title="Training Session Next Week"
                                data-content="Mandatory training for all altar servers on February 12, 2026 at 3:00 PM in the parish hall. We will cover proper liturgical procedures and new guidelines. Attendance is required for all active servers. Please bring your own notebook and pen."
                                data-author="Fr. John Smith"
                                class="text-blue-600 text-sm font-semibold hover:text-blue-800 flex items-center gap-1"
                            >
                                Read more 
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-100 rounded-2xl p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-600">Schedule</span>
                            <span class="text-slate-400 text-xs">Feb 2, 2026</span>
                        </div>
                        
                        <h4 class="font-bold text-slate-800 text-lg mb-2">Easter Schedule Posted</h4>
                        
                        <p class="text-slate-500 text-sm leading-relaxed mb-4 line-clamp-2">
                            The Easter week schedule has been posted. Please check your assignments and confirm your availability. We need all servers to be present during Holy Week services. If you have conflicts, please inform us immediately.
                        </p>

                        <div class="flex items-center justify-between border-t border-slate-50 pt-4 mt-2">
                            <p class="text-xs text-slate-400 font-medium">Posted by Admin</p>
                            
                            <button 
                                onclick="openModal(this)"
                                data-category="Schedule"
                                data-color="blue"
                                data-date="Feb 2, 2026"
                                data-title="Easter Schedule Posted"
                                data-content="The Easter week schedule has been posted. Please check your assignments and confirm your availability. We need all servers to be present during Holy Week services. If you have conflicts, please inform us immediately."
                                data-author="Admin"
                                class="text-blue-600 text-sm font-semibold hover:text-blue-800 flex items-center gap-1"
                            >
                                Read more 
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </button>
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

    <div id="announcementModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <div class="flex justify-between items-center">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Announcement Details</h3>
                                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <span id="modalBadge" class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-600">Training</span>
                                    <span id="modalDate" class="text-slate-400 text-xs">Feb 5, 2026</span>
                                </div>
                                <h4 id="modalHeading" class="font-bold text-slate-800 text-xl mb-3">Training Session Next Week</h4>
                                <p id="modalContent" class="text-sm text-slate-500 leading-relaxed">
                                    </p>
                                <p class="text-xs text-slate-400 font-medium mt-4 pt-4 border-t border-slate-50">
                                    Posted by <span id="modalAuthor">Fr. John Smith</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" onclick="closeModal()" class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-3 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:w-full transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(button) {
            // Get data from clicked button attributes
            const category = button.getAttribute('data-category');
            const color = button.getAttribute('data-color'); // 'purple' or 'blue'
            const date = button.getAttribute('data-date');
            const title = button.getAttribute('data-title');
            const content = button.getAttribute('data-content');
            const author = button.getAttribute('data-author');

            // Update Modal Content
            document.getElementById('modalDate').innerText = date;
            document.getElementById('modalHeading').innerText = title;
            document.getElementById('modalContent').innerText = content;
            document.getElementById('modalAuthor').innerText = author;

            // Update Badge Logic
            const badge = document.getElementById('modalBadge');
            badge.innerText = category;
            
            // Remove old colors
            badge.className = 'px-3 py-1 rounded-full text-xs font-semibold';
            // Add new colors
            if(color === 'purple') {
                badge.classList.add('bg-purple-100', 'text-purple-600');
            } else {
                badge.classList.add('bg-blue-100', 'text-blue-600');
            }

            // Show Modal
            document.getElementById('announcementModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('announcementModal').classList.add('hidden');
        }
    </script>

</body>
</html>