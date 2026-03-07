<div class="mb-8">
    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Activity Center</h2>
    <p class="text-slate-500 text-sm mt-1 font-medium">Your central hub for updates, announcements, and personal alerts.</p>
</div>

<!-- Segmented Control Tabs & Actions -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="inline-flex bg-slate-100/80 p-1.5 rounded-2xl gap-1 min-w-max">
        <button onclick="filterUpdates('all')" id="tab-all" class="relative px-6 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 tab-btn text-white bg-primary shadow-sm">
            <span class="relative z-10">All Updates</span>
        </button>
        <button onclick="filterUpdates('announcement')" id="tab-announcement" class="relative px-6 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 tab-btn text-slate-500 hover:text-slate-700 hover:bg-white/50">
            <span class="relative z-10 flex items-center gap-2">
                <i class="ph-bold ph-megaphone-simple text-sm"></i>
                Announcements
            </span>
        </button>
        <button onclick="filterUpdates('personal')" id="tab-personal" class="relative px-6 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 tab-btn text-slate-500 hover:text-slate-700 hover:bg-white/50">
            <span class="relative z-10 flex items-center gap-2">
                <i class="ph-bold ph-bell-ringing text-sm"></i>
                Personal Alerts
                <?php if ($personalCount > 0): ?>
                    <span id="tab-badge-personal" class="bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-md ml-1 animate-pulse"><?= $personalCount ?> new</span>
                <?php endif; ?>
            </span>
        </button>
    </div>

    <?php if ($personalCount > 0): ?>
    <form id="markAllReadForm" action="<?= URLROOT ?>/notifications/mark-all-read" method="POST">
        <?php csrf_field(); ?>
        <button type="button" onclick="showConfirm('Mark all notifications as read?', 'Confirm Action', () => document.getElementById('markAllReadForm').submit())" 
                class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 hover:text-primary hover:border-primary/30 transition-all shadow-sm">
            <i class="ph-bold ph-checks text-base"></i>
            Mark all as Read
        </button>
    </form>
    <?php endif; ?>
</div>

