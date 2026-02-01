<div class="flex items-end justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Schedule Management</h2>
        <p class="text-slate-500 text-sm mt-1">Create and manage mass schedules</p>
    </div>
    
    <a href="<?= URLROOT ?>/schedules/create" class="bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center gap-2 font-semibold text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create Schedule
    </a>
</div>

<div class="space-y-6">

    <?php if(!empty($schedules)): ?>
        <?php foreach($schedules as $schedule): ?>
            <?php 
                $date = new DateTime($schedule->mass_date);
                $month = $date->format('M');
                $day = $date->format('d');
            ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex flex-col md:flex-row gap-6 mb-6">
                    
                    <div class="flex items-start gap-4 flex-1">
                        <div class="bg-blue-600 text-white rounded-xl w-16 h-16 flex flex-col items-center justify-center shrink-0 shadow-md shadow-blue-200">
                            <span class="text-[10px] uppercase font-bold tracking-wider opacity-80"><?= $month ?></span>
                            <span class="text-2xl font-bold leading-none"><?= $day ?></span>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-slate-800 mb-1"><?= $schedule->mass_type ?></h3>
                            <div class="flex items-center gap-4 text-sm text-slate-500">
                                <div class="flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?= date('g:i A', strtotime($schedule->mass_time)) ?>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Main Church
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold 
                                        <?= $schedule->status == 'Confirmed' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' ?>">
                                        <?= $schedule->status ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <a href="<?= URLROOT ?>/schedules/delete?id=<?= $schedule->id ?>" onclick="return confirm('Are you sure?')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="h-px bg-slate-100 mb-4 w-full"></div>

                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase mb-3">Assigned Servers:</p>
                    <div class="flex flex-wrap gap-4">
                        
                        <!-- Mocked Assigned Servers for now -->
                        <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl p-2 pr-4">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                JD
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-700">John Doe</span>
                                <span class="text-[10px] text-slate-400">Server 1</span>
                            </div>
                        </div>

                         <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl p-2 pr-4">
                            <button class="text-xs font-bold text-blue-600 hover:underline">+ Assign Server</button>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-slate-500">No schedules found.</p>
    <?php endif; ?>

</div>

<div class="fixed bottom-6 right-6 z-40">
    <button class="bg-slate-900 text-white p-3 rounded-full shadow-lg hover:scale-110 transition-transform">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </button>
</div>