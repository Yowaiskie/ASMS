<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
        Dashboard 
        <span class="text-red-500 text-xl">🎯</span>
    </h2>
    <p class="text-slate-500 text-sm mt-1">Overview and quick stats</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-200">
        <div class="flex justify-between items-start mb-4">
            <h3 class="font-medium opacity-90">Total Servers</h3>
            <div class="p-2 bg-white/20 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>
        <div class="flex flex-col">
            <span class="text-4xl font-bold mb-1"><?= $stats['totalServers'] ?></span>
            <span class="text-xs opacity-70">Active members</span>
        </div>
    </div>

    <div class="bg-emerald-500 rounded-3xl p-6 text-white shadow-lg shadow-emerald-200">
        <div class="flex justify-between items-start mb-4">
            <h3 class="font-medium opacity-90">Present Today</h3>
            <div class="p-2 bg-white/20 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="flex flex-col">
            <span class="text-4xl font-bold mb-1"><?= $stats['presentToday'] ?></span>
            <span class="text-xs opacity-70">Marked present</span>
        </div>
    </div>

    <div class="bg-purple-600 rounded-3xl p-6 text-white shadow-lg shadow-purple-200">
        <div class="flex justify-between items-start mb-4">
            <h3 class="font-medium opacity-90">Mass Schedules</h3>
            <div class="p-2 bg-white/20 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        <div class="flex flex-col">
            <span class="text-4xl font-bold mb-1"><?= $stats['upcomingMasses'] ?></span>
            <span class="text-xs opacity-70">Upcoming masses</span>
        </div>
    </div>

    <div class="bg-orange-500 rounded-3xl p-6 text-white shadow-lg shadow-orange-200">
        <div class="flex justify-between items-start mb-4">
            <h3 class="font-medium opacity-90">Attendance</h3>
            <div class="p-2 bg-white/20 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
        </div>
        <div class="flex flex-col">
            <span class="text-4xl font-bold mb-1"><?= $stats['attendanceRate'] ?>%</span>
            <span class="text-xs opacity-70">Overall rate</span>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Chart: Attendance Distribution -->
    <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-700 mb-6">Attendance Distribution</h3>
        <div class="h-64 flex justify-center">
            <canvas id="dashboardDistChart"></canvas>
        </div>
    </div>

    <!-- Quick Legend / Info -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-700 mb-6">Service Status</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Present
                </div>
                <span class="font-bold"><?= $chartData['values'][0] ?></span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span> Late
                </div>
                <span class="font-bold"><?= $chartData['values'][1] ?></span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span> Absent
                </div>
                <span class="font-bold"><?= $chartData['values'][2] ?></span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-700 mb-6 text-lg">Today's Attendance</h3>
        
        <div class="space-y-4">
            <?php if(!empty($todayAttendance)): ?>
                <?php foreach($todayAttendance as $att): ?>
                <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-sm">
                            <?= strtoupper(substr($att->name, 0, 2)) ?>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm"><?= h($att->name) ?></h4>
                            <p class="text-slate-400 text-xs"><?= date('h:i A', strtotime($att->created_at)) ?></p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600"><?= h($att->status) ?></span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-slate-400 text-sm text-center py-8">No attendance marked today.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-700 mb-6 text-lg">Upcoming Schedules</h3>
        
        <div class="space-y-4">
            <?php if(!empty($upcomingSchedules)): ?>
                <?php foreach($upcomingSchedules as $sch): ?>
                <div class="p-4 rounded-2xl border border-slate-100 hover:shadow-md transition-shadow bg-slate-50/50">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-bold text-slate-800"><?= h($sch->mass_type) ?></h4>
                        <span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-lg">Scheduled</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-500">
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <?= date('M d, Y', strtotime($sch->mass_date)) ?>
                        </div>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <?= date('h:i A', strtotime($sch->mass_time)) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-slate-400 text-sm text-center py-8">No upcoming mass schedules.</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctxDist = document.getElementById('dashboardDistChart').getContext('2d');
        new Chart(ctxDist, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartData['labels']) ?>,
                datasets: [{
                    label: 'Count',
                    data: <?= json_encode($chartData['values']) ?>,
                    backgroundColor: [
                        '#10b981', // emerald-500
                        '#f59e0b', // amber-500
                        '#ef4444'  // red-500
                    ],
                    borderRadius: 12,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>