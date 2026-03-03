<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .day-column {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .day-column:hover {
        transform: translateY(-4px);
    }
    .slot-card {
        transition: all 0.2s ease;
    }
    .slot-card:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

<div class="mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="px-2 py-0.5 rounded-md bg-primary-100 text-primary text-[10px] font-black uppercase tracking-widest">Master Pattern</span>
            <div class="h-1 w-8 bg-primary-200 rounded-full"></div>
        </div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Weekly Master Plan</h2>
        <p class="text-slate-500 text-sm mt-1 font-medium">Design your parish's routine. One-click <span class="text-primary font-bold">Auto-Fill</span> will use this pattern.</p>
    </div>
    
    <div class="flex flex-wrap gap-3">
        <button onclick="openGenerateModal()" class="bg-primary hover:bg-primary-700 text-white px-6 py-3 rounded-2xl shadow-xl shadow-primary-200 transition-all flex items-center gap-2 font-bold text-sm">
            <i class="ph-bold ph-magic-wand text-lg"></i>
            Auto-Fill Now
        </button>
        <a href="<?= URLROOT ?>/schedules" class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-5 py-3 rounded-2xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm">
            <i class="ph-bold ph-calendar text-lg"></i>
            Calendar
        </a>
    </div>
</div>

<!-- Auto-Fill Target Modal -->
<div id="generateModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[80] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden p-8 animate-fade-in-up">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-primary-50 rounded-2xl flex items-center justify-center text-primary">
                <i class="ph-bold ph-magic-wand text-2xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Auto-Fill Target</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Select Month and Year</p>
            </div>
        </div>
        
        <div class="space-y-4">
            <!-- Start Date -->
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Start Month</label>
                    <select id="genMonth" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                        <?php 
                        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        $currentM = date('n');
                        foreach($months as $i => $m): 
                        ?>
                            <option value="<?= $i+1 ?>" <?= ($i+1) == ($currentM % 12 + 1) ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Start Year</label>
                    <select id="genYear" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                        <?php 
                        $currentY = date('Y');
                        for($y = $currentY; $y <= $currentY + 2; $y++): 
                        ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- Separator -->
            <div class="flex items-center gap-4 py-1">
                <div class="flex-1 h-px bg-slate-100"></div>
                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Until (Optional)</span>
                <div class="flex-1 h-px bg-slate-100"></div>
            </div>

            <!-- End Date -->
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">End Month</label>
                    <select id="genEndMonth" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                        <option value="">-- None --</option>
                        <?php foreach($months as $i => $m): ?>
                            <option value="<?= $i+1 ?>"><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">End Year</label>
                    <select id="genEndYear" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                        <?php for($y = $currentY; $y <= $currentY + 2; $y++): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="flex-1 py-3 text-sm font-bold text-slate-400">Cancel</button>
                <button onclick="confirmGenerate()" class="flex-1 bg-primary text-white py-3 rounded-2xl text-sm font-bold shadow-lg shadow-primary-100">Generate Now</button>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Grid -->
<div class="overflow-x-auto pb-8 custom-scrollbar">
    <div class="flex gap-5 min-w-[1400px]">
        <?php 
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach($days as $dayIndex => $dayName): 
            $dayTemplates = array_filter($templates, function($t) use ($dayIndex) { return (int)$t->day_of_week === $dayIndex; });
            usort($dayTemplates, function($a, $b) { return strcmp($a->mass_time, $b->mass_time); });
            $slotCount = count($dayTemplates);
        ?>
            <!-- Day Column -->
            <div class="flex-1 min-w-[190px] flex flex-col gap-5 day-column">
                <!-- Day Header -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-1 h-full bg-primary-500"></div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-0.5"><?= substr($dayName, 0, 3) ?></span>
                        <span class="text-sm font-black text-slate-800 tracking-tight"><?= $dayName ?></span>
                    </div>
                    
                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="openCopyModal(<?= $dayIndex ?>)" title="Copy Day" class="p-2 text-slate-400 hover:text-primary hover:bg-primary-50 rounded-xl transition-all">
                            <i class="ph-bold ph-copy text-sm"></i>
                        </button>
                        <button onclick="clearDay(<?= $dayIndex ?>)" title="Clear Day" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                            <i class="ph-bold ph-trash-simple text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Slots List Container -->
                <div class="flex-1 flex flex-col gap-4 min-h-[500px] p-3 bg-slate-100/40 rounded-[2.5rem] border-2 border-dashed border-slate-200/60 relative group/list">
                    
                    <?php if($slotCount === 0): ?>
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-6 grayscale opacity-20">
                            <div class="w-16 h-16 bg-slate-200 rounded-3xl flex items-center justify-center mb-4">
                                <i class="ph-bold ph-calendar-blank text-3xl"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-600">No Routines</span>
                        </div>
                    <?php else: ?>
                        <?php foreach($dayTemplates as $t): 
                            $time = date('h:i A', strtotime($t->mass_time));
                            $color = $t->color ?? 'blue';
                            $accentClass = "bg-{$color}-500";
                            $textClass = "text-{$color}-700";
                            if($color == 'yellow') { $accentClass = "bg-yellow-400"; $textClass = "text-yellow-800"; }
                        ?>
                            <div class="bg-white p-4 rounded-[1.8rem] border border-slate-200 shadow-sm slot-card relative group/card">
                                <div class="flex items-center gap-2.5 mb-2.5">
                                    <div class="w-2 h-2 rounded-full <?= $accentClass ?> shadow-sm"></div>
                                    <span class="text-[11px] font-black text-slate-800 truncate tracking-tight uppercase"><?= $t->mass_type ?></span>
                                </div>
                                
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="text-sm font-black <?= $textClass ?> mb-0.5 tracking-tighter"><?= $time ?></p>
                                        <?php if($t->event_name): ?>
                                            <p class="text-[9px] text-slate-400 font-bold truncate max-w-[120px]"><?= $t->event_name ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <button onclick="deleteSlot(<?= $t->id ?>)" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all opacity-0 group-hover/card:opacity-100">
                                        <i class="ph-bold ph-x-circle text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Floating Add Button -->
                    <button onclick="openAddModal(<?= $dayIndex ?>)" class="mt-2 w-full py-4 bg-white/60 hover:bg-white border-2 border-dashed border-slate-200 hover:border-primary-400 text-slate-400 hover:text-primary rounded-[1.8rem] transition-all flex items-center justify-center gap-2 group/btn">
                        <div class="w-6 h-6 rounded-full bg-slate-100 group-hover/btn:bg-primary-100 flex items-center justify-center transition-colors">
                            <i class="ph-bold ph-plus text-xs"></i>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest">New Slot</span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Modal -->
<div id="templateModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[80] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <div>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Configure Routine</h3>
                <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-widest">Day: <span id="modalDayName" class="text-primary"></span></p>
            </div>
            <button onclick="closeModal()" class="p-3 hover:bg-slate-50 rounded-2xl text-slate-300 hover:text-slate-600 transition-all">
                <i class="ph-bold ph-x text-xl"></i>
            </button>
        </div>

        <form action="<?= URLROOT ?>/schedules/store-template" method="POST" class="p-8 space-y-6">
            <?php csrf_field(); ?>
            <input type="hidden" name="day_of_week" id="inputDayOfWeek">

            <div class="grid grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] ml-1">Type</label>
                    <select name="mass_type" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all">
                        <option value="Sunday Mass">Sunday Mass</option>
                        <option value="Anticipated Mass">Anticipated Mass</option>
                        <option value="Weekday Mass">Weekday Mass</option>
                        <option value="Wedding">Wedding</option>
                        <option value="Funeral">Funeral</option>
                        <option value="Baptism">Baptism</option>
                        <option value="Special Event">Special Event</option>
                        <option value="Meeting">Meeting</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] ml-1">Time</label>
                    <input type="time" name="mass_time" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] ml-1">Event Details (Optional)</label>
                <input type="text" name="event_name" placeholder="e.g. Regular Parish Mass" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] ml-1">Visual Marker</label>
                <div class="flex flex-wrap gap-3 mt-1">
                    <?php $colors = ['green','purple','yellow','blue','indigo','pink','red','teal','gray']; foreach($colors as $c): $bg = "bg-{$c}-500"; if($c=='yellow') $bg="bg-yellow-400"; ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="<?= $c ?>" <?= $c === 'blue' ? 'checked' : '' ?> class="peer hidden">
                            <span class="block w-8 h-8 rounded-full <?= $bg ?> ring-offset-2 peer-checked:ring-2 peer-checked:ring-slate-800 transition-all hover:scale-110"></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-4 flex gap-4">
                <button type="button" onclick="closeModal()" class="flex-1 py-4 rounded-2xl border border-slate-100 text-slate-500 text-sm font-bold hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" class="flex-1 bg-primary text-white py-4 rounded-2xl font-bold text-sm hover:bg-primary-700 shadow-xl shadow-primary-200 transition-all">Save Routine</button>
            </div>
        </form>
    </div>
