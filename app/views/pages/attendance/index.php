<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Attendance Management</h2>
    <p class="text-slate-500 text-sm mt-1">Mark and manage altar server attendance</p>
</div>

<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Select Activity</label>
            <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer">
                <option>Sunday Mass</option>
                <option>Formation Training</option>
                <option>Special Event</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Date</label>
            <input type="date" value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Time</label>
            <input type="time" value="08:00" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

    </form>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-green-50/50 border border-green-200 p-6 rounded-2xl">
        <p class="text-xs font-medium text-slate-500 mb-1">Present</p>
        <p class="text-3xl font-bold text-green-600">5</p>
    </div>
    <div class="bg-yellow-50/50 border border-yellow-200 p-6 rounded-2xl">
        <p class="text-xs font-medium text-slate-500 mb-1">Late</p>
        <p class="text-3xl font-bold text-yellow-600">1</p>
    </div>
    <div class="bg-red-50/50 border border-red-200 p-6 rounded-2xl">
        <p class="text-xs font-medium text-slate-500 mb-1">Absent</p>
        <p class="text-3xl font-bold text-red-600">2</p>
    </div>
</div>

<div>
    <h3 class="font-bold text-slate-700 mb-4">Mark Attendance</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        
        <?php if (!empty($logs)): ?>
            <?php foreach($logs as $log): ?>
                <?php 
                    $borderColor = 'border-slate-100';
                    $btnPresentClass = 'bg-white border border-slate-200 text-slate-600 hover:bg-green-50 hover:text-green-600 hover:border-green-200';
                    $btnLateClass = 'bg-white border border-slate-200 text-slate-600 hover:bg-yellow-50 hover:text-yellow-600 hover:border-yellow-200';
                    $btnAbsentClass = 'bg-white border border-slate-200 text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200';

                    if ($log->status == 'Present') {
                        $borderColor = 'border-green-500 border-2';
                        $btnPresentClass = 'bg-green-500 text-white shadow-md shadow-green-200';
                    } elseif ($log->status == 'Late') {
                        $borderColor = 'border-yellow-500 border-2';
                        $btnLateClass = 'bg-yellow-500 text-white shadow-md shadow-yellow-200';
                    } elseif ($log->status == 'Absent') {
                        $borderColor = 'border-red-500 border-2';
                        $btnAbsentClass = 'bg-red-500 text-white shadow-md shadow-red-200';
                    }
                ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm transition-all <?= $borderColor ?>">
                    <div class="mb-4">
                        <h4 class="font-bold text-slate-800 text-lg"><?= h($log->server_name) ?></h4>
                        <p class="text-slate-400 text-xs">Mass: <?= h($log->mass_time) ?></p>
                    </div>
                    
                    <form action="<?= URLROOT ?>/attendance/update" method="POST" class="flex gap-2">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="attendance_id" value="<?= $log->id ?>">
                        
                        <button type="submit" name="status" value="Present" class="flex-1 py-2 rounded-lg text-sm font-medium transition-all <?= $btnPresentClass ?>">Present</button>
                        <button type="submit" name="status" value="Late" class="flex-1 py-2 rounded-lg text-sm font-medium transition-all <?= $btnLateClass ?>">Late</button>
                        <button type="submit" name="status" value="Absent" class="flex-1 py-2 rounded-lg text-sm font-medium transition-all <?= $btnAbsentClass ?>">Absent</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-slate-500">No logs found.</p>
        <?php endif; ?>

    </div>
</div>

<div class="fixed bottom-6 right-6 z-40">
    <button class="bg-primary hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow-lg hover:scale-105 transition-all flex items-center gap-2 font-bold">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
        </svg>
        Save Changes
    </button>
</div>