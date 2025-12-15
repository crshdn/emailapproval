<?php $pageTitle = htmlspecialchars($client['name']); ob_start(); ?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-slate-400 mb-6">
    <a href="/admin/clients" class="hover:text-white">Clients</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-white"><?= htmlspecialchars($client['name']) ?></span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Client Info Card -->
    <div class="lg:col-span-2 bg-slate-900 rounded-xl border border-slate-800 p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Client Details</h3>
        
        <form method="POST" action="/admin/clients/<?= $client['id'] ?>/update" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="<?= htmlspecialchars($client['name']) ?>"
                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-500"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="<?= htmlspecialchars($client['email']) ?>"
                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-500"
                    >
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" <?= $client['is_active'] ? 'checked' : '' ?> class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-electric-600 focus:ring-electric-500">
                <label for="is_active" class="text-slate-300">Active</label>
            </div>
            
            <button type="submit" class="px-4 py-2 bg-electric-600 hover:bg-electric-500 text-white font-medium rounded-lg transition-all">
                Save Changes
            </button>
        </form>
    </div>
    
    <!-- Portal Link Card -->
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Portal Access</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Portal URL</label>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        readonly 
                        value="<?= htmlspecialchars($appUrl) ?>/portal/<?= htmlspecialchars($client['access_token']) ?>"
                        class="flex-1 px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-400 text-sm truncate"
                        id="portalUrl"
                    >
                    <button onclick="navigator.clipboard.writeText(document.getElementById('portalUrl').value); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000)" class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm transition-colors">
                        Copy
                    </button>
                </div>
            </div>
            
            <div class="flex flex-col gap-2">
                <form method="POST" action="/admin/clients/<?= $client['id'] ?>/send-link">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-all">
                        Send Link to Client
                    </button>
                </form>
                
                <form method="POST" action="/admin/clients/<?= $client['id'] ?>/regenerate-token" onsubmit="return confirm('This will invalidate the current link. Continue?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button type="submit" class="w-full px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-medium rounded-lg transition-all">
                        Regenerate Link
                    </button>
                </form>
            </div>
            
            <!-- Delete Client -->
            <div class="pt-4 mt-4 border-t border-slate-700">
                <form method="POST" action="/admin/clients/<?= $client['id'] ?>/delete" onsubmit="return confirm('Are you sure you want to delete this client? This will also delete all their campaigns and content. This action cannot be undone.')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button type="submit" class="w-full px-4 py-2 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white font-medium rounded-lg transition-all border border-red-600/30 hover:border-red-600">
                        Delete Client
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Campaigns Section -->
<div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-white">Campaigns</h3>
        <button 
            onclick="document.getElementById('createCampaignModal').classList.remove('hidden')"
            class="px-4 py-2 bg-electric-600 hover:bg-electric-500 text-white font-medium rounded-lg transition-all text-sm"
        >
            + Add Campaign
        </button>
    </div>
    
    <?php if (empty($campaigns)): ?>
        <p class="text-slate-400 text-center py-8">No campaigns yet. Create one to start adding email content for approval.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($campaigns as $campaign): ?>
                <a href="/admin/campaigns/<?= $campaign['id'] ?>" class="block p-4 bg-slate-800/50 rounded-lg hover:bg-slate-800 transition-colors">
                    <h4 class="font-medium text-white"><?= htmlspecialchars($campaign['name']) ?></h4>
                    <?php if ($campaign['description']): ?>
                        <p class="text-sm text-slate-400 mt-1 truncate"><?= htmlspecialchars($campaign['description']) ?></p>
                    <?php endif; ?>
                    
                    <div class="flex gap-4 mt-3 text-sm">
                        <div>
                            <span class="text-slate-500">Subjects:</span>
                            <?php if ($campaign['pending_subjects'] > 0): ?>
                                <span class="text-amber-400"><?= $campaign['pending_subjects'] ?> pending</span>
                            <?php else: ?>
                                <span class="text-emerald-400"><?= $campaign['approved_subjects'] ?> approved</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="text-slate-500">Emails:</span>
                            <?php if ($campaign['pending_emails'] > 0): ?>
                                <span class="text-amber-400"><?= $campaign['pending_emails'] ?> pending</span>
                            <?php else: ?>
                                <span class="text-emerald-400"><?= $campaign['approved_emails'] ?> approved</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Create Campaign Modal -->
<div id="createCampaignModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-slate-900 rounded-2xl border border-slate-800 w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">Add New Campaign</h3>
            <button onclick="document.getElementById('createCampaignModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="/admin/campaigns/create" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
            
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Campaign Name</label>
                <input 
                    type="text" 
                    name="name" 
                    required
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-500"
                    placeholder="e.g. Auto Insurance, Mortgage, Debt"
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Description (optional)</label>
                <textarea 
                    name="description" 
                    rows="3"
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-500"
                    placeholder="Brief description of this campaign"
                ></textarea>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('createCampaignModal').classList.add('hidden')" class="flex-1 px-4 py-3 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-electric-600 text-white font-medium rounded-lg hover:bg-electric-500 transition-colors">
                    Create Campaign
                </button>
            </div>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>

