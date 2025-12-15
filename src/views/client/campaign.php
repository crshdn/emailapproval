<?php $pageTitle = htmlspecialchars($campaign['name']); $token = $client['access_token']; ob_start(); ?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-slate-400 mb-6">
    <a href="/portal/<?= htmlspecialchars($token) ?>" class="hover:text-white">Campaigns</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-white"><?= htmlspecialchars($campaign['name']) ?></span>
</div>

<div x-data="{ 
    activeTab: 'subjects',
    processingApproval: false,
    feedbackModal: false,
    currentItem: null,
    currentItemType: null,
    feedback: ''
}" class="space-y-6">

    <!-- Tab Navigation -->
    <div class="flex gap-4 border-b border-slate-800 pb-4">
        <button 
            @click="activeTab = 'subjects'" 
            :class="activeTab === 'subjects' ? 'text-electric-400 border-electric-400' : 'text-slate-400 border-transparent hover:text-white'"
            class="pb-2 border-b-2 font-medium transition-colors"
        >
            Subject Lines
            <?php if (count($pendingSubjects) > 0): ?>
                <span class="ml-2 px-2 py-0.5 bg-amber-600/20 text-amber-400 rounded text-xs"><?= count($pendingSubjects) ?></span>
            <?php endif; ?>
        </button>
        <button 
            @click="activeTab = 'emails'" 
            :class="activeTab === 'emails' ? 'text-electric-400 border-electric-400' : 'text-slate-400 border-transparent hover:text-white'"
            class="pb-2 border-b-2 font-medium transition-colors"
        >
            Email Bodies
            <?php if (count($pendingEmails) > 0): ?>
                <span class="ml-2 px-2 py-0.5 bg-amber-600/20 text-amber-400 rounded text-xs"><?= count($pendingEmails) ?></span>
            <?php endif; ?>
        </button>
    </div>

    <!-- Subject Lines Tab -->
    <div x-show="activeTab === 'subjects'" x-transition>
        <?php if ($currentSubject): ?>
            <!-- Current Subject to Review -->
            <div class="bg-slate-900 rounded-xl border border-slate-800 p-8 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-slate-400">Review Subject Line (<?= count($pendingSubjects) ?> remaining)</span>
                    <span class="text-sm text-slate-500">Revision <?= $currentSubject['revision_number'] ?></span>
                </div>
                
                <p class="text-2xl font-medium text-white mb-8"><?= htmlspecialchars($currentSubject['subject_text']) ?></p>
                
                <div class="flex flex-wrap gap-4">
                    <button 
                        @click="if(!processingApproval) { processingApproval = true; approveItem('subject_line', <?= $currentSubject['id'] ?>); }"
                        :disabled="processingApproval"
                        class="btn-approve disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve
                    </button>
                    
                    <button 
                        @click="if(!processingApproval) { processingApproval = true; denyItem('subject_line', <?= $currentSubject['id'] ?>, ''); }"
                        :disabled="processingApproval"
                        class="btn-deny disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Deny
                    </button>
                    
                    <button 
                        @click="currentItem = <?= $currentSubject['id'] ?>; currentItemType = 'subject_line'; feedbackModal = true"
                        :disabled="processingApproval"
                        class="btn-feedback disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Deny with Feedback
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-slate-900 rounded-xl border border-slate-800 p-8 text-center">
                <div class="w-16 h-16 bg-emerald-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">All caught up!</h3>
                <p class="text-slate-400">No pending subject lines to review</p>
            </div>
        <?php endif; ?>
        
        <!-- All Subject Lines Summary -->
        <?php if (!empty($allSubjects)): ?>
            <div class="bg-slate-900/50 rounded-xl border border-slate-800 p-6">
                <h4 class="text-sm font-medium text-slate-400 mb-4">All Subject Lines</h4>
                <div class="space-y-2">
                    <?php foreach ($allSubjects as $subject): ?>
                        <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg">
                            <p class="text-white truncate flex-1 mr-4"><?= htmlspecialchars($subject['subject_text']) ?></p>
                            <?php if ($subject['status'] === 'pending'): ?>
                                <span class="px-2 py-1 bg-amber-600/20 text-amber-400 rounded text-xs">Pending</span>
                            <?php elseif ($subject['status'] === 'approved'): ?>
                                <span class="px-2 py-1 bg-emerald-600/20 text-emerald-400 rounded text-xs">Approved</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-rose-600/20 text-rose-400 rounded text-xs">Denied</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Email Bodies Tab -->
    <div x-show="activeTab === 'emails'" x-transition>
        <?php if ($currentEmail): ?>
            <!-- Current Email to Review -->
            <div class="bg-slate-900 rounded-xl border border-slate-800 p-8 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-slate-400">Review Email Body (<?= count($pendingEmails) ?> remaining)</span>
                    <span class="text-sm text-slate-500">Revision <?= $currentEmail['revision_number'] ?></span>
                </div>
                
                <div class="prose prose-invert max-w-none bg-white text-slate-900 p-6 rounded-lg mb-8">
                    <?= $currentEmail['body_content'] ?>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    <button 
                        @click="if(!processingApproval) { processingApproval = true; approveItem('email_body', <?= $currentEmail['id'] ?>); }"
                        :disabled="processingApproval"
                        class="btn-approve disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve
                    </button>
                    
                    <button 
                        @click="if(!processingApproval) { processingApproval = true; denyItem('email_body', <?= $currentEmail['id'] ?>, ''); }"
                        :disabled="processingApproval"
                        class="btn-deny disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Deny
                    </button>
                    
                    <button 
                        @click="currentItem = <?= $currentEmail['id'] ?>; currentItemType = 'email_body'; feedbackModal = true"
                        :disabled="processingApproval"
                        class="btn-feedback disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Deny with Feedback
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-slate-900 rounded-xl border border-slate-800 p-8 text-center">
                <div class="w-16 h-16 bg-emerald-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">All caught up!</h3>
                <p class="text-slate-400">No pending email bodies to review</p>
            </div>
        <?php endif; ?>
        
        <!-- All Email Bodies Summary -->
        <?php if (!empty($allEmails)): ?>
            <div class="bg-slate-900/50 rounded-xl border border-slate-800 p-6">
                <h4 class="text-sm font-medium text-slate-400 mb-4">All Email Bodies</h4>
                <div class="space-y-3">
                    <?php foreach ($allEmails as $email): ?>
                        <div class="p-4 bg-slate-800/50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <?php if ($email['internal_name']): ?>
                                    <p class="font-medium text-white"><?= htmlspecialchars($email['internal_name']) ?></p>
                                <?php else: ?>
                                    <p class="text-slate-400 text-sm">Email #<?= $email['id'] ?></p>
                                <?php endif; ?>
                                <?php if ($email['status'] === 'pending'): ?>
                                    <span class="px-2 py-1 bg-amber-600/20 text-amber-400 rounded text-xs">Pending</span>
                                <?php elseif ($email['status'] === 'approved'): ?>
                                    <span class="px-2 py-1 bg-emerald-600/20 text-emerald-400 rounded text-xs">Approved</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-rose-600/20 text-rose-400 rounded text-xs">Denied</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-slate-400 line-clamp-2"><?= htmlspecialchars(strip_tags($email['body_content'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Feedback Modal -->
    <div x-show="feedbackModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.away="feedbackModal = false" class="bg-slate-900 rounded-2xl border border-slate-800 w-full max-w-lg p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Deny with Feedback</h3>
            <p class="text-slate-400 mb-4">Please provide feedback on what changes you'd like to see:</p>
            
            <textarea 
                x-model="feedback"
                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-500 mb-4"
                rows="4"
                placeholder="Enter your feedback here..."
            ></textarea>
            
            <div class="flex gap-3">
                <button @click="feedbackModal = false; feedback = ''" class="flex-1 px-4 py-3 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-700 transition-colors">
                    Cancel
                </button>
                <button 
                    @click="if(feedback.trim()) { processingApproval = true; feedbackModal = false; denyItem(currentItemType, currentItem, feedback); feedback = ''; }"
                    class="flex-1 px-4 py-3 bg-rose-600 text-white font-medium rounded-lg hover:bg-rose-500 transition-colors"
                >
                    Submit Feedback
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = '<?= htmlspecialchars($csrfToken) ?>';

async function approveItem(itemType, itemId) {
    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('item_type', itemType);
        formData.append('item_id', itemId);
        
        const response = await fetch('/api/approve', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'An error occurred');
            location.reload();
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        location.reload();
    }
}

async function denyItem(itemType, itemId, feedback) {
    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('item_type', itemType);
        formData.append('item_id', itemId);
        formData.append('feedback', feedback);
        
        const response = await fetch('/api/deny', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'An error occurred');
            location.reload();
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        location.reload();
    }
}
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/client.php'; ?>

