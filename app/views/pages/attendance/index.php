<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.2s ease-out forwards; }
</style>

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Attendance Management</h2>
        <p class="text-slate-500 text-sm mt-1">Daily tracking for Mass and Meetings</p>
    </div>
    
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <!-- View Toggle -->
        <?php if ($_SESSION['role'] !== 'Superadmin'): ?>
        <div class="bg-white p-1 rounded-xl border border-slate-200 shadow-sm flex w-full sm:w-auto">
            <a href="<?= URLROOT ?>/attendance?view=manage" class="flex-1 sm:px-4 py-2 rounded-lg text-xs font-bold transition-all text-center <?= (!isset($_GET['view']) || $_GET['view'] === 'manage') ? 'bg-slate-800 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50' ?>">
                Management
            </a>
            <a href="<?= URLROOT ?>/attendance?view=personal" class="flex-1 sm:px-4 py-2 rounded-lg text-xs font-bold transition-all text-center <?= (isset($_GET['view']) && $_GET['view'] === 'personal') ? 'bg-slate-800 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50' ?>">
                My History
            </a>
        </div>
        <?php endif; ?>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="<?= URLROOT ?>/attendance/downloadReport?date=<?= $date ?>" data-loading="Generating Report..." class="flex-1 sm:flex-none bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 font-bold text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Report
            </a>

            <form action="<?= URLROOT ?>/attendance" method="GET" class="flex items-center gap-2 flex-1 sm:flex-none">
                <input type="hidden" name="date" value="<?= $date ?>">
                <div class="relative flex-1">
                    <input type="text" name="search" value="<?= h($search ?? '') ?>" placeholder="Search..." class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 shadow-sm transition-all">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>
                
                <div class="bg-white p-1 rounded-xl border border-slate-200 shadow-sm shrink-0">
                    <input type="date" name="date" value="<?= $date ?>" class="px-2 py-2 bg-transparent text-sm font-bold text-slate-700 focus:outline-none" onchange="this.form.submit()">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border-2 border-slate-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
        <thead>
            <tr class="bg-slate-100 text-slate-600 text-[10px] uppercase font-black tracking-widest">
                <th class="p-4 border-b-2 border-r-2 border-slate-300 w-1/3">Server Name</th>
                <th class="p-4 border-b-2 border-r-2 border-slate-300 text-center w-1/3">Sunday Schedule</th>
                <th class="p-4 border-b-2 border-slate-300 text-center w-1/3">Meeting / Other</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            <?php if (!empty($attendanceList)): ?>
                <?php foreach($attendanceList as $server): ?>
                <tr class="group">
                    <td class="p-4 border-b-2 border-r-2 border-slate-300 bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-black text-[11px] border-2 border-slate-300">
                                <?= strtoupper(substr($server['name'], 0, 2)) ?>
                            </div>
                            <span class="font-black text-slate-800"><?= h($server['name']) ?></span>
                        </div>
                    </td>
                    
                    <!-- Mass Column -->
                    <td class="p-4 border-b-2 border-r-2 border-slate-300 bg-white">
                        <div class="flex flex-col items-center gap-4">
                            <?php if (!empty($server['masses'])): ?>
                                <?php foreach($server['masses'] as $mass): ?>
                                    <div class="flex flex-col items-center gap-2 pb-3 last:pb-0 border-b-2 border-slate-100 last:border-0 w-full">
                                        <div class="flex justify-center gap-2 p-1.5 bg-slate-100 rounded-full w-fit border-2 border-slate-200">
                                            <?php renderStatusButtons($mass->attendance_id, $mass->status, $date, $mass->schedule_id, $server['id']); ?>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-tight">
                                            <?= date('h:i A', strtotime($mass->mass_time)) ?> • <?= h($mass->mass_type) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full bg-slate-50 text-slate-400 text-[10px] font-black uppercase border-2 border-dashed border-slate-200">No Assignment</span>
                            <?php endif; ?>

                            <?php if (!empty($dailySchedules)): ?>
                                <button onclick="openManualAttendanceModal(<?= $server['id'] ?>, '<?= h($server['name']) ?>')" class="text-[10px] font-black text-primary hover:text-primary-700 transition-colors flex items-center gap-1 mt-1 bg-primary/5 px-3 py-1 rounded-lg border border-primary/20">
                                    <i class="ph-bold ph-plus-circle"></i> <?= !empty($server['masses']) ? 'Add Another Mass' : 'Add to Mass' ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>

                    <!-- Meeting Column -->
                    <td class="p-4 border-b-2 border-slate-300 bg-white">
                        <?php if ($server['meeting']): ?>
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex justify-center gap-2 p-1.5 bg-slate-100 rounded-full w-fit border-2 border-slate-200">
                                    <?php renderStatusButtons($server['meeting']->attendance_id, $server['meeting']->status, $date, $server['meeting']->schedule_id, $server['id']); ?>
                                </div>
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-tight">
                                    <?= h($server['meeting']->mass_type) ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <div class="flex justify-center">
                                <span class="px-3 py-1 rounded-full bg-slate-50 text-slate-400 text-[10px] font-black uppercase border-2 border-dashed border-slate-200">No Meeting</span>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="p-12 text-center text-slate-500 font-bold italic border-b-2 border-slate-300">No active servers found for this date.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>

    <!-- Pagination -->
    <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
    <div class="px-6 py-4 border-t border-slate-50 flex flex-col md:flex-row items-center justify-center gap-4 bg-slate-50/30">
        <div class="text-[10px] text-slate-500 order-2 md:order-1">
            Page <span class="font-bold"><?= $pagination['page'] ?></span> of <span class="font-bold"><?= $pagination['totalPages'] ?></span>
        </div>
        <div class="flex items-center gap-1.5 order-1 md:order-2">
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
                    $active = ($i == $pagination['page']) ? 'bg-primary text-white border-primary shadow-md shadow-primary-100' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 shadow-sm';
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

