<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800">My Schedule</h2>
    <p class="text-slate-500 text-sm mt-1">Assignments and upcoming activities</p>
</div>

<!-- View Toggle -->
<div class="bg-white p-1.5 rounded-xl border border-slate-100 shadow-sm inline-flex mb-8 animate-fade-in-up">
    <button onclick="switchView('calendar')" id="btn-calendar" class="px-4 py-2 rounded-lg text-sm font-bold bg-slate-800 text-white shadow-md transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        Calendar View
    </button>
    <button onclick="switchView('list')" id="btn-list" class="px-4 py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        List View
    </button>
</div>

<!-- Calendar View -->
<div id="view-calendar" class="animate-fade-in-up delay-100">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 id="currentMonth" class="font-bold text-lg text-slate-800">February 2026</h3>
            <div class="flex gap-2">
                <button onclick="changeMonth(-1)" class="p-2 hover:bg-slate-50 rounded-lg text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button onclick="changeMonth(1)" class="p-2 hover:bg-slate-50 rounded-lg text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-7 gap-4 mb-2">
            <div class="text-center text-xs font-bold text-slate-400 uppercase">Sun</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase">Mon</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase">Tue</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase">Wed</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase">Thu</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase">Fri</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase">Sat</div>
        </div>
        
        <div id="calendarGrid" class="grid grid-cols-7 gap-4">
            <!-- Calendar Days will be injected here -->
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
</div>

