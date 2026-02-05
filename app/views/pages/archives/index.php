<div class="flex items-end justify-between mb-8 animate-fade-in-up">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Archive Center</h2>
        <p class="text-slate-500 text-sm mt-1">Restore or permanently delete removed accounts</p>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-up delay-100">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Account</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Deleted At</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                <?php if(!empty($archived)): ?>
                    <?php foreach($archived as $row): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs uppercase">
                                    <?= strtoupper(substr($row->username, 0, 2)) ?>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700"><?= h($row->username) ?></p>
                                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider"><?= h($row->first_name . ' ' . $row->last_name) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-blue-50 text-blue-600">
                                <?= h($row->role) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500 font-medium">
                            <?= date('M d, Y • h:i A', strtotime($row->deleted_at)) ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= URLROOT ?>/archives/restore/<?= $row->id ?>" 
                                   class="p-2.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-xl transition-all font-bold text-xs flex items-center gap-1.5 shadow-sm"
                                   title="Restore Account">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    Restore
                                </a>
                                
                                <button type="button" 
                                        onclick="showConfirm('WARNING: This will permanently delete the account and all associated attendance records. This action CANNOT be undone. Proceed?', 'Delete Permanently?', () => window.location.href='<?= URLROOT ?>/archives/delete/<?= $row->id ?>')"
                                        class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl transition-all font-bold text-xs flex items-center gap-1.5 shadow-sm"
                                        title="Delete Permanently">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="p-16 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400 opacity-40">
                                <svg class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                <p class="font-bold italic">The archive is empty.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>