<?php $pageTitle = 'Your Campaigns'; $token = $client['access_token']; ob_start(); ?>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white mb-2">Welcome, <?= htmlspecialchars($client['name']) ?></h2>
    <p class="text-slate-400">Select a campaign to review and approve email content</p>
</div>

<?php if (empty($campaigns)): ?>
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-12 text-center">
        <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-white mb-2">No campaigns yet</h3>
        <p class="text-slate-400">Check back later for content to review</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($campaigns as $campaign): ?>
            <?php $totalPending = $campaign['pending_subjects'] + $campaign['pending_emails']; ?>
            <a href="/portal/<?= htmlspecialchars($token) ?>/campaign/<?= $campaign['id'] ?>" 
               class="block bg-slate-900 rounded-xl border border-slate-800 p-6 hover:border-electric-500/50 hover:shadow-lg hover:shadow-electric-500/10 transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-electric-600/20 rounded-xl flex items-center justify-center group-hover:bg-electric-600/30 transition-colors">
                        <svg class="w-6 h-6 text-electric-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <?php if ($totalPending > 0): ?>
                        <span class="px-3 py-1 bg-amber-600/20 text-amber-400 rounded-full text-sm font-medium">
                            <?= $totalPending ?> pending
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-emerald-600/20 text-emerald-400 rounded-full text-sm font-medium">
                            All reviewed
                        </span>
                    <?php endif; ?>
                </div>
                
                <h3 class="text-lg font-semibold text-white mb-3 group-hover:text-electric-400 transition-colors">
                    <?= htmlspecialchars($campaign['name']) ?>
                </h3>
                
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Subject Lines</span>
                        <div class="flex gap-2">
                            <?php if ($campaign['pending_subjects'] > 0): ?>
                                <span class="text-amber-400"><?= $campaign['pending_subjects'] ?> pending</span>
                            <?php endif; ?>
                            <?php if ($campaign['approved_subjects'] > 0): ?>
                                <span class="text-emerald-400"><?= $campaign['approved_subjects'] ?> approved</span>
                            <?php endif; ?>
                            <?php if ($campaign['denied_subjects'] > 0): ?>
                                <span class="text-rose-400"><?= $campaign['denied_subjects'] ?> denied</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Email Bodies</span>
                        <div class="flex gap-2">
                            <?php if ($campaign['pending_emails'] > 0): ?>
                                <span class="text-amber-400"><?= $campaign['pending_emails'] ?> pending</span>
                            <?php endif; ?>
                            <?php if ($campaign['approved_emails'] > 0): ?>
                                <span class="text-emerald-400"><?= $campaign['approved_emails'] ?> approved</span>
                            <?php endif; ?>
                            <?php if ($campaign['denied_emails'] > 0): ?>
                                <span class="text-rose-400"><?= $campaign['denied_emails'] ?> denied</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/client.php'; ?>

