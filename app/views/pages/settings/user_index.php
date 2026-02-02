<div class="mb-8 animate-fade-in-up flex items-end justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Account Settings</h2>
        <p class="text-slate-500 text-sm mt-1">Manage your profile and security</p>
    </div>
    
    <div>
        <?php if($_SESSION['is_verified']): ?>
            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full text-xs font-bold border border-emerald-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Verified Account
            </span>
        <?php else: ?>
            <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 px-4 py-2 rounded-full text-xs font-bold border border-amber-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                Unverified
            </span>
        <?php endif; ?>
    </div>
</div>

<?php if(!$_SESSION['is_verified']): ?>
    <div class="bg-amber-50 border border-amber-100 p-6 rounded-3xl mb-8 animate-fade-in-up flex items-start gap-4">
        <div class="p-3 bg-white rounded-2xl shadow-sm text-amber-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <h4 class="font-bold text-amber-900">Complete Your Profile</h4>
            <p class="text-sm text-amber-700 mt-1">Please provide your <b>Full Name</b>, <b>Contact Number</b>, and <b>Email Address</b> to verify your account. Once verified, you can start joining mass schedules.</p>
        </div>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Profile Edit Form -->
    <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-sm border border-slate-100 animate-fade-in-up">
        <h3 class="font-bold text-slate-800 text-lg mb-6">Personal Information</h3>
        
        <form action="<?= URLROOT ?>/settings/store" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="update_profile">

            <!-- Profile Image -->
            <div class="flex items-center gap-6 mb-6">
                <div class="relative group">
                    <?php if(!empty($profile->profile_image)): ?>
                        <img src="<?= URLROOT ?>/uploads/profiles/<?= h($profile->profile_image) ?>" alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-slate-50">
                    <?php else: ?>
                        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-3xl border-4 border-slate-50">
                            <?= strtoupper(substr($profile->username ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    
                    <label for="profile_image" class="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow-md border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*">
                    </label>
                </div>
                <div>
                    <h4 class="font-bold text-slate-700">Profile Photo</h4>
                    <p class="text-xs text-slate-400 mt-1">Accepts JPG, PNG. Max 2MB.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Full Name</label>
                    <input type="text" name="name" value="<?= h($profile->name ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Age</label>
                    <input type="number" name="age" value="<?= h($profile->age ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Contact Number</label>
                    <input type="text" name="phone" value="<?= h($profile->phone ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Address</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"><?= h($profile->address ?? '') ?></textarea>
                </div>
                
                 <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Email Address</label>
                    <input type="email" name="email" value="<?= h($profile->email ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-blue-200 transform active:scale-[0.98]">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Security & Info -->
    <div class="space-y-8">
        
        <!-- Account Info -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 animate-fade-in-up delay-100">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-slate-100 text-slate-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800">Account Info</h3>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Username</label>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-slate-600 text-sm font-medium">
                        <?= h($profile->username) ?>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Role</label>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-slate-600 text-sm font-medium">
                        <?= h($profile->role) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 animate-fade-in-up delay-200">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-slate-100 text-slate-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800">Security</h3>
            </div>

            <form action="<?= URLROOT ?>/settings/store" method="POST" class="space-y-4">
                <?php csrf_field(); ?>
                <input type="hidden" name="action" value="update_password">

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Current Password</label>
                    <input type="password" name="current_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">New Password</label>
                    <input type="password" name="new_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Confirm Password</label>
                    <input type="password" name="confirm_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl transition-all shadow-lg transform active:scale-[0.98]">
                    Update Password
                </button>
            </form>
        </div>

    </div>

</div>