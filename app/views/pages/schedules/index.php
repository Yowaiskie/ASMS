<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Schedule Management</h2>
        <p class="text-slate-500 text-sm mt-1">Manage mass schedules and assignments</p>
    </div>
    
    <div class="flex flex-wrap gap-2">
        <button onclick="toggleSelectionMode()" id="selectModeBtn" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 p-2.5 rounded-xl shadow-sm transition-all" title="Select Multiple">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>

        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Import
        </button>

        <?php if ($_SESSION['role'] === 'Superadmin'): ?>
        <a href="<?= URLROOT ?>/schedules/templates" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Auto-Fill
        </a>
        <?php endif; ?>
        
        <button onclick="openModal('add')" class="bg-primary hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl shadow-lg shadow-primary-200 transition-all flex items-center gap-2 font-semibold text-sm">
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

            <div id="dropZone" class="border-2 border-dashed border-slate-200 rounded-3xl p-10 flex flex-col items-center justify-center gap-4 hover:border-primary-400 hover:bg-primary-50 transition-all cursor-pointer group text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-primary-100 group-hover:text-primary transition-colors">
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
            
            <div id="fileInfo" class="mt-4 p-4 bg-primary-50 rounded-2xl border border-primary-100 hidden">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span id="fileName" class="text-xs font-bold text-primary-800 truncate">file.csv</span>
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
    <div id="selectionBar" class="hidden absolute top-0 left-0 right-0 z-10 bg-primary text-white p-4 flex justify-between items-center animate-fade-in-up">
        <div class="flex items-center gap-3">
            <span class="font-bold text-sm" id="selectedCount">0 Selected</span>
            <div class="h-4 w-px bg-primary-400"></div>
            <button type="button" onclick="selectAllCalendar(false)" class="text-xs hover:underline">Clear</button>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="submitBulk('edit')" class="bg-white text-primary hover:bg-primary-50 px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
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
    
    <div class="overflow-x-auto custom-scrollbar">
        <div class="grid grid-cols-7 gap-4 mb-4 text-center min-w-[700px]">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sun</div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mon</div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tue</div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Wed</div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Thu</div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Fri</div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sat</div>
        </div>
        
        <div id="calendarGrid" class="grid grid-cols-7 gap-4 auto-rows-fr min-w-[700px]">
            <!-- Calendar Cells -->
        </div>
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
<form action="<?= URLROOT ?>/schedules/bulk-delete" method="POST" id="bulkForm" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="action" id="bulkAction" value="">
    <input type="hidden" name="ids" id="bulkIds" value="">
</form>