</div>

<!-- Copy Modal -->
<div id="copyModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[80] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden p-8">
        <h3 class="text-xl font-black text-slate-800 mb-2">Copy Routine</h3>
        <p class="text-xs text-slate-400 font-bold mb-6 uppercase tracking-widest">Duplicate <span id="copyFromDayName" class="text-primary"></span> to:</p>
        
        <form action="<?= URLROOT ?>/schedules/copy-template" method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <input type="hidden" name="from_day" id="copyFromDayId">
            
            <div class="grid grid-cols-2 gap-2">
                <?php foreach($days as $idx => $name): ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="to_day" value="<?= $idx ?>" class="peer hidden">
                        <span class="block px-3 py-2 rounded-xl border border-slate-100 text-[10px] font-black text-slate-500 text-center uppercase peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary transition-all"><?= $name ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('copyModal').classList.add('hidden')" class="flex-1 py-3 text-sm font-bold text-slate-400">Cancel</button>
                <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-xl text-sm font-bold shadow-lg shadow-primary-100">Copy Pattern</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteTemplateForm" action="<?= URLROOT ?>/schedules/delete-template" method="POST" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="id" id="deleteId">
</form>

<form id="clearDayForm" action="<?= URLROOT ?>/schedules/clear-templates" method="POST" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="day_of_week" id="clearDayId">
</form>

