<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Reports</h2>
        <p class="text-slate-500 text-sm mt-1">View attendance summaries and analytics</p>
    </div>
    
    <div class="flex gap-3">
        <button class="bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-red-200 transition-all flex items-center gap-2 text-sm font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Export PDF
        </button>
        <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-200 transition-all flex items-center gap-2 text-sm font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export Excel
        </button>
    </div>
</div>

<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
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
    <div class="bg-green-500 rounded-2xl p-6 text-white shadow-lg shadow-green-200">
        <p class="text-white/80 text-sm font-medium mb-1">Total Present</p>
        <h3 class="text-4xl font-bold mb-2">540</h3>
        <p class="text-xs bg-white/20 inline-block px-2 py-1 rounded-lg">88.8% rate</p>
    </div>
    <div class="bg-amber-500 rounded-2xl p-6 text-white shadow-lg shadow-amber-200">
        <p class="text-white/80 text-sm font-medium mb-1">Late</p>
        <h3 class="text-4xl font-bold mb-2">38</h3>
        <p class="text-xs bg-white/20 inline-block px-2 py-1 rounded-lg">6.3% rate</p>
    </div>
    <div class="bg-red-600 rounded-2xl p-6 text-white shadow-lg shadow-red-200">
        <p class="text-white/80 text-sm font-medium mb-1">Absent</p>
        <h3 class="text-4xl font-bold mb-2">30</h3>
        <p class="text-xs bg-white/20 inline-block px-2 py-1 rounded-lg">4.9% rate</p>
    </div>
    <div class="bg-blue-500 rounded-2xl p-6 text-white shadow-lg shadow-blue-200">
        <p class="text-white/80 text-sm font-medium mb-1">Total Activities</p>
        <h3 class="text-4xl font-bold mb-2">24</h3>
        <p class="text-xs bg-white/20 inline-block px-2 py-1 rounded-lg">This month</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-700 mb-6">Monthly Attendance Trend</h3>
        <div class="h-64">
            <canvas id="attendanceBarChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
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
        <h3 class="font-bold text-slate-700">Server Performance</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold text-slate-500 uppercase bg-slate-50/50">
                    <th class="px-6 py-4">Server Name</th>
                    <th class="px-6 py-4 w-1/3">Attendance Rate</th>
                    <th class="px-6 py-4">Total Activities</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-700">John Doe</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: 95%"></div>
                            </div>
                            <span class="text-xs font-bold text-emerald-600">95%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">20</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Excellent</span>
                    </td>
                </tr>
                
                <!-- More rows here (truncated for brevity but design is captured) -->
                 <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-700">Jane Smith</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: 92%"></div>
                            </div>
                            <span class="text-xs font-bold text-emerald-600">92%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">19</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Excellent</span>
                    </td>
                </tr>

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
            labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
            datasets: [
                {
                    label: 'Present',
                    data: [85, 90, 88, 92, 87, 90],
                    backgroundColor: '#10b981', // Emerald 500
                    borderRadius: 4,
                    barPercentage: 0.6,
                }
            ]
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
                    max: 100,
                    grid: { borderDash: [2, 2] }
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
                data: [88.8, 6.3, 4.9],
                backgroundColor: [
                    '#10b981', // Emerald 500
                    '#f59e0b', // Amber 500
                    '#ef4444'  // Red 500
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>