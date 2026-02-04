    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

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

<?php 
    $isLocked = ($_SESSION['role'] === 'User' && !$profile->can_edit_profile);
?>

<?php if($isLocked): ?>
    <div class="bg-slate-800 text-white p-6 rounded-3xl mb-8 animate-fade-in-up flex items-start gap-4 shadow-xl">
        <div class="p-3 bg-slate-700 rounded-2xl text-amber-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
        </div>
        <div>
            <h4 class="font-bold">Profile Editing Locked</h4>
            <p class="text-sm text-slate-300 mt-1">You have already completed your one-time profile setup. To make further changes, please contact the <b>Admin</b> or <b>Superadmin</b> to unlock your account editing permission.</p>
        </div>
    </div>
<?php elseif(!$_SESSION['is_verified']): ?>
    <div class="bg-amber-50 border border-amber-100 p-6 rounded-3xl mb-8 animate-fade-in-up flex items-start gap-4">
        <div class="p-3 bg-white rounded-2xl shadow-sm text-amber-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <h4 class="font-bold text-amber-900">Complete Your Profile</h4>
            <p class="text-sm text-amber-700 mt-1">Please provide your <b>First Name</b>, <b>Last Name</b>, <b>Contact Number</b>, and <b>Email Address</b> to verify your account. Once verified, you can start joining mass schedules.</p>
        </div>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Profile Edit Form -->
    <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-sm border border-slate-100 animate-fade-in-up">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-slate-800 text-lg">Personal Information</h3>
            <?php if($isLocked): ?>
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 flex items-center gap-1.5">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    Read Only Mode
                </span>
            <?php endif; ?>
        </div>

        <form action="<?= URLROOT ?>/settings/store" method="POST" enctype="multipart/form-data" id="profileForm" class="space-y-6">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="update_profile">
            <input type="hidden" name="cropped_image" id="cropped_image_input">

            <!-- Profile Image -->
            <div class="flex items-center gap-6 mb-6">
                <div class="relative group">
                    <div id="profile-preview-container">
                        <?php if(!empty($profile->profile_image)): ?>
                            <img src="<?= URLROOT ?>/uploads/profiles/<?= h($profile->profile_image) ?>" id="current-profile-img" alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-slate-50">
                        <?php else: ?>
                            <div id="initials-avatar" class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-3xl border-4 border-slate-50">
                                <?= strtoupper(substr($profile->username ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if(!$isLocked): ?>
                    <label for="profile_image" class="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow-md border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors text-slate-500">     
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*" onchange="handleImageSelect(this)">
                    </label>
                    <?php endif; ?>
                </div>
                <div>
                    <h4 class="font-bold text-slate-700">Profile Photo</h4>
                    <p class="text-xs text-slate-400 mt-1">
                        <?= $isLocked ? 'Photo editing is locked.' : 'Accepts JPG, PNG. Max 2MB. 1:1 Aspect Ratio Recommended.' ?>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">First Name</label>
                        <input type="text" name="first_name" value="<?= h($profile->first_name ?? '') ?>" <?= $isLocked ? 'disabled' : 'required' ?> class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all disabled:opacity-70">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Middle Name</label>
                        <input type="text" name="middle_name" value="<?= h($profile->middle_name ?? '') ?>" <?= $isLocked ? 'disabled' : '' ?> class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none disabled:opacity-70">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Last Name</label> 
                        <input type="text" name="last_name" value="<?= h($profile->last_name ?? '') ?>" <?= $isLocked ? 'disabled' : 'required' ?> class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none disabled:opacity-70">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Age</label>
                    <input type="text" name="age" value="<?= h($profile->age ?? '') ?>" maxlength="2" <?= $isLocked ? 'disabled' : '' ?> oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0,2)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all disabled:opacity-70">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Contact Number</label>
                    <input type="text" name="phone" value="<?= h($profile->phone ?? '') ?>" maxlength="11" pattern="\d{11}" <?= $isLocked ? 'disabled' : '' ?> oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="09xxxxxxxxx" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all disabled:opacity-70">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Address</label>       
                    <textarea name="address" rows="2" <?= $isLocked ? 'disabled' : '' ?> class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all disabled:opacity-70"><?= h($profile->address ?? '') ?></textarea>
                </div>

                 <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Email Address</label>
                    <input type="email" name="email" value="<?= h($profile->email ?? '') ?>" <?= $isLocked ? 'disabled' : '' ?> class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all disabled:opacity-70">
                </div>
            </div>

            <?php if(!$isLocked): ?>
            <div class="pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-blue-200 transform active:scale-[0.98]">
                    Save Changes
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Security & Info -->
    <div class="space-y-8">
        
        <!-- Cropping Modal -->
        <div id="cropperModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Crop Profile Photo</h3>
                    <button onclick="closeCropper()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="w-full aspect-square bg-slate-100 rounded-2xl overflow-hidden mb-6">
                        <img id="cropperImage" class="max-w-full block">
                    </div>
                    <div class="flex gap-3">
                        <button onclick="applyCrop()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all">Apply & Save</button>
                        <button onclick="closeCropper()" class="px-8 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let cropper;
            const profileInput = document.getElementById('profile_image');
            const cropperModal = document.getElementById('cropperModal');
            const cropperImage = document.getElementById('cropperImage');
            const croppedInput = document.getElementById('cropped_image_input');
            const currentImg = document.getElementById('current-profile-img');
            const initialsAvatar = document.getElementById('initials-avatar');
            const profileForm = document.getElementById('profileForm');
            const saveConfirmModal = document.getElementById('saveConfirmModal');

            function handleImageSelect(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        cropperImage.src = e.target.result;
                        cropperModal.classList.remove('hidden');
                        cropperModal.classList.add('flex');
                        
                        if (cropper) cropper.destroy();
                        
                        cropper = new Cropper(cropperImage, {
                            aspectRatio: 1,
                            viewMode: 2,
                            background: false,
                            autoCropArea: 1
                        });
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function closeCropper() {
                cropperModal.classList.add('hidden');
                cropperModal.classList.remove('flex');
                profileInput.value = '';
                if (cropper) cropper.destroy();
            }

            function applyCrop() {
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400
                });
                
                const dataURL = canvas.toDataURL('image/jpeg', 0.8);
                croppedInput.value = dataURL;
                
                // Update preview
                if (currentImg) {
                    currentImg.src = dataURL;
                } else if (initialsAvatar) {
                    initialsAvatar.parentElement.innerHTML = `<img src="${dataURL}" id="current-profile-img" alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-slate-50">`;
                }
                
                cropperModal.classList.add('hidden');
                cropperModal.classList.remove('flex');
            }

            // Form Validation and Global Confirmation Modal
            const profileForm = document.getElementById('profileForm');
            profileForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop form from submitting immediately

                const hasExistingImage = <?= !empty($profile->profile_image) ? 'true' : 'false' ?>;
                const hasNewImage = document.getElementById('cropped_image_input').value !== '';

                if (!hasExistingImage && !hasNewImage) {
                    showAlert('Profile photo is required. Please upload and crop a photo first.');
                    return false;
                }

                showConfirm(
                    'Regular users are only allowed to edit their profile ONCE. After saving, your profile will be locked for editing. Are you sure you want to proceed?', 
                    'Save Profile Changes?', 
                    function() {
                        profileForm.submit();
                    }
                );
            });
        </script>        
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
                    <div class="relative">
                        <input type="password" name="current_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-12">
                        <button type="button" onclick="toggleFieldPassword(this)" class="absolute right-3 top-2.5 text-slate-400 hover:text-blue-600 transition-colors">
                            <i class="ph ph-eye text-base"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">New Password</label>
                    <div class="relative">
                        <input type="password" name="new_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-12">
                        <button type="button" onclick="toggleFieldPassword(this)" class="absolute right-3 top-2.5 text-slate-400 hover:text-blue-600 transition-colors">
                            <i class="ph ph-eye text-base"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-12">
                        <button type="button" onclick="toggleFieldPassword(this)" class="absolute right-3 top-2.5 text-slate-400 hover:text-blue-600 transition-colors">
                            <i class="ph ph-eye text-base"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl transition-all shadow-lg transform active:scale-[0.98]">
                    Update Password
                </button>
            </form>
        </div>

    </div>

</div>

<script>
    function toggleFieldPassword(btn) {
        const input = btn.parentElement.querySelector('input');
        const icon = btn.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('ph-eye');
            icon.classList.add('ph-eye-closed');
        } else {
            input.type = 'password';
            icon.classList.remove('ph-eye-closed');
            icon.classList.add('ph-eye');
        }
    }
</script>