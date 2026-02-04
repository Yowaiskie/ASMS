<div class="flex items-end justify-between mb-8 animate-fade-in-up">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Manage Excuse Letters</h2>
        <p class="text-slate-500 text-sm mt-1">Review and action pending excuse requests</p>
    </div>
    
    <div class="flex gap-2">
        <button type="button" onclick="toggleSelectionMode()" id="selectModeBtn" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 p-2.5 rounded-xl shadow-sm transition-all" title="Select Multiple">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-up relative">
    <!-- Selection Bar -->
    <div id="selectionBar" class="hidden absolute top-0 left-0 right-0 z-20 bg-blue-600 text-white p-4 flex flex-col gap-2 shadow-md">
        <div class="flex justify-between items-center w-full">
            <div class="flex items-center gap-3">
                <span class="font-bold text-sm" id="selectedCount">0 Selected</span>
                <div class="h-4 w-px bg-blue-400"></div>
                <button type="button" id="mainSelectAllBtn" onclick="selectAll(true)" class="text-xs hover:underline">Select All on Page</button>
                <button type="button" onclick="toggleSelectionMode()" class="text-xs hover:underline">Cancel</button>
            </div>
            <button type="button" onclick="submitBulkDelete()" class="bg-red-500 text-white hover:bg-red-600 px-4 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                Delete Selected
            </button>
        </div>
        <div id="allPagesPrompt" class="hidden bg-blue-700/50 -mx-4 -mb-4 p-2 text-center text-[10px] font-medium border-t border-blue-500">
            All items on this page are selected. 
            <button type="button" onclick="toggleAllPages(true)" class="underline font-bold ml-1">Select all <span id="totalCountText">0</span> items in the system</button>
        </div>
        <div id="clearSelectionBtn" class="hidden bg-blue-700/50 -mx-4 -mb-4 p-2 text-center text-[10px] font-medium border-t border-blue-500">
            All <span id="totalSelectedText">0</span> items are selected.
            <button type="button" onclick="selectAll(false)" class="underline font-bold ml-1">Clear selection</button>
        </div>
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <th class="p-4 w-12 selection-col hidden"></th>
                <th class="p-6 font-semibold">Server Name</th>
                <th class="p-6 font-semibold">Activity & Date</th>
                <th class="p-6 font-semibold">Reason</th>
                <th class="p-6 font-semibold">Proof</th>
                <th class="p-6 font-semibold text-center">Status</th>
                <th class="p-6 font-semibold text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
            <?php if(!empty($excuses)): ?>
                <?php foreach($excuses as $exc): ?>
                <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="toggleRow(this, event)">
                    <td class="p-4 selection-col hidden">
                        <input type="checkbox" name="ids[]" value="<?= $exc->id ?>" class="excuse-checkbox rounded text-blue-600 border-gray-300 w-5 h-5 pointer-events-none">
                    </td>
                    <td class="p-6 font-bold text-slate-700">
                        <?= h($exc->server_name) ?>
                    </td>
                    <td class="p-6">
                        <div class="flex flex-col">
                            <span class="font-bold text-blue-600 text-[10px] uppercase"><?= h($exc->type) ?></span>
                            <span class="text-slate-700 font-medium"><?= date('M d, Y', strtotime($exc->absence_date)) ?></span>
                            <span class="text-[10px] text-slate-400"><?= h($exc->absence_time) ?></span>
                        </div>
                    </td>
                    <td class="p-6">
                        <p class="text-slate-600 max-w-xs line-clamp-2" title="<?= h($exc->reason) ?>"><?= h($exc->reason) ?></p>
                    </td>
                    <td class="p-6">
                        <?php if($exc->image_path): ?>
                            <a href="<?= URLROOT ?>/uploads/excuses/<?= $exc->image_path ?>" target="_blank" class="text-blue-500 hover:underline flex items-center gap-1 font-bold text-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                View Image
                            </a>
                        <?php else: ?>
                            <span class="text-slate-300 italic text-xs">No proof</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-6 text-center">
                        <?php 
                            $statusColor = 'bg-yellow-50 text-yellow-600';
                            if($exc->status == 'Approved') $statusColor = 'bg-green-50 text-green-600';
                            if($exc->status == 'Rejected') $statusColor = 'bg-red-50 text-red-600';
                        ?>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?= $statusColor ?>">
                            <?= h($exc->status) ?>
                        </span>
                    </td>
                    <td class="p-6">
                        <div class="flex justify-center gap-2">
                            <button onclick='viewExcuse(<?= json_encode($exc) ?>)' class="bg-blue-50 text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition-all" title="View Details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                            <?php if($exc->status === 'Pending'): ?>
                            <form action="<?= URLROOT ?>/excuses/update-status" method="POST">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= $exc->id ?>">
                                <input type="hidden" name="status" value="Approved">
                                <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg shadow-sm transition-all" title="Approve">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </form>
                            <form action="<?= URLROOT ?>/excuses/update-status" method="POST">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= $exc->id ?>">
                                <input type="hidden" name="status" value="Rejected">
                                <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow-sm transition-all" title="Reject">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="p-12 text-center text-slate-400 italic">No excuse letters found in the system.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
