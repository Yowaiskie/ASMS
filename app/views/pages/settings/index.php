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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Full Name</label>
                        <input type="text" name="name" value="<?= h($profile->name ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Age</label>
                        <input type="number" name="age" value="<?= h($profile->age ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Contact Number</label>
                        <input type="text" name="phone" value="<?= h($profile->phone ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                </div>
            </div>

            <?php if ($_SESSION['role'] === 'Superadmin'): ?>
            <!-- General Information (Superadmin Only) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up delay-100">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">General Information</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">System Name</label>
                        <input type="text" name="system_name" value="Altar Servers Management System" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Admin Email</label>
                        <input type="email" name="admin_email" value="admin@church.org" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Contact Phone</label>
                        <input type="text" name="contact_phone" value="+63 912 345 6789" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Security & Policy (Superadmin Only) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up delay-200">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Security & Policy</h3>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Default Password for New Users</label>
                        <input type="text" name="default_password" value="Church@123" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <h4 class="font-bold text-slate-700 text-sm">Force Password Reset</h4>
                            <p class="text-xs text-slate-400">Require users to change password upon first login</p>
                        </div>
                        <input type="checkbox" checked class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <div class="lg:col-span-1 space-y-8">
            <!-- Password Side (Always visible) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up delay-300">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-slate-100 text-slate-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Account Security</h3>
                </div>
                <div class="space-y-4">
                    <input type="password" name="current_password" placeholder="Current Password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none">
                    <input type="password" name="new_password" placeholder="New Password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none">
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none">
                </div>
            </div>

            <?php if ($_SESSION['role'] === 'Superadmin'): ?>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up">
                <h3 class="text-lg font-bold text-slate-700 mb-4">Database</h3>
                <p class="text-xs text-slate-400 mb-6">Backup system data regularly.</p>
                <button type="button" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Backup Now
                </button>
            </div>

            <div class="bg-slate-900 rounded-3xl p-8 text-white animate-fade-in-up">
                <h4 class="font-bold text-lg mb-2">ASMS v1.0</h4>
                <p class="text-xs text-slate-400 leading-relaxed">System Administrator Access. Authorized users only.</p>
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