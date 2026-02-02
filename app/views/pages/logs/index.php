<div class="flex items-end justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Audit Logs</h2>
        <p class="text-slate-500 text-sm mt-1">Monitor system activities and user actions</p>
    </div>
    
    <a href="<?= URLROOT ?>/logs?export=1&<?= http_build_query($filters) ?>" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl shadow-lg transition-all flex items-center gap-2 font-semibold text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Export Log
    </a>
</div>

<div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
    <form method="GET" action="<?= URLROOT ?>/logs" class="flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="<?= h($filters['search']) ?>" placeholder="Search user, IP, or desc..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all">
        </div>
        
        <select name="role" class="px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer text-sm transition-all">
            <option value="">All Roles</option>
            <option value="Superadmin" <?= $filters['role'] == 'Superadmin' ? 'selected' : '' ?>>Superadmin</option>
            <option value="Admin" <?= $filters['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
            <option value="User" <?= $filters['role'] == 'User' ? 'selected' : '' ?>>User</option>
        </select>

        <select name="action" class="px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer text-sm transition-all">
            <option value="">All Actions</option>
            <option value="Login" <?= $filters['action'] == 'Login' ? 'selected' : '' ?>>Login</option>
            <option value="Create" <?= $filters['action'] == 'Create' ? 'selected' : '' ?>>Create</option>
            <option value="Update" <?= $filters['action'] == 'Update' ? 'selected' : '' ?>>Update</option>
            <option value="Delete" <?= $filters['action'] == 'Delete' ? 'selected' : '' ?>>Delete</option>
        </select>

        <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 rounded-xl px-2">
            <input type="date" name="start_date" value="<?= h($filters['start_date']) ?>" class="bg-transparent border-none py-2 px-2 text-slate-600 focus:ring-0 text-sm">
            <span class="text-slate-300 text-xs">to</span>
            <input type="date" name="end_date" value="<?= h($filters['end_date']) ?>" class="bg-transparent border-none py-2 px-2 text-slate-600 focus:ring-0 text-sm">
        </div>

        <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-100 text-sm font-semibold">
            Filter
        </button>
        
        <?php if(!empty(array_filter($filters))): ?>
            <a href="<?= URLROOT ?>/logs" class="text-slate-400 hover:text-slate-600 text-xs font-medium px-2">Clear</a>
        <?php endif; ?>
    </form>
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
                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-[10px]">
                                    <?= strtoupper(substr($log->username ?? '?', 0, 2)) ?>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700 leading-tight"><?= h($log->username ?? 'System') ?></p>
                                    <p class="text-[10px] text-slate-400"><?= h($log->user_role ?? 'N/A') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                                $color = 'bg-blue-100 text-blue-700';
                                if($log->action == 'Create') $color = 'bg-green-100 text-green-700';
                                if($log->action == 'Delete') $color = 'bg-red-100 text-red-700';
                                if($log->action == 'Update') $color = 'bg-amber-100 text-amber-700';
                                if($log->action == 'Login') $color = 'bg-indigo-100 text-indigo-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?= $color ?>"><?= h($log->action) ?></span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-medium"><?= h($log->module) ?></td>
                        <td class="px-6 py-4 text-slate-500 max-w-xs truncate" title="<?= h($log->description) ?>"><?= h($log->description) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>No activity logs found for the selected filters.</p>
                        </div>
                    </td></tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>