<!-- List View -->
<div id="view-list" class="hidden animate-fade-in-up delay-100">
    <!-- ... (No changes needed here as we use onclick on rows) ... -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="p-6 font-semibold">Date</th>
                    <th class="p-6 font-semibold">Time</th>
                    <th class="p-6 font-semibold">Activity</th>
                    <th class="p-6 font-semibold">Assignment</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <?php if(!empty($schedules)): ?>
                    <?php foreach($schedules as $sch): ?>
                    <?php $isAssigned = in_array($sch->id, $assignedIds); ?>
                    <tr onclick="viewDetails(<?= h(json_encode($sch)) ?>)" class="hover:bg-slate-50 transition-colors cursor-pointer <?= $isAssigned ? 'bg-blue-50/30' : '' ?>">
                        <td class="p-6 font-bold text-slate-700"><?= date('M d, Y', strtotime($sch->mass_date)) ?></td>
                        <td class="p-6 text-slate-600 font-medium"><?= date('h:i A', strtotime($sch->mass_time)) ?></td>
                        <td class="p-6">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                <?= h($sch->mass_type === 'Special Event' ? ($sch->event_name ?: $sch->mass_type) : $sch->mass_type) ?>
                            </span>
                        </td>
                        <td class="p-6">
                            <?php if($isAssigned): ?>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-blue-600 text-white flex items-center gap-1 w-fit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Your Schedule
                                </span>
                            <?php else: ?>
                                <span class="text-slate-300 text-xs">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="p-8 text-center text-slate-400">No schedules found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in-up">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-800">Schedule Details</h3>
                <button onclick="closeDetails()" class="text-slate-400 hover:text-slate-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>

            <div id="modalBody" class="space-y-6">
                <div class="flex flex-col gap-1">
                    <span id="detailType" class="text-xl font-extrabold text-blue-600">Activity Name</span>
                    <span id="detailDateTime" class="text-sm text-slate-500 font-medium">Date and Time</span>
                </div>

                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Assigned Servers</h4>
                    <div id="serverList" class="space-y-2">
                        <!-- Servers injected here -->
                    </div>
                </div>

                <form id="assignForm" action="<?= URLROOT ?>/schedules/self-assign" method="POST" class="hidden">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="schedule_id" id="targetScheduleId">
                    <button type="submit" class="w-full py-4 bg-primary text-white rounded-2xl font-bold text-sm shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                        Join this Schedule
                    </button>
                </form>

                <div id="alreadyAssignedMsg" class="hidden text-center p-4 bg-blue-50 text-blue-700 rounded-2xl border border-blue-100 text-xs font-bold">
                    <p>You are already in this schedule.</p>
                    <p class="text-[10px] opacity-70 mt-1 font-normal italic">Contact Admin to unassign.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Prepare Schedule Data from PHP
    const schedules = <?= json_encode($schedules) ?>;
    const assignedIds = <?= json_encode($assignedIds) ?>;

    let currentDate = new Date();

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
            const cell = document.createElement('div');
            cell.className = "min-h-[100px] border border-slate-100 rounded-2xl p-2 transition-all hover:border-blue-200 hover:shadow-md flex flex-col gap-1 relative group cursor-pointer bg-slate-50/30";
            cell.onclick = () => {
                // If cell has events, show details of first event or show placeholder?
                // Request says "pag click nila yung schedule"
            };

            const dayNum = document.createElement('span');
            dayNum.className = "text-sm font-bold text-slate-400 mb-1";
            dayNum.innerText = day;
            cell.appendChild(dayNum);
            
            const dayEvents = schedules.filter(s => s.mass_date === dateStr);
            dayEvents.forEach(evt => {
                const isAssigned = assignedIds.includes(parseInt(evt.id));
                const eventEl = document.createElement('div');
                
                let colorClass = 'bg-green-100 text-green-700';
                if (evt.color) colorClass = `bg-${evt.color}-100 text-${evt.color}-700`;
                else if (evt.mass_type === 'Funeral') colorClass = 'bg-purple-100 text-purple-700';
                else if (evt.mass_type === 'Wedding') colorClass = 'bg-yellow-100 text-yellow-800';
                
                eventEl.className = `text-[10px] font-bold px-2 py-1 rounded-lg truncate transition-all ${colorClass} ${isAssigned ? 'ring-2 ring-blue-600 ring-offset-1 z-10 shadow-sm' : 'opacity-80'}`;
                
                let title = evt.mass_type;
                if (evt.mass_type === 'Special Event' && evt.event_name) title = evt.event_name;
                
                eventEl.innerText = `${evt.mass_time.substring(0,5)} ${title}`;
                if (isAssigned) {
                    eventEl.innerHTML = `<span class="flex items-center gap-1"><span>⭐</span> ${eventEl.innerText}</span>`;
                }

                eventEl.onclick = (e) => {
                    e.stopPropagation();
                    viewDetails(evt);
                };

                cell.appendChild(eventEl);
            });
            
            grid.appendChild(cell);
        }
    }

    async function viewDetails(evt) {
        const modal = document.getElementById('detailsModal');
        const isAssigned = assignedIds.includes(parseInt(evt.id));
        
        document.getElementById('detailType').innerText = evt.mass_type === 'Special Event' ? (evt.event_name || evt.mass_type) : evt.mass_type;
        document.getElementById('detailDateTime').innerText = `${new Date(evt.mass_date).toLocaleDateString('default', { month: 'long', day: 'numeric', year: 'numeric' })} • ${evt.mass_time}`;
        document.getElementById('targetScheduleId').value = evt.id;

        // Show/Hide Assign UI
        if (isAssigned) {
            document.getElementById('assignForm').classList.add('hidden');
            document.getElementById('alreadyAssignedMsg').classList.remove('hidden');
        } else {
            document.getElementById('assignForm').classList.remove('hidden');
            document.getElementById('alreadyAssignedMsg').classList.add('hidden');
        }

        // Fetch Assigned Servers
        const list = document.getElementById('serverList');
        list.innerHTML = '<p class="text-xs text-slate-400 italic">Loading servers...</p>';
        
        modal.classList.remove('hidden');

        try {
            // Using a hidden div or dynamic fetch? 
            // Better: Add an endpoint to fetch assignments or just use repo data if available.
            // Since we don't have a specific API route, let's fetch it via a quick async call to current page with param or new route.
            const response = await fetch(`<?= URLROOT ?>/reports/download?schedule_id=${evt.id}&ajax=1`);
            // Wait, Reports is for download. Let's add a small helper in ScheduleController instead.
            
            // For now, let's just mock or add the API.
            // Actually, I'll just add a quick helper in repo and return it.
            
            // Let's use fetch. I'll add the route next.
            const serversResponse = await fetch(`<?= URLROOT ?>/schedules/get-servers?id=${evt.id}`);
            const servers = await serversResponse.json();
            
            list.innerHTML = '';
            if (servers.length === 0) {
                list.innerHTML = '<p class="text-xs text-slate-400 italic text-center py-2">No servers scheduled yet.</p>';
            } else {
                servers.forEach(s => {
                    const div = document.createElement('div');
                    div.className = "flex items-center justify-between";
                    div.innerHTML = `
                        <span class="text-xs font-bold text-slate-700">${s.name}</span>
                        <span class="text-[9px] text-slate-400 font-bold uppercase">${s.rank}</span>
                    `;
                    list.appendChild(div);
                });
            }
        } catch (e) {
            list.innerHTML = '<p class="text-xs text-red-400">Failed to load servers.</p>';
        }
    }

    function closeDetails() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    function changeMonth(delta) {
        currentDate.setMonth(currentDate.getMonth() + delta);
        renderCalendar();
    }

    function switchView(view) {
        const calView = document.getElementById('view-calendar');
        const listView = document.getElementById('view-list');
        const btnCal = document.getElementById('btn-calendar');
        const btnList = document.getElementById('btn-list');

        if (view === 'calendar') {
            calView.classList.remove('hidden');
            listView.classList.add('hidden');
            
            btnCal.className = "px-4 py-2 rounded-lg text-sm font-bold bg-slate-800 text-white shadow-md transition-all flex items-center gap-2";
            btnList.className = "px-4 py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-all flex items-center gap-2";
        } else {
            calView.classList.add('hidden');
            listView.classList.remove('hidden');
            
            btnList.className = "px-4 py-2 rounded-lg text-sm font-bold bg-slate-800 text-white shadow-md transition-all flex items-center gap-2";
            btnCal.className = "px-4 py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-all flex items-center gap-2";
        }
    }

    // Init
    renderCalendar();
</script>