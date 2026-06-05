<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Ether POS</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.classList.toggle('light', savedTheme === 'light');
            if (savedTheme === 'light') document.documentElement.classList.remove('dark');
        })();
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#b6a0ff",
                        "secondary": "#00e3fd",
                        "surface": "#0a0e14",
                        "surface-container": "#151a21",
                        "on-surface": "#f1f3fc",
                        "on-surface-variant": "#a8abb3",
                    },
                    fontFamily: {
                        "headline": ["Space Grotesk"],
                        "body": ["Inter"],
                    },
                },
            },
        }
    </script>
    <style>
        body { background-color: #0a0e14; color: #f1f3fc; transition: all 0.3s ease; }
        html.light body { background-color: #f9fafb; color: #1f2937; }
        .material-symbols { font-family: 'Material Symbols Outlined'; font-weight: normal; font-style: normal; letter-spacing: -0.02em; text-transform: none; display: inline-flex; white-space: nowrap; word-wrap: normal; direction: ltr; }
        .card-border { border: 1px solid rgba(168, 171, 179, 0.1); }
    </style>
</head>
<body class="bg-surface min-h-screen flex flex-col items-center justify-center relative overflow-hidden px-6">
    <main class="max-w-4xl w-full relative z-10">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center gap-3 mb-6">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-3xl font-bold text-surface">⚡</div>
                <h1 class="text-5xl font-headline font-bold text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-primary">Ether POS</h1>
            </div>
            <p class="text-xl text-on-surface-variant font-body leading-relaxed max-w-2xl mx-auto mb-8">
                Next-generation point-of-sale system designed for modern retail. Fast, secure, and beautiful.
            </p>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <div class="card-border rounded-xl bg-surface-container-low p-8">
                <div class="text-4xl mb-4">⚡</div>
                <h3 class="text-xl font-headline font-bold text-on-surface mb-3">Lightning Fast</h3>
                <p class="text-on-surface-variant">Zero-latency transaction processing with instant synchronization</p>
            </div>

            <div class="card-border rounded-xl bg-surface-container-low p-8">
                <div class="text-4xl mb-4">🔐</div>
                <h3 class="text-xl font-headline font-bold text-on-surface mb-3">Encrypted Core</h3>
                <p class="text-on-surface-variant">Bank-grade data protection for all transactions</p>
            </div>

            <div class="card-border rounded-xl bg-surface-container-low p-8">
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-xl font-headline font-bold text-on-surface mb-3">Real-time Analytics</h3>
                <p class="text-on-surface-variant">Track sales, inventory, and performance instantly</p>
            </div>

            <div class="card-border rounded-xl bg-surface-container-low p-8">
                <div class="text-4xl mb-4">🎯</div>
                <h3 class="text-xl font-headline font-bold text-on-surface mb-3">Smart Management</h3>
                <p class="text-on-surface-variant">Intuitive interface for efficient store operations</p>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-12">
            @if(auth()->check())
                <a href="{{ route('home') }}" class="px-8 py-4 rounded-lg font-headline font-bold text-lg text-surface bg-gradient-to-r from-primary to-secondary hover:shadow-lg transition text-center">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="px-8 py-4 rounded-lg font-headline font-bold text-lg text-surface bg-gradient-to-r from-primary to-secondary hover:shadow-lg transition text-center">
                    Login
                </a>
                <a href="{{ route('login') }}" class="px-8 py-4 rounded-lg font-headline font-bold text-lg text-secondary border-2 border-secondary/50 hover:border-secondary hover:bg-secondary/10 transition text-center">
                    Learn More
                </a>
            @endif
        </div>

        <!-- Footer Info -->
        <div class="mt-16 pt-8 border-t border-on-surface-variant/10 text-center text-on-surface-variant text-sm">
            <p>Ether POS v4.2.0 • Secure • Fast • Reliable</p>
        </div>
    </main>
</body>
</html>