<div class="max-w-4xl space-y-4 pb-20">
    <?php if (empty($updates)): ?>
        <div class="bg-white/50 border border-slate-200/60 border-dashed rounded-3xl p-16 text-center">
            <div class="w-24 h-24 bg-slate-100 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ph-bold ph-ghost text-5xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-700 tracking-tight">All caught up!</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto mt-2 font-medium">You don't have any new updates or notifications at the moment.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($updates as $u): ?>
                <?php 
                    $isAnnouncement = ($u->source === 'announcement');
                    $isRead = $isAnnouncement ? true : $u->is_read;
                    
                    $iconClass = 'ph-info';
                    $iconColor = 'text-blue-500';
                    $iconBg = 'bg-blue-50';
                    $tagLabel = 'Personal Alert';
                    $tagStyle = 'bg-slate-100 text-slate-500 border-slate-200';

                    if (!$isAnnouncement) {
                        if ($u->type === 'success') { $iconClass = 'ph-check-circle'; $iconColor = 'text-emerald-500'; $iconBg = 'bg-emerald-50'; }
                        if ($u->type === 'warning') { $iconClass = 'ph-warning'; $iconColor = 'text-amber-500'; $iconBg = 'bg-amber-50'; }
                        if ($u->type === 'danger') { $iconClass = 'ph-warning-octagon'; $iconColor = 'text-red-500'; $iconBg = 'bg-red-50'; }
                        if ($u->type === 'schedule') { $iconClass = 'ph-calendar-check'; $iconColor = 'text-primary'; $iconBg = 'bg-primary-50'; }
                    } else {
                        $iconClass = 'ph-megaphone-simple';
                        $iconColor = 'text-indigo-500';
                        $iconBg = 'bg-indigo-50';
                        $tagLabel = 'Announcement';
                        $tagStyle = 'bg-indigo-50 text-indigo-600 border-indigo-100';
                    }
                ?>
                <div class="update-item transition-all duration-300" data-source="<?= $u->source ?>">
                    <div id="<?= $isAnnouncement ? 'ann-'.$u->id : 'notif-'.$u->id ?>" 
                         class="group bg-white rounded-2xl p-5 border <?= $isRead ? 'border-slate-100' : 'border-primary shadow-md shadow-primary/5' ?> hover:border-slate-300 transition-all flex gap-4 relative">
                        
                        <!-- Unread Indicator Dot -->
                        <?php if (!$isRead): ?>
                            <div class="absolute top-6 right-5 w-2.5 h-2.5 bg-primary rounded-full animate-pulse"></div>
                        <?php endif; ?>

                        <!-- Icon -->
                        <div class="shrink-0 mt-1">
                            <div class="w-10 h-10 <?= $iconBg ?> <?= $iconColor ?> rounded-full flex items-center justify-center text-xl border border-white shadow-sm">
                                <i class="ph-bold <?= $iconClass ?>"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0 pr-6">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="px-2 py-0.5 rounded-md border text-[10px] font-bold uppercase tracking-wide <?= $tagStyle ?>">
                                    <?= $tagLabel ?>
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400">
                                    <?= date('M d, g:i A', strtotime($u->created_at)) ?>
                                </span>
                            </div>
                            
                            <h4 class="font-bold text-slate-800 text-sm md:text-base mb-1 <?= !$isRead ? 'text-primary' : '' ?>">
                                <?= h($u->title) ?>
                            </h4>
                            
                            <p class="text-slate-600 text-sm leading-relaxed mb-3">
                                <?= $isAnnouncement ? nl2br(h($u->message)) : h($u->message) ?>
                            </p>
                            
                            <div class="flex items-center gap-4 mt-2">
                                <?php if (!$isAnnouncement && $u->link): ?>
                                    <a href="<?= URLROOT . $u->link ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-primary-600 transition-colors">
                                        View Details
                                        <i class="ph-bold ph-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!$isAnnouncement && !$u->is_read): ?>
                                    <button onclick="markRead(<?= $u->id ?>)" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-slate-700 transition-colors bg-slate-50 hover:bg-slate-100 px-3 py-1 rounded-lg">
                                        <i class="ph-bold ph-check"></i>
                                        Mark as Read
                                    </button>
                                <?php endif; ?>

                                <?php if ($isAnnouncement && isset($u->author)): ?>
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-lg">
                                        <i class="ph-fill ph-user-circle text-slate-300 text-sm"></i>
                                        <?= h($u->author) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function filterUpdates(source) {
        // Update Tabs styling
        const tabs = ['all', 'announcement', 'personal'];
        tabs.forEach(t => {
            const btn = document.getElementById('tab-' + t);
            if (t === source) {
                // Active state
                btn.classList.add('bg-primary', 'text-white', 'shadow-sm');
                btn.classList.remove('text-slate-500', 'hover:text-slate-700', 'hover:bg-white/50');
            } else {
                // Inactive state
                btn.classList.remove('bg-primary', 'text-white', 'shadow-sm');
                btn.classList.add('text-slate-500', 'hover:text-slate-700', 'hover:bg-white/50');
            }
        });

        // Filter Items with simple animation
        const items = document.querySelectorAll('.update-item');
        items.forEach(item => {
            if (source === 'all' || item.dataset.source === source) {
                item.style.display = 'block';
                // Trigger reflow for animation
                void item.offsetWidth;
                item.classList.add('opacity-100', 'translate-y-0');
                item.classList.remove('opacity-0', 'translate-y-4');
            } else {
                item.style.display = 'none';
                item.classList.add('opacity-0', 'translate-y-4');
                item.classList.remove('opacity-100', 'translate-y-0');
            }
        });
    }

    function markRead(id) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');

        fetch('<?= URLROOT ?>/notifications/mark-read', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const el = document.getElementById('notif-' + id);
                // Adjust border
                el.classList.remove('border-primary', 'shadow-md', 'shadow-primary/5');
                el.classList.add('border-slate-100');
                
                // Remove title highlight
                const title = el.querySelector('h4');
                if(title) title.classList.remove('text-primary');

                // Remove the unread dot
                const dot = el.querySelector('.animate-pulse');
                if (dot) dot.remove();
                
                // Remove the Mark as Read button
                const btn = el.querySelector('button[onclick^="markRead"]');
                if (btn) btn.remove();
                
                // Update badge count in tab if exists
                const badge = document.getElementById('tab-badge-personal');
                if(badge) {
                    let count = parseInt(badge.innerText);
                    if(count > 1) {
                        badge.innerText = (count - 1) + ' new';
                    } else {
                        badge.remove();
                    }
                }

                // Update Sidebar and Mobile Badges
                updateGlobalBadges(-1);
            }
        });
    }

    function updateGlobalBadges(diff) {
        // This targets the red dots/numbers in the sidebar
        const sidebarBadges = document.querySelectorAll('.unread-badge-count');
        sidebarBadges.forEach(b => {
            let current = parseInt(b.innerText) || 0;
            let newVal = current + diff;
            if(newVal > 0) {
                b.innerText = newVal;
            } else {
                b.classList.add('hidden');
            }
        });
    }
</script>
