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
    
    /* Animation for presets */
    .preset-row { transition: all 0.2s; border-left: 3px solid transparent; }
    .preset-row:has(input:checked) { background: rgba(37, 99, 235, 0.03); border-left-color: #2563eb; }
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
        <button onclick="openPresetModal()" class="bg-white hover:bg-slate-50 text-indigo-600 border border-slate-200 px-5 py-3 rounded-2xl shadow-sm transition-all flex items-center gap-2 font-bold text-sm">
            <i class="ph-bold ph-sparkle text-lg"></i>
            Quick Setup
        </button>
        <button onclick="openAddSeasonModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl shadow-xl shadow-indigo-100 transition-all flex items-center gap-2 font-bold text-sm">
            <i class="ph-bold ph-calendar-plus text-lg"></i>
            Liturgical Event
        </button>
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

<!-- Liturgical Seasons Section -->
<?php if(!empty($seasons)): ?>
<div class="mb-8">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
            <i class="ph-bold ph-sparkle text-lg"></i>
        </div>
        <h3 class="text-lg font-black text-slate-800 tracking-tight">Active Liturgical Overrides</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach($seasons as $s): 
            $isPast = strtotime($s->end_date) < time();
            $statusClass = $isPast ? 'bg-slate-100 text-slate-400' : 'bg-green-100 text-green-600';
            $statusText = $isPast ? 'Past' : 'Active';
            $exempted = !empty($s->exempted_types) ? json_decode($s->exempted_types, true) : [];
        ?>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative group overflow-hidden">
                <div class="absolute top-0 right-0 p-3 opacity-0 group-hover:opacity-100 transition-all flex gap-1">
                    <button onclick='openEditSeasonModal(<?= json_encode($s) ?>)' class="p-1.5 text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                        <i class="ph-bold ph-pencil-simple text-sm"></i>
                    </button>
                    <button onclick="deleteSeason(<?= $s->id ?>)" class="p-1.5 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                        <i class="ph-bold ph-trash text-sm"></i>
                    </button>
                </div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-3 h-3 rounded-full bg-<?= $s->color ?>-500 shadow-sm"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest <?= $statusClass ?> px-2 py-0.5 rounded-md"><?= $statusText ?></span>
                </div>
                <h4 class="font-black text-slate-800 mb-1"><?= h($s->name) ?></h4>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">
                    <?= date('M d', strtotime($s->start_date)) ?> - <?= date('M d, Y', strtotime($s->end_date)) ?>
                </p>
                <?php if(!empty($exempted)): ?>
                    <div class="flex flex-wrap gap-1">
                        <?php foreach($exempted as $ex): ?>
                            <span class="text-[8px] font-bold bg-slate-50 text-slate-500 px-1.5 py-0.5 rounded border border-slate-100 italic"><?= h($ex) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Auto-Fill Target Modal -->
<div id="generateModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[80] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden p-8 animate-fade-in-up">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-primary-50 rounded-2xl flex items-center justify-center text-primary">
                <i class="ph-bold ph-magic-wand text-2xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Auto-Fill Target</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Generate from Master Plan</p>
            </div>
        </div>
        
        <div class="space-y-5">
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Start Month</label>
                <div class="grid grid-cols-2 gap-3">
                    <select id="genMonth" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700">
                        <?php 
                        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        $currentM = date('n');
                        foreach($months as $i => $m): 
                        ?>
                            <option value="<?= $i+1 ?>" <?= ($i+1) == ($currentM) ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="genYear" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700">
                        <?php 
                        $currentY = date('Y');
                        for($y = $currentY; $y <= $currentY + 2; $y++): 
                        ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Generate for...</label>
                <div class="grid grid-cols-2 gap-3">
                    <select id="genDurationType" onchange="toggleGenValueOptions('genDurationValue', this.value)" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700">
                        <option value="weeks">Weeks</option>
                        <option value="months">Months</option>
                    </select>
                    <select id="genDurationValue" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700">
                        <!-- Populated by JS -->
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
                                    
                                    <div class="flex gap-1 opacity-0 group-hover/card:opacity-100 transition-all">
                                        <button onclick='openEditModal(<?= json_encode($t) ?>)' class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-primary hover:bg-primary-50 rounded-xl transition-all">
                                            <i class="ph-bold ph-pencil-simple text-lg"></i>
                                        </button>
                                        <button onclick="deleteSlot(<?= $t->id ?>)" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                            <i class="ph-bold ph-x-circle text-lg"></i>
                                        </button>
                                    </div>
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

<!-- Config Modal (Add/Edit Routine) -->
<div id="templateModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[80] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-md overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <div>
                <h3 id="modalTitle" class="text-2xl font-black text-slate-800 tracking-tight">Configure Routine</h3>
                <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-widest">Day: <span id="modalDayName" class="text-primary"></span></p>
            </div>
            <button onclick="closeModal()" class="p-3 hover:bg-slate-50 rounded-2xl text-slate-300 hover:text-slate-600 transition-all">
                <i class="ph-bold ph-x text-xl"></i>
            </button>
        </div>

        <form id="templateForm" action="<?= URLROOT ?>/schedules/store-template" method="POST" class="p-8 space-y-6">
            <?php csrf_field(); ?>
            <input type="hidden" name="id" id="inputSlotId">
            <input type="hidden" name="day_of_week" id="inputDayOfWeek">

            <div class="grid grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] ml-1">Type</label>
                    <select name="mass_type" id="inputMassType" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all">
                        <?php foreach($activityTypes as $at): ?>
                            <option value="<?= h($at->name) ?>"><?= h($at->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] ml-1">Time</label>
                    <input type="time" name="mass_time" id="inputMassTime" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] ml-1">Event Details (Optional)</label>
                <input type="text" name="event_name" id="inputEventName" placeholder="e.g. Regular Parish Mass" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] ml-1">Visual Marker</label>
                <div class="flex flex-wrap gap-3 mt-1">
                    <?php $colors = ['green','purple','yellow','blue','indigo','pink','red','teal','gray']; foreach($colors as $c): $bg = "bg-{$c}-500"; if($c=='yellow') $bg="bg-yellow-400"; ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="<?= $c ?>" class="peer hidden color-radio">
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

