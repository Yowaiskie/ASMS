<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800">Database Management</h2>
    <p class="text-slate-500 text-sm mt-1">Export backups and restore system data</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 animate-fade-in-up">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i class="ph-bold ph-database text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Backup & Restore Tools</h3>
            </div>

            <!-- Backup Section -->
            <div class="mb-10 pb-10 border-b border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">Export SQL Backup</h4>
                        <p class="text-xs text-slate-400 mt-1">Download a copy of the database. You can optionally filter by date range.</p>
                    </div>
                </div>

                <form action="<?= URLROOT ?>/settings/backup" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Start Date</label>
                        <input type="date" name="start_date" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">End Date</label>
                        <input type="date" name="end_date" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-all">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-xl transition-all flex items-center justify-center gap-2 text-xs shadow-lg shadow-slate-200">
                            <i class="ph-bold ph-download-simple"></i>
                            Generate Backup
                        </button>
                    </div>
                </form>
            </div>

            <!-- Restore Section -->
            <div>
                <div class="mb-6">
                    <h4 class="text-sm font-bold text-slate-800">Restore System Data</h4>
                    <p class="text-xs text-slate-400 mt-1">Upload a previously exported .sql file to restore the database state.</p>
                </div>

                <form action="<?= URLROOT ?>/settings/restore" method="POST" enctype="multipart/form-data" id="restoreForm">
                    <?php csrf_field(); ?>
                    
                    <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-4 mb-6">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                            <i class="ph-bold ph-warning-octagon text-xl"></i>
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-amber-900">Critical Action Warning</h5>
                            <p class="text-[10px] text-amber-700 leading-relaxed mt-0.5">
                                This process will <b>permanently overwrite</b> all current data. 
                                It is highly recommended to perform a backup first.
                            </p>
                        </div>
                    </div>

                    <div class="relative group">
                        <input type="file" name="backup_file" id="backup_file" accept=".sql" class="absolute inset-0 opacity-0 cursor-pointer z-10" required onchange="updateFileName(this)">
                        <div id="file-dropzone" class="border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center group-hover:border-primary group-hover:bg-primary-50/30 transition-all">
                            <div id="file-name-display" class="text-slate-500 transition-all">
                                <i class="ph ph-cloud-arrow-up text-4xl block mb-2 opacity-40 group-hover:text-primary group-hover:opacity-100"></i>
                                <span class="text-xs font-bold block">Click to browse or drag SQL file here</span>
                                <span class="text-[10px] opacity-60">Maximum file size: 10MB</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="button" onclick="confirmRestore()" class="px-8 py-3 bg-white border border-slate-200 hover:bg-red-50 hover:border-red-200 hover:text-red-600 text-slate-700 font-bold rounded-xl transition-all flex items-center gap-2 text-sm shadow-sm">
                            <i class="ph-bold ph-arrow-counter-clockwise"></i>
                            Start Restoration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-gradient-to-br from-primary-600 to-primary-800 rounded-3xl p-8 text-white shadow-xl shadow-primary-100 relative overflow-hidden">
            <i class="ph-fill ph-shield-check text-9xl absolute -right-4 -bottom-4 opacity-10"></i>
            <h4 class="font-bold text-lg mb-2 relative z-10">System Integrity</h4>
            <p class="text-xs text-primary-100 leading-relaxed relative z-10 opacity-80">
                Backups are your safety net. Regular exports ensure that server schedules and attendance records are never lost due to hardware failure or errors.
            </p>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Quick Stats</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Last Backup</span>
                    <span class="text-xs font-bold text-slate-700">Never</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Database Size</span>
                    <span class="text-xs font-bold text-slate-700">~2.4 MB</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const display = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            display.innerHTML = `
                <i class="ph-fill ph-file-sql text-4xl text-primary block mb-2"></i>
                <span class="text-xs font-bold text-slate-800 block">${input.files[0].name}</span>
                <span class="text-[10px] text-emerald-600 font-bold uppercase mt-1 block tracking-wider">File Ready for Import</span>
            `;
        } else {
            display.innerHTML = `
                <i class="ph ph-cloud-arrow-up text-4xl block mb-2 opacity-40"></i>
                <span class="text-xs font-bold block">Click to browse or drag SQL file here</span>
                <span class="text-[10px] opacity-60">Maximum file size: 10MB</span>
            `;
        }
    }

    function confirmRestore() {
        const fileInput = document.getElementById('backup_file');
        if (!fileInput.files || !fileInput.files[0]) {
            showAlert('Please select a valid .sql backup file.', 'No File Selected');
            return;
        }

        showConfirm(
            'CRITICAL WARNING: This will completely WIPE your current database and replace it with the data from the selected file. This action is IRREVERSIBLE. Are you sure you want to proceed?',
            'Danger: Restore Database?',
            function() {
                const btn = event.target.closest('button');
                if (btn) btn.classList.add('btn-loading');
                document.getElementById('restoreForm').submit();
            }
        );
    }

    // Monitor download cookie
    const backupForm = document.querySelector('form[action$="/settings/backup"]');
    if (backupForm) {
        backupForm.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            
            const checkDownload = setInterval(function() {
                if (document.cookie.indexOf('download_started=true') !== -1) {
                    // Stop spinner
                    if (btn) {
                        btn.classList.remove('btn-loading');
                        btn.style.pointerEvents = 'auto';
                    }
                    // Clear cookie
                    document.cookie = 'download_started=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                    clearInterval(checkDownload);
                }
            }, 500);
        });
    }
</script>