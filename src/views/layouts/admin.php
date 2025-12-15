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
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors; }
        .sidebar-link.active { @apply bg-electric-600 text-white; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col">
            <div class="p-6 border-b border-slate-800">
                <h1 class="text-xl font-bold text-white">Email Approval</h1>
                <p class="text-sm text-slate-400 mt-1">Admin Portal</p>
            </div>
            
            <nav class="flex-1 p-4 space-y-2">
                <a href="/admin" class="sidebar-link <?= ($_SERVER['REQUEST_URI'] === '/admin') ? 'active' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="/admin/clients" class="sidebar-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/clients') ? 'active' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Clients
                </a>
            </nav>
            
            <div class="p-4 border-t border-slate-800">
                <a href="/admin/logout" class="sidebar-link text-red-400 hover:text-red-300 hover:bg-red-900/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
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