<script>
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    function openAddModal(dayIndex) {
        document.getElementById('inputDayOfWeek').value = dayIndex;
        document.getElementById('modalDayName').innerText = days[dayIndex];
        document.getElementById('templateModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('templateModal').classList.add('hidden');
    }

    function openCopyModal(dayIndex) {
        document.getElementById('copyFromDayId').value = dayIndex;
        document.getElementById('copyFromDayName').innerText = days[dayIndex];
        document.getElementById('copyModal').classList.remove('hidden');
    }

    function deleteSlot(id) {
        showConfirm('Remove this routine from the Master Plan?', 'Delete Routine', function() {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteTemplateForm').submit();
        });
    }

    function clearDay(dayIndex) {
        showConfirm('Remove ALL routines for ' + days[dayIndex] + '?', 'Clear Day', function() {
            document.getElementById('clearDayId').value = dayIndex;
            document.getElementById('clearDayForm').submit();
        });
    }

    function openGenerateModal() {
        document.getElementById('generateModal').classList.remove('hidden');
    }

    function confirmGenerate() {
        const m = document.getElementById('genMonth').value;
        const y = document.getElementById('genYear').value;
        const em = document.getElementById('genEndMonth').value;
        const ey = document.getElementById('genEndYear').value;
        
        const monthName = document.querySelector(`#genMonth option[value="${m}"]`).innerText;
        let msg = `Generate all mass slots for ${monthName} ${y}`;
        
        if (em) {
            const endMonthName = document.querySelector(`#genEndMonth option[value="${em}"]`).innerText;
            msg += ` until ${endMonthName} ${ey}`;
        }
        
        msg += ` based on this Master Plan?`;
        
        showConfirm(msg, 'Confirm Auto-Fill', function() {
            let url = `<?= URLROOT ?>/schedules/generate?month=${m}&year=${y}`;
            if (em) url += `&end_month=${em}&end_year=${ey}`;
            window.location.href = url;
        });
    }
</script>
