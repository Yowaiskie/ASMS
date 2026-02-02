<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
        Welcome, <?= h($_SESSION['username']) ?>! 
        <span class="text-2xl">👋</span>
    </h2>
    <p class="text-slate-500 text-sm mt-1">Here is your performance overview</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Next Schedule -->
    <div class="bg-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-200 animate-fade-in-up relative overflow-hidden flex flex-col justify-between min-h-[200px]">
        
        <div>
            <div class="flex items-center gap-2 mb-4 opacity-90">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-bold uppercase tracking-wider">Next Schedule</span>
            </div>
            
            <?php if($nextSchedule): ?>
                <h3 class="text-2xl font-bold mb-1"><?= h($nextSchedule->mass_type) ?></h3>
                <p class="text-blue-100 text-sm mb-4"><?= date('l, F d, Y', strtotime($nextSchedule->mass_date)) ?> • <?= date('h:i A', strtotime($nextSchedule->mass_time)) ?></p>
            <?php else: ?>
                <h3 class="text-xl font-bold mb-1 opacity-80">No upcoming schedules</h3>
                <p class="text-blue-100 text-sm mb-4">Enjoy your break!</p>
            <?php endif; ?>
        </div>

        <div>
            <a href="<?= URLROOT ?>/schedules" class="inline-block bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg text-xs font-bold transition-all">
                View All Schedules
            </a>
        </div>
    </div>

    <!-- Mass Performance -->
    <div class="bg-green-600 p-6 rounded-3xl shadow-lg shadow-green-200 animate-fade-in-up delay-100 flex flex-col items-center justify-center text-center text-white">
        <div class="w-16 h-16 rounded-full bg-white/20 text-white flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <span class="text-4xl font-bold"><?= $stats['massRate'] ?? 0 ?>%</span>
        <p class="text-xs font-bold opacity-80 uppercase tracking-wider mt-1">Mass Attendance</p>
        <p class="text-xs opacity-90 mt-2">
            Present: <span class="font-bold"><?= $stats['massPresent'] ?? 0 ?></span> / <?= $stats['massTotal'] ?? 0 ?>
        </p>
    </div>

    <!-- Meeting Performance -->
    <div class="bg-purple-600 p-6 rounded-3xl shadow-lg shadow-purple-200 animate-fade-in-up delay-200 flex flex-col items-center justify-center text-center text-white">
        <div class="w-16 h-16 rounded-full bg-white/20 text-white flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <span class="text-4xl font-bold"><?= $stats['meetingRate'] ?? 0 ?>%</span>
        <p class="text-xs font-bold opacity-80 uppercase tracking-wider mt-1">Meeting Attendance</p>
        <p class="text-xs opacity-90 mt-2">
            Present: <span class="font-bold"><?= $stats['meetingPresent'] ?? 0 ?></span> / <?= $stats['meetingTotal'] ?? 0 ?>
        </p>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- Performance Chart -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 animate-fade-in-up delay-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-slate-800">Activity History</h3>
        </div>
        
        <div class="h-64 w-full">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <!-- Recent Announcements -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 animate-fade-in-up delay-300 flex flex-col">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-700">Latest Announcements</h3>
        </div>

        <div class="space-y-3 flex-1 overflow-y-auto max-h-64">
            <?php if(!empty($announcements)): ?>
                <?php foreach($announcements as $ann): ?>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-slate-800 text-xs mb-1"><?= h($ann->title) ?></h4>
                        <p class="text-slate-500 text-[10px] line-clamp-2"><?= h($ann->message) ?></p>
                    </div>
                    <span class="text-[9px] text-slate-400 whitespace-nowrap ml-2"><?= date('M d', strtotime($ann->created_at)) ?></span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-slate-400 text-sm text-center py-4">No announcements yet.</p>
            <?php endif; ?>
        </div>
        
        <div class="mt-4 pt-4 border-t border-slate-50 text-center">
            <a href="<?= URLROOT ?>/announcements" class="text-xs font-bold text-blue-600 hover:text-blue-700">View All Announcements</a>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartData['labels'] ?? []) ?>,
                datasets: [
                    {
                        label: 'Mass Served',
                        data: <?= json_encode($chartData['mass'] ?? []) ?>,
                        backgroundColor: '#10b981', // emerald-500
                        borderRadius: 6,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Meetings Attended',
                        data: <?= json_encode($chartData['meeting'] ?? []) ?>,
                        backgroundColor: '#8b5cf6', // purple-500
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { size: 10 }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false,
                        },
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>