<!-- Quick Setup Preset Modal -->
<div id="presetModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[80] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Quick Setup Library</h3>
                <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-widest">Select presets to populate Master Plan</p>
            </div>
            <div class="flex gap-2">
                <button onclick="openAddPresetModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition-all">
                    <i class="ph-bold ph-plus"></i> New Preset
                </button>
                <button onclick="document.getElementById('presetModal').classList.add('hidden')" class="p-2.5 hover:bg-slate-100 rounded-xl text-slate-400 transition-all">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
            </div>
        </div>

        <form id="presetForm" action="<?= URLROOT ?>/schedules/apply-presets" method="POST" class="p-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
            <?php csrf_field(); ?>
            
            <?php if(empty($presets)): ?>
                <div class="text-center py-12 opacity-40">
                    <i class="ph-bold ph-stack text-5xl mb-4 block"></i>
                    <p class="font-bold uppercase tracking-widest text-xs">No presets found in library.</p>
                </div>
            <?php else: 
                $groups = [];
                foreach($presets as $p) $groups[$p->preset_group][] = $p;
                foreach($groups as $groupName => $items):
            ?>
                <div class="mb-10 last:mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs border-b-2 border-indigo-100 pb-1"><?= h($groupName) ?></h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach($items as $p): ?>
                            <div class="flex items-center gap-3 p-4 rounded-2xl border border-slate-100 preset-row group/row relative overflow-hidden">
                                <input type="checkbox" name="presets[]" value="<?= $p->id ?>" checked class="rounded text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                                <div class="flex-1">
                                    <p class="text-xs font-black text-slate-700"><?= h($p->name) ?></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter"><?= $days[$p->day_of_week] ?> at <?= date('h:i A', strtotime($p->mass_time)) ?></p>
                                </div>
                                <div class="flex gap-1 opacity-0 group-hover/row:opacity-100 transition-all">
                                    <button type="button" onclick='openEditPresetModal(<?= json_encode($p) ?>)' class="p-2 text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg">
                                        <i class="ph-bold ph-pencil-simple text-sm"></i>
                                    </button>
                                    <button type="button" onclick="deletePreset(<?= $p->id ?>)" class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                        <i class="ph-bold ph-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; endif; ?>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="bg-primary-50/50 p-6 rounded-[2rem] border border-primary-100">
                    <label class="flex items-center gap-3 cursor-pointer mb-4">
                        <input type="checkbox" name="also_generate" id="alsoGenerate" value="1" onchange="toggleGenerateFields()" class="rounded text-primary w-5 h-5 focus:ring-primary border-primary-200">
                        <span class="text-sm font-black text-primary-800 tracking-tight">Auto-generate schedules immediately?</span>
                    </label>
                    
                    <div id="genFields" class="hidden grid grid-cols-2 gap-4 animate-fade-in">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-primary-400 uppercase tracking-widest ml-1">Start Month</label>
                            <select name="gen_month" class="w-full px-4 py-3 bg-white border border-primary-100 rounded-2xl text-xs font-bold text-slate-700">
                                <?php foreach($months as $i => $m): ?>
                                    <option value="<?= $i+1 ?>" <?= ($i+1) == ($currentM) ? 'selected' : '' ?>><?= $m ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-primary-400 uppercase tracking-widest ml-1">Generate for...</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="gen_duration_type" onchange="toggleGenValueOptions('presetGenValue', this.value)" class="w-full px-3 py-3 bg-white border border-primary-100 rounded-2xl text-xs font-bold text-slate-700">
                                    <option value="weeks">Weeks</option>
                                    <option value="months">Months</option>
                                </select>
                                <select name="gen_duration" id="presetGenValue" class="w-full px-3 py-3 bg-white border border-primary-100 rounded-2xl text-xs font-bold text-slate-700">
                                    <!-- Populated by JS -->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex gap-4 sticky bottom-0 bg-white/90 backdrop-blur-sm -mx-8 px-8 pb-4">
                <button type="button" onclick="document.getElementById('presetModal').classList.add('hidden')" class="flex-1 py-4 text-sm font-bold text-slate-400">Cancel</button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold text-sm hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">Apply Selected to Master Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Manage Individual Preset Modal (Add/Edit) -->
