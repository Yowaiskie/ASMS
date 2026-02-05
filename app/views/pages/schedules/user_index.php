<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800">My Schedule</h2>
    <p class="text-slate-500 text-sm mt-1">Assignments and upcoming activities</p>
</div>

<!-- View Toggle and Filters -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 animate-fade-in-up">
    <div class="bg-white p-1.5 rounded-xl border border-slate-100 shadow-sm inline-flex w-fit">
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

    <!-- Calendar Filters (Only visible in calendar view) -->
    <div id="calendar-filters" class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 no-scrollbar">
        <button onclick="setFilter('all')" id="filter-all" class="filter-btn px-4 py-2 rounded-full text-xs font-bold bg-blue-600 text-white shadow-md transition-all whitespace-nowrap">All Schedules</button>
        <button onclick="setFilter('mine')" id="filter-mine" class="filter-btn px-4 py-2 rounded-full text-xs font-bold bg-white text-slate-500 border border-slate-100 hover:bg-slate-50 transition-all whitespace-nowrap">My Schedule</button>
        <button onclick="setFilter('open')" id="filter-open" class="filter-btn px-4 py-2 rounded-full text-xs font-bold bg-white text-slate-500 border border-slate-100 hover:bg-slate-50 transition-all whitespace-nowrap">Open Slots</button>
    </div>
</div>

