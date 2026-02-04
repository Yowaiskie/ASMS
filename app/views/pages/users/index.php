<div class="flex items-end justify-between mb-8 animate-fade-in-up">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">User Management</h2>
        <p class="text-slate-500 text-sm mt-1">Manage system access and roles</p>
    </div>
    
    <div class="flex gap-2">
        <button type="button" onclick="toggleSelectionMode()" id="selectModeBtn" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 p-2.5 rounded-xl shadow-sm transition-all" title="Select Multiple">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>

        <button type="button" onclick="toggleForm()" class="bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center gap-2 font-semibold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add User
        </button>
    </div>
</div>

<!-- Create User Form -->
<div id="createUserForm" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8 hidden animate-fade-in-up">
    <h3 class="text-lg font-bold text-slate-800 mb-6">Create New User</h3>
    <form action="<?= URLROOT ?>/users/store" method="POST" class="space-y-6">
        <?php csrf_field(); ?>
        <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">First Name</label>
                <input type="text" name="first_name" required placeholder="First Name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Middle Name</label>
                <input type="text" name="middle_name" placeholder="Middle Name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Last Name</label>
                <input type="text" name="last_name" required placeholder="Last Name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Username</label>
                <input type="text" name="username" required placeholder="Choose a username" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Email Address</label>
                <input type="email" name="email" placeholder="Enter email address (Optional)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Contact Number</label>
                <input type="text" name="phone" maxlength="11" pattern="\d{11}" placeholder="09xxxxxxxxx (Optional)" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Password</label>
                <input type="password" name="password" value="<?php echo DEFAULT_USER_PASSWORD; ?>" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Role</label>
                <select name="role" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                    <option value="Superadmin">Superadmin</option>
                </select>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all">Create Account</button>
            <button type="button" onclick="toggleForm()" class="px-8 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all">Cancel</button>
        </div>
    </form>
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
                <thead class="bg-slate-50/50 text-xs font-bold text-slate-500 uppercase border-b border-slate-100">
                    <tr>
                        <th class="p-4 w-12 selection-col hidden"></th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Created At</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    <?php if(!empty($users)): ?>
                        <?php foreach($users as $user): ?>
                        <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="toggleRow(this, event)">
                            <td class="p-4 selection-col hidden">
                                <input type="checkbox" name="ids[]" value="<?= $user->id ?>" class="user-checkbox rounded text-blue-600 border-gray-300 w-5 h-5 pointer-events-none">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                        <?= strtoupper(substr($user->username, 0, 2)) ?>
                                    </div>
                                    <span class="font-bold text-slate-700"><?= h($user->username) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                    $roleClass = 'bg-blue-100 text-blue-600';
                                    if($user->role === 'Admin') $roleClass = 'bg-purple-100 text-purple-600';
                                    if($user->role === 'Superadmin') $roleClass = 'bg-pink-100 text-pink-600';
                                ?>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase <?= $roleClass ?>">
                                    <?= h($user->role) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                <?= date('M d, Y', strtotime($user->created_at)) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick='openEditModal(<?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>)' class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors action-btn"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                                    <form action="<?= URLROOT ?>/users/delete" method="POST" onsubmit="return confirm('Are you sure?')" class="inline action-btn">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?= $user->id ?>">
                                        <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
                                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-12 text-center text-slate-400 italic">No users found.</td>
                        </tr>
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
                    <a href="<?= URLROOT ?>/users?page=<?= $pagination['page'] - 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 shadow-sm transition-all">
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
                    <a href="<?= URLROOT ?>/users?page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center border rounded-lg text-xs font-bold transition-all <?= $active ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($pagination['page'] < $pagination['totalPages']): ?>
                    <a href="<?= URLROOT ?>/users?page=<?= $pagination['page'] + 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 shadow-sm transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bulk Delete Hidden Form -->
<form action="<?= URLROOT ?>/users/bulk-delete" method="POST" id="hiddenBulkDeleteForm" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
    <div id="bulkIdInputs"></div>
</form>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 transform scale-95 transition-transform duration-300" id="editModalContent">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Edit User</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form action="<?= URLROOT ?>/users/update" method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <input type="hidden" name="id" id="editUserId">
            <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">First Name</label>
                    <input type="text" name="first_name" id="editFirstName" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Middle Name</label>
                    <input type="text" name="middle_name" id="editMiddleName" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Last Name</label>
                    <input type="text" name="last_name" id="editLastName" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Username</label>
                <input type="text" id="editUsername" disabled class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">New Password (Optional)</label>
                <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Role</label>
                <select name="role" id="editUserRole" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                    <option value="Superadmin">Superadmin</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg">Update User</button>
                <button type="button" onclick="closeEditModal()" class="px-8 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl">Cancel</button>
            </div>
        </form>
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
        
        const cb = tr.querySelector('.user-checkbox');
        cb.checked = !cb.checked;
        tr.classList.toggle('bg-blue-50', cb.checked);
        updateSelectedCount();
    }

    function selectAll(check) {
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.checked = check;
            cb.closest('tr').classList.toggle('bg-blue-50', check);
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.user-checkbox:checked').length;
        document.getElementById('selectedCount').innerText = `${count} Selected`;
    }

    function submitBulkDelete() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('No users selected.');
            return;
        }

        if (confirm(`Are you sure you want to delete ${checkboxes.length} selected users?`)) {
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
        }
    }

    function toggleForm() {
        document.getElementById('createUserForm').classList.toggle('hidden');
    }

    function openEditModal(user) {
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editUsername').value = user.username;
        document.getElementById('editUserRole').value = user.role;
        document.getElementById('editFirstName').value = user.first_name || '';
        document.getElementById('editMiddleName').value = user.middle_name || '';
        document.getElementById('editLastName').value = user.last_name || '';
        const modal = document.getElementById('editUserModal');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); document.getElementById('editModalContent').classList.remove('scale-95'); }, 10);
    }

    function closeEditModal() {
        const modal = document.getElementById('editUserModal');
        modal.classList.add('opacity-0');
        document.getElementById('editModalContent').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
</script>