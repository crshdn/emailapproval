<?php $pageTitle = htmlspecialchars($campaign['name']); ob_start(); ?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-slate-400 mb-6">
    <a href="/admin/clients" class="hover:text-white">Clients</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="/admin/clients/<?= $campaign['client_id'] ?>" class="hover:text-white"><?= htmlspecialchars($campaign['client_name']) ?></a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-white"><?= htmlspecialchars($campaign['name']) ?></span>
</div>

<!-- Subject Lines Section -->
<div class="bg-slate-900 rounded-xl border border-slate-800 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-white">Subject Lines</h3>
        <button 
            onclick="document.getElementById('addSubjectModal').classList.remove('hidden')"
            class="px-4 py-2 bg-electric-600 hover:bg-electric-500 text-white font-medium rounded-lg transition-all text-sm"
        >
            + Add Subject Line
        </button>
    </div>
    
    <?php if (empty($subjects)): ?>
        <p class="text-slate-400 text-center py-8">No subject lines yet</p>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($subjects as $subject): ?>
                <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-lg" x-data="{ editing: false, showResubmit: false }">
                    <div class="flex-1">
                        <template x-if="!editing && !showResubmit">
                            <p class="text-white"><?= htmlspecialchars($subject['subject_text']) ?></p>
                        </template>
                        
                        <!-- Edit Form -->
                        <template x-if="editing">
                            <form method="POST" action="/admin/subjects/<?= $subject['id'] ?>/update" class="flex gap-2">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="text" name="subject_text" value="<?= htmlspecialchars($subject['subject_text']) ?>" class="flex-1 px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white">
                                <button type="submit" class="px-3 py-2 bg-electric-600 text-white rounded">Save</button>
                                <button type="button" @click="editing = false" class="px-3 py-2 bg-slate-600 text-white rounded">Cancel</button>
                            </form>
                        </template>
                        
                        <!-- Resubmit Form -->
                        <template x-if="showResubmit">
                            <form method="POST" action="/admin/subjects/<?= $subject['id'] ?>/resubmit" class="flex gap-2">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="text" name="subject_text" value="<?= htmlspecialchars($subject['subject_text']) ?>" class="flex-1 px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white">
                                <button type="submit" class="px-3 py-2 bg-amber-600 text-white rounded">Resubmit</button>
                                <button type="button" @click="showResubmit = false" class="px-3 py-2 bg-slate-600 text-white rounded">Cancel</button>
                            </form>
                        </template>
                        
                        <p class="text-sm text-slate-500 mt-1">Rev <?= $subject['revision_number'] ?> • <?= date('M j, Y', strtotime($subject['created_at'])) ?></p>
                    </div>
                    
                    <div class="flex items-center gap-3 ml-4">
                        <?php if ($subject['status'] === 'pending'): ?>
                            <span class="px-2 py-1 bg-amber-600/20 text-amber-400 rounded text-sm">Pending</span>
                        <?php elseif ($subject['status'] === 'approved'): ?>
                            <span class="px-2 py-1 bg-emerald-600/20 text-emerald-400 rounded text-sm">Approved</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-rose-600/20 text-rose-400 rounded text-sm">Denied</span>
                        <?php endif; ?>
                        
                        <template x-if="!editing && !showResubmit">
                            <div class="flex gap-2">
                                <?php if ($subject['status'] === 'pending'): ?>
                                    <button @click="editing = true" class="text-slate-400 hover:text-white">Edit</button>
                                <?php else: ?>
                                    <button @click="showResubmit = true" class="text-amber-400 hover:text-amber-300">Resubmit</button>
                                <?php endif; ?>
                                <form method="POST" action="/admin/subjects/<?= $subject['id'] ?>/delete" onsubmit="return confirm('Delete this subject line?')">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <button type="submit" class="text-rose-400 hover:text-rose-300">Delete</button>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Email Bodies Section -->