<!-- Single Delete Form -->
<form action="<?= URLROOT ?>/schedules/delete" method="POST" id="singleDeleteForm" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="id" id="deleteScheduleId">
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
                        <select name="mass_type" id="mass_type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <?php foreach($activityTypes as $at): ?>
                                <option value="<?= h($at->name) ?>"><?= h($at->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Status</label>
                        <select name="status" id="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"><option value="Confirmed">Confirmed</option><option value="Pending">Pending</option><option value="Cancelled">Cancelled</option></select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Event / Mass Name <span class="font-normal text-slate-400">(Optional - e.g. Memorial of St. Joseph)</span></label>
                    <input type="text" name="event_name" id="event_name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Enter specific name if applicable">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Date</label><input type="date" name="mass_date" id="mass_date" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></div>
                    <div><label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Time</label><input type="time" name="mass_time" id="mass_time" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></div>
                </div>

                <!-- Smart Recurring Assignment (Checkbox Style) -->
                <div id="recurringSection" class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <label class="flex items-center justify-between cursor-pointer group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-slate-100 group-hover:border-primary/30 transition-colors">
                                <i class="ph-bold ph-magic-wand text-primary text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Apply to Master Plan</p>
                                <p class="text-[10px] text-slate-500 font-medium">Sync assignment to all future similar slots</p>
                            </div>
                        </div>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="masterPlanCheckbox" onchange="toggleMasterPlan(this)" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </div>
                    </label>

                    <input type="hidden" name="is_recurring" id="is_recurring_hidden">
                    <input type="hidden" name="frequency" id="frequency_hidden" value="weekly">
                    <input type="hidden" name="end_date" id="end_date_hidden">

                    <div id="pattern-desc" class="mt-3 px-3 py-2.5 bg-primary/5 rounded-xl hidden flex items-start gap-2 border border-primary/10">
                        <i class="ph-bold ph-info text-primary mt-0.5 text-xs"></i>
                        <p class="text-[11px] font-bold text-primary leading-tight"></p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Color Label</label>
                    <div class="flex flex-wrap gap-2"><?php $colors = ['green','purple','yellow','blue','indigo','pink','red','teal','gray']; foreach($colors as $c): $bg = "bg-{$c}-500"; if($c=='yellow') $bg="bg-yellow-400"; ?><label class="cursor-pointer"><input type="radio" name="color" value="<?= $c ?>" class="peer hidden color-radio"><span class="block w-6 h-6 rounded-full <?= $bg ?> peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-slate-400 transition-all"></span></label><?php endforeach; ?></div>
                </div>

                <div>
                    <div class="flex justify-between items-end mb-1.5 px-1">
                        <div class="flex items-center gap-3">
                            <label class="block text-xs font-bold text-slate-500">Assigned Servers <span id="assignedCounter" class="text-primary-600 ml-0.5"></span></label>
                            <div class="h-3 w-px bg-slate-200"></div>
                            <label class="flex items-center gap-1.5 cursor-pointer group">
                                <input type="checkbox" id="assignedOnlyCheckbox" onclick="toggleAssignedFilter(this)" class="rounded text-primary border-slate-300 w-3 h-3 focus:ring-primary-500 cursor-pointer">
                                <span class="text-[9px] font-bold text-slate-400 group-hover:text-primary-500 transition-colors uppercase tracking-tighter">View Assigned Only</span>
                            </label>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-1.5 cursor-pointer group">
                                <input type="checkbox" id="assignAllCheckbox" onclick="toggleAssignAll(this)" class="rounded text-primary border-slate-300 w-3 h-3 focus:ring-primary-500 cursor-pointer">
                                <span class="text-[9px] font-bold text-slate-400 group-hover:text-primary-500 transition-colors uppercase tracking-tighter">Select All</span>
                            </label>
                            <input type="text" id="serverSearch" onkeyup="filterServers()" placeholder="Search..." class="text-[10px] px-2.5 py-1 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 transition-all w-24">
                        </div>
                    </div>
                    <div class="max-h-32 overflow-y-auto border border-slate-200 rounded-xl p-2 bg-slate-50 custom-scrollbar" id="serverList">
                        <?php foreach($servers as $svr): ?>
                            <label class="flex items-center gap-2 p-1.5 hover:bg-white rounded-lg cursor-pointer server-item transition-colors">
                                <input type="checkbox" name="assigned_servers[]" value="<?= $svr->id ?>" onchange="sortServers()" class="server-checkbox rounded text-primary border-gray-300 w-4 h-4 focus:ring-primary-500">
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
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all">Save Schedule</button>
            </div>
            <div id="deleteBtnContainer" class="hidden mt-4 pt-3 border-t border-slate-100 text-center"><button type="button" id="deleteLink" class="text-xs text-red-500 font-bold hover:underline">Delete Schedule</button></div>
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
            <select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none mb-4">
                <option value="">No Change</option>
                <option value="Confirmed">Confirmed</option>
                <option value="Pending">Pending</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Update Color Label</label>
            <input type="hidden" name="color" id="bulkColorInput">
            <div class="flex flex-wrap gap-2 mb-6 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <button type="button" onclick="selectBulkColor('')" class="w-6 h-6 rounded-full border border-slate-300 flex items-center justify-center text-slate-400 hover:border-slate-400 transition-all" title="No Change">
                    <i class="ph-bold ph-prohibit text-[10px]"></i>
                </button>
                <?php $colors = ['green','purple','yellow','blue','indigo','pink','red','teal','gray']; foreach($colors as $c): $bg = "bg-{$c}-500"; if($c=='yellow') $bg="bg-yellow-400"; ?>
                    <button type="button" onclick="selectBulkColor('<?= $c ?>')" data-bulk-color="<?= $c ?>" class="bulk-color-btn w-6 h-6 rounded-full <?= $bg ?> ring-offset-1 transition-all hover:scale-110"></button>
                <?php endforeach; ?>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('bulkEditModal').classList.add('hidden')" class="flex-1 py-2.5 text-slate-500 font-bold">Cancel</button>
                <button type="submit" class="flex-1 bg-primary text-white py-2.5 rounded-xl font-bold shadow-lg shadow-primary-200">Apply Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Auto-Fill Configuration Modal -->
<div id="configModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-10">
            <div>
                <h3 class="text-xl font-bold text-slate-800 tracking-tight">Auto-Fill Settings</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Define default mass slots for each day of the week.</p>
            </div>
            <button onclick="closeConfigModal()" class="p-2 hover:bg-slate-50 rounded-xl text-slate-400 transition-colors">
                <i class="ph-bold ph-x text-xl"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50">
            <div class="flex gap-1 mb-6 bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm overflow-x-auto custom-scrollbar">
                <?php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; 
                foreach($days as $index => $day): ?>
                    <button onclick="switchConfigDay(<?= $index ?>)" 
                            class="day-tab flex-1 min-w-[80px] py-2 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= $index === 0 ? 'bg-primary text-white shadow-md' : 'text-slate-500 hover:bg-slate-50' ?>"
                            data-day="<?= $index ?>">
                        <?= $day ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div id="templatesList" class="space-y-3">
                <div class="text-center py-12 bg-white rounded-3xl border border-dashed border-slate-200">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i class="ph-bold ph-calendar-blank text-3xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">No default slots for this day</p>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-slate-100 bg-white">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Add New Slot Template</h4>
            <form id="addTemplateForm" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <select name="mass_type" required class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="Sunday Mass">Sunday Mass</option>
                    <option value="Anticipated Mass">Anticipated Mass</option>
                    <option value="Weekday Mass">Weekday Mass</option>
                    <option value="Wedding">Wedding</option>
                    <option value="Funeral">Funeral</option>
                    <option value="Baptism">Baptism</option>
                    <option value="Meeting">Meeting</option>
                </select>
                <input type="time" name="mass_time" required class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <button type="submit" class="bg-primary text-white px-4 py-2.5 rounded-xl font-bold text-xs hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all flex items-center justify-center gap-2">
                    <i class="ph-bold ph-plus"></i> Add Slot
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function selectBulkColor(color) {
        document.getElementById('bulkColorInput').value = color;
        document.querySelectorAll('.bulk-color-btn').forEach(btn => {
            if (btn.dataset.bulkColor === color && color !== '') {
                btn.classList.add('ring-2', 'ring-slate-800');
            } else {
                btn.classList.remove('ring-2', 'ring-slate-800');
            }
        });
    }

    function setEndDuration(months) {
        const startDateInput = document.getElementById('mass_date');
        const endDateInput = document.getElementById('end_date');
        
        if (!startDateInput.value) {
            alert('Please select a start date first.');
            return;
        }

        const date = new Date(startDateInput.value);
        date.setMonth(date.getMonth() + months);
        
        // Format to YYYY-MM-DD
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        
        endDateInput.value = `${year}-${month}-${day}`;
    }

    function setPattern(frequency) {
        const isRecHidden = document.getElementById('is_recurring_hidden');
        const freqHidden = document.getElementById('frequency_hidden');
        const endDateHidden = document.getElementById('end_date_hidden');
        const descDiv = document.getElementById('pattern-desc');
        const startDateVal = document.getElementById('mass_date').value;
        const configMonths = <?= (int)($policy_schedule_duration ?? 1) ?>;

        // Reset All Buttons
        document.querySelectorAll('.pattern-btn').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white', 'shadow-md', 'shadow-primary/20');
            btn.classList.add('text-slate-600');
        });

        if (frequency === 'none') {
            isRecHidden.value = '';
            const btn = document.getElementById('btn-pattern-none');
            btn.classList.add('bg-primary', 'text-white', 'shadow-md', 'shadow-primary/20');
            btn.classList.remove('text-slate-600');
            descDiv.classList.add('hidden');
            return;
        }

        if (!startDateVal) {
            showAlert('Please select a start date first.');
            return;
        }

        // Active state for chosen button
        const patternKey = (frequency === 'master') ? 'master' : frequency;
        const activeBtn = document.getElementById('btn-pattern-' + patternKey);
        if(activeBtn) {
            activeBtn.classList.add('bg-primary', 'text-white', 'shadow-md', 'shadow-primary/20');
            activeBtn.classList.remove('text-slate-600');
        }
        
        isRecHidden.value = '1';
        const actualFreq = (frequency === 'master') ? 'weekly' : frequency;
        freqHidden.value = actualFreq;

        const date = new Date(startDateVal);
        date.setMonth(date.getMonth() + configMonths);
        const endStr = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        endDateHidden.value = endStr;

        let desc = '';
        if (frequency === 'master') {
            const dayName = new Date(startDateVal).toLocaleDateString('en-US', {weekday: 'long'});
            desc = `Repeats every ${dayName} until ${date.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'})} (${configMonths}m Master Plan)`;
        }

        descDiv.querySelector('p').innerText = desc;
        descDiv.classList.remove('hidden');
    }

    const schedules = <?= json_encode($schedules) ?>;
    const currentServerId = <?= json_encode($currentServerId) ?>;
    let currentDate = new Date();
    let isSelectionMode = false;
    let selectedIds = [];
    let activeConfigDay = 0;

    function openConfigModal() {
        document.getElementById('configModal').classList.remove('hidden');
    }

    function closeConfigModal() {
        document.getElementById('configModal').classList.add('hidden');
    }

    function switchConfigDay(dayIndex) {
        activeConfigDay = dayIndex;
        document.querySelectorAll('.day-tab').forEach(tab => {
            if (parseInt(tab.dataset.day) === dayIndex) {
                tab.classList.add('bg-primary', 'text-white', 'shadow-md');
                tab.classList.remove('text-slate-500', 'hover:bg-slate-50');
            } else {
                tab.classList.remove('bg-primary', 'text-white', 'shadow-md');
                tab.classList.add('text-slate-500', 'hover:bg-slate-50');
            }
        });
        // Logic to load templates for this day will go here
    }

    const modal = document.getElementById('scheduleModal');
    const modalContent = document.getElementById('modalContent');

    function getColorClass(color, isPast) {
        if (isPast) return 'bg-slate-100 text-slate-400 hover:bg-slate-200 grayscale-[0.5] opacity-60';
        
        const maps = {
            'green': 'bg-green-100 text-green-700 hover:bg-green-200',
            'purple': 'bg-purple-100 text-purple-700 hover:bg-purple-200',
            'yellow': 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
            'blue': 'bg-blue-100 text-blue-700 hover:bg-blue-200',
            'indigo': 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200',
            'pink': 'bg-pink-100 text-pink-700 hover:bg-pink-200',
            'red': 'bg-red-100 text-red-700 hover:bg-red-200',
            'teal': 'bg-teal-100 text-teal-700 hover:bg-teal-200',
            'gray': 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        };
        
        return maps[color] || maps['blue'];
    }

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
            cell.className = `min-h-[100px] border ${isToday ? 'border-primary-500 ring-2 ring-primary-100 bg-white' : 'border-slate-100 bg-slate-50/30'} rounded-2xl p-2 transition-all hover:border-primary-300 hover:shadow-md cursor-pointer flex flex-col gap-1 relative group`;            
            // Add schedule
            cell.onclick = (e) => { 
                if(!isSelectionMode && (e.target === cell || e.target.classList.contains('day-num') || e.target.classList.contains('today-label'))) openModal('add', dateStr); 
            };
            
            const headerDiv = document.createElement('div');
            headerDiv.className = "flex justify-between items-start mb-1";

            const dayNum = document.createElement('span');
            dayNum.className = `text-sm font-bold ${isToday ? 'text-primary' : 'text-slate-400'} day-num`; dayNum.innerText = day;
            headerDiv.appendChild(dayNum);

            if (isToday) {
                const todayLabel = document.createElement('span');
                todayLabel.className = "text-[9px] font-extrabold uppercase tracking-tighter text-primary bg-primary-50 px-1.5 py-0.5 rounded today-label";
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

                let color = evt.color;
                if (!color) {
                    if (evt.mass_type === 'Funeral') color = 'purple';
                    else if (evt.mass_type === 'Wedding') color = 'yellow';
                    else color = 'green';
                }

                let colorClass = getColorClass(color, isPast);
                
                // Selection Style
                const isSelected = selectedIds.includes(evt.id.toString());
                if (isSelectionMode) {
                    if (isSelected) {
                        eventEl.className = `text-[10px] font-bold px-2 py-1 rounded-lg truncate transition-all ring-2 ring-primary-500 ring-offset-1 bg-white text-primary relative`;
                    } else {
                        eventEl.className = `text-[10px] font-bold px-2 py-1 rounded-lg truncate transition-all opacity-60 hover:opacity-100 ${colorClass}`;
                    }
                } else {
                    eventEl.className = `text-[10px] font-bold px-2 py-1 rounded-lg truncate transition-colors ${colorClass}`;
                }

                // Format Time to 12h
                const [h, m] = evt.mass_time.substring(0,5).split(':');
                const h12 = h % 12 || 12;
                const ampm = h >= 12 ? 'PM' : 'AM';
                const time12 = `${h12}:${m} ${ampm}`;

                eventEl.innerText = `${time12} ${evt.mass_type === 'Special Event' ? (evt.event_name || evt.mass_type) : evt.mass_type}`;
                
                // Add Check Icon if Selected
                if (isSelectionMode && isSelected) {
                    eventEl.className += ' ring-2 ring-primary-500 ring-offset-1 bg-white text-primary relative';
                    eventEl.innerHTML += ' <span class="absolute right-1 top-1 text-primary">✓</span>';
                } else if (currentServerId && evt.assigned_ids && evt.assigned_ids.includes(parseInt(currentServerId))) {
                    // Admin Assigned: Highlight and add star
                    eventEl.className += ' ring-2 ring-primary ring-offset-1 z-10 shadow-sm';
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
            btn.classList.add('bg-primary-50', 'text-primary', 'border-primary-200', 'ring-2', 'ring-primary-200');
            bar.classList.remove('hidden');
            header.classList.add('pt-16'); // Push header down
        } else {
            btn.classList.remove('bg-primary-50', 'text-primary', 'border-primary-200', 'ring-2', 'ring-primary-200');
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

    let showAssignedOnly = false;

    function toggleAssignedFilter(checkbox) {
        showAssignedOnly = checkbox.checked;
        filterServers();
    }

    function filterServers() {
        const query = document.getElementById('serverSearch').value.toLowerCase();
        document.querySelectorAll('.server-item').forEach(item => {
            const name = item.querySelector('.server-name').innerText.toLowerCase();
            const isChecked = item.querySelector('input').checked;
            
            let matchesSearch = name.includes(query);
            let matchesFilter = !showAssignedOnly || isChecked;
            
            item.style.display = (matchesSearch && matchesFilter) ? 'flex' : 'none';
        });
    }

    function toggleAssignAll(checkbox) {
        const checked = checkbox.checked;
        document.querySelectorAll('.server-checkbox').forEach(cb => {
            cb.checked = checked;
        });
        updateAssignedCounter();
        sortServers();
    }

    function toggleMasterPlan(checkbox) {
        const isRecHidden = document.getElementById('is_recurring_hidden');
        const freqHidden = document.getElementById('frequency_hidden');
        const endDateHidden = document.getElementById('end_date_hidden');
        const descDiv = document.getElementById('pattern-desc');
        const startDateVal = document.getElementById('mass_date').value;
        const configStartDate = <?= json_encode($policy_schedule_start_date) ?>;
        const configEndDate = <?= json_encode($policy_schedule_end_date) ?>;

        if (checkbox.checked) {
            if (!startDateVal) {
                showAlert('Please select a date first.');
                checkbox.checked = false;
                return;
            }

            if (!configEndDate) {
                showAlert('Master Plan End Date is not set in System Configuration.');
                checkbox.checked = false;
                return;
            }

            isRecHidden.value = '1';
            freqHidden.value = 'weekly';
            endDateHidden.value = configEndDate;

            const dayName = new Date(startDateVal).toLocaleDateString('en-US', {weekday: 'long'});
            const formattedStart = configStartDate ? new Date(configStartDate).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) : 'now';
            const formattedEnd = new Date(configEndDate).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
            
            descDiv.querySelector('p').innerText = `Syncing to all future ${dayName} slots (Period: ${formattedStart} to ${formattedEnd})`;
            descDiv.classList.remove('hidden');
        } else {
            isRecHidden.value = '';
            descDiv.classList.add('hidden');
        }
    }

    function sortServers() {
        const list = document.getElementById('serverList');
        const items = Array.from(list.querySelectorAll('.server-item'));
        
        items.sort((a, b) => {
            const aChecked = a.querySelector('input').checked;
            const bChecked = b.querySelector('input').checked;
            if (aChecked && !bChecked) return -1;
            if (!aChecked && bChecked) return 1;
            return 0;
        });
        
        items.forEach(item => list.appendChild(item));
        updateAssignedCounter();
    }

    function updateAssignedCounter() {
        const count = document.querySelectorAll('.server-checkbox:checked').length;
        const counterEl = document.getElementById('assignedCounter');
        if (counterEl) {
            counterEl.innerText = count > 0 ? `(${count} Assigned)` : '';
        }
    }

    function openModal(mode, date = null, event = null) {
        // Reset form first
        document.getElementById('scheduleForm').reset();
        
        modal.classList.remove('hidden'); 
        setTimeout(() => { 
            modal.classList.remove('opacity-0'); 
            modalContent.classList.remove('scale-95'); 
        }, 10);

        document.querySelectorAll('.server-checkbox').forEach(cb => cb.checked = false);
        if(document.getElementById('assignAllCheckbox')) document.getElementById('assignAllCheckbox').checked = false;
        document.querySelectorAll('.color-radio').forEach(r => r.checked = false);
        
        // Reset Recurring UI
        document.getElementById('is_recurring_hidden').value = '';
        document.getElementById('pattern-desc').classList.add('hidden');
        const masterPlanCb = document.getElementById('masterPlanCheckbox');
        if(masterPlanCb) masterPlanCb.checked = false;

        // Reset Filter
        const assignedOnlyCb = document.getElementById('assignedOnlyCheckbox');
        if(assignedOnlyCb) assignedOnlyCb.checked = false;
        showAssignedOnly = false;

        const joinBtn = document.getElementById('joinBtnContainer');
        joinBtn.classList.add('hidden');

        if (mode === 'add') {
            document.getElementById('modalTitle').innerText = 'Add Schedule'; 
            document.getElementById('scheduleId').value = ''; 
            document.getElementById('deleteBtnContainer').classList.add('hidden');
            if(date) document.getElementById('mass_date').value = date;
            document.getElementById('recurringSection').classList.remove('hidden');
            sortServers(); // Refresh list to default
        } else {
            document.getElementById('modalTitle').innerText = 'Edit Schedule'; 
            document.getElementById('scheduleId').value = event.id;
            document.getElementById('recurringSection').classList.remove('hidden');
            
            document.getElementById('mass_type').value = event.mass_type; 
            document.getElementById('event_name').value = event.event_name || '';
            document.getElementById('mass_date').value = event.mass_date; 
            document.getElementById('mass_time').value = event.mass_time;
            document.getElementById('status').value = event.status;
            
            if (event.color) { 
                const r = document.querySelector(`.color-radio[value="${event.color}"]`); 
                if (r) r.checked = true; 
            }
            
            if (event.assigned_ids) {
                event.assigned_ids.forEach(id => { 
                    const cb = document.querySelector(`.server-checkbox[value="${id}"]`); 
                    if (cb) cb.checked = true; 
                });
            }

            // Move checked servers to top
            sortServers();

            document.getElementById('deleteBtnContainer').classList.remove('hidden');
            document.getElementById('deleteLink').onclick = () => showConfirm('Delete this schedule?', 'Delete Schedule', function() {
                document.getElementById('deleteScheduleId').value = event.id;
                document.getElementById('singleDeleteForm').submit();
            });

            // Check if past or if user is Superadmin
            const eventDateTime = new Date(`${event.mass_date} ${event.mass_time}`);
            const isPast = eventDateTime < new Date();
            const isSuperadmin = <?= json_encode($_SESSION['role'] === 'Superadmin') ?>;

            if (!isPast && !isSuperadmin && currentServerId && event.assigned_ids && !event.assigned_ids.includes(parseInt(currentServerId))) {
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
        dropZone.classList.add('border-primary-400', 'bg-primary-50');
    });

    ['dragleave', 'drop'].forEach(event => {
        dropZone.addEventListener(event, () => {
            dropZone.classList.remove('border-primary-400', 'bg-primary-50');
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
            submitBtn.classList.add('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary-200');
        }
    }
    renderCalendar();
</script>
