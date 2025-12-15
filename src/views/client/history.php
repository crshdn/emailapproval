<?php $pageTitle = 'Approval History'; $token = $client['access_token']; ob_start(); ?>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white mb-2">Approval History</h2>
    <p class="text-slate-400">Review all your past approval decisions</p>
</div>

<?php if (empty($history)): ?>
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-12 text-center">
        <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-white mb-2">No history yet</h3>
        <p class="text-slate-400">Your approval history will appear here once you start reviewing content</p>
    </div>
<?php else: ?>
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="divide-y divide-slate-800">
            <?php foreach ($history as $item): ?>
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 <?= $item['action'] === 'approved' ? 'bg-emerald-600/20' : 'bg-rose-600/20' ?>">
                            <?php if ($item['action'] === 'approved'): ?>
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
                            <div class="flex items-center gap-3 mb-2">
                                <span class="font-medium text-white">
                                    <?= $item['item_type'] === 'subject_line' ? 'Subject Line' : 'Email Body' ?>
                                </span>
                                <span class="px-2 py-0.5 rounded text-xs <?= $item['action'] === 'approved' ? 'bg-emerald-600/20 text-emerald-400' : 'bg-rose-600/20 text-rose-400' ?>">
                                    <?= ucfirst($item['action']) ?>
                                </span>
                                <?php if ($item['campaign_name']): ?>
                                    <span class="text-slate-500 text-sm"><?= htmlspecialchars($item['campaign_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($item['item_preview']): ?>
                                <p class="text-slate-300 mb-2">
                                    <?php if ($item['item_type'] === 'subject_line'): ?>
                                        "<?= htmlspecialchars($item['item_preview']) ?>"
                                    <?php else: ?>
                                        <?= htmlspecialchars(substr(strip_tags($item['item_preview']), 0, 150)) ?>...
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($item['feedback']): ?>
                                <div class="mt-3 p-3 bg-amber-900/20 border border-amber-700/30 rounded-lg">
                                    <p class="text-sm text-amber-300 font-medium mb-1">Your Feedback:</p>
                                    <p class="text-slate-300"><?= htmlspecialchars($item['feedback']) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <p class="text-sm text-slate-500 mt-2">
                                <?= date('F j, Y \a\t g:i A', strtotime($item['created_at'])) ?>
                                • Revision <?= $item['revision_number'] ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/client.php'; ?>