<div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-white">Email Bodies</h3>
        <button 
            onclick="document.getElementById('addEmailModal').classList.remove('hidden')"
            class="px-4 py-2 bg-electric-600 hover:bg-electric-500 text-white font-medium rounded-lg transition-all text-sm"
        >
            + Add Email Body
        </button>
    </div>
    
    <?php if (empty($emails)): ?>
        <p class="text-slate-400 text-center py-8">No email bodies yet</p>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($emails as $email): ?>
                <div class="p-4 bg-slate-800/50 rounded-lg">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <?php if ($email['internal_name']): ?>
                                <p class="font-medium text-white"><?= htmlspecialchars($email['internal_name']) ?></p>
                            <?php endif; ?>
                            <p class="text-sm text-slate-500">Rev <?= $email['revision_number'] ?> • <?= date('M j, Y', strtotime($email['created_at'])) ?></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <?php if ($email['status'] === 'pending'): ?>
                                <span class="px-2 py-1 bg-amber-600/20 text-amber-400 rounded text-sm">Pending</span>
                            <?php elseif ($email['status'] === 'approved'): ?>
                                <span class="px-2 py-1 bg-emerald-600/20 text-emerald-400 rounded text-sm">Approved</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-rose-600/20 text-rose-400 rounded text-sm">Denied</span>
                            <?php endif; ?>
                            
                            <button onclick="openEditEmailModal(<?= $email['id'] ?>, '<?= htmlspecialchars(addslashes($email['internal_name'] ?? '')) ?>', `<?= htmlspecialchars(addslashes($email['body_content'])) ?>`, '<?= $email['status'] ?>')" class="text-slate-400 hover:text-white text-sm">
                                <?= $email['status'] === 'pending' ? 'Edit' : 'Resubmit' ?>
                            </button>
                            
                            <form method="POST" action="/admin/emails/<?= $email['id'] ?>/delete" onsubmit="return confirm('Delete this email body?')" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" class="text-rose-400 hover:text-rose-300 text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="prose prose-invert prose-sm max-w-none bg-slate-900 p-4 rounded border border-slate-700">
                        <?= $email['body_content'] ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add Subject Modal -->
<div id="addSubjectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-slate-900 rounded-2xl border border-slate-800 w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">Add Subject Line</h3>
            <button onclick="document.getElementById('addSubjectModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="/admin/subjects/create" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="campaign_id" value="<?= $campaign['id'] ?>">
            
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Subject Line</label>
                <input 
                    type="text" 
                    name="subject_text" 
                    required
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-500"
                    placeholder="Enter subject line text"
                >
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('addSubjectModal').classList.add('hidden')" class="flex-1 px-4 py-3 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-electric-600 text-white font-medium rounded-lg hover:bg-electric-500 transition-colors">
                    Add Subject Line
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Email Modal -->
<div id="addEmailModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-slate-900 rounded-2xl border border-slate-800 w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white" id="emailModalTitle">Add Email Body</h3>
            <button onclick="closeEmailModal()" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form method="POST" id="emailForm" action="/admin/emails/create" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="campaign_id" value="<?= $campaign['id'] ?>">
            
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Internal Name (optional)</label>
                <input 
                    type="text" 
                    name="internal_name"
                    id="emailInternalName"
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-500"
                    placeholder="e.g. Welcome Email v2"
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email Body</label>
                <textarea 
                    name="body_content"
                    id="emailBodyContent"
                    class="tinymce-editor w-full"
                ></textarea>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeEmailModal()" class="flex-1 px-4 py-3 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="emailSubmitBtn" class="flex-1 px-4 py-3 bg-electric-600 text-white font-medium rounded-lg hover:bg-electric-500 transition-colors">
                    Add Email Body
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditEmailModal(id, internalName, bodyContent, status) {
    document.getElementById('emailModalTitle').textContent = status === 'pending' ? 'Edit Email Body' : 'Resubmit Email Body';
    document.getElementById('emailSubmitBtn').textContent = status === 'pending' ? 'Save Changes' : 'Resubmit for Approval';
    document.getElementById('emailForm').action = status === 'pending' ? '/admin/emails/' + id + '/update' : '/admin/emails/' + id + '/resubmit';
    document.getElementById('emailInternalName').value = internalName;
    
    if (typeof tinymce !== 'undefined' && tinymce.get('emailBodyContent')) {
        tinymce.get('emailBodyContent').setContent(bodyContent);
    } else {
        document.getElementById('emailBodyContent').value = bodyContent;
    }
    
    document.getElementById('addEmailModal').classList.remove('hidden');
}

function closeEmailModal() {
    document.getElementById('addEmailModal').classList.add('hidden');
    document.getElementById('emailModalTitle').textContent = 'Add Email Body';
    document.getElementById('emailSubmitBtn').textContent = 'Add Email Body';
    document.getElementById('emailForm').action = '/admin/emails/create';
    document.getElementById('emailInternalName').value = '';
    
    if (typeof tinymce !== 'undefined' && tinymce.get('emailBodyContent')) {
        tinymce.get('emailBodyContent').setContent('');
    } else {
        document.getElementById('emailBodyContent').value = '';
    }
}

// Initialize TinyMCE
document.addEventListener('DOMContentLoaded', function() {
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#emailBodyContent',
            height: 400,
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | code',
            content_style: 'body { font-family: DM Sans, sans-serif; font-size: 14px; }',
            skin: 'oxide-dark',
            content_css: 'dark'
        });
    }
});
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>

