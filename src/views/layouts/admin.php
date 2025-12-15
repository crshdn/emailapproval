<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> - Email Approval System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                    },
                    colors: {
                        slate: {
                            850: '#172033',
                            950: '#0a0f1a',
                        },
                        electric: {
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: #94a3b8;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .nav-item:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #e2e8f0;
        }
        .nav-item.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .nav-item.active .nav-icon {
            color: #ffffff;
        }
        .nav-item .nav-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        .nav-item.logout-btn {
            color: #f87171;
        }
        .nav-item.logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900/80 backdrop-blur-sm border-r border-slate-800/50 flex flex-col">
            <!-- Logo Section -->
            <div class="p-5 border-b border-slate-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-white leading-tight">Email Approval</h1>
                        <p class="text-xs text-slate-500">Admin Portal</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">Menu</p>
                <div class="space-y-1">
                    <a href="/admin" class="nav-item <?= ($_SERVER['REQUEST_URI'] === '/admin') ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="/admin/clients" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/clients') || str_starts_with($_SERVER['REQUEST_URI'], '/admin/campaigns') ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Clients</span>
                    </a>
                </div>
            </nav>
            
            <!-- Logout Section -->
            <div class="p-4 border-t border-slate-800/50">
                <a href="/admin/logout" class="nav-item logout-btn">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-slate-900/50 border-b border-slate-800 px-8 py-4">
                <h2 class="text-2xl font-semibold"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h2>
            </header>
            
            <div class="p-8">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mb-6 p-4 bg-green-900/50 border border-green-700 rounded-lg text-green-200" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mb-6 p-4 bg-red-900/50 border border-red-700 rounded-lg text-red-200" x-data="{ show: true }" x-show="show">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button @click="show = false" class="float-right">&times;</button>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>
    
    <script>
        // Initialize TinyMCE for any rich text editors
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '.tinymce-editor',
                height: 400,
                menubar: false,
                plugins: 'lists link image table code',
                toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | code',
                content_style: 'body { font-family: DM Sans, sans-serif; font-size: 14px; }',
                skin: 'oxide-dark',
                content_css: 'dark'
            });
        }
    </script>
</body>
</html>