<!-- Calendar View -->
<div id="view-calendar" class="animate-fade-in-up delay-100">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 id="currentMonth" class="font-bold text-lg text-slate-800">Month Year</h3>
            <div class="flex gap-2">
                <button onclick="changeMonth(-1)" class="p-2 hover:bg-slate-50 rounded-lg text-slate-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button onclick="changeMonth(1)" class="p-2 hover:bg-slate-50 rounded-lg text-slate-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-7 gap-4 mb-2">
            <div class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Sun</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Mon</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Tue</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Wed</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Thu</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Fri</div>
            <div class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Sat</div>
        </div>
        
        <div id="calendarGrid" class="grid grid-cols-7 gap-4"></div>

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
                <?php 
                    $assignedSchedules = array_filter($schedules, function($s) use ($assignedIds) {
                        return in_array($s->id, $assignedIds);
                    });
                ?>
                <?php if(!empty($assignedSchedules)): ?>
                    <?php foreach($assignedSchedules as $sch): ?>
                    <tr onclick="viewDetails(<?= h(json_encode($sch)) ?>)" class="hover:bg-slate-50 transition-colors cursor-pointer bg-blue-50/30">
                        <td class="p-6 font-bold text-slate-700"><?= date('M d, Y', strtotime($sch->mass_date)) ?></td>
                        <td class="p-6 text-slate-600 font-medium"><?= date('h:i A', strtotime($sch->mass_time)) ?></td>
                        <td class="p-6">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                <?= h($sch->mass_type === 'Special Event' ? ($sch->event_name ?: $sch->mass_type) : $sch->mass_type) ?>
                            </span>
                        </td>
                        <td class="p-6">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-blue-600 text-white flex items-center gap-1 w-fit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Assigned to You
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="p-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <p class="text-slate-400 font-medium">You have no upcoming assignments.</p>
                                <button onclick="switchView('calendar')" class="text-blue-600 font-bold text-xs hover:underline">Check calendar to join</button>
                            </div>
                        </td>
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
                    <div id="serverList" class="space-y-2"></div>
                </div>

                <form id="assignForm" action="<?= URLROOT ?>/schedules/self-assign" method="POST" class="hidden">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="schedule_id" id="targetScheduleId">
                    <button type="submit" data-loading="Joining..." class="w-full py-4 bg-primary text-white rounded-2xl font-bold text-sm shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                        Join this Schedule
                    </button>
                </form>

                <div id="pastEventMsg" class="hidden text-center p-4 bg-slate-100 text-slate-500 rounded-2xl border border-slate-200 text-xs font-bold">
                    <p>This event has already passed.</p>
                </div>

                <div id="alreadyAssignedMsg" class="hidden text-center p-4 bg-blue-50 text-blue-700 rounded-2xl border border-blue-100 text-xs font-bold">
                    <p>You are already in this schedule.</p>
                    <p class="text-[10px] opacity-70 mt-1 font-normal italic">Contact Admin to unassign.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const schedules = <?= json_encode($schedules) ?>;
    const assignedIds = <?= json_encode($assignedIds) ?>;

    let currentDate = new Date();
    let currentFilter = 'all';

    function setFilter(filter) {
        currentFilter = filter;
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.className = "filter-btn px-4 py-2 rounded-full text-xs font-bold bg-white text-slate-500 border border-slate-100 hover:bg-slate-50 transition-all whitespace-nowrap";
        });
        const activeBtn = document.getElementById(`filter-${filter}`);
        activeBtn.className = "filter-btn px-4 py-2 rounded-full text-xs font-bold bg-blue-600 text-white shadow-md transition-all whitespace-nowrap";
        renderCalendar();
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
            cell.className = `min-h-[100px] border ${isToday ? 'border-blue-500 ring-2 ring-blue-100 bg-white' : 'border-slate-100 bg-slate-50/30'} rounded-2xl p-2 transition-all hover:border-blue-200 hover:shadow-md flex flex-col gap-1 relative group cursor-pointer`;
            
            cell.onclick = () => {
                const dayEvents = schedules.filter(s => s.mass_date === dateStr);
                if (dayEvents.length > 0) {
                    let visibleEvents = dayEvents;
                    if (currentFilter === 'mine') visibleEvents = dayEvents.filter(evt => assignedIds.includes(parseInt(evt.id)));
                    else if (currentFilter === 'open') visibleEvents = dayEvents.filter(evt => !assignedIds.includes(parseInt(evt.id)));
                    if (visibleEvents.length > 0) viewDetails(visibleEvents[0]);
                }
            };

            const headerDiv = document.createElement('div');
            headerDiv.className = "flex justify-between items-start mb-1 pointer-events-none";

            const dayNum = document.createElement('span');
            dayNum.className = `text-sm font-bold ${isToday ? 'text-blue-600' : 'text-slate-400'}`;
            dayNum.innerText = day;
            headerDiv.appendChild(dayNum);

            if (isToday) {
                const todayLabel = document.createElement('span');
                todayLabel.className = "text-[9px] font-extrabold uppercase tracking-tighter text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded";
                todayLabel.innerText = "Today";
                headerDiv.appendChild(todayLabel);
            }
            cell.appendChild(headerDiv);
            
            let dayEvents = schedules.filter(s => s.mass_date === dateStr);
            if (currentFilter === 'mine') dayEvents = dayEvents.filter(evt => assignedIds.includes(parseInt(evt.id)));
            else if (currentFilter === 'open') dayEvents = dayEvents.filter(evt => !assignedIds.includes(parseInt(evt.id)));

            dayEvents.forEach(evt => {
                const isAssigned = assignedIds.includes(parseInt(evt.id));
                const eventEl = document.createElement('div');
                const eventDateTime = new Date(`${evt.mass_date} ${evt.mass_time}`);
                const isPast = eventDateTime < new Date();

                let colorClass = 'bg-green-100 text-green-700';
                if (isPast) colorClass = 'bg-slate-100 text-slate-400 grayscale-[0.5] opacity-60';
                else if (evt.color) colorClass = `bg-${evt.color}-100 text-${evt.color}-700`;
                else if (evt.mass_type === 'Funeral') colorClass = 'bg-purple-100 text-purple-700';
                else if (evt.mass_type === 'Wedding') colorClass = 'bg-yellow-100 text-yellow-800';
                
                eventEl.className = `text-[10px] font-bold px-2 py-1 rounded-lg truncate transition-all ${colorClass} ${isAssigned ? 'ring-2 ring-blue-600 ring-offset-1 z-10 shadow-sm' : 'opacity-80'}`;
                let title = evt.mass_type === 'Special Event' && evt.event_name ? evt.event_name : evt.mass_type;
                eventEl.innerText = `${evt.mass_time.substring(0,5)} ${title}`;
                if (isAssigned) eventEl.innerHTML = `⭐ ${eventEl.innerText}`;

                eventEl.onclick = (e) => { e.stopPropagation(); viewDetails(evt); };
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

        const isPast = new Date(`${evt.mass_date} ${evt.mass_time}`) < new Date();
        document.getElementById('pastEventMsg').classList.add('hidden');
        document.getElementById('alreadyAssignedMsg').classList.add('hidden');
        document.getElementById('assignForm').classList.add('hidden');

        if (isAssigned) document.getElementById('alreadyAssignedMsg').classList.remove('hidden');
        else if (isPast) document.getElementById('pastEventMsg').classList.remove('hidden');
        else document.getElementById('assignForm').classList.remove('hidden');

        const list = document.getElementById('serverList');
        list.innerHTML = '<p class="text-xs text-slate-400 italic">Loading...</p>';
        modal.classList.remove('hidden');

        try {
            const res = await fetch(`<?= URLROOT ?>/schedules/get-servers?id=${evt.id}`);
            const servers = await res.json();
            list.innerHTML = '';
            if (servers.length === 0) {
                const msg = isPast ? 'No servers were scheduled for this activity.' : 'No servers scheduled yet.';
                list.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-2">${msg}</p>`;
            } else {
                servers.forEach(s => {
                    const div = document.createElement('div');
                    div.className = "flex items-center justify-between";
                    div.innerHTML = `<span class="text-xs font-bold text-slate-700">${s.name}</span><span class="text-[9px] text-slate-400 font-bold uppercase">${s.rank}</span>`;
                    list.appendChild(div);
                });
            }
        } catch (e) { list.innerHTML = '<p class="text-xs text-red-400">Error loading servers.</p>'; }
    }

    function closeDetails() { document.getElementById('detailsModal').classList.add('hidden'); }
    function changeMonth(d) { currentDate.setMonth(currentDate.getMonth() + d); renderCalendar(); }

    function switchView(view) {
        const isCal = view === 'calendar';
        document.getElementById('view-calendar').classList.toggle('hidden', !isCal);
        document.getElementById('view-list').classList.toggle('hidden', isCal);
        document.getElementById('calendar-filters').classList.toggle('hidden', !isCal);
        
        document.getElementById('btn-calendar').className = isCal ? "px-4 py-2 rounded-lg text-sm font-bold bg-slate-800 text-white shadow-md transition-all flex items-center gap-2" : "px-4 py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-all flex items-center gap-2";
        document.getElementById('btn-list').className = !isCal ? "px-4 py-2 rounded-lg text-sm font-bold bg-slate-800 text-white shadow-md transition-all flex items-center gap-2" : "px-4 py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-all flex items-center gap-2";
    }

    renderCalendar();
</script>
