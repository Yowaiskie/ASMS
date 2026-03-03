<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">System Configuration</h2>
    <p class="text-slate-500 text-sm mt-1 font-medium">Manage global system branding and lookup lists.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Left Column: Branding & Categories -->
    <div class="space-y-8">
        <!-- Parish Branding Card -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-3">
                <i class="ph-bold ph-buildings text-primary"></i>
                Parish Profile
            </h3>
            <form action="<?= URLROOT ?>/settings/system/update" method="POST" class="space-y-5">
                <?php csrf_field(); ?>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">System Name</label>
                    <input type="text" name="system_name" value="<?= h($system_name) ?>" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Parish Name</label>
                    <input type="text" name="parish_name" value="<?= h($parish_name) ?>" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Admin Email Address</label>
                    <input type="email" name="admin_email" value="<?= h($admin_email) ?>" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                </div>
                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold text-sm hover:bg-black transition-all">Update Profile</button>
            </form>
        </div>

        <!-- Announcement Categories -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-3">
                <i class="ph-bold ph-megaphone text-orange-500"></i>
                Announcement Categories
            </h3>
            <div class="space-y-3 mb-6">
                <?php foreach($categories as $cat): ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl group transition-all">
                        <span class="text-sm font-bold text-slate-700"><?= h($cat->name) ?></span>
                        <a href="<?= URLROOT ?>/settings/category/delete/<?= $cat->id ?>" onclick="return confirm('Delete this category?')" class="p-2 text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all">
                            <i class="ph-bold ph-trash-simple text-lg"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <form action="<?= URLROOT ?>/settings/category/store" method="POST" class="flex gap-3">
                <?php csrf_field(); ?>
                <input type="text" name="name" required placeholder="New Category..." class="flex-1 px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none">
                <button type="submit" class="bg-primary text-white px-5 rounded-2xl font-bold"><i class="ph-bold ph-plus"></i></button>
            </form>
        </div>
    </div>

    <!-- Right Column: Activity Types & Ranks -->
    <div class="space-y-8">
        <!-- Activity Types -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-3">
                <i class="ph-bold ph-calendar text-indigo-500"></i>
                Mass / Activity Types
            </h3>
            <div class="grid grid-cols-1 gap-3 mb-6">
                <?php foreach($activityTypes as $type): ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl group transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-<?= $type->default_color ?>-500"></div>
                            <span class="text-sm font-bold text-slate-700"><?= h($type->name) ?></span>
                        </div>
                        <a href="<?= URLROOT ?>/settings/activity-type/delete/<?= $type->id ?>" onclick="return confirm('Delete this activity type?')" class="p-2 text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all">
                            <i class="ph-bold ph-trash-simple text-lg"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <form action="<?= URLROOT ?>/settings/activity-type/store" method="POST" class="space-y-4">
                <?php csrf_field(); ?>
                <div class="flex gap-3">
                    <input type="text" name="name" required placeholder="New Mass Type (e.g. Wedding)..." class="flex-1 px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none">
                    <select name="color" class="px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-500">
                        <option value="blue">Blue</option>
                        <option value="green">Green</option>
                        <option value="red">Red</option>
                        <option value="purple">Purple</option>
                        <option value="yellow">Yellow</option>
                        <option value="indigo">Indigo</option>
                    </select>
                    <button type="submit" class="bg-primary text-white px-5 rounded-2xl font-bold"><i class="ph-bold ph-plus"></i></button>
                </div>
            </form>
        </div>

        <!-- Server Ranks -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-3">
                <i class="ph-bold ph-medal text-teal-500"></i>
                Server Ranks
            </h3>
            <div class="grid grid-cols-1 gap-3 mb-6">
                <?php foreach($ranks as $rank): ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl group transition-all">
                        <span class="text-sm font-bold text-slate-700 uppercase tracking-widest"><?= h($rank->name) ?></span>
                        <a href="<?= URLROOT ?>/settings/rank/delete/<?= $rank->id ?>" onclick="return confirm('Delete this rank?')" class="p-2 text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all">
                            <i class="ph-bold ph-trash-simple text-lg"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <form action="<?= URLROOT ?>/settings/rank/store" method="POST" class="flex gap-3">
                <?php csrf_field(); ?>
                <input type="text" name="name" required placeholder="New Rank (e.g. Senior)..." class="flex-1 px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none">
                <button type="submit" class="bg-primary text-white px-5 rounded-2xl font-bold"><i class="ph-bold ph-plus"></i></button>
            </form>
        </div>
    </div>
</div>
