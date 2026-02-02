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
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <?php if(!empty($schedules)): ?>
                    <?php foreach($schedules as $sch): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-6 font-bold text-slate-700"><?= date('M d, Y', strtotime($sch->mass_date)) ?></td>
                        <td class="p-6 text-slate-600 font-medium"><?= date('h:i A', strtotime($sch->mass_time)) ?></td>
                        <td class="p-6">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600">
                                <?= h($sch->mass_type) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="p-8 text-center text-slate-400">No schedules assigned.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Prepare Schedule Data from PHP
    const schedules = <?= json_encode(array_map(function($s) {
        return [
            'date' => $s->mass_date, // YYYY-MM-DD
            'time' => $s->mass_time,
            'type' => $s->mass_type
        ];
    }, $schedules)) ?>;

    let currentDate = new Date();

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        document.getElementById('currentMonth').innerText = new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';
        
        // Empty cells for previous month
        for(let i = 0; i < firstDay; i++) {
            const div = document.createElement('div');
            grid.appendChild(div);
        }
        
        // Days
        for(let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const div = document.createElement('div');
            div.className = "h-24 rounded-xl border border-slate-100 p-2 flex flex-col justify-between transition-all hover:border-blue-200 relative group";
            
            // Check if day has schedule
            const daySchedules = schedules.filter(s => s.date === dateStr);
            
            if (daySchedules.length > 0) {
                div.classList.add('bg-blue-50', 'border-blue-200'); // Highlight
            }

            const dayNum = document.createElement('span');
            dayNum.className = "text-sm font-bold " + (daySchedules.length > 0 ? "text-blue-600" : "text-slate-400");
            dayNum.innerText = day;
            div.appendChild(dayNum);
            
            if (daySchedules.length > 0) {
                // Indicator dots
                const dots = document.createElement('div');
                dots.className = "flex gap-1 mt-1 flex-wrap";
                daySchedules.forEach(s => {
                    const dot = document.createElement('div');
                    dot.className = "h-1.5 w-1.5 rounded-full bg-blue-500";
                    dot.title = s.time + ' - ' + s.type;
                    dots.appendChild(dot);
                });
                div.appendChild(dots);

                // Tooltip (Simple)
                const tooltip = document.createElement('div');
                tooltip.className = "absolute bottom-full left-1/2 -translate-x-1/2 mb-2 bg-slate-800 text-white text-[10px] p-2 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10";
                tooltip.innerHTML = daySchedules.map(s => `${s.time} ${s.type}`).join('<br>');
                div.appendChild(tooltip);
            }
            
            grid.appendChild(div);
        }
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