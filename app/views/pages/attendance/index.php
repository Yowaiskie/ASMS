<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Attendance Management</h2>
        <p class="text-slate-500 text-sm mt-1">Daily tracking for Mass and Meetings</p>
    </div>
    
    <form action="" method="GET" class="flex items-center gap-2 bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
        <input type="date" name="date" value="<?= $date ?>" class="px-4 py-2 bg-transparent text-sm font-bold text-slate-700 focus:outline-none" onchange="this.form.submit()">
    </form>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/50 text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-100">
                <th class="p-6 w-1/3">Server Name</th>
                <th class="p-6 text-center w-1/3">Sunday Schedule</th>
                <th class="p-6 text-center w-1/3">Meeting / Other</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 text-sm">
            <?php if (!empty($attendanceList)): ?>
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
                                    <?php renderStatusButtons($server['mass']->attendance_id, $server['mass']->status, $date); ?>
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
                                    <?php renderStatusButtons($server['meeting']->attendance_id, $server['meeting']->status, $date); ?>
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
</div>

<?php
function renderStatusButtons($id, $currentStatus, $currentDate) {
    // Redesigned modern status selector
    $statuses = [
        'Present' => ['label' => 'P', 'color' => 'bg-emerald-500', 'hover' => 'hover:bg-emerald-500/20', 'text' => 'text-emerald-600'],
        'Late' =>    ['label' => 'L', 'color' => 'bg-amber-500', 'hover' => 'hover:bg-amber-500/20', 'text' => 'text-amber-600'],
        'Absent' =>  ['label' => 'A', 'color' => 'bg-rose-500', 'hover' => 'hover:bg-rose-500/20', 'text' => 'text-rose-600']
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
            <input type='hidden' name='status' value='$status'>
            <input type='hidden' name='date' value='$currentDate'>
            <button type='submit' class='w-8 h-8 rounded-full font-extrabold text-xs transition-all duration-300 $btnClass transform active:scale-95' title='Mark as $status'>
                {$style['label']}
            </button>
        </form>
        ";
    }
}

// Helper for inline CSRF since we are inside a function
function csrf_field_inline() {
    $token = $_SESSION['csrf_token'] ?? '';
    return "<input type='hidden' name='csrf_token' value='$token'>";
}
?>