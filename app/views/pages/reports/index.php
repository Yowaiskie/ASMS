<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
</style>

<div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4 animate-fade-in-up">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Reports</h2>
        <p class="text-slate-500 text-sm mt-1">View attendance summaries and analytics</p>
    </div>
    
    <div class="flex gap-3">
        <a href="<?= URLROOT ?>/reports/download" data-loading="Generating Report..." class="bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-red-200 transition-all flex items-center gap-2 text-sm font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download PDF
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8 animate-fade-in-up delay-100">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        <h3 class="font-bold text-slate-700">Filters</h3>
    </div>
    
    <form class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Time Period</label>
            <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer text-sm">
                <option>This Month</option>
                <option>Last Month</option>
                <option>Last 3 Months</option>
                <option>This Year</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Activity Type</label>
            <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer text-sm">
                <option>All Activities</option>
                <option>Sunday Mass</option>
                <option>Training</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Date Range</label>
            <input type="date" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-green-500 rounded-2xl p-6 text-white shadow-lg shadow-green-200 animate-fade-in-up delay-200">
        <p class="text-white/80 text-sm font-medium mb-1">Total Present</p>
        <h3 class="text-4xl font-bold mb-2"><?= $stats->total_present ?? 0 ?></h3>
        <p class="text-xs bg-white/20 inline-block px-2 py-1 rounded-lg"><?= $rates['present'] ?>% rate</p>
    </div>
    <div class="bg-amber-500 rounded-2xl p-6 text-white shadow-lg shadow-amber-200 animate-fade-in-up delay-200">
        <p class="text-white/80 text-sm font-medium mb-1">Late</p>
        <h3 class="text-4xl font-bold mb-2"><?= $stats->total_late ?? 0 ?></h3>
        <p class="text-xs bg-white/20 inline-block px-2 py-1 rounded-lg"><?= $rates['late'] ?>% rate</p>
    </div>
    <div class="bg-red-600 rounded-2xl p-6 text-white shadow-lg shadow-red-200 animate-fade-in-up delay-200">
        <p class="text-white/80 text-sm font-medium mb-1">Absent</p>
        <h3 class="text-4xl font-bold mb-2"><?= $stats->total_absent ?? 0 ?></h3>
        <p class="text-xs bg-white/20 inline-block px-2 py-1 rounded-lg"><?= $rates['absent'] ?>% rate</p>
    </div>
    <div class="bg-blue-500 rounded-2xl p-6 text-white shadow-lg shadow-blue-200 animate-fade-in-up delay-200">
        <p class="text-white/80 text-sm font-medium mb-1">Total Activities</p>
        <h3 class="text-4xl font-bold mb-2"><?= $totalActivities ?? 0 ?></h3>
        <p class="text-xs bg-white/20 inline-block px-2 py-1 rounded-lg">This month</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 animate-fade-in-up delay-300">
        <h3 class="font-bold text-slate-700 mb-6">Monthly Attendance Trend</h3>
        <div class="h-64">
            <canvas id="attendanceBarChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 animate-fade-in-up delay-300">
        <h3 class="font-bold text-slate-700 mb-6">Overall Distribution</h3>
        <div class="h-64 flex justify-center">
            <canvas id="distributionPieChart"></canvas>
        </div>
        <div class="flex justify-center gap-4 mt-4 text-xs font-medium">
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Present
            </div>
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span> Late
            </div>
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-red-500"></span> Absent
            </div>
        </div>
    </div>

</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-50">
        <h3 class="font-bold text-slate-700">Top Performing Servers</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold text-slate-500 uppercase bg-slate-50/50">
                    <th class="px-6 py-4">Server Name</th>
                    <th class="px-6 py-4 w-1/3">Attendance Rate</th>
                    <th class="px-6 py-4">Present / Assigned</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(!empty($topServers)): ?>
                    <?php foreach($topServers as $srv): ?>
                        <?php 
                            $rate = $srv->total_assigned > 0 ? round(($srv->present_count / $srv->total_assigned) * 100) : 0;
                            $status = 'Good';
                            $statusClass = 'bg-blue-100 text-blue-700';
                            if ($rate >= 90) { $status = 'Excellent'; $statusClass = 'bg-green-100 text-green-700'; }
                            elseif ($rate < 70) { $status = 'Needs Improvement'; $statusClass = 'bg-amber-100 text-amber-700'; }
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-700"><?= h($srv->name) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: <?= $rate ?>%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-emerald-600"><?= $rate ?>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?= $srv->present_count ?> / <?= $srv->total_assigned ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $statusClass ?>"><?= $status ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="p-6 text-center text-slate-400">No data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Bar Chart Config
    const ctxBar = document.getElementById('attendanceBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?= $trendLabels ?>,
            datasets: [
                {
                    label: 'Present',
                    data: <?= $trendData ?>,
                    backgroundColor: '#10b981', // Emerald 500
                    borderRadius: 4,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 2000,
                easing: 'easeOutQuart',
                delay: (context) => context.dataIndex * 100
            },
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { 
                        color: '#f1f5f9',
                        drawBorder: false
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Donut Chart Config
    const ctxPie = document.getElementById('distributionPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Late', 'Absent'],
            datasets: [{
                data: [<?= $rates['present'] ?>, <?= $rates['late'] ?>, <?= $rates['absent'] ?>],
                backgroundColor: [
                    '#10b981', // Emerald 500
                    '#f59e0b', // Amber 500
                    '#ef4444'  // Red 500
                ],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 2500,
                easing: 'easeInOutBack'
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>