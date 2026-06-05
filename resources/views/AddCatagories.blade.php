<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Ether POS</title>
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
                        "surface-container-low": "#0f141a",
                        "on-surface": "#f1f3fc",
                        "on-surface-variant": "#a8abb3",
                        "error": "#ff6e84",
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
    </style>
</head>
<body class="bg-surface min-h-screen">
    @include('Every.sidebar')
    
    <div class="ml-64">
        @include('Every.topbar')

        <main class="pt-16 p-8">
            <div class="mb-8">
                <h1 class="text-4xl font-headline font-bold mb-2">Categories</h1>
                <p class="text-on-surface-variant">Manage product categories</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Add Category Form -->
                <div class="lg:col-span-1">
                    <div class="border border-outline/10 rounded-xl bg-surface-container-low p-8 sticky top-24">
                        <h2 class="text-2xl font-headline font-bold mb-6 text-on-surface">Add Category</h2>
                        <form action="{{ route('insertcat') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-headline font-bold text-on-surface mb-2">Category Name</label>
                                <input type="text" name="cat_name" class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition" placeholder="Enter category name" required>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-surface font-headline font-bold py-3 rounded-lg hover:shadow-lg transition">Add Category</button>
                        </form>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="lg:col-span-2">
                    <div class="border border-outline/10 rounded-xl bg-surface-container-low p-8">
                        <h2 class="text-2xl font-headline font-bold mb-6 text-on-surface">Available Categories</h2>
                        
                        @if ($categories->isEmpty())
                            <div class="text-center py-12 text-on-surface-variant">
                                <p class="text-lg">No categories yet</p>
                                <p class="text-sm">Add your first category to get started</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($categories as $category)
                                    <div class="flex items-center justify-between p-4 border border-outline/10 bg-surface-container/30 rounded-lg hover:bg-surface-container/50 transition">
                                        <div>
                                            <h3 class="font-headline font-bold text-on-surface">{{ $category->cat_name }}</h3>
                                            <p class="text-sm text-on-surface-variant">{{ $category->products_count ?? 0 }} products</p>
                                        </div>
                                        <form action="{{ route('deletecat', $category->cat_id) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 bg-error/20 text-error rounded-lg hover:bg-error/30 transition font-bold text-sm">Delete</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>