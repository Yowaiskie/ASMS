<div class="flex items-end justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Schedule Management</h2>
        <p class="text-slate-500 text-sm mt-1">Manage mass schedules and assignments</p>
    </div>
    
    <div class="flex gap-2">
        <button onclick="toggleSelectionMode()" id="selectModeBtn" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 p-2.5 rounded-xl shadow-sm transition-all" title="Select Multiple">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>

        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Import CSV
        </button>

        <button onclick="generateSundays()" data-loading class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Auto-Fill
        </button>
        
        <button onclick="openModal('add')" class="bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center gap-2 font-semibold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Schedule
        </button>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Bulk Import Schedules</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>

        <form action="<?= URLROOT ?>/schedules/import" method="POST" enctype="multipart/form-data" id="importForm">
            <?php csrf_field(); ?>

            <div class="mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-[10px] text-slate-600">
                <strong class="block mb-2 text-slate-700 uppercase tracking-widest text-[9px]">Required CSV Format (In Order):</strong>
                <code class="block bg-white p-2 rounded border border-slate-200 leading-relaxed break-all">
                    Date (YYYY-MM-DD), Time (HH:MM), Mass Type, Event Name (Optional)
                </code>
                <p class="mt-2 text-slate-400 italic font-medium">* Date must be YYYY-MM-DD (e.g., 2026-02-14)</p>
            </div>

            <div id="dropZone" class="border-2 border-dashed border-slate-200 rounded-3xl p-10 flex flex-col items-center justify-center gap-4 hover:border-blue-400 hover:bg-blue-50 transition-all cursor-pointer group text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-700">Drop CSV file here</p>
                    <p class="text-xs text-slate-400 mt-1">or click to browse</p>
                </div>
                <input type="file" name="csv_file" id="fileInput" class="hidden" accept=".csv">
            </div>
            
            <div id="fileInfo" class="mt-4 p-4 bg-blue-50 rounded-2xl border border-blue-100 hidden">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span id="fileName" class="text-xs font-bold text-blue-800 truncate">file.csv</span>
                </div>
            </div>

            <button type="submit" id="submitImport" disabled class="w-full mt-6 py-4 bg-slate-100 text-slate-400 rounded-2xl font-bold text-sm transition-all">
                Upload and Import
            </button>
        </form>
    </div>
</div>

<!-- Calendar View -->
<div id="calendar-view" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 relative overflow-hidden">
    
    <!-- Selection Bar -->
    <div id="selectionBar" class="hidden absolute top-0 left-0 right-0 z-10 bg-blue-600 text-white p-4 flex justify-between items-center animate-fade-in-up">
        <div class="flex items-center gap-3">
            <span class="font-bold text-sm" id="selectedCount">0 Selected</span>
            <div class="h-4 w-px bg-blue-400"></div>
            <button type="button" onclick="selectAllCalendar(false)" class="text-xs hover:underline">Clear</button>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="submitBulk('edit')" class="bg-white text-blue-600 hover:bg-blue-50 px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
                Edit Status
            </button>
            <button type="button" onclick="submitBulk('delete')" class="bg-red-500 text-white hover:bg-red-600 px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
                Delete
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between mb-8 transition-all" id="calendarHeader">
        <h3 id="currentMonth" class="font-bold text-2xl text-slate-800">Month Year</h3>
        <div class="flex gap-2">
            <button onclick="changeMonth(-1)" class="p-2 hover:bg-slate-50 rounded-xl text-slate-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button onclick="changeMonth(1)" class="p-2 hover:bg-slate-50 rounded-xl text-slate-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>
    
    <div class="grid grid-cols-7 gap-4 mb-4 text-center">
        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sun</div>
        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mon</div>
        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tue</div>
        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Wed</div>
        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Thu</div>
        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Fri</div>
        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sat</div>
    </div>
    
    <div id="calendarGrid" class="grid grid-cols-7 gap-4 auto-rows-fr">
        <!-- Calendar Cells -->
    </div>

    <!-- Legend -->
    <div class="mt-6 flex flex-wrap gap-4 border-t border-slate-100 pt-4">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span><span class="text-xs text-slate-500 font-medium">Regular Mass</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-teal-500"></span><span class="text-xs text-slate-500 font-medium">Anticipated</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-purple-500"></span><span class="text-xs text-slate-500 font-medium">Funeral</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-yellow-500"></span><span class="text-xs text-slate-500 font-medium">Wedding</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-sky-500"></span><span class="text-xs text-slate-500 font-medium">Baptism</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-indigo-500"></span><span class="text-xs text-slate-500 font-medium">Special Event</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-gray-500"></span><span class="text-xs text-slate-500 font-medium">Meeting</span></div>
    </div>
