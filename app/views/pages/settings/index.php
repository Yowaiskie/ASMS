<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">System Settings</h2>
    <p class="text-slate-500 text-sm mt-1">Configure global application settings</p>
</div>

<form action="<?= URLROOT ?>/settings/store" method="POST">
    <?php csrf_field(); ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">General Information</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">System Name</label>
                        <input type="text" name="system_name" value="Altar Servers Management System" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Admin Email</label>
                        <input type="email" name="admin_email" value="admin@church.org" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Contact Phone</label>
                        <input type="text" name="contact_phone" value="+63 912 345 6789" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Security & Policy</h3>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Default Password for New Users</label>
                        <input type="text" name="default_password" value="Church@123" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <h4 class="font-bold text-slate-700 text-sm">Force Password Reset</h4>
                            <p class="text-xs text-slate-400">Require users to change password upon first login</p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="force_reset" id="force_reset" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" checked/>
                            <label for="force_reset" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="lg:col-span-1 space-y-8">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Configuration</h3>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-slate-700 text-sm">Maintenance Mode</h4>
                            <p class="text-xs text-slate-400">Disable access for users</p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="maintenance_mode" id="maintenance_mode" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"/>
                            <label for="maintenance_mode" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-slate-700 text-sm">Allow Registration</h4>
                            <p class="text-xs text-slate-400">Enable new user signups</p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="allow_registration" id="allow_registration" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" checked/>
                            <label for="allow_registration" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                    
                    <div class="h-px bg-slate-100"></div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-slate-700 text-sm">Email Notifications</h4>
                            <p class="text-xs text-slate-400">Send system alerts via email</p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="email_notif" id="email_notif" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" checked/>
                            <label for="email_notif" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-lg font-bold text-slate-700 mb-4">Database</h3>
                <p class="text-xs text-slate-400 mb-6">Last backup: 2 days ago</p>
                <button type="button" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Backup Now
                </button>
            </div>

        </div>

    </div>

    <div class="fixed bottom-6 right-6 z-40">
        <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-8 py-3 rounded-full shadow-lg shadow-blue-200 transition-all flex items-center gap-2 font-bold text-sm hover:scale-105 transform">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Save All Changes
        </button>
    </div>

</form>

<style>
    .toggle-checkbox:checked {
        right: 0;
        border-color: #1e63d4;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #1e63d4;
    }
</style>