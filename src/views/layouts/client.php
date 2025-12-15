<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Email Approval Portal') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
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
        .btn-approve { @apply bg-emerald-600 hover:bg-emerald-500 text-white font-medium px-6 py-3 rounded-lg transition-all hover:scale-105 hover:shadow-lg hover:shadow-emerald-500/25; }
        .btn-deny { @apply bg-rose-600 hover:bg-rose-500 text-white font-medium px-6 py-3 rounded-lg transition-all hover:scale-105 hover:shadow-lg hover:shadow-rose-500/25; }
        .btn-feedback { @apply bg-amber-600 hover:bg-amber-500 text-white font-medium px-6 py-3 rounded-lg transition-all hover:scale-105 hover:shadow-lg hover:shadow-amber-500/25; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <!-- Header -->
    <header class="bg-slate-900 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">Email Approval Portal</h1>
                <?php if (isset($client)): ?>
                    <p class="text-sm text-slate-400">Welcome, <?= htmlspecialchars($client['name']) ?></p>
                <?php endif; ?>
            </div>
            <?php if (isset($token)): ?>
                <nav class="flex items-center gap-4">
                    <a href="/portal/<?= htmlspecialchars($token) ?>" class="text-slate-300 hover:text-white transition-colors">
                        Campaigns
                    </a>
                    <a href="/portal/<?= htmlspecialchars($token) ?>/history" class="text-slate-300 hover:text-white transition-colors">
                        History
                    </a>
                </nav>
            <?php endif; ?>
        </div>
    </header>
    
    <main class="max-w-6xl mx-auto px-6 py-8">
        <?= $content ?? '' ?>
    </main>
    
    <footer class="border-t border-slate-800 mt-auto">
        <div class="max-w-6xl mx-auto px-6 py-4 text-center text-slate-500 text-sm">
            &copy; <?= date('Y') ?> Email Approval System
        </div>
    </footer>
</body>
</html>