<div class="mt-6 flex items-center justify-between">
    <div class="text-xs text-slate-500">
        Page <span class="font-bold"><?= $pagination['page'] ?></span> of <span class="font-bold"><?= $pagination['totalPages'] ?></span>
    </div>
    <div class="flex items-center gap-1.5">
        <?php if ($pagination['page'] > 1): ?>
            <a href="<?= URLROOT ?>/excuses?page=<?= $pagination['page'] - 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
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
            <a href="<?= URLROOT ?>/excuses?page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center border rounded-lg text-xs font-bold transition-all <?= $active ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($pagination['page'] < $pagination['totalPages']): ?>
            <a href="<?= URLROOT ?>/excuses?page=<?= $pagination['page'] + 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Bulk Delete Hidden Form -->
<form action="<?= URLROOT ?>/excuses/bulk-delete" method="POST" id="hiddenBulkDeleteForm" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
    <div id="bulkIdInputs"></div>
</form>

<!-- View Excuse Modal -->
<div id="viewModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[85vh]" id="modalContent">
        <!-- Scrollable Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span id="modalStatus" class="px-2 py-1 rounded-full text-[10px] font-bold uppercase mb-2 inline-block"></span>
                    <h3 id="modalName" class="text-lg font-bold text-slate-800">Server Name</h3>
                    <p id="modalDate" class="text-xs text-slate-500">Date and Time</p>
                </div>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Reason for Absence</label>
                    <div id="modalReason" style="word-break: break-word; overflow-wrap: anywhere;" class="text-slate-700 leading-relaxed text-sm bg-slate-50 p-4 rounded-2xl border border-slate-100 whitespace-pre-wrap w-full max-w-full overflow-hidden">
                        Full reason goes here...
                    </div>
                </div>

                <div id="modalProofContainer">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Attached Proof</label>
                    <div class="bg-slate-100 rounded-2xl overflow-hidden flex items-center justify-center border border-slate-100 shadow-sm">
                        <img id="modalImage" src="" class="max-w-full max-h-[250px] w-auto h-auto object-contain block mx-auto">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Footer for Actions -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-2" id="modalActions">
            <!-- Action buttons will be injected here -->
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('viewModal');
    const content = document.getElementById('modalContent');

    let isSelectionMode = false;
    let allPagesSelected = false;
    const totalRecords = <?= $pagination['totalRecords'] ?? 0 ?>;

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
        if (event.target.closest('button') || event.target.closest('a') || event.target.closest('form')) return;
        
        const cb = tr.querySelector('.excuse-checkbox');
        cb.checked = !cb.checked;
        tr.classList.toggle('bg-blue-50', cb.checked);
        
        if (allPagesSelected) {
            allPagesSelected = false; // Deselect "All Pages" if a single row is toggled
        }
        updateSelectedCount();
    }

    function selectAll(check) {
        allPagesSelected = false; 
        document.querySelectorAll('.excuse-checkbox').forEach(cb => {
            cb.checked = check;
            cb.closest('tr').classList.toggle('bg-blue-50', check);
        });
        updateSelectedCount();
    }

    function toggleAllPages(value) {
        allPagesSelected = value;
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.excuse-checkbox:checked');
        const count = checkboxes.length;
        const countDisplay = document.getElementById('selectedCount');
        const prompt = document.getElementById('allPagesPrompt');
        const clearBtn = document.getElementById('clearSelectionBtn');
        
        if (allPagesSelected) {
            countDisplay.innerText = `All ${totalRecords} items selected`;
            prompt.classList.add('hidden');
            clearBtn.classList.remove('hidden');
            document.getElementById('totalSelectedText').innerText = totalRecords;
        } else {
            countDisplay.innerText = `${count} Selected`;
            clearBtn.classList.add('hidden');
            
            const totalOnPage = document.querySelectorAll('.excuse-checkbox').length;
            if (count === totalOnPage && totalRecords > totalOnPage) {
                prompt.classList.remove('hidden');
                document.getElementById('totalCountText').innerText = totalRecords;
            } else {
                prompt.classList.add('hidden');
            }
        }
    }

    function submitBulkDelete() {
        const checkboxes = document.querySelectorAll('.excuse-checkbox:checked');
        if (checkboxes.length === 0 && !allPagesSelected) {
            alert('No letters selected.');
            return;
        }

        const count = allPagesSelected ? totalRecords : checkboxes.length;
        if (confirm(`Are you sure you want to delete ${count} selected excuse letters?`)) {
            const form = document.getElementById('hiddenBulkDeleteForm');
            const container = document.getElementById('bulkIdInputs');
            container.innerHTML = ''; 
            
            if (allPagesSelected) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'all_selected';
                input.value = '1';
                container.appendChild(input);
            } else {
                checkboxes.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    container.appendChild(input);
                });
            }

            form.submit();
        }
    }

    function viewExcuse(data) {
        document.getElementById('modalName').innerText = data.server_name;
        document.getElementById('modalDate').innerText = `${data.absence_date} at ${data.absence_time}`;
        document.getElementById('modalReason').innerText = data.reason;
        
        const statusEl = document.getElementById('modalStatus');
        statusEl.innerText = data.status;
        statusEl.className = `px-2 py-1 rounded-full text-[10px] font-bold uppercase mb-2 inline-block `;
        if(data.status === 'Pending') statusEl.classList.add('bg-yellow-50', 'text-yellow-600');
        else if(data.status === 'Approved') statusEl.classList.add('bg-green-50', 'text-green-600');
        else statusEl.classList.add('bg-red-50', 'text-red-600');

        const img = document.getElementById('modalImage');
        const container = document.getElementById('modalProofContainer');
        if(data.image_path) {
            img.src = `<?= URLROOT ?>/uploads/excuses/${data.image_path}`;
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }

        // Action Buttons
        const actions = document.getElementById('modalActions');
        actions.innerHTML = '';
        if(data.status === 'Pending') {
            actions.innerHTML = `
                <form action="<?= URLROOT ?>/excuses/update-status" method="POST" class="flex-1">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="id" value="${data.id}">
                    <input type="hidden" name="status" value="Rejected">
                    <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
                    <button type="submit" class="w-full py-3 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition-all">Disapprove</button>
                </form>
                <form action="<?= URLROOT ?>/excuses/update-status" method="POST" class="flex-1">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="id" value="${data.id}">
                    <input type="hidden" name="status" value="Approved">
                    <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
                    <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">Approve Request</button>
                </form>
            `;
        } else {
            actions.innerHTML = `<button onclick="closeModal()" class="w-full py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-all">Close Details</button>`;
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeModal() {
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>