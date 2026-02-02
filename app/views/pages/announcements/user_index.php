<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800">Announcements</h2>
    <p class="text-slate-500 text-sm mt-1">Updates and news for altar servers</p>
</div>

<div class="space-y-6">
    <?php if(!empty($announcements)): ?>
        <?php foreach($announcements as $index => $ann): ?>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 animate-fade-in-up" style="animation-delay: <?= $index * 0.1 ?>s">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <?php
                        $iconBg = 'bg-blue-50 text-blue-600';
                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>';
                        
                        if (stripos($ann->category ?? '', 'Urgent') !== false) {
                            $iconBg = 'bg-red-50 text-red-600';
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
                        }
                    ?>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center <?= $iconBg ?>">
                        <?= $icon ?>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg"><?= h($ann->title) ?></h3>
                        <p class="text-xs text-slate-400">Posted by <?= h($ann->author) ?> • <?= date('M d, Y', strtotime($ann->created_at)) ?></p>
                    </div>
                </div>
                
                <?php if(!empty($ann->category)): ?>
                <span class="self-start md:self-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    <?= h($ann->category) ?>
                </span>
                <?php endif; ?>
            </div>

            <div class="text-slate-600 text-sm leading-relaxed whitespace-pre-line pl-[3.25rem]">
                <?= h($ann->message) ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="bg-white p-12 rounded-2xl shadow-sm border border-slate-100 text-center">
            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 mb-1">No announcements</h3>
            <p class="text-slate-400 text-sm">Check back later for updates.</p>
        </div>
    <?php endif; ?>
</div>