</div>

<!-- Manual Attendance Modal -->
<div id="manualAttendanceModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden animate-fade-in-up">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Assign to Mass</h3>
                <p class="text-[10px] text-slate-500 mt-0.5"><span id="modalServerName" class="font-bold text-primary"></span></p>
            </div>
            <button onclick="closeManualAttendanceModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="ph-bold ph-x text-base"></i>
            </button>
        </div>
        
        <div class="p-4">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Select Mass (<?= date('M d', strtotime($date)) ?>)</p>
            
            <div class="space-y-1.5 max-h-[240px] overflow-y-auto pr-1 custom-scrollbar">
                <?php foreach ($dailySchedules as $sch): ?>
                    <?php if (stripos($sch->mass_type, 'Meeting') === false): ?>
                        <form action="<?= URLROOT ?>/attendance/update" method="POST">
                            <?= csrf_field_inline() ?>
                            <input type="hidden" name="schedule_id" value="<?= $sch->id ?>">
                            <input type="hidden" id="modalServerId_<?= $sch->id ?>" name="server_id" value="">
                            <input type="hidden" name="status" value="Present">
                            <input type="hidden" name="date" value="<?= $date ?>">
                            
                            <button type="submit" class="w-full group flex items-center justify-between p-3 rounded-xl border border-slate-50 hover:border-primary-100 hover:bg-primary-50/30 transition-all text-left">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 group-hover:bg-primary-100 flex items-center justify-center text-slate-400 group-hover:text-primary transition-colors">
                                        <i class="ph-bold ph-calendar-check text-base"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-700 group-hover:text-primary-700 transition-colors"><?= h($sch->mass_type) ?></div>
                                        <div class="text-[9px] font-medium text-slate-400"><?= date('h:i A', strtotime($sch->mass_time)) ?></div>
                                    </div>
                                </div>
                                <i class="ph-bold ph-caret-right text-slate-200 group-hover:text-primary transition-all group-hover:translate-x-0.5"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="p-3 bg-slate-50/50 border-t border-slate-100 flex justify-end">
            <button onclick="closeManualAttendanceModal()" class="px-4 py-1.5 rounded-lg text-[11px] font-bold text-slate-500 hover:bg-slate-100 transition-all">
                Cancel
            </button>
        </div>
    </div>
</div>

    <script>
        function openManualAttendanceModal(serverId, serverName) {
            document.getElementById('modalServerName').textContent = serverName;
            // Set server ID for all forms inside the modal
            document.querySelectorAll('[id^="modalServerId_"]').forEach(input => {
                input.value = serverId;
            });
            
            const modal = document.getElementById('manualAttendanceModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeManualAttendanceModal() {
            const modal = document.getElementById('manualAttendanceModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeManualAttendanceModal();
        });

        // Close on backdrop click
        document.getElementById('manualAttendanceModal').addEventListener('click', (e) => {
            if (e.target.id === 'manualAttendanceModal') closeManualAttendanceModal();
        });

        // Old toggle function for backward compatibility if needed elsewhere
        function toggleAddDropdown(event, serverId) {
            event.preventDefault();
            event.stopPropagation();
        }
    </script>
<?php
function renderStatusButtons($id, $currentStatus, $currentDate, $scheduleId = null, $serverId = null) {
    // Redesigned modern status selector
    $statuses = [
        'Present' => ['label' => 'P', 'color' => 'bg-emerald-500', 'hover' => 'hover:bg-emerald-500/20', 'text' => 'text-emerald-600'],
        'Late' =>    ['label' => 'L', 'color' => 'bg-amber-500', 'hover' => 'hover:bg-amber-500/20', 'text' => 'text-amber-600'],
        'Absent' =>  ['label' => 'A', 'color' => 'bg-rose-500', 'hover' => 'hover:bg-rose-500/20', 'text' => 'text-rose-600'],
        'Excused' => ['label' => 'E', 'color' => 'bg-primary-500', 'hover' => 'hover:bg-primary-500/20', 'text' => 'text-primary']
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