<div id="managePresetModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[80] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-indigo-50/30">
            <div>
                <h3 id="presetModalTitle" class="text-2xl font-black text-slate-800 tracking-tight">Setup Preset</h3>
                <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-widest">Configuration for Preset Library</p>
            </div>
            <button onclick="document.getElementById('managePresetModal').classList.add('hidden')" class="p-3 hover:bg-white rounded-2xl text-slate-300 hover:text-slate-600 transition-all shadow-sm">
                <i class="ph-bold ph-x text-xl"></i>
            </button>
        </div>

        <form id="managePresetForm" action="<?= URLROOT ?>/schedules/store-preset" method="POST" class="p-8 space-y-6">
            <?php csrf_field(); ?>
            <input type="hidden" name="id" id="inputPresetId">
            
            <div class="grid grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Preset Name</label>
                    <input type="text" name="name" id="inputPresetName" required placeholder="e.g. Sunday 6AM" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Group</label>
                    <input type="text" name="preset_group" id="inputPresetGroup" required placeholder="Sunday, Weekday, etc" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Day of Week</label>
                    <select name="day_of_week" id="inputPresetDay" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                        <?php foreach($days as $idx => $name): ?>
                            <option value="<?= $idx ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Time</label>
                    <input type="time" name="mass_time" id="inputPresetTime" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Activity Type</label>
                    <select name="mass_type" id="inputPresetType" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                        <?php foreach($activityTypes as $at): ?>
                            <option value="<?= h($at->name) ?>"><?= h($at->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Visual Color</label>
                    <select name="color" id="inputPresetColor" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                        <?php $presetColors = ['green','purple','yellow','blue','indigo','pink','red','teal','gray']; foreach($presetColors as $c): ?>
                            <option value="<?= $c ?>"><?= ucfirst($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex gap-4">
                <button type="button" onclick="document.getElementById('managePresetModal').classList.add('hidden')" class="flex-1 py-4 rounded-2xl border border-slate-100 text-slate-500 text-sm font-bold hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold text-sm hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">Save to Library</button>
            </div>
        </form>
    </div>
</div>

<!-- Liturgical Season Modal (Add/Edit) -->
<div id="seasonModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[80] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-xl overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <div>
                <h3 id="seasonModalTitle" class="text-2xl font-black text-slate-800 tracking-tight">Liturgical Event</h3>
                <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-widest">Override Schedule Colors</p>
            </div>
            <button onclick="document.getElementById('seasonModal').classList.add('hidden')" class="p-3 hover:bg-slate-50 rounded-2xl text-slate-300 hover:text-slate-600 transition-all">
                <i class="ph-bold ph-x text-xl"></i>
            </button>
        </div>

        <form id="seasonForm" action="<?= URLROOT ?>/schedules/store-season" method="POST" class="p-8 space-y-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
            <?php csrf_field(); ?>
            <input type="hidden" name="id" id="inputSeasonId">
            
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Season/Event Name</label>
                    <input type="text" name="name" id="inputSeasonName" required placeholder="e.g. Lent, Advent, Parish Fiesta" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Start Date</label>
                        <input type="date" name="start_date" id="inputSeasonStart" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">End Date</label>
                        <input type="date" name="end_date" id="inputSeasonEnd" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Theme Color (Override)</label>
                    <div class="flex flex-wrap gap-3 mt-1">
                        <?php $colors = ['green','purple','yellow','blue','indigo','pink','red','teal','gray']; foreach($colors as $c): $bg = "bg-{$c}-500"; if($c=='yellow') $bg="bg-yellow-400"; ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="<?= $c ?>" required class="peer hidden season-color-radio">
                                <span class="block w-8 h-8 rounded-full <?= $bg ?> ring-offset-2 peer-checked:ring-2 peer-checked:ring-slate-800 transition-all hover:scale-110"></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Exemptions -->
                <div class="pt-4 border-t border-slate-50">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 block mb-3">Exempt Categories <span class="text-slate-300 font-medium lowercase">(Categories that will NOT be overridden)</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <?php foreach($activityTypes as $at): ?>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 cursor-pointer hover:bg-slate-50 transition-all">
                                <input type="checkbox" name="exempted_types[]" value="<?= h($at->name) ?>" class="exempt-checkbox rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                <span class="text-[11px] font-bold text-slate-600"><?= h($at->name) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="pt-6 flex gap-4 sticky bottom-0 bg-white/90 backdrop-blur-sm pb-2">
                <button type="button" onclick="document.getElementById('seasonModal').classList.add('hidden')" class="flex-1 py-4 rounded-2xl border border-slate-100 text-slate-500 text-sm font-bold hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold text-sm hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">Save Override</button>
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

<form id="deleteSeasonForm" action="<?= URLROOT ?>/schedules/delete-season" method="POST" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="id" id="deleteSeasonId">
</form>

<form id="deletePresetForm" action="<?= URLROOT ?>/schedules/delete-preset" method="POST" class="hidden">
    <?php csrf_field(); ?>
    <input type="hidden" name="id" id="deletePresetId">
</form>

<script>
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    function toggleGenValueOptions(selectId, type) {
        const select = document.getElementById(selectId);
        select.innerHTML = '';
        if (type === 'weeks') {
            for(let i=1; i<=8; i++) {
                const opt = document.createElement('option');
                opt.value = i; opt.innerText = `${i} Week${i>1?'s':''}`;
                select.appendChild(opt);
            }
        } else {
            for(let i=1; i<=6; i++) {
                const opt = document.createElement('option');
                opt.value = i; opt.innerText = `${i} Month${i>1?'s':''}`;
                select.appendChild(opt);
            }
        }
    }

    function openPresetModal() {
        toggleGenValueOptions('presetGenValue', 'weeks');
        document.getElementById('presetModal').classList.remove('hidden');
    }

    function openAddPresetModal() {
        document.getElementById('managePresetForm').reset();
        document.getElementById('inputPresetId').value = '';
        document.getElementById('presetModalTitle').innerText = 'New Setup Preset';
        document.getElementById('managePresetModal').classList.remove('hidden');
    }

    function openEditPresetModal(preset) {
        document.getElementById('managePresetForm').reset();
        document.getElementById('inputPresetId').value = preset.id;
        document.getElementById('presetModalTitle').innerText = 'Edit Setup Preset';
        
        document.getElementById('inputPresetName').value = preset.name;
        document.getElementById('inputPresetGroup').value = preset.preset_group;
        document.getElementById('inputPresetDay').value = preset.day_of_week;
        document.getElementById('inputPresetTime').value = preset.mass_time;
        document.getElementById('inputPresetType').value = preset.mass_type;
        document.getElementById('inputPresetColor').value = preset.color;

        document.getElementById('managePresetModal').classList.remove('hidden');
    }

    function deletePreset(id) {
        showConfirm('Delete this preset from your library?', 'Remove Preset', function() {
            document.getElementById('deletePresetId').value = id;
            document.getElementById('deletePresetForm').submit();
        });
    }

    function toggleGenerateFields() {
        const checked = document.getElementById('alsoGenerate').checked;
        document.getElementById('genFields').classList.toggle('hidden', !checked);
    }

    function openAddModal(dayIndex) {
        document.getElementById('templateForm').reset();
        document.getElementById('inputSlotId').value = '';
        document.getElementById('inputDayOfWeek').value = dayIndex;
        document.getElementById('modalDayName').innerText = days[dayIndex];
        document.getElementById('modalTitle').innerText = 'Configure Routine';
        
        // Default color blue
        const blueRadio = document.querySelector('.color-radio[value="blue"]');
        if (blueRadio) blueRadio.checked = true;

        document.getElementById('templateModal').classList.remove('hidden');
    }

    function openEditModal(slot) {
        document.getElementById('templateForm').reset();
        document.getElementById('inputSlotId').value = slot.id;
        document.getElementById('inputDayOfWeek').value = slot.day_of_week;
        document.getElementById('modalDayName').innerText = days[slot.day_of_week];
        document.getElementById('modalTitle').innerText = 'Edit Routine';
        
        document.getElementById('inputMassType').value = slot.mass_type;
        document.getElementById('inputMassTime').value = slot.mass_time;
        document.getElementById('inputEventName').value = slot.event_name || '';
        
        const color = slot.color || 'blue';
        const colorRadio = document.querySelector(`.color-radio[value="${color}"]`);
        if (colorRadio) colorRadio.checked = true;

        document.getElementById('templateModal').classList.remove('hidden');
    }

    function openAddSeasonModal() {
        document.getElementById('seasonForm').reset();
        document.getElementById('inputSeasonId').value = '';
        document.getElementById('seasonModalTitle').innerText = 'Liturgical Event';
        
        // Uncheck all exemptions
        document.querySelectorAll('.exempt-checkbox').forEach(cb => cb.checked = false);

        document.getElementById('seasonModal').classList.remove('hidden');
    }

    function openEditSeasonModal(season) {
        document.getElementById('seasonForm').reset();
        document.getElementById('inputSeasonId').value = season.id;
        document.getElementById('seasonModalTitle').innerText = 'Edit Liturgical Event';
        
        document.getElementById('inputSeasonName').value = season.name;
        document.getElementById('inputSeasonStart').value = season.start_date;
        document.getElementById('inputSeasonEnd').value = season.end_date;
        
        const colorRadio = document.querySelector(`.season-color-radio[value="${season.color}"]`);
        if (colorRadio) colorRadio.checked = true;

        // Set exemptions
        const exempted = season.exempted_types ? JSON.parse(season.exempted_types) : [];
        document.querySelectorAll('.exempt-checkbox').forEach(cb => {
            cb.checked = exempted.includes(cb.value);
        });

        document.getElementById('seasonModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('templateModal').classList.add('hidden');
    }

    function openCopyModal(dayIndex) {
        document.getElementById('copyFromDayId').value = dayIndex;
        document.getElementById('copyFromDayName').innerText = days[dayIndex];
        document.getElementById('copyModal').classList.remove('hidden');
    }

    function openSeasonModal() {
        openAddSeasonModal();
    }

    function deleteSlot(id) {
        showConfirm('Remove this routine from the Master Plan?', 'Delete Routine', function() {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteTemplateForm').submit();
        });
    }

    function deleteSeason(id) {
        showConfirm('Remove this liturgical override?', 'Remove Event', function() {
            document.getElementById('deleteSeasonId').value = id;
            document.getElementById('deleteSeasonForm').submit();
        });
    }

    function clearDay(dayIndex) {
        showConfirm('Remove ALL routines for ' + days[dayIndex] + '?', 'Clear Day', function() {
            document.getElementById('clearDayId').value = dayIndex;
            document.getElementById('clearDayForm').submit();
        });
    }

    function openGenerateModal() {
        toggleGenValueOptions('genDurationValue', 'weeks');
        document.getElementById('generateModal').classList.remove('hidden');
    }

    function confirmGenerate() {
        const m = document.getElementById('genMonth').value;
        const y = document.getElementById('genYear').value;
        const type = document.getElementById('genDurationType').value;
        const val = document.getElementById('genDurationValue').value;
        
        const monthName = document.querySelector(`#genMonth option[value="${m}"]`).innerText;
        const durationStr = val + ' ' + (type === 'weeks' ? (val>1?'weeks':'week') : (val>1?'months':'month'));
        
        let msg = `Generate all mass slots starting ${monthName} ${y} for a duration of ${durationStr} based on the Master Plan?`;
        
        showConfirm(msg, 'Confirm Auto-Fill', function() {
            let url = `<?= URLROOT ?>/schedules/generate?month=${m}&year=${y}&duration_type=${type}&duration=${val}`;
            window.location.href = url;
        });
    }
</script>
