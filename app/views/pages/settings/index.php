<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800">System & Account Settings</h2>
    <p class="text-slate-500 text-sm mt-1">Configure global application settings and manage your profile</p>
</div>

<form action="<?= URLROOT ?>/settings/store" method="POST" enctype="multipart/form-data">
    <?php csrf_field(); ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Admin Profile (For Admin & Superadmin) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Personal Profile</h3>
                </div>
                
                <input type="hidden" name="action" value="update_profile">
                
                <div class="flex items-center gap-6 mb-6">
                    <div class="relative group">
                        <div class="w-20 h-20 rounded-full bg-slate-100 overflow-hidden border-2 border-slate-200">
                            <?php if(!empty($profile->profile_image)): ?>
                                <img src="<?= URLROOT ?>/uploads/profiles/<?= h($profile->profile_image) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <label for="profile_image" class="absolute bottom-0 right-0 bg-white border border-slate-200 p-1.5 rounded-full shadow-sm cursor-pointer hover:bg-slate-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700 text-sm">Profile Photo</h4>
                        <p class="text-[10px] text-slate-400">Update your avatar</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Full Name</label>
                        <input type="text" name="name" value="<?= h($profile->name ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Nickname</label>
                        <input type="text" name="nickname" value="<?= h($profile->nickname ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Age</label>
                        <input type="number" name="age" value="<?= h($profile->age ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Date of Birth</label>
                        <input type="date" name="dob" value="<?= h($profile->dob ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Contact Number</label>
                        <input type="text" name="phone" value="<?= h($profile->phone ?? '') ?>" maxlength="11" pattern="\d{11}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="09xxxxxxxxx" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Email Address</label>
                        <input type="email" name="email" value="<?= h($profile->email ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Home Address</label>
                        <textarea name="address" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none"><?= h($profile->address ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <?php if ($_SESSION['role'] === 'Superadmin'): ?>
            <!-- System Configuration (Superadmin Only) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up delay-100">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">System Configuration</h3>
                </div>
                
                <input type="hidden" name="system_settings" value="1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">System Display Name</label>
                        <input type="text" name="system_name" value="<?= h($system['system_name'] ?? 'Altar Servers Management System') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Support Email</label>
                        <input type="email" name="admin_email" value="<?= h($system['admin_email'] ?? 'admin@church.org') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Support Hotline</label>
                        <input type="text" name="contact_phone" value="<?= h($system['contact_phone'] ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>

                    <div class="md:col-span-2 space-y-4 pt-4 border-t border-slate-50">
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div>
                                <h4 class="font-bold text-slate-700 text-sm">Maintenance Mode</h4>
                                <p class="text-[10px] text-slate-400">Restrict access to everyone except Superadmins</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="maintenance_mode" value="on" <?= ($system['maintenance_mode'] ?? 'off') === 'on' ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div>
                                <h4 class="font-bold text-slate-700 text-sm">Public Registration</h4>
                                <p class="text-[10px] text-slate-400">Allow new users to register an account</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_registration" value="on" <?= ($system['allow_registration'] ?? 'off') === 'on' ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <div class="lg:col-span-1 space-y-8">
            <!-- Password Side -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up delay-300">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-slate-100 text-slate-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Account Security</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 mb-1 ml-1 uppercase">Current Password</label>
                        <input type="password" name="current_password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 mb-1 ml-1 uppercase">New Password</label>
                        <input type="password" name="new_password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 mb-1 ml-1 uppercase">Confirm New</label>
                        <input type="password" name="confirm_password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none">
                    </div>
                </div>
            </div>

            <?php if ($_SESSION['role'] === 'Superadmin'): ?>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 1.1.9 2 2 2h12a2 2 0 002-2V7M4 7c0-1.1.9-2 2-2h12a2 2 0 002 2M4 7l8 5 8-5M12 12l8 5" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Database Tools</h3>
                </div>
                <p class="text-[10px] text-slate-400 mb-6">Download a complete backup of the system database (SQL format).</p>
                <a href="<?= URLROOT ?>/settings/backup" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 text-sm shadow-lg shadow-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Export Database
                </a>
            </div>

            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-8 text-white animate-fade-in-up relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <h4 class="font-bold text-lg mb-2 relative z-10">System v1.2</h4>
                <p class="text-[10px] text-slate-400 leading-relaxed relative z-10">Secure Administrator Access. Changes made here affect the entire system and all connected users.</p>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="fixed bottom-6 right-6 z-40">
        <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-8 py-3 rounded-full shadow-lg shadow-blue-200 transition-all flex items-center gap-2 font-bold text-sm hover:scale-105 transform">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Save All Changes
        </button>
    </div>

</form>