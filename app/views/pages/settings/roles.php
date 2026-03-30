<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight"><?= $pageTitle ?></h2>
        <p class="text-slate-500 text-sm mt-1">Manage system roles and their module-based permissions.</p>
    </div>
    <div class="flex items-center gap-3">
        <?php if (hasPermission('Roles Management', 'create')): ?>
            <button onclick="openAddRoleModal()" class="flex items-center gap-2 bg-primary hover:bg-primary-600 text-white px-4 py-2 rounded-xl transition-all font-bold text-sm shadow-sm shadow-primary/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Role
            </button>
        <?php endif; ?>
    </div>
</div>

<?php flash('msg_success'); ?>
<?php flash('msg_error'); ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Roles List -->
    <div class="lg:col-span-4 space-y-4">
        <?php foreach ($roles as $role): ?>
            <div onclick="selectRole(<?= htmlspecialchars(json_encode($role)) ?>)" 
                 id="role-card-<?= $role->id ?>"
                 class="role-card p-4 bg-white border border-slate-100 rounded-2xl cursor-pointer hover:border-primary/30 transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-slate-800 group-hover:text-primary transition-colors"><?= h($role->name) ?></h3>
                    <?php if (!in_array($role->name, ['Superadmin', 'Admin', 'User'])): ?>
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="event.stopPropagation(); deleteRole(<?= $role->id ?>, '<?= h($role->name) ?>')" class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-slate-500 line-clamp-2"><?= h($role->description ?? 'No description provided.') ?></p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <?= count($role->permissions) ?> Permissions
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Permissions Grid -->
    <div class="lg:col-span-8">
        <div id="permissions-container" class="bg-white border border-slate-100 rounded-2xl p-6 hidden">
            <form action="<?= URLROOT ?>/settings/roles/update" method="POST" id="role-permissions-form">
                <?php csrf_field(); ?>
                <input type="hidden" name="id" id="edit-role-id">
                
                <div class="mb-6 flex items-center justify-between border-b border-slate-50 pb-4">
                    <div>
                        <input type="text" name="name" id="edit-role-name" class="text-xl font-bold text-slate-800 border-none p-0 focus:ring-0 w-full" placeholder="Role Name">
                        <textarea name="description" id="edit-role-description" class="text-sm text-slate-500 border-none p-0 focus:ring-0 w-full mt-1 resize-none" placeholder="Description"></textarea>
                    </div>
                    <?php if (hasPermission('Roles Management', 'edit')): ?>
                        <button type="submit" class="bg-primary hover:bg-primary-600 text-white px-6 py-2 rounded-xl transition-all font-bold text-sm">
                            Save Changes
                        </button>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($groupedPermissions as $module => $perms): ?>
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary/40"></span>
                                <?= h($module) ?>
                            </h4>
                            <div class="bg-slate-50/50 rounded-xl p-3 space-y-2">
                                <?php foreach ($perms as $p): ?>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" name="permissions[]" value="<?= $p->id ?>" 
                                                   data-perm-id="<?= $p->id ?>"
                                                   class="permission-checkbox w-4.5 h-4.5 rounded border-slate-300 text-primary focus:ring-primary/20 transition-all cursor-pointer">
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-slate-700 group-hover:text-slate-900 transition-colors">
                                                <?= ucwords(str_replace('_', ' ', $p->action)) ?>
                                            </span>
                                            <span class="text-[10px] text-slate-400">Can <?= strtolower(str_replace('_', ' ', $p->action)) ?> <?= strtolower($module) ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>

        <!-- Empty State -->
        <div id="permissions-empty" class="bg-slate-50/50 border-2 border-dashed border-slate-200 rounded-2xl p-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h3 class="text-slate-800 font-bold">Select a Role</h3>
            <p class="text-slate-500 text-sm mt-1 max-w-xs">Select a role from the list to view and manage its permissions.</p>
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div id="addRoleModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <form action="<?= URLROOT ?>/settings/roles/store" method="POST">
            <?php csrf_field(); ?>
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-xl font-bold text-slate-800 tracking-tight">Add New Role</h3>
                <button type="button" onclick="closeAddRoleModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18" /></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Role Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm outline-none" placeholder="e.g. Moderator">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm outline-none resize-none" placeholder="What can this role do?"></textarea>
                </div>
            </div>
            <div class="p-6 bg-slate-50 flex items-center gap-3">
                <button type="button" onclick="closeAddRoleModal()" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-white transition-all">Cancel</button>
                <button type="submit" class="flex-1 bg-primary hover:bg-primary-600 text-white px-4 py-3 rounded-xl font-bold text-sm transition-all shadow-sm shadow-primary/20">Create Role</button>
            </div>
        </form>
    </div>
</div>

<script>
    function selectRole(role) {
        // Toggle card styles
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('border-primary', 'bg-primary/5', 'shadow-sm'));
        const selectedCard = document.getElementById('role-card-' + role.id);
        selectedCard.classList.add('border-primary', 'bg-primary/5', 'shadow-sm');

        // Show permissions container
        document.getElementById('permissions-empty').classList.add('hidden');
        document.getElementById('permissions-container').classList.remove('hidden');

        // Fill form
        document.getElementById('edit-role-id').value = role.id;
        document.getElementById('edit-role-name').value = role.name;
        document.getElementById('edit-role-description').value = role.description || '';

        // Disable name editing for system roles
        const isSystemRole = ['Superadmin', 'Admin', 'User'].includes(role.name);
        document.getElementById('edit-role-name').readOnly = isSystemRole;
        if (isSystemRole) {
            document.getElementById('edit-role-name').classList.add('opacity-70');
        } else {
            document.getElementById('edit-role-name').classList.remove('opacity-70');
        }

        // Set permissions
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(cb => {
            const permId = parseInt(cb.getAttribute('data-perm-id'));
            cb.checked = role.permissions.includes(permId);
            
            // Superadmin permissions are read-only
            if (role.name === 'Superadmin') {
                cb.disabled = true;
            } else {
                cb.disabled = false;
            }
        });
    }

    function openAddRoleModal() {
        document.getElementById('addRoleModal').classList.remove('hidden');
    }

    function closeAddRoleModal() {
        document.getElementById('addRoleModal').classList.add('hidden');
    }

    function deleteRole(id, name) {
        showConfirm(`Are you sure you want to delete the role "${name}"? This will affect users assigned to this role.`, 'Delete Role', function() {
            window.location.href = `<?= URLROOT ?>/settings/roles/delete/${id}`;
        });
    }

    // Auto-select first role on load if available
    window.onload = function() {
        const firstCard = document.querySelector('.role-card');
        if (firstCard) {
            // Wait a bit to ensure animations or other scripts are ready
            setTimeout(() => firstCard.click(), 100);
        }
    };
</script>
