<div class="flex items-end justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Audit Logs</h2>
        <p class="text-slate-500 text-sm mt-1">Monitor system activities and user actions</p>
    </div>
    
    <button class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl shadow-lg transition-all flex items-center gap-2 font-semibold text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Export Log
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Timestamp</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Action</th>
                    <th class="px-6 py-4">Module</th>
                    <th class="px-6 py-4">Description</th>
                    <th class="px-6 py-4">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                
                <?php if(!empty($logs)): ?>
                    <?php foreach($logs as $log): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                            <?= date('M d, Y', strtotime($log->created_at)) ?><br>
                            <?= date('h:i:s A', strtotime($log->created_at)) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">
                                    <?= strtoupper(substr($log->username ?? '?', 0, 2)) ?>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700"><?= h($log->username ?? 'System') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                                $color = 'bg-blue-100 text-blue-700';
                                if($log->action == 'Create') $color = 'bg-green-100 text-green-700';
                                if($log->action == 'Delete') $color = 'bg-red-100 text-red-700';
                                if($log->action == 'Update') $color = 'bg-amber-100 text-amber-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?= $color ?>"><?= h($log->action) ?></span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-medium"><?= h($log->module) ?></td>
                        <td class="px-6 py-4 text-slate-500"><?= h($log->description) ?></td>
                        <td class="px-6 py-4 text-slate-400 font-mono text-xs"><?= h($log->ip_address) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">No logs found.</td></tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>