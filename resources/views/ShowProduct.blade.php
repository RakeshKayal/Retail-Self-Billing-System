<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Ether POS</title>
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
                <h1 class="text-4xl font-headline font-bold mb-2">Product Catalogue</h1>
                <p class="text-on-surface-variant">Browse all products in your inventory</p>
            </div>

            <div class="mb-6">
                <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterTable()" class="w-full bg-[#0f141a] border border-outline/10 rounded-lg px-4 py-3 text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:border-secondary/50">
            </div>

            <div class="border border-outline/10 rounded-xl bg-surface-container-low overflow-hidden">
                @if ($pro->isEmpty())
                    <div class="p-12 text-center text-on-surface-variant">
                        <p class="text-lg">No products found</p>
                        <a href="{{ route('addProduct') }}" class="text-secondary font-bold hover:underline mt-2">Add your first product</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="productTable">
                            <thead class="border-b border-outline/10 bg-surface-container/50">
                                <tr>
                                    <th class="px-6 py-4 text-left font-headline font-bold text-on-surface">#</th>
                                    <th class="px-6 py-4 text-left font-headline font-bold text-on-surface">Image</th>
                                    <th class="px-6 py-4 text-left font-headline font-bold text-on-surface">Product Name</th>
                                    <th class="px-6 py-4 text-left font-headline font-bold text-on-surface">Category</th>
                                    <th class="px-6 py-4 text-left font-headline font-bold text-on-surface">Price</th>
                                    <th class="px-6 py-4 text-left font-headline font-bold text-on-surface">Barcode</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline/10">
                                @foreach ($pro as $index => $item)
                                    <tr class="hover:bg-surface-container/30 transition">
                                        <td class="px-6 py-4 text-on-surface-variant">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            @if($item->p_img)
                                                <img src="{{ asset('product_img/' . $item->p_img) }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded-lg object-cover border border-outline/10">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center border border-outline/10">
                                                    <span class="material-symbols-outlined text-lg text-on-surface-variant">image</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-on-surface font-medium">{{ $item->product_name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-bold">{{ $item->category->cat_name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-secondary font-bold">₹{{ number_format($item->product_price, 2) }}</td>
                                        <td class="px-6 py-4 font-mono text-on-surface-variant text-xs">{{ $item->product_barcode }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const table = document.getElementById('productTable');
            const rows = table.querySelectorAll('tbody tr');
            const filter = input.value.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }
    </script>
</body>
</html>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const table = document.getElementById('productTable');
            const rows = table.querySelectorAll('tbody tr');
            const filter = input.value.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }
    </script>
</body>
</html>