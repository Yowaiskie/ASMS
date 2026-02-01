<div class="flex items-end justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Altar Servers</h2>
        <p class="text-slate-500 text-sm mt-1">Manage altar servers and their details</p>
    </div>
    
    <button onclick="toggleElement('createUserForm')" class="bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center gap-2 font-semibold text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Register Server
    </button>
</div>

<div id="createUserForm" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 mb-8 hidden transition-all duration-300">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-slate-800">Register New Server</h3>
    </div>

    <form action="<?= URLROOT ?>/servers/store" method="POST" class="space-y-6">
        <?php csrf_field(); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Full Name</label>
                <input type="text" name="name" placeholder="Enter full name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-slate-400">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Rank/Position</label>
                <select name="rank" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none cursor-pointer">
                    <option value="Senior Server">Senior Server</option>
                    <option value="Junior Server">Junior Server</option>
                    <option value="Aspirant">Aspirant</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Team</label>
                <select name="team" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none cursor-pointer">
                    <option value="Team A">Team A</option>
                    <option value="Team B">Team B</option>
                    <option value="Team C">Team C</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Email</label>
                <input type="email" name="email" placeholder="Enter email address" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-slate-400">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Status</label>
                <select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none cursor-pointer">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-[0.98]">Save Server</button>
            <button type="button" onclick="toggleElement('createUserForm')" class="px-8 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all active:scale-[0.98]">Cancel</button>
        </div>

    </form>
</div>

<div class="bg-white rounded-t-2xl border-b border-slate-100 p-6 flex flex-col md:flex-row gap-4 justify-between items-center shadow-sm">
    <div class="w-full md:w-96 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input type="text" placeholder="Search server..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
    </div>
    <button class="flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        Filter
    </button>
</div>

<div class="bg-white rounded-b-2xl shadow-sm border border-slate-100 border-t-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50/50 text-xs font-bold text-slate-500 uppercase border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Rank/Position</th>
                    <th class="px-6 py-4">Team</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                
                <?php if(!empty($servers)): ?>
                    <?php foreach($servers as $server): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php 
                                    // Generate initials
                                    $parts = explode(' ', $server->name);
                                    $initials = '';
                                    if(count($parts) >= 2) {
                                        $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
                                    } else {
                                        $initials = strtoupper(substr($server->name, 0, 2));
                                    }
                                ?>
                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs"><?= h($initials) ?></div>
                                <span class="font-bold text-slate-700"><?= h($server->name) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500"><?= h($server->rank) ?></td>
                        <td class="px-6 py-4 text-slate-500"><?= h($server->team) ?></td>
                        <td class="px-6 py-4">
                            <?php if($server->status == 'Active'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                                                                <div class="flex items-center justify-center gap-2">
                                                                    <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                                                                    <a href="<?= URLROOT ?>/servers/delete?id=<?= $server->id ?>" onclick="return confirm('Are you sure?')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></a>
                                                                </div>                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No servers found.</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>
