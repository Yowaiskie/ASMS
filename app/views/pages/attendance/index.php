<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Attendance Management</h2>
        <p class="text-slate-500 text-sm mt-1">Daily tracking for Mass and Meetings</p>
    </div>
    
    <div class="flex items-center gap-2">
        <a href="<?= URLROOT ?>/attendance/downloadReport?date=<?= $date ?>" data-loading class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Download Report
        </a>

        <form action="<?= URLROOT ?>/attendance" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="date" value="<?= $date ?>">
            <div class="relative">
                <input type="text" name="search" value="<?= h($search ?? '') ?>" placeholder="Search server..." class="pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm w-64 transition-all">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            
            <div class="flex items-center gap-2 bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
                <input type="date" name="date" value="<?= $date ?>" class="px-4 py-2 bg-transparent text-sm font-bold text-slate-700 focus:outline-none" onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <!-- ... existing thead ... -->
        <thead>
            <tr class="bg-slate-50/50 text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-100">
                <th class="p-6 w-1/3">Server Name</th>
                <th class="p-6 text-center w-1/3">Sunday Schedule</th>
                <th class="p-6 text-center w-1/3">Meeting / Other</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 text-sm">
            <?php if (!empty($attendanceList)): ?>
                <!-- ... existing rows ... -->
                <?php foreach($attendanceList as $server): ?>
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs">
                                <?= strtoupper(substr($server['name'], 0, 2)) ?>
                            </div>
                            <span class="font-bold text-slate-700"><?= h($server['name']) ?></span>
                        </div>
                    </td>
                    
                    <!-- Mass Column -->
                    <td class="p-6">
                        <?php if ($server['mass']): ?>
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex justify-center gap-2 p-1 bg-slate-100 rounded-full w-fit">
                                    <?php renderStatusButtons($server['mass']->attendance_id, $server['mass']->status, $date, $server['mass']->schedule_id, $server['id']); ?>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                    <?= date('h:i A', strtotime($server['mass']->mass_time)) ?> • <?= h($server['mass']->mass_type) ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <div class="flex justify-center">
                                <span class="px-3 py-1 rounded-full bg-slate-50 text-slate-300 text-[10px] font-bold uppercase border border-dashed border-slate-200">No Assignment</span>
                            </div>
                        <?php endif; ?>
                    </td>

                    <!-- Meeting Column -->
                    <td class="p-6">
                        <?php if ($server['meeting']): ?>
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex justify-center gap-2 p-1 bg-slate-100 rounded-full w-fit">
                                    <?php renderStatusButtons($server['meeting']->attendance_id, $server['meeting']->status, $date, $server['meeting']->schedule_id, $server['id']); ?>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                    <?= h($server['meeting']->mass_type) ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <div class="flex justify-center">
                                <span class="px-3 py-1 rounded-full bg-slate-50 text-slate-300 text-[10px] font-bold uppercase border border-dashed border-slate-200">No Meeting</span>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="p-12 text-center text-slate-400 italic">No active servers found for this date.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
    <div class="px-6 py-4 border-t border-slate-50 flex items-center justify-between bg-slate-50/30">
        <div class="text-xs text-slate-500">
            Page <span class="font-bold"><?= $pagination['page'] ?></span> of <span class="font-bold"><?= $pagination['totalPages'] ?></span>
        </div>
        <div class="flex items-center gap-1.5">
            <?php 
                $baseUrl = URLROOT . "/attendance?date=$date&search=" . urlencode($search);
            ?>
            
            <?php if ($pagination['page'] > 1): ?>
                <a href="<?= $baseUrl ?>&page=<?= $pagination['page'] - 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 shadow-sm transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
                </a>
            <?php endif; ?>

            <?php 
                $start = max(1, $pagination['page'] - 2);
                $end = min($pagination['totalPages'], $start + 4);
                if ($end - $start < 4) $start = max(1, $end - 4);
                
                for($i = $start; $i <= $end; $i++): 
                    $active = ($i == $pagination['page']) ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-100' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 shadow-sm';
            ?>
                <a href="<?= $baseUrl ?>&page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center border rounded-lg text-xs font-bold transition-all <?= $active ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($pagination['page'] < $pagination['totalPages']): ?>
                <a href="<?= $baseUrl ?>&page=<?= $pagination['page'] + 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 shadow-sm transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
function renderStatusButtons($id, $currentStatus, $currentDate, $scheduleId = null, $serverId = null) {
    // Redesigned modern status selector
    $statuses = [
        'Present' => ['label' => 'P', 'color' => 'bg-emerald-500', 'hover' => 'hover:bg-emerald-500/20', 'text' => 'text-emerald-600'],
        'Late' =>    ['label' => 'L', 'color' => 'bg-amber-500', 'hover' => 'hover:bg-amber-500/20', 'text' => 'text-amber-600'],
        'Absent' =>  ['label' => 'A', 'color' => 'bg-rose-500', 'hover' => 'hover:bg-rose-500/20', 'text' => 'text-rose-600'],
        'Excused' => ['label' => 'E', 'color' => 'bg-blue-500', 'hover' => 'hover:bg-blue-500/20', 'text' => 'text-blue-600']
    ];

    foreach ($statuses as $status => $style) {
        $isActive = ($currentStatus === $status);
        $btnClass = $isActive 
            ? "{$style['color']} text-white shadow-lg scale-110" 
            : "bg-white {$style['text']} {$style['hover']} scale-90 opacity-60 hover:opacity-100 shadow-sm";
        
            echo "
            <form action='".URLROOT."/attendance/update' method='POST' class='inline'>
                " . csrf_field_inline() . "
                <input type='hidden' name='attendance_id' value='$id'>
                <input type='hidden' name='schedule_id' value='$scheduleId'>
                <input type='hidden' name='server_id' value='$serverId'>
                <input type='hidden' name='status' value='$status'>
                <input type='hidden' name='date' value='$currentDate'>
                <input type='hidden' name='page' value='" . ($_GET['page'] ?? 1) . "'>
                <input type='hidden' name='search' value='" . htmlspecialchars($_GET['search'] ?? '') . "'>
                <button type='submit' class='w-8 h-8 rounded-full font-extrabold text-xs transition-all duration-300 $btnClass transform active:scale-95' title='Mark as $status'>
                    {$style['label']}
                </button>
            </form>
            ";    }
}

// Helper for inline CSRF since we are inside a function
function csrf_field_inline() {
    $token = $_SESSION['csrf_token'] ?? '';
    return "<input type='hidden' name='csrf_token' value='$token'>";
}
?>