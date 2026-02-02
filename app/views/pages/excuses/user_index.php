<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800">Excuse Letter</h2>
    <p class="text-slate-500 text-sm mt-1">Submit an excuse for your absence</p>
</div>

<!-- View Toggle -->
<div class="bg-white p-1.5 rounded-xl border border-slate-100 shadow-sm inline-flex mb-8 animate-fade-in-up">
    <button onclick="switchTab('form')" id="btn-form" class="px-4 py-2 rounded-lg text-sm font-bold bg-slate-800 text-white shadow-md transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        File Excuse
    </button>
    <button onclick="switchTab('history')" id="btn-history" class="px-4 py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        My History
    </button>
</div>

<div id="tab-form" class="animate-fade-in-up delay-100">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <form action="<?= URLROOT ?>/excuses/store" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Type of Activity</label>
                    <div class="relative">
                        <select name="type" id="excuse_type" onchange="toggleTimeField()" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none">
                            <option value="" disabled selected>Select Activity</option>
                            <option value="Sunday Schedule">Sunday Schedule</option>
                            <option value="Meeting">Meeting</option>
                            <option value="Special Event">Special Event / Others</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Date of Absence</label>
                    <input type="date" name="date" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div id="time_field" class="hidden animate-fade-in-up">
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Select Schedule Time</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                    <?php 
                        $sundayTimes = ['06:00', '07:30', '09:00', '16:00', '17:30', '19:00'];
                        foreach($sundayTimes as $time):
                            $timeId = str_replace(':', '', $time);
                    ?>
                    <div class="relative">
                        <input type="radio" name="time" id="time_<?= $timeId ?>" value="<?= $time ?>" class="peer hidden">
                        <label for="time_<?= $timeId ?>" class="block py-3 text-center bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 cursor-pointer transition-all hover:bg-slate-100 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 peer-checked:shadow-lg peer-checked:shadow-blue-100 peer-checked:hover:bg-blue-700 peer-checked:hover:text-white">
                            <?= date('h:i A', strtotime($time)) ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Reason / Remarks</label>
                <textarea name="reason" rows="4" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Please state your reason here..."></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Upload Proof (Optional)</label>
                <div class="flex items-center gap-4">
                    <label class="flex-1 cursor-pointer group">
                        <div class="w-full flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100 hover:border-blue-400 hover:text-blue-500 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">Click to upload image</span>
                            <span class="text-xs opacity-70 mt-1">JPG, PNG (Max 5MB)</span>
                        </div>
                        <input type="file" name="proof_image" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                    <div id="image-preview" class="w-24 h-24 bg-slate-100 rounded-xl border border-slate-200 hidden items-center justify-center overflow-hidden">
                        <img src="" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-blue-200 transform active:scale-[0.98] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Submit Excuse Letter
                </button>
            </div>
        </form>
    </div>
</div>

<div id="tab-history" class="hidden animate-fade-in-up delay-100">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="p-6 font-semibold">Date Filed</th>
                    <th class="p-6 font-semibold">Absence Date</th>
                    <th class="p-6 font-semibold">Activity</th>
                    <th class="p-6 font-semibold">Reason</th>
                    <th class="p-6 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <?php if(!empty($excuses)): ?>
                    <?php foreach($excuses as $exc): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-6 text-slate-500"><?= date('M d, Y', strtotime($exc->created_at)) ?></td>
                        <td class="p-6 font-bold text-slate-700"><?= date('M d, Y', strtotime($exc->absence_date)) ?></td>
                        <td class="p-6">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 uppercase">
                                <?= h($exc->type) ?>
                            </span>
                        </td>
                        <td class="p-6 text-slate-600 max-w-xs truncate" title="<?= h($exc->reason) ?>">
                            <?= h($exc->reason) ?>
                        </td>
                        <td class="p-6">
                            <?php 
                                $statusColor = 'bg-yellow-50 text-yellow-600';
                                if($exc->status == 'Approved') $statusColor = 'bg-green-50 text-green-600';
                                if($exc->status == 'Rejected') $statusColor = 'bg-red-50 text-red-600';
                            ?>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold <?= $statusColor ?>">
                                <?= h($exc->status) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400">No excuse letters filed yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function switchTab(tab) {
        const formView = document.getElementById('tab-form');
        const historyView = document.getElementById('tab-history');
        const btnForm = document.getElementById('btn-form');
        const btnHistory = document.getElementById('btn-history');

        if (tab === 'form') {
            formView.classList.remove('hidden');
            historyView.classList.add('hidden');
            
            btnForm.className = "px-4 py-2 rounded-lg text-sm font-bold bg-slate-800 text-white shadow-md transition-all flex items-center gap-2";
            btnHistory.className = "px-4 py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-all flex items-center gap-2";
        } else {
            formView.classList.add('hidden');
            historyView.classList.remove('hidden');
            
            btnHistory.className = "px-4 py-2 rounded-lg text-sm font-bold bg-slate-800 text-white shadow-md transition-all flex items-center gap-2";
            btnForm.className = "px-4 py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-all flex items-center gap-2";
        }
    }

    function toggleTimeField() {
        const type = document.getElementById('excuse_type').value;
        const timeField = document.getElementById('time_field');
        const timeInputs = timeField.querySelectorAll('input');

        if (type === 'Sunday Schedule') {
            timeField.classList.remove('hidden');
            timeInputs.forEach(input => input.required = true);
        } else {
            timeField.classList.add('hidden');
            timeInputs.forEach(input => {
                input.required = false;
                input.checked = false;
            });
        }
    }

    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const img = preview.querySelector('img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
                preview.classList.add('flex');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>