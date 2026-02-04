<div class="flex items-end justify-between mb-8 animate-fade-in-up">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Altar Servers Directory</h2>
        <p class="text-slate-500 text-sm mt-1">Manage server profiles and master list</p>
    </div>
    
    <div class="flex gap-2">
        <button type="button" onclick="toggleSelectionMode()" id="selectModeBtn" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 p-2.5 rounded-xl shadow-sm transition-all" title="Select Multiple">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>

        <a href="<?= URLROOT ?>/servers/download" data-loading class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Download PDF
        </a>
        <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
            </svg>
            Import CSV
        </button>
        <button onclick="openModal('add')" class="bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center gap-2 font-semibold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Server
        </button>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Bulk Import Servers</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>

        <form action="<?= URLROOT ?>/servers/import" method="POST" enctype="multipart/form-data" id="importForm">
            <?php csrf_field(); ?>
            
            <div class="mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-[10px] text-slate-600">
                <strong class="block mb-2 text-slate-700 uppercase tracking-widest text-[9px]">Required CSV Format (In Order):</strong>
                <code class="block bg-white p-2 rounded border border-slate-200 leading-relaxed break-all">
                    First Name, Middle Name, Last Name, Nickname, Address, DOB (YYYY-MM-DD), Age, Phone, Joined (YYYY-MM), Investiture (YYYY-MM-DD), Order, Position, Rank, Team, Status, Email
                </code>
                <p class="mt-2 text-slate-400 italic font-medium">* 16 columns total. Ensure the column order is exact.</p>
            </div>

            <div id="dropZone" class="border-2 border-dashed border-slate-200 rounded-3xl p-10 flex flex-col items-center justify-center gap-4 hover:border-blue-400 hover:bg-blue-50 transition-all cursor-pointer group">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-700">Drop your CSV file here</p>
                    <p class="text-xs text-slate-400 mt-1">or click to browse</p>
                </div>
                <input type="file" name="csv_file" id="fileInput" class="hidden" accept=".csv">
            </div>
            
            <div id="fileInfo" class="mt-4 p-4 bg-blue-50 rounded-2xl border border-blue-100 hidden">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span id="fileName" class="text-xs font-bold text-blue-800 truncate">file.csv</span>
                </div>
            </div>

            <button type="submit" id="submitImport" disabled class="w-full mt-6 py-4 bg-slate-100 text-slate-400 rounded-2xl font-bold text-sm transition-all">
                Upload and Import
            </button>
        </form>
    </div>
</div>

<div class="relative">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-up delay-100 relative">
        <!-- Selection Bar -->
        <div id="selectionBar" class="hidden absolute top-0 left-0 right-0 z-20 bg-blue-600 text-white p-4 flex justify-between items-center shadow-md">
            <div class="flex items-center gap-3">
                <span class="font-bold text-sm" id="selectedCount">0 Selected</span>
                <div class="h-4 w-px bg-blue-400"></div>
                <button type="button" onclick="selectAll(true)" class="text-xs hover:underline">Select All</button>
                <button type="button" onclick="toggleSelectionMode()" class="text-xs hover:underline">Cancel</button>
            </div>
            <button type="button" onclick="submitBulkDelete()" class="bg-red-500 text-white hover:bg-red-600 px-4 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                Delete Selected
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-[10px] font-bold text-slate-500 uppercase border-b border-slate-100">
                    <tr>
                        <th class="p-4 w-12 selection-col hidden"></th>
                        <th class="px-6 py-4">Full Name</th>
                        <th class="px-6 py-4">Nickname</th>
                        <th class="px-6 py-4 w-1/4">Address</th>
                        <th class="px-6 py-4">Birth Date</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Rank/Pos</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <?php if(!empty($servers)): ?>
                        <?php foreach($servers as $svr): ?>
                        <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="toggleRow(this, event)">
                            <td class="p-4 selection-col hidden">
                                <input type="checkbox" name="ids[]" value="<?= $svr->id ?>" class="server-checkbox rounded text-blue-600 border-gray-300 w-5 h-5 pointer-events-none">
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-700 block"><?= h($svr->name) ?></span>
                                <span class="text-[10px] text-slate-400">Joined: <?= h($svr->month_joined) ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium"><?= h($svr->nickname) ?></td>
                            <td class="px-6 py-4 text-slate-500 leading-relaxed"><?= h($svr->address) ?></td>
                            <td class="px-6 py-4 text-slate-600"><?= $svr->dob ? date('M d, Y', strtotime($svr->dob)) : '-' ?></td>
                            <td class="px-6 py-4 text-slate-500"><?= h($svr->phone) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-blue-600"><?= h($svr->rank) ?></span>
                                    <span class="text-[10px] text-slate-400 font-medium uppercase"><?= h($svr->position) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick='openModal("edit", <?= json_encode($svr) ?>)' class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors action-btn" title="Edit Profile"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                                    
                                                                    <?php if($svr->status === 'Suspended'): ?>
                                                                        <form action="<?= URLROOT ?>/servers/update-status" method="POST" class="inline action-btn">
                                                                            <?php csrf_field(); ?>
                                                                            <input type="hidden" name="id" value="<?= $svr->id ?>">
                                                                            <input type="hidden" name="action" value="unsuspend">
                                                                            <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
                                                                            <button type="submit" data-loading title="Unsuspend Server" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                                                                                <i class="ph ph-shield-check text-lg"></i>
                                                                            </button>
                                                                        </form>
                                                                    <?php else: ?>
                                                                        <form action="<?= URLROOT ?>/servers/update-status" method="POST" id="suspend-server-<?= $svr->id ?>" class="inline action-btn">
                                                                            <?php csrf_field(); ?>
                                                                            <input type="hidden" name="id" value="<?= $svr->id ?>">
                                                                            <input type="hidden" name="action" value="suspend">
                                                                            <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
                                                                            <button type="button" onclick="showConfirm('Suspend this server for 30 days?', 'Suspend Server', () => document.getElementById('suspend-server-<?= $svr->id ?>').submit())" data-loading title="Suspend Server" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                                                                <i class="ph ph-hand-palm text-lg"></i>
                                                                            </button>
                                                                        </form>
                                                                    <?php endif; ?>
                                    
                                                                    <button type="button" onclick="showConfirm('Are you sure you want to delete this server profile?', 'Delete Server', () => window.location.href='<?= URLROOT ?>/servers/delete?id=<?= $svr->id ?>&page=<?= $pagination['page'] ?? 1 ?>')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors action-btn" title="Delete Server"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                                                </div>
                                                            </td>                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="p-12 text-center text-slate-400 italic">No servers found in directory.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="text-xs text-slate-500">
                Page <span class="font-bold"><?= $pagination['page'] ?></span> of <span class="font-bold"><?= $pagination['totalPages'] ?></span>
            </div>
            <div class="flex items-center gap-1.5">
                <?php if ($pagination['page'] > 1): ?>
                    <a href="<?= URLROOT ?>/servers?page=<?= $pagination['page'] - 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
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
                    <a href="<?= URLROOT ?>/servers?page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center border rounded-lg text-xs font-bold transition-all <?= $active ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($pagination['page'] < $pagination['totalPages']): ?>
                    <a href="<?= URLROOT ?>/servers?page=<?= $pagination['page'] + 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bulk Delete Hidden Form -->
            <form action="<?= URLROOT ?>/servers/bulk-delete" method="POST" id="hiddenBulkDeleteForm" class="hidden">
                <?php csrf_field(); ?>
                <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
                <div id="bulkIdInputs"></div>
            </form>
