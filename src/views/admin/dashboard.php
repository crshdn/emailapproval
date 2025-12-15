<?php $pageTitle = 'Dashboard'; ob_start(); ?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-sm">Total Clients</p>
                <p class="text-3xl font-bold text-white mt-1"><?= count($clients) ?></p>
            </div>
            <div class="w-12 h-12 bg-electric-600/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-electric-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-sm">Pending Approvals</p>
                <p class="text-3xl font-bold text-amber-400 mt-1"><?= $totalPending ?></p>
            </div>
            <div class="w-12 h-12 bg-amber-600/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-sm">Recent Activity</p>
                <p class="text-3xl font-bold text-white mt-1"><?= count($recentActivity) ?></p>
            </div>
            <div class="w-12 h-12 bg-emerald-600/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Clients with Pending Items -->
<?php $clientsWithPending = array_filter($clients, fn($c) => ($c['pending_subjects'] + $c['pending_emails']) > 0); ?>
<?php if (!empty($clientsWithPending)): ?>
<div class="bg-slate-900 rounded-xl border border-slate-800 p-6 mb-8">
    <h3 class="text-lg font-semibold text-white mb-4">Clients with Pending Approvals</h3>
    <div class="space-y-3">
        <?php foreach ($clientsWithPending as $client): ?>
            <a href="/admin/clients/<?= $client['id'] ?>" class="flex items-center justify-between p-4 bg-slate-800/50 rounded-lg hover:bg-slate-800 transition-colors">
                <div>
                    <p class="font-medium text-white"><?= htmlspecialchars($client['name']) ?></p>
                    <p class="text-sm text-slate-400"><?= $client['campaign_count'] ?> campaign(s)</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-3 py-1 bg-amber-600/20 text-amber-400 rounded-full text-sm">
                        <?= $client['pending_subjects'] + $client['pending_emails'] ?> pending
                    </span>
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Recent Activity -->
<div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">Recent Activity</h3>
    <?php if (empty($recentActivity)): ?>
        <p class="text-slate-400 text-center py-8">No activity yet</p>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($recentActivity as $activity): ?>
                <div class="flex items-start gap-4 p-4 bg-slate-800/50 rounded-lg">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center <?= $activity['action'] === 'approved' ? 'bg-emerald-600/20' : 'bg-rose-600/20' ?>">
                        <?php if ($activity['action'] === 'approved'): ?>
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        <?php else: ?>
                            <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-white"><?= htmlspecialchars($activity['client_name'] ?? 'Unknown') ?></span>
                            <span class="text-slate-500">•</span>
                            <span class="text-slate-400"><?= htmlspecialchars($activity['campaign_name'] ?? '') ?></span>
                        </div>
                        <p class="text-sm text-slate-400 mt-1">
                            <?= ucfirst($activity['action']) ?> 
                            <?= $activity['item_type'] === 'subject_line' ? 'subject line' : 'email body' ?>
                        </p>
                        <?php if (!empty($activity['item_preview'])): ?>
                            <p class="text-sm text-slate-500 mt-1 truncate"><?= htmlspecialchars(strip_tags($activity['item_preview'])) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($activity['feedback'])): ?>
                            <p class="text-sm text-amber-400 mt-2 italic">"<?= htmlspecialchars($activity['feedback']) ?>"</p>
                        <?php endif; ?>
                    </div>
                    <div class="text-sm text-slate-500">
                        <?= date('M j, g:i A', strtotime($activity['created_at'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>

