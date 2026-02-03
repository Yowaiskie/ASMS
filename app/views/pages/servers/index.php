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
        <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm relative group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
            </svg>
            Import CSV
            <!-- ... tooltip ... -->
        </button>
        <button onclick="openModal('add')" class="bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center gap-2 font-semibold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Server
        </button>
    </div>
</div>

<!-- ... Import Modal ... -->

<div class="relative">
    <form action="<?= URLROOT ?>/servers/bulk-delete" method="POST" id="bulkDeleteForm">
        <?php csrf_field(); ?>
        
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-up delay-100 relative">
            <!-- Selection Bar -->
            <div id="selectionBar" class="hidden absolute top-0 left-0 right-0 z-20 bg-blue-600 text-white p-4 flex justify-between items-center shadow-md">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-sm" id="selectedCount">0 Selected</span>
                    <div class="h-4 w-px bg-blue-400"></div>
                    <button type="button" onclick="selectAll(true)" class="text-xs hover:underline">Select All</button>
                    <button type="button" onclick="toggleSelectionMode()" class="text-xs hover:underline">Cancel</button>
                </div>
                <button type="submit" onclick="return confirm('Delete selected servers?')" class="bg-red-500 text-white hover:bg-red-600 px-4 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
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
                                <!-- ... rest of columns ... -->
                                <td class="px-6 py-4 text-slate-600 font-medium">
                                    <?= h($svr->nickname) ?>
                                </td>
                                <td class="px-6 py-4 text-slate-500 leading-relaxed">
                                    <?= h($svr->address) ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <?= $svr->dob ? date('M d, Y', strtotime($svr->dob)) : '-' ?>
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    <?= h($svr->phone) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-blue-600"><?= h($svr->rank) ?></span>
                                        <span class="text-[10px] text-slate-400 font-medium uppercase"><?= h($svr->position) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick='openModal("edit", <?= json_encode($svr) ?>)' class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors action-btn"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                                        <a href="<?= URLROOT ?>/servers/delete?id=<?= $svr->id ?>" onclick="return confirm('Delete this server?')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors action-btn"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="p-12 text-center text-slate-400 italic">No servers found in directory.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
    
            <!-- Pagination -->
            <!-- ... -->
        </div>
    </form>
</div>

<!-- ... Modal ... -->

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

    // ... existing scripts ...
</script>

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

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Full Name</label>
                        <input type="text" name="name" id="svr_name" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Nickname</label>
                            <input type="text" name="nickname" id="svr_nickname" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Age</label>
                            <input type="number" name="age" id="svr_age" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
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
    const modal = document.getElementById('serverModal');
    const content = document.getElementById('modalContent');

    function openModal(mode, data = null) {
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        
        if (mode === 'add') {
            document.getElementById('modalTitle').innerText = 'Register New Server';
            document.getElementById('serverForm').reset();
            document.getElementById('serverId').value = '';
        } else {
            document.getElementById('modalTitle').innerText = 'Edit Server Profile';
            document.getElementById('serverId').value = data.id;
            document.getElementById('svr_name').value = data.name;
            document.getElementById('svr_nickname').value = data.nickname || '';
            document.getElementById('svr_dob').value = data.dob || '';
            document.getElementById('svr_age').value = data.age || '';
            document.getElementById('svr_phone').value = data.phone || '';
            document.getElementById('svr_address').value = data.address || '';
            document.getElementById('svr_rank').value = data.rank || '';
            document.getElementById('svr_joined').value = data.month_joined || '';
            document.getElementById('svr_invest').value = data.investiture_date || '';
            document.getElementById('svr_order').value = data.order_name || '';
            document.getElementById('svr_position').value = data.position || '';
            document.getElementById('svr_status').value = data.status;
        }
    }

    function closeModal() {
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // Drag and Drop Logic
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileNameDisplay = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitImport');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    });

    ['dragleave', 'drop'].forEach(event => {
        dropZone.addEventListener(event, () => {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });

    fileInput.addEventListener('change', handleFileSelect);

    function handleFileSelect() {
        if (fileInput.files.length) {
            fileNameDisplay.textContent = fileInput.files[0].name;
            fileInfo.classList.remove('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-slate-100', 'text-slate-400');
            submitBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-200');
        }
    }
</script>