<!-- Modal -->
<div id="serverModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]" id="modalContent">
        <div class="p-6 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Register New Server</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>

            <form action="<?= URLROOT ?>/servers/store" method="POST" id="serverForm" class="space-y-4">
                <?php csrf_field(); ?>
                <input type="hidden" name="id" id="serverId">
                <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">

                <div class="grid grid-cols-1 gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">First Name</label>
                            <input type="text" name="first_name" id="svr_fname" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Middle Name</label>
                            <input type="text" name="middle_name" id="svr_mname" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Last Name</label>
                            <input type="text" name="last_name" id="svr_lname" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Nickname</label>
                            <input type="text" name="nickname" id="svr_nickname" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Age</label>
                            <input type="text" name="age" id="svr_age" maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0,2)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Date of Birth</label>
                            <input type="date" name="dob" id="svr_dob" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Contact</label>
                            <input type="text" name="phone" id="svr_phone" maxlength="11" pattern="\d{11}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="09xxxxxxxxx" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Home Address</label>
                        <textarea name="address" id="svr_address" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm"></textarea>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Rank</label>
                            <input type="text" name="rank" id="svr_rank" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Order</label>
                            <input type="text" name="order_name" id="svr_order" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Position</label>
                            <input type="text" name="position" id="svr_position" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Joined</label>
                            <input type="text" name="month_joined" id="svr_joined" placeholder="2023-07" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Status</label>
                            <select name="status" id="svr_status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex gap-2">
                    <button type="button" onclick="closeModal()" class="flex-1 py-3 border border-slate-200 rounded-xl text-slate-600 font-bold text-sm">Cancel</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg text-sm">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let isSelectionMode = false;

    function toggleSelectionMode() {
        isSelectionMode = !isSelectionMode;
        const btn = document.getElementById('selectModeBtn');
        const bar = document.getElementById('selectionBar');
        const cols = document.querySelectorAll('.selection-col');
        
        if (isSelectionMode) {
            btn.classList.add('bg-blue-50', 'text-blue-600', 'border-blue-200', 'ring-2', 'ring-blue-200');
            bar.classList.remove('hidden');
            cols.forEach(col => col.classList.remove('hidden'));
        } else {
            btn.classList.remove('bg-blue-50', 'text-blue-600', 'border-blue-200', 'ring-2', 'ring-blue-200');
            bar.classList.add('hidden');
            cols.forEach(col => col.classList.add('hidden'));
            selectAll(false);
        }
    }

    function toggleRow(tr, event) {
        if (!isSelectionMode) return;
        if (event.target.closest('.action-btn')) return;
        
        const cb = tr.querySelector('.server-checkbox');
        cb.checked = !cb.checked;
        tr.classList.toggle('bg-blue-50', cb.checked);
        updateSelectedCount();
    }

    function selectAll(check) {
        document.querySelectorAll('.server-checkbox').forEach(cb => {
            cb.checked = check;
            cb.closest('tr').classList.toggle('bg-blue-50', check);
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.server-checkbox:checked').length;
        document.getElementById('selectedCount').innerText = `${count} Selected`;
    }

    function submitBulkDelete() {
        const checkboxes = document.querySelectorAll('.server-checkbox:checked');
        if (checkboxes.length === 0) {
            showAlert('No servers selected.');
            return;
        }

        showConfirm(`Are you sure you want to delete ${checkboxes.length} selected servers?`, 'Bulk Delete', function() {
            const container = document.getElementById('bulkIdInputs');
            container.innerHTML = ''; 
            
            checkboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });

            document.getElementById('hiddenBulkDeleteForm').submit();
        });
    }