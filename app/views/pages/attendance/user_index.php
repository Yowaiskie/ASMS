<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800">My Attendance</h2>
    <p class="text-slate-500 text-sm mt-1">View your attendance history</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex justify-between items-center animate-fade-in-up">
        <div>
            <p class="text-slate-500 text-xs font-medium mb-1">Present</p>
            <h3 class="text-3xl font-bold text-green-600"><?= $stats['Present'] ?></h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex justify-between items-center animate-fade-in-up delay-100">
        <div>
            <p class="text-slate-500 text-xs font-medium mb-1">Late</p>
            <h3 class="text-3xl font-bold text-yellow-600"><?= $stats['Late'] ?></h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex justify-between items-center animate-fade-in-up delay-200">
        <div>
            <p class="text-slate-500 text-xs font-medium mb-1">Absent</p>
            <h3 class="text-3xl font-bold text-red-600"><?= $stats['Absent'] ?></h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-up delay-300">
    <div class="p-6 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="font-bold text-slate-800">Attendance Records</h3>
        
        <div class="flex bg-slate-100 p-1 rounded-lg">
            <button onclick="switchTab('mass')" id="tab-mass" class="px-4 py-1.5 text-xs font-bold rounded-md bg-white text-slate-800 shadow-sm transition-all">Mass</button>
            <button onclick="switchTab('meeting')" id="tab-meeting" class="px-4 py-1.5 text-xs font-bold rounded-md text-slate-500 hover:text-slate-700 transition-all">Meeting</button>
        </div>
    </div>
    
    <div id="content-mass" class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white text-slate-500 text-xs uppercase tracking-wider">
                    <th class="p-6 font-semibold">Date</th>
                    <th class="p-6 font-semibold">Activity</th>
                    <th class="p-6 font-semibold">Time</th>
                    <th class="p-6 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                <?php if(!empty($massRecords)): ?>
                    <?php foreach($massRecords as $row): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-6 text-slate-600 font-medium"><?= date('M d, Y', strtotime($row->mass_date)) ?></td>
                        <td class="p-6 text-slate-800"><?= h($row->mass_type) ?></td>
                        <td class="p-6 text-slate-500"><?= h($row->mass_time) ?></td>
                        <td class="p-6">
                            <?php 
                                $color = 'bg-green-50 text-green-700';
                                if($row->status == 'Late') $color = 'bg-yellow-50 text-yellow-700';
                                if($row->status == 'Absent') $color = 'bg-red-50 text-red-700';
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                <?= h($row->status) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-400">No mass attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="content-meeting" class="overflow-x-auto hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white text-slate-500 text-xs uppercase tracking-wider">
                    <th class="p-6 font-semibold">Date</th>
                    <th class="p-6 font-semibold">Event</th>
                    <th class="p-6 font-semibold">Time</th>
                    <th class="p-6 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                <?php if(!empty($meetingRecords)): ?>
                    <?php foreach($meetingRecords as $row): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-6 text-slate-600 font-medium"><?= date('M d, Y', strtotime($row->mass_date)) ?></td>
                        <td class="p-6 text-slate-800"><?= h($row->mass_type) ?></td>
                        <td class="p-6 text-slate-500"><?= h($row->mass_time) ?></td>
                        <td class="p-6">
                            <?php 
                                $color = 'bg-green-50 text-green-700';
                                if($row->status == 'Late') $color = 'bg-yellow-50 text-yellow-700';
                                if($row->status == 'Absent') $color = 'bg-red-50 text-red-700';
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                <?= h($row->status) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-400">No meeting attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function switchTab(tab) {
        const massContent = document.getElementById('content-mass');
        const meetingContent = document.getElementById('content-meeting');
        const massTab = document.getElementById('tab-mass');
        const meetingTab = document.getElementById('tab-meeting');

        if (tab === 'mass') {
            massContent.classList.remove('hidden');
            meetingContent.classList.add('hidden');
            
            massTab.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
            massTab.classList.remove('text-slate-500');
            
            meetingTab.classList.remove('bg-white', 'text-slate-800', 'shadow-sm');
            meetingTab.classList.add('text-slate-500');
        } else {
            massContent.classList.add('hidden');
            meetingContent.classList.remove('hidden');
            
            meetingTab.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
            meetingTab.classList.remove('text-slate-500');
            
            massTab.classList.remove('bg-white', 'text-slate-800', 'shadow-sm');
            massTab.classList.add('text-slate-500');
        }
    }
</script>