</div>

<!-- Forms for Bulk Action -->
<form action="<?= URLROOT ?>/schedules/bulk-action" method="POST" id="bulkForm" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="action" id="bulkAction" value="">
    <input type="hidden" name="ids" id="bulkIds" value="">
</form>

<!-- Add/Edit Modal -->
<div id="scheduleModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-6 transform scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto" id="modalContent">
        <div class="flex justify-between items-center mb-5">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800">Add Schedule</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
        <form action="<?= URLROOT ?>/schedules/store" method="POST" id="scheduleForm">
            <?php csrf_field(); ?><input type="hidden" name="id" id="scheduleId">
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Activity / Mass Type</label>
                        <select name="mass_type" id="mass_type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Sunday Mass">Sunday Mass</option>
                            <option value="Anticipated Mass">Anticipated Mass</option>
                            <option value="Weekday Mass">Weekday Mass</option>
                            <option value="Wedding">Wedding</option>
                            <option value="Funeral">Funeral</option>
                            <option value="Baptism">Baptism</option>
                            <option value="Special Event">Special Event</option>
                            <option value="Meeting">Meeting</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Status</label>
                        <select name="status" id="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><option value="Confirmed">Confirmed</option><option value="Pending">Pending</option><option value="Cancelled">Cancelled</option></select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Event / Mass Name <span class="font-normal text-slate-400">(Optional - e.g. Memorial of St. Joseph)</span></label>
                    <input type="text" name="event_name" id="event_name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter specific name if applicable">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Date</label><input type="date" name="mass_date" id="mass_date" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></div>
                    <div><label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Time</label><input type="time" name="mass_time" id="mass_time" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></div>
                </div>

                <!-- Recurring Options -->
                <div id="recurringSection" class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_recurring" id="is_recurring" class="rounded text-blue-600 border-gray-300 w-4 h-4 focus:ring-blue-500" onchange="toggleRecurringOptions()">
                            <span class="text-xs font-bold text-slate-700">Recurring Schedule</span>
                        </label>
                    </div>

                    <div id="recurringOptions" class="hidden space-y-3 pt-2 border-t border-slate-200">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Frequency</label>
                                <select name="frequency" id="frequency" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="toggleFrequencyOptions()">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Repeat Every</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="interval" id="interval" value="1" min="1" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span id="intervalUnit" class="text-[10px] font-bold text-slate-400">days</span>
                                </div>
                            </div>
                        </div>

                        <div id="weeklyOptions" class="hidden">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">On these days</label>
                            <div class="flex flex-wrap gap-2">
                                <?php $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; 
                                foreach($days as $i => $d): ?>
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="recurring_days[]" value="<?= $i ?>" class="peer hidden">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-slate-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition-all uppercase tracking-tighter"><?= $d ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">End Recurrence</label>
                            <input type="date" name="end_date" id="end_date" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Color Label</label>
                    <div class="flex flex-wrap gap-2"><?php $colors = ['green','purple','yellow','blue','indigo','pink','red','teal','gray']; foreach($colors as $c): $bg = "bg-{$c}-500"; if($c=='yellow') $bg="bg-yellow-400"; ?><label class="cursor-pointer"><input type="radio" name="color" value="<?= $c ?>" class="peer hidden color-radio"><span class="block w-6 h-6 rounded-full <?= $bg ?> peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-slate-400 transition-all"></span></label><?php endforeach; ?></div>
                </div>

                <div>
                    <div class="flex justify-between items-end mb-1.5">
                        <label class="block text-xs font-bold text-slate-500 ml-1">Assigned Servers</label>
                        <input type="text" id="serverSearch" onkeyup="filterServers()" placeholder="Search..." class="text-[10px] px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none">
                    </div>
                    <div class="max-h-32 overflow-y-auto border border-slate-200 rounded-xl p-2 bg-slate-50 custom-scrollbar" id="serverList">
                        <?php foreach($servers as $svr): ?>
                            <label class="flex items-center gap-2 p-1.5 hover:bg-white rounded-lg cursor-pointer server-item transition-colors">
                                <input type="checkbox" name="assigned_servers[]" value="<?= $svr->id ?>" class="server-checkbox rounded text-blue-600 border-gray-300 w-4 h-4 focus:ring-blue-500">
                                <span class="text-xs text-slate-700 font-medium server-name"><?= h($svr->name) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-colors">Cancel</button>
                <div id="joinBtnContainer" class="hidden flex-1">
                    <button type="button" onclick="selfAssign()" class="w-full py-2.5 rounded-xl bg-green-500 text-white text-sm font-bold hover:bg-green-600 shadow-lg shadow-green-200 transition-all">Join Schedule</button>
                </div>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">Save Schedule</button>
            </div>
            <div id="deleteBtnContainer" class="hidden mt-4 pt-3 border-t border-slate-100 text-center"><button type="button" id="deleteLink" data-loading class="text-xs text-red-500 font-bold hover:underline">Delete Schedule</button></div>
        </form>
    </div>
