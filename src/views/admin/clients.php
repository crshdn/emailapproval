<?php $pageTitle = 'Clients'; ob_start(); ?>

<div class="flex items-center justify-between mb-6">
    <p class="text-slate-400"><?= count($clients) ?> client(s)</p>
    <button 
        onclick="document.getElementById('createClientModal').classList.remove('hidden')"
        class="px-4 py-2 bg-electric-600 hover:bg-electric-500 text-white font-medium rounded-lg transition-all"
    >
        + Add Client
    </button>
</div>

<?php if (empty($clients)): ?>
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-12 text-center">
        <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-white mb-2">No clients yet</h3>
        <p class="text-slate-400 mb-6">Get started by adding your first client</p>
        <button 
            onclick="document.getElementById('createClientModal').classList.remove('hidden')"
            class="px-4 py-2 bg-electric-600 hover:bg-electric-500 text-white font-medium rounded-lg transition-all"
        >
            Add Client
        </button>
    </div>
<?php else: ?>
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-800/50">
                <tr>
                    <th class="text-left px-6 py-4 text-sm font-medium text-slate-300">Client</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-slate-300">Campaigns</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-slate-300">Status</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-slate-300">Pending</th>
                    <th class="text-right px-6 py-4 text-sm font-medium text-slate-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                <?php foreach ($clients as $client): ?>
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-white"><?= htmlspecialchars($client['name']) ?></p>
                                <p class="text-sm text-slate-400"><?= htmlspecialchars($client['email']) ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-300">
                            <?= $client['campaign_count'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($client['is_active']): ?>
                                <span class="px-2 py-1 bg-emerald-600/20 text-emerald-400 rounded text-sm">Active</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-slate-600/20 text-slate-400 rounded text-sm">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php $pending = $client['pending_subjects'] + $client['pending_emails']; ?>
                            <?php if ($pending > 0): ?>
                                <span class="px-2 py-1 bg-amber-600/20 text-amber-400 rounded text-sm"><?= $pending ?></span>
                            <?php else: ?>
                                <span class="text-slate-500">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="/admin/clients/<?= $client['id'] ?>" class="text-electric-400 hover:text-electric-300 font-medium">
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Create Client Modal -->
<div id="createClientModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-slate-900 rounded-2xl border border-slate-800 w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">Add New Client</h3>
            <button onclick="document.getElementById('createClientModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="/admin/clients/create" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Client Name</label>
                <input 
                    type="text" 
                    name="name" 
                    required
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-500"
                    placeholder="e.g. Acme Corp"
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                <input 
                    type="email" 
                    name="email" 
                    required
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-500"
                    placeholder="contact@example.com"
                >
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('createClientModal').classList.add('hidden')" class="flex-1 px-4 py-3 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-electric-600 text-white font-medium rounded-lg hover:bg-electric-500 transition-colors">
                    Create Client
                </button>
            </div>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>

