<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Create New Mass Schedule</h2>
    <p class="text-slate-500 text-sm mt-1">Add a new mass to the calendar</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 max-w-2xl">
    <form action="<?= URLROOT ?>/schedules/store" method="POST" class="space-y-6">
        <?php csrf_field(); ?>

        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Mass Type / Description</label>
            <input type="text" name="mass_type" required placeholder="e.g. Sunday Early Mass, Wedding" 
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Date</label>
                <input type="date" name="mass_date" required 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Time</label>
                <input type="time" name="mass_time" required 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Initial Status</label>
            <select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                <option value="Confirmed">Confirmed</option>
                <option value="Pending">Pending</option>
            </select>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-200 transition-all">Create Schedule</button>
            <a href="<?= URLROOT ?>/schedules" class="px-8 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-center transition-all">Cancel</a>
        </div>
    </form>
</div>