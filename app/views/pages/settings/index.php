<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800">System & Account Settings</h2>
    <p class="text-slate-500 text-sm mt-1">Manage system information and your admin profile</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 space-y-8">
        <!-- Personal Information -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Admin Profile</h3>
            </div>

            <form action="<?= URLROOT ?>/settings/store" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php csrf_field(); ?>
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

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Email Address</label>
                        <input type="email" name="email" value="<?= h($profile->email ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-blue-200 transform active:scale-95">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

    </div>

    <div class="lg:col-span-1 space-y-8">
        <!-- Security -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up delay-200">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Security</h3>
            </div>

            <form action="<?= URLROOT ?>/settings/store" method="POST" class="space-y-5">
                <?php csrf_field(); ?>
                <input type="hidden" name="action" value="update_password">

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Current Password</label>
                    <input type="password" name="current_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">New Password</label>
                    <input type="password" name="new_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Confirm Password</label>
                    <input type="password" name="confirm_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none">
                </div>

                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-purple-200 transform active:scale-95">
                    Change Password
                </button>
            </form>
        </div>
    </div>

</div>