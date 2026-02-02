<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Attendance Management</h2>
        <p class="text-slate-500 text-sm mt-1">Daily tracking for Mass and Meetings</p>
    </div>
    
    <form action="" method="GET" class="flex items-center gap-2 bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
        <input type="date" name="date" value="<?= $date ?>" class="px-4 py-2 bg-transparent text-sm font-bold text-slate-700 focus:outline-none" onchange="this.form.submit()">
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <th class="p-4 font-semibold w-1/4">Server Name</th>
                <th class="p-4 font-semibold text-center w-1/4">Serve (Mass)</th>
                <th class="p-4 font-semibold text-center w-1/4">Meeting</th>
                <th class="p-4 font-semibold text-center w-1/4">Others</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
            <?php if (!empty($attendanceList)): ?>
                <?php foreach($attendanceList as $server): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4 font-bold text-slate-700">
                        <?= h($server['name']) ?>
                    </td>
                    
                    <!-- Mass Column -->
                    <td class="p-4 text-center">
                        <?php if ($server['mass']): ?>
                            <div class="flex justify-center gap-1">
                                <?php renderStatusButtons($server['mass']->attendance_id, $server['mass']->status); ?>
                            </div>
                            <div class="text-[10px] text-slate-400 mt-1"><?= date('h:i A', strtotime($server['mass']->mass_time)) ?></div>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs italic">No Schedule</span>
                        <?php endif; ?>
                    </td>

                    <!-- Meeting Column -->
                    <td class="p-4 text-center">
                        <?php if ($server['meeting']): ?>
                            <div class="flex justify-center gap-1">
                                <?php renderStatusButtons($server['meeting']->attendance_id, $server['meeting']->status); ?>
                            </div>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs italic">No Meeting</span>
                        <?php endif; ?>
                    </td>

                    <!-- Others / Actions -->
                    <td class="p-4 text-center">
                        <button class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                            + Add Service
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-400">No active servers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
function renderStatusButtons($id, $currentStatus) {
    // Helper to render P/L/A buttons
    $statuses = [
        'Present' => ['label' => 'P', 'class' => 'bg-green-100 text-green-700 hover:bg-green-200', 'active' => 'bg-green-500 text-white shadow-md shadow-green-200 ring-2 ring-green-500 ring-offset-1'],
        'Late' =>    ['label' => 'L', 'class' => 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200', 'active' => 'bg-yellow-500 text-white shadow-md shadow-yellow-200 ring-2 ring-yellow-500 ring-offset-1'],
        'Absent' =>  ['label' => 'A', 'class' => 'bg-red-100 text-red-700 hover:bg-red-200', 'active' => 'bg-red-500 text-white shadow-md shadow-red-200 ring-2 ring-red-500 ring-offset-1']
    ];

    foreach ($statuses as $status => $style) {
        $isActive = ($currentStatus === $status);
        $css = $isActive ? $style['active'] : $style['class'];
        
        echo "
        <form action='".URLROOT."/attendance/update' method='POST' class='inline'>
            <input type='hidden' name='attendance_id' value='$id'>
            <input type='hidden' name='status' value='$status'>
            <button type='submit' class='w-8 h-8 rounded-full font-bold text-xs transition-all duration-200 $css' title='$status'>
                {$style['label']}
            </button>
        </form>
        ";
    }
}
?>