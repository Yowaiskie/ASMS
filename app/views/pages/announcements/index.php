<div class="flex items-end justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Announcements</h2>
        <p class="text-slate-500 text-sm mt-1">Create and manage announcements</p>
    </div>
    
    <button onclick="toggleElement('createForm')" class="bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center gap-2 font-semibold text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Announcement
    </button>
</div>

<div id="createForm" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 mb-8 hidden transition-all duration-300">
    <div class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800">Create New Announcement</h3>
    </div>

    <form action="<?= URLROOT ?>/announcements/store" method="POST" class="space-y-6">
        <?php csrf_field(); ?>
        <input type="hidden" name="page" value="<?= $pagination['page'] ?? 1 ?>">
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Title</label>
            <input type="text" name="title" required placeholder="Enter announcement title" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-slate-400">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Category</label>
            <div class="relative">
                <select name="category" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all appearance-none cursor-pointer">
                    <option value="General">General</option>
                    <option value="Training">Training</option>
                    <option value="Schedule">Schedule</option>
                    <option value="Reminder">Reminder</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Message</label>
            <textarea name="message" required rows="6" placeholder="Write your announcement here..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-slate-400 resize-none"></textarea>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-[0.98]">Post Announcement</button>
            <button type="button" onclick="toggleElement('createForm')" class="px-8 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all active:scale-[0.98]">Cancel</button>
        </div>
    </form>
</div>

<div class="space-y-6">
    <?php if(!empty($announcements)): ?>
        <?php foreach($announcements as $announcement): ?>
            <?php 
                $bgColor = 'bg-blue-100 text-blue-600';
                if($announcement->category == 'Training') $bgColor = 'bg-purple-100 text-purple-600';
                if($announcement->category == 'Reminder') $bgColor = 'bg-yellow-100 text-yellow-600';
            ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative group hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $bgColor ?>"><?= h($announcement->category) ?></span>
                        <span class="text-xs text-slate-400 font-medium"><?= date('M j, Y', strtotime($announcement->created_at)) ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="<?= URLROOT ?>/announcements/delete?id=<?= $announcement->id ?>&page=<?= $pagination['page'] ?? 1 ?>" data-loading onclick="return confirm('Are you sure?')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></a>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2"><?= h($announcement->title) ?></h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-4"><?= nl2br(h($announcement->message)) ?></p>
                <div class="pt-4 border-t border-slate-50"><p class="text-xs text-slate-400 font-medium">Posted by <span class="text-slate-600"><?= h($announcement->author) ?></span></p></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-slate-500 text-center py-8">No announcements posted yet.</p>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
<div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-6">
    <div class="text-xs text-slate-500">
        Page <span class="font-bold"><?= $pagination['page'] ?></span> of <span class="font-bold"><?= $pagination['totalPages'] ?></span>
    </div>
    <div class="flex items-center gap-1.5">
        <?php if ($pagination['page'] > 1): ?>
            <a href="<?= URLROOT ?>/announcements?page=<?= $pagination['page'] - 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
            </a>
        <?php endif; ?>

        <?php 
            $start = max(1, $pagination['page'] - 2);
            $end = min($pagination['totalPages'], $start + 4);
            if ($end - $start < 4) $start = max(1, $end - 4);
            
            for($i = $start; $i <= $end; $i++): 
                $active = ($i == $pagination['page']) ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-100' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 shadow-sm';
        ?>
            <a href="<?= URLROOT ?>/announcements?page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center border rounded-lg text-xs font-bold transition-all <?= $active ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($pagination['page'] < $pagination['totalPages']): ?>
            <a href="<?= URLROOT ?>/announcements?page=<?= $pagination['page'] + 1 ?>" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>