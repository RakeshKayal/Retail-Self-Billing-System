<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kinetic Ether POS</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.classList.toggle('light', savedTheme === 'light');
            if (savedTheme === 'light') document.documentElement.classList.remove('dark');
        })();
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
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
                        "surface-container-high": "#1b2028",
                        "on-surface": "#f1f3fc",
                        "on-surface-variant": "#a8abb3",
                        "outline": "#72757d",
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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-surface min-h-screen">
    @include('Every.sidebar')
    <div class="ml-64">
        @include('Every.topbar')
        
        <main class="pt-16 p-8 max-w-7xl mx-auto">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-4xl font-headline font-bold mb-2">Welcome, {{ auth()->user()->name ?? 'Admin' }}</h1>
                <p class="text-on-surface-variant">Manage your store inventory and transactions</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-4 gap-6 mb-8">
                <!-- Total Products Card -->
                <div class="bg-surface-container-low p-6 rounded-xl border border-outline/10 hover:border-primary/30 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-on-surface-variant text-sm mb-2">Total Products</p>
                            <p class="text-3xl font-bold font-headline">{{ $totalProducts ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary">inventory_2</span>
                        </div>
                    </div>
                    <p class="text-secondary text-xs mt-3"><a href="{{ route('addProduct') }}" class="hover:underline">+ Add Product</a></p>
                </div>

                <!-- Categories Card -->
                <div class="bg-surface-container-low p-6 rounded-xl border border-outline/10 hover:border-secondary/30 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-on-surface-variant text-sm mb-2">Categories</p>
                            <p class="text-3xl font-bold font-headline">{{ $totalCategories ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary">category</span>
                        </div>
                    </div>
                </div>

                <!-- Today's Orders Card -->
                <div class="bg-surface-container-low p-6 rounded-xl border border-outline/10 hover:border-tertiary/30 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-on-surface-variant text-sm mb-2">Today's Orders</p>
                            <p class="text-3xl font-bold font-headline">{{ $todayBills ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-[#ff59e3]/10 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#ff59e3]">receipt</span>
                        </div>
                    </div>
                    <p class="text-[#ff59e3] text-xs mt-3"><a href="{{ route('bill') }}" class="hover:underline">Go to Billing</a></p>
                </div>

                <!-- Revenue Card -->
                <div class="bg-surface-container-low p-6 rounded-xl border border-outline/10 hover:border-emerald-500/30 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-on-surface-variant text-sm mb-2">Total Revenue</p>
                            <p class="text-3xl font-bold font-headline">₹{{ number_format($totalRevenue ?? 0, 0) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-emerald-500">trending_up</span>
                        </div>
                    </div>
                    <p class="text-emerald-500 text-xs mt-3">Real-time data</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-3 gap-6 mb-8">
                <a href="{{ route('addProduct') }}" class="bg-gradient-to-br from-primary to-[#7e51ff] p-6 rounded-xl text-white hover:shadow-lg hover:shadow-primary/20 transition transform hover:scale-105">
                    <span class="material-symbols-outlined text-3xl mb-3 block">add_circle</span>
                    <h3 class="font-headline font-bold">Add Product</h3>
                    <p class="text-sm opacity-90">Create new products</p>
                </a>

                <a href="{{ route('bill') }}" class="bg-gradient-to-br from-secondary to-[#00d4ec] p-6 rounded-xl text-[#003d4d] hover:shadow-lg hover:shadow-secondary/20 transition transform hover:scale-105">
                    <span class="material-symbols-outlined text-3xl mb-3 block">point_of_sale</span>
                    <h3 class="font-headline font-bold">Billing</h3>
                    <p class="text-sm opacity-90">Process transactions</p>
                </a>

                <a href="{{ route('addcat') }}" class="bg-gradient-to-br from-[#ff59e3] to-[#ff85e4] p-6 rounded-xl text-white hover:shadow-lg hover:shadow-[#ff59e3]/20 transition transform hover:scale-105">
                    <span class="material-symbols-outlined text-3xl mb-3 block">category</span>
                    <h3 class="font-headline font-bold">Categories</h3>
                    <p class="text-sm opacity-90">Manage categories</p>
                </a>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Recent Products -->
                <div class="bg-surface-container-low rounded-xl border border-outline/10 p-6">
                    <h3 class="font-headline font-bold mb-4">Recent Products</h3>
                    <div class="space-y-3">
                        @forelse($recentProducts ?? [] as $product)
                            <div class="flex items-center justify-between p-3 bg-surface-container rounded-lg hover:bg-surface-container-high transition">
                                <div>
                                    <p class="text-sm font-medium">{{ $product->product_name }}</p>
                                    <p class="text-xs text-on-surface-variant">₹{{ number_format($product->product_price, 0) }}</p>
                                </div>
                                <span class="text-secondary text-xs font-bold">{{ $product->category->cat_name ?? 'N/A' }}</span>
                            </div>
                        @empty
                            <div class="p-3 text-center text-on-surface-variant text-sm">
                                No products yet. <a href="{{ route('addProduct') }}" class="text-secondary hover:underline">Add one</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-surface-container-low rounded-xl border border-outline/10 p-6">
                    <h3 class="font-headline font-bold mb-4">Store Performance</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-on-surface-variant">Products Available</span>
                                <span class="text-sm font-bold">{{ $totalProducts ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-surface-container rounded-full h-2">
                                <div class="bg-gradient-to-r from-primary to-secondary h-2 rounded-full" style="width: {{ min(($totalProducts ?? 0) / 200 * 100, 100) }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-on-surface-variant">Total Categories</span>
                                <span class="text-sm font-bold">{{ $totalCategories ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-surface-container rounded-full h-2">
                                <div class="bg-gradient-to-r from-secondary to-[#ff59e3] h-2 rounded-full" style="width: {{ min(($totalCategories ?? 0) / 20 * 100, 100) }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-on-surface-variant">Today's Transactions</span>
                                <span class="text-sm font-bold">{{ $todayBills ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-surface-container rounded-full h-2">
                                <div class="bg-gradient-to-r from-emerald-500 to-primary h-2 rounded-full" style="width: {{ min(($todayBills ?? 0) / 50 * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>