</div>

<!-- Self Assign Form -->
<form action="<?= URLROOT ?>/schedules/self-assign" method="POST" id="selfAssignForm" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="schedule_id" id="selfAssignId">
</form>

<!-- Bulk Edit Modal -->
<div id="bulkEditModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6">
        <h3 class="font-bold text-lg mb-4">Bulk Edit</h3>
        <form action="<?= URLROOT ?>/schedules/bulk-update" method="POST">
            <?php csrf_field(); ?>
            <input type="hidden" name="ids" id="bulkEditIds">
            
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Update Status</label>
            <select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none mb-6">
                <option value="">No Change</option>
                <option value="Confirmed">Confirmed</option>
                <option value="Pending">Pending</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('bulkEditModal').classList.add('hidden')" class="flex-1 py-2 border rounded-xl text-sm font-bold">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    const schedules = <?= json_encode($schedules) ?>;
    const currentServerId = <?= json_encode($currentServerId) ?>;
    let currentDate = new Date();
    let isSelectionMode = false;
    let selectedIds = [];

    const modal = document.getElementById('scheduleModal');
    const modalContent = document.getElementById('modalContent');

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        document.getElementById('currentMonth').innerText = new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';
        
        for(let i = 0; i < firstDay; i++) grid.appendChild(document.createElement('div'));
        
        for(let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const today = new Date();
            const isToday = dateStr === `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            
            const cell = document.createElement('div');
            cell.className = `min-h-[100px] border ${isToday ? 'border-blue-500 ring-2 ring-blue-100 bg-white' : 'border-slate-100 bg-slate-50/30'} rounded-2xl p-2 transition-all hover:border-blue-300 hover:shadow-md cursor-pointer flex flex-col gap-1 relative group`;
            
            // Add schedule
            cell.onclick = (e) => { 
                if(!isSelectionMode && (e.target === cell || e.target.classList.contains('day-num') || e.target.classList.contains('today-label'))) openModal('add', dateStr); 
            };
            
            const headerDiv = document.createElement('div');
            headerDiv.className = "flex justify-between items-start mb-1";

            const dayNum = document.createElement('span');
            dayNum.className = `text-sm font-bold ${isToday ? 'text-blue-600' : 'text-slate-400'} day-num`; dayNum.innerText = day;
            headerDiv.appendChild(dayNum);

            if (isToday) {
                const todayLabel = document.createElement('span');
                todayLabel.className = "text-[9px] font-extrabold uppercase tracking-tighter text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded today-label";
                todayLabel.innerText = "Today";
                headerDiv.appendChild(todayLabel);
            }

            cell.appendChild(headerDiv);
            
            const dayEvents = schedules.filter(s => s.mass_date === dateStr);
            dayEvents.forEach(evt => {
                const eventEl = document.createElement('div');
                
                // Check if past
                const eventDateTime = new Date(`${evt.mass_date} ${evt.mass_time}`);
                const isPast = eventDateTime < new Date();

                let colorClass = 'bg-green-100 text-green-700 hover:bg-green-200';
                if (isPast) {
                    colorClass = 'bg-slate-100 text-slate-400 hover:bg-slate-200 grayscale-[0.5] opacity-60';
                } else if (evt.color) {
                    colorClass = `bg-${evt.color}-100 text-${evt.color}-700 hover:bg-${evt.color}-200`;
                } else if (evt.mass_type === 'Funeral') {
                    colorClass = 'bg-purple-100 text-purple-700 hover:bg-purple-200';
                } else if (evt.mass_type === 'Wedding') {
                    colorClass = 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200';
                }
                
                // Selection Style
                const isSelected = selectedIds.includes(evt.id.toString());
                if (isSelectionMode) {
                    if (isSelected) {
                        eventEl.className = `text-[10px] font-bold px-2 py-1 rounded-lg truncate transition-all ring-2 ring-blue-500 ring-offset-1 bg-white text-blue-600 relative`;
                    } else {
                        eventEl.className = `text-[10px] font-bold px-2 py-1 rounded-lg truncate transition-all opacity-60 hover:opacity-100 ${colorClass}`;
                    }
                } else {
                    eventEl.className = `text-[10px] font-bold px-2 py-1 rounded-lg truncate transition-colors ${colorClass}`;
                }

                eventEl.innerText = `${evt.mass_time.substring(0,5)} ${evt.mass_type === 'Special Event' ? (evt.event_name || evt.mass_type) : evt.mass_type}`;
                
                // Add Check Icon if Selected
                if (isSelectionMode && isSelected) {
                    eventEl.className += ' ring-2 ring-blue-500 ring-offset-1 bg-white text-blue-600 relative';
                    eventEl.innerHTML += ' <span class="absolute right-1 top-1 text-blue-600">✓</span>';
                } else if (currentServerId && evt.assigned_ids && evt.assigned_ids.includes(parseInt(currentServerId))) {
                    // Admin Assigned: Highlight and add star
                    eventEl.className += ' ring-2 ring-blue-600 ring-offset-1 z-10 shadow-sm';
                    eventEl.innerHTML = `<span class="flex items-center gap-1"><span>⭐</span> ${eventEl.innerText}</span>`;
                }

                eventEl.onclick = (e) => { 
                    e.stopPropagation(); 
                    if (isSelectionMode) {
                        toggleSelection(evt.id.toString());
                    } else {
                        openModal('edit', null, evt); 
                    }
                };
                cell.appendChild(eventEl);
            });
            grid.appendChild(cell);
        }
    }

    function toggleSelectionMode() {
        isSelectionMode = !isSelectionMode;
        const btn = document.getElementById('selectModeBtn');
        const bar = document.getElementById('selectionBar');
        const header = document.getElementById('calendarHeader');
        
        if (isSelectionMode) {
            btn.classList.add('bg-blue-50', 'text-blue-600', 'border-blue-200', 'ring-2', 'ring-blue-200');
            bar.classList.remove('hidden');
            header.classList.add('pt-16'); // Push header down
        } else {
            btn.classList.remove('bg-blue-50', 'text-blue-600', 'border-blue-200', 'ring-2', 'ring-blue-200');
            bar.classList.add('hidden');
            header.classList.remove('pt-16');
            selectedIds = [];
        }
        renderCalendar();
        updateSelectedCount();
    }

    function toggleSelection(id) {
        if (selectedIds.includes(id)) {
            selectedIds = selectedIds.filter(i => i !== id);
        } else {
            selectedIds.push(id);
        }
        renderCalendar();
        updateSelectedCount();
    }

    function selectAllCalendar(check) {
        if (!check) {
            selectedIds = [];
        } // Add true case if needed to select ALL currently visible? Maybe later.
        renderCalendar();
        updateSelectedCount();
    }

    function updateSelectedCount() {
        document.getElementById('selectedCount').innerText = `${selectedIds.length} Selected`;
    }

    function submitBulk(action) {
        if (selectedIds.length === 0) return showAlert('No items selected');

        if (action === 'delete') {
            showConfirm(`Delete ${selectedIds.length} selected schedules?`, 'Confirm Bulk Delete', function() {
                const form = document.getElementById('bulkForm');
                form.action = "<?= URLROOT ?>/schedules/bulk-delete";
                
                // Let's create inputs:
                const formEl = document.getElementById('bulkForm');
                formEl.innerHTML = '<?php csrf_field(); ?>'; // Reset form content to token
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    formEl.appendChild(input);
                });
                
                formEl.submit();
            });
        } else if (action === 'edit') {
            document.getElementById('bulkEditIds').value = JSON.stringify(selectedIds);
            document.getElementById('bulkEditModal').classList.remove('hidden');
        }
    }

    function filterServers() {
        const query = document.getElementById('serverSearch').value.toLowerCase();
        document.querySelectorAll('.server-item').forEach(item => {
            item.style.display = item.querySelector('.server-name').innerText.toLowerCase().includes(query) ? 'flex' : 'none';
        });
    }

    function toggleRecurringOptions() {
        const isRecurring = document.getElementById('is_recurring').checked;
        const options = document.getElementById('recurringOptions');
        if (isRecurring) {
            options.classList.remove('hidden');
            toggleFrequencyOptions();
        } else {
            options.classList.add('hidden');
        }
    }

    function toggleFrequencyOptions() {
        const freq = document.getElementById('frequency').value;
        const weekly = document.getElementById('weeklyOptions');
        const unit = document.getElementById('intervalUnit');
        
        if (freq === 'weekly') {
            weekly.classList.remove('hidden');
            unit.innerText = 'weeks';
        } else {
            weekly.classList.add('hidden');
            unit.innerText = freq === 'daily' ? 'days' : 'months';
        }
    }

    function openModal(mode, date = null, event = null) {
        modal.classList.remove('hidden'); setTimeout(() => { modal.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
        document.querySelectorAll('.server-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.color-radio').forEach(r => r.checked = false);
        
        // Reset Recurring
        document.getElementById('is_recurring').checked = false;
        document.getElementById('recurringOptions').classList.add('hidden');
        document.getElementById('recurringSection').classList.remove('hidden');
        document.querySelectorAll('input[name="recurring_days[]"]').forEach(cb => cb.checked = false);

        const joinBtn = document.getElementById('joinBtnContainer');
        joinBtn.classList.add('hidden');

        if (mode === 'add') {
            document.getElementById('modalTitle').innerText = 'Add Schedule'; document.getElementById('scheduleForm').reset();
            document.getElementById('scheduleId').value = ''; document.getElementById('deleteBtnContainer').classList.add('hidden');
            if(date) document.getElementById('mass_date').value = date;
        } else {
            document.getElementById('modalTitle').innerText = 'Edit Schedule'; document.getElementById('scheduleId').value = event.id;
            
            // Hide recurring for edit mode for now to keep it simple (editing series is complex)
            document.getElementById('recurringSection').classList.add('hidden');
            document.getElementById('mass_type').value = event.mass_type; document.getElementById('event_name').value = event.event_name || '';
            document.getElementById('mass_date').value = event.mass_date; document.getElementById('mass_time').value = event.mass_time;
            document.getElementById('status').value = event.status;
            if (event.color) { const r = document.querySelector(`.color-radio[value="${event.color}"]`); if (r) r.checked = true; }
            if (event.assigned_servers) event.assigned_servers.forEach(id => { const cb = document.querySelector(`.server-checkbox[value="${id}"]`); if (cb) cb.checked = true; });
            document.getElementById('deleteBtnContainer').classList.remove('hidden');
            document.getElementById('deleteLink').onclick = () => showConfirm('Delete this schedule?', 'Delete Schedule', () => window.location.href=`<?= URLROOT ?>/schedules/delete?id=${event.id}`);

            // Check if past
            const eventDateTime = new Date(`${event.mass_date} ${event.mass_time}`);
            const isPast = eventDateTime < new Date();

            // Self-Assign Logic for Admin (Only if not past)
            if (!isPast && currentServerId && event.assigned_ids && !event.assigned_ids.includes(parseInt(currentServerId))) {
                joinBtn.classList.remove('hidden');
                document.getElementById('selfAssignId').value = event.id;
            }
        }
    }

    function selfAssign() {
        document.getElementById('selfAssignForm').submit();
    }

    function closeModal() { modal.classList.add('opacity-0'); modalContent.classList.add('scale-95'); setTimeout(() => modal.classList.add('hidden'), 300); }
    function changeMonth(d) { currentDate.setMonth(currentDate.getMonth() + d); renderCalendar(); }
    
    // Drag and Drop Logic
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileNameDisplay = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitImport');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    });

    ['dragleave', 'drop'].forEach(event => {
        dropZone.addEventListener(event, () => {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });

    fileInput.addEventListener('change', handleFileSelect);

    function handleFileSelect() {
        if (fileInput.files.length) {
            fileNameDisplay.textContent = fileInput.files[0].name;
            fileInfo.classList.remove('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-slate-100', 'text-slate-400');
            submitBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-200');
        }
    }

    function generateSundays() {
        const m = prompt("Month (1-12):", new Date().getMonth() + 1);
        if (m) { const y = prompt("Year:", new Date().getFullYear()); if (y) window.location.href = `<?= URLROOT ?>/schedules/generate?month=${m}&year=${y}`; }
    }
    renderCalendar();
</script>