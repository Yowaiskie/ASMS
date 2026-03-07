<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">System Configuration</h2>
    <p class="text-slate-500 text-sm mt-1 font-medium">Manage global branding, policies, and lookup data from one place.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
    
    <!-- 1. Parish Profile Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 flex flex-col h-full">
        <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center text-2xl mb-6">
            <i class="ph-bold ph-buildings"></i>
        </div>
        <h3 class="text-xl font-black text-slate-800">Parish Profile</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed flex-1">
            Update the system display name, parish name, and official administrator email address used for notifications.
        </p>
        <div class="mt-6 pt-6 border-t border-slate-50 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= h($parish_name) ?></span>
            <button onclick="openModal('profileModal')" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-black transition-all">Edit Profile</button>
        </div>
    </div>

    <!-- 2. System Policies Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 flex flex-col h-full">
        <div class="w-14 h-14 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-2xl mb-6">
            <i class="ph-bold ph-shield-check"></i>
        </div>
        <h3 class="text-xl font-black text-slate-800">Attendance Policies</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed flex-1">
            Configure automated rules for server suspensions, late-to-absent ratios, and excuse filing deadlines.
        </p>
        <div class="mt-6 pt-6 border-t border-slate-50 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Threshold: <?= $policy_suspension_threshold ?> Absences</span>
            <button onclick="openModal('policyModal')" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-black transition-all">Manage Rules</button>
        </div>
    </div>

    <!-- 3. Scheduling Policies Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 flex flex-col h-full">
        <div class="w-14 h-14 bg-blue-50 text-primary rounded-2xl flex items-center justify-center text-2xl mb-6">
            <i class="ph-bold ph-calendar-check"></i>
        </div>
        <h3 class="text-xl font-black text-slate-800">Scheduling Policies</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed flex-1">
            Define default durations for master plans, recurring assignment propagation, and schedule generation rules.
        </p>
        <div class="mt-6 pt-6 border-t border-slate-50 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate mr-2">
                Period: <?= date('M j', strtotime($policy_schedule_start_date)) ?> - <?= date('M j, Y', strtotime($policy_schedule_end_date)) ?>
            </span>
            <button onclick="openModal('schedulePolicyModal')" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-black transition-all shrink-0">Manage Config</button>
        </div>
    </div>

    <!-- 4. Server Ranks Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 flex flex-col h-full">
        <div class="w-14 h-14 bg-teal-50 text-teal-500 rounded-2xl flex items-center justify-center text-2xl mb-6">
            <i class="ph-bold ph-medal"></i>
        </div>
        <h3 class="text-xl font-black text-slate-800">Server Ranks</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed flex-1">
            Define hierarchy levels for altar servers (e.g., Junior, Senior, Master of Ceremonies) to organize the ministry.
        </p>
        <div class="mt-6 pt-6 border-t border-slate-50 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= count($ranks) ?> Ranks Active</span>
            <button onclick="openModal('rankModal')" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-black transition-all">Manage Ranks</button>
        </div>
    </div>

    <!-- 5. Announcement Categories Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 flex flex-col h-full">
        <div class="w-14 h-14 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center text-2xl mb-6">
            <i class="ph-bold ph-megaphone"></i>
        </div>
        <h3 class="text-xl font-black text-slate-800">Announcement Categories</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed flex-1">
            Create categories like "General", "Urgent", or "Training" to help servers filter through ministry updates.
        </p>
        <div class="mt-6 pt-6 border-t border-slate-50 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= count($categories) ?> Categories</span>
            <button onclick="openModal('categoryModal')" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-black transition-all">Manage Categories</button>
        </div>
    </div>
</div>

<!-- MODAL: Parish Profile -->
<div id="profileModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-black text-slate-800 text-xl text-center">Edit Parish Profile</h3>
            <button onclick="closeModal('profileModal')" class="text-slate-400 hover:text-slate-600"><i class="ph-bold ph-x text-xl"></i></button>
        </div>
        <form action="<?= URLROOT ?>/settings/system/update" method="POST" class="p-8 space-y-5">
            <?php csrf_field(); ?>
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">System Display Name</label>
                <input type="text" name="system_name" value="<?= h($system_name) ?>" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Parish Name</label>
                <input type="text" name="parish_name" value="<?= h($parish_name) ?>" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Admin Email</label>
                <input type="email" name="admin_email" value="<?= h($admin_email) ?>" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black text-sm shadow-xl shadow-primary/20 transition-all active:scale-[0.98]">Save Changes</button>
        </form>
    </div>
</div>

<!-- MODAL: Policies -->
<div id="policyModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="font-black text-slate-800 text-lg">Attendance Policy</h3>
                <p class="text-[10px] text-slate-500 font-medium uppercase tracking-tight">Rules and Penalties</p>
            </div>
            <button onclick="closeModal('policyModal')" class="p-2 text-slate-400 hover:text-slate-600 transition-colors"><i class="ph-bold ph-x text-lg"></i></button>
        </div>
        <form action="<?= URLROOT ?>/settings/system/update" method="POST" class="p-6 space-y-5">
            <?php csrf_field(); ?>
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Warning (Abs)</label>
                    <input type="number" name="policy_suspension_warning" value="<?= $policy_suspension_warning ?>" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/10">
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Suspension (Abs)</label>
                    <input type="number" name="policy_suspension_threshold" value="<?= $policy_suspension_threshold ?>" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/10">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Duration (Days)</label>
                    <input type="number" name="policy_suspension_duration" value="<?= $policy_suspension_duration ?>" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold text-slate-700 focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Late Ratio (X:1)</label>
                    <input type="number" name="policy_late_to_absent_ratio" value="<?= $policy_late_to_absent_ratio ?>" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold text-slate-700 focus:outline-none">
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Excuse Deadline (Hours)</label>
                <input type="number" name="policy_excuse_lead_time" value="<?= $policy_excuse_lead_time ?>" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold text-slate-700 focus:outline-none">
            </div>

            <div class="space-y-2">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 text-center block">Monitored Activities</label>
                <div class="grid grid-cols-2 gap-2 max-h-28 overflow-y-auto custom-scrollbar p-1">
                    <?php foreach($activityTypes as $type): ?>
                        <label class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg cursor-pointer border border-transparent has-[:checked]:border-primary/20 has-[:checked]:bg-primary/5 transition-all">
                            <input type="checkbox" name="policy_activity_types[]" value="<?= h($type->name) ?>" <?= in_array($type->name, $policy_suspension_activity_types ?? []) ? 'checked' : '' ?> class="w-3.5 h-3.5 rounded text-primary focus:ring-primary/20">
                            <span class="text-[11px] font-bold text-slate-600 truncate"><?= h($type->name) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl flex items-center justify-between">
                <p class="text-[10px] font-bold text-white">Auto-Removal From Schedules</p>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="policy_auto_remove_on_suspension" value="1" <?= ($policy_auto_remove_on_suspension ?? 1) ? 'checked' : '' ?> class="sr-only peer">
                    <div class="w-9 h-5 bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                </label>
            </div>
            <button type="submit" class="w-full bg-primary text-white py-3.5 rounded-xl font-black text-xs shadow-lg shadow-primary/20 active:scale-[0.98] transition-all">Save All Rules</button>
        </form>
    </div>
</div>

<!-- MODAL: Scheduling Policy -->
<div id="schedulePolicyModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="font-black text-slate-800 text-lg">Scheduling Policy</h3>
                <p class="text-[10px] text-slate-500 font-medium uppercase tracking-tight">Recurring & Master Plan Rules</p>
            </div>
            <button onclick="closeModal('schedulePolicyModal')" class="p-2 text-slate-400 hover:text-slate-600 transition-colors"><i class="ph-bold ph-x text-lg"></i></button>
        </div>
        <form action="<?= URLROOT ?>/settings/system/update" method="POST" class="p-6 space-y-6">
            <?php csrf_field(); ?>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Start Date</label>
                        <input type="date" name="policy_schedule_start_date" value="<?= h($policy_schedule_start_date) ?>" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">End Date</label>
                        <input type="date" name="policy_schedule_end_date" value="<?= h($policy_schedule_end_date) ?>" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                    </div>
                </div>
                <div class="p-4 bg-primary/5 rounded-2xl border border-primary/10 flex items-start gap-3">
                    <i class="ph-bold ph-info text-primary mt-0.5"></i>
                    <p class="text-[10px] text-slate-600 leading-relaxed">
                        Define the **active period** for the current master plan. Recurring assignments and auto-fill operations will operate within this date range.
                    </p>
                </div>
            </div>

            <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black text-xs shadow-lg shadow-primary-200 active:scale-[0.98] transition-all">Update Scheduling Rules</button>
        </form>
    </div>
</div>

<!-- MODAL: Server Ranks -->
<div id="rankModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-black text-slate-800 text-xl">Manage Server Ranks</h3>
            <button onclick="closeModal('rankModal')" class="text-slate-400"><i class="ph-bold ph-x text-xl"></i></button>
        </div>
        <div class="p-8">
            <div class="space-y-3 mb-8 max-h-60 overflow-y-auto custom-scrollbar pr-2">
                <?php foreach($ranks as $rank): ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <span class="text-sm font-bold text-slate-700 uppercase tracking-widest"><?= h($rank->name) ?></span>
                        <button type="button" onclick="showConfirm('Delete this rank?', 'Confirm Delete', () => window.location.href='<?= URLROOT ?>/settings/rank/delete/<?= $rank->id ?>')" class="text-red-400 hover:opacity-80 transition-all"><i class="ph-bold ph-trash text-lg"></i></button>
                    </div>
                <?php endforeach; ?>
            </div>
            <form action="<?= URLROOT ?>/settings/rank/store" method="POST" class="pt-4 border-t border-slate-50 space-y-4">
                <?php csrf_field(); ?>
                <input type="text" name="name" required placeholder="Rank Name (e.g. Master)..." class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none">
                <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black text-sm">Add New Rank</button>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Categories -->
<div id="categoryModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-black text-slate-800 text-xl">Announcement Categories</h3>
            <button onclick="closeModal('categoryModal')" class="text-slate-400"><i class="ph-bold ph-x text-xl"></i></button>
        </div>
        <div class="p-8">
            <div class="space-y-3 mb-8 max-h-60 overflow-y-auto custom-scrollbar pr-2">
                <?php foreach($categories as $cat): ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <span class="text-sm font-bold text-slate-700"><?= h($cat->name) ?></span>
                        <button type="button" onclick="showConfirm('Delete this category?', 'Confirm Delete', () => window.location.href='<?= URLROOT ?>/settings/category/delete/<?= $cat->id ?>')" class="text-red-400 hover:opacity-80 transition-all"><i class="ph-bold ph-trash text-lg"></i></button>
                    </div>
                <?php endforeach; ?>
            </div>
            <form action="<?= URLROOT ?>/settings/category/store" method="POST" class="pt-4 border-t border-slate-50 space-y-4">
                <?php csrf_field(); ?>
                <input type="text" name="name" required placeholder="Category Name..." class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none">
                <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black text-sm">Add Category</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close on backdrop click
    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            event.target.classList.add('hidden');
            event.target.classList.remove('flex');
        }
    }
</script>
