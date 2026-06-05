<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Ether POS</title>
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
                <h1 class="text-4xl font-headline font-bold mb-2">Add New Product</h1>
                <p class="text-on-surface-variant">Add a new product to your inventory</p>
            </div>

            <div class="border border-outline/10 rounded-xl bg-surface-container-low p-8 max-w-2xl">
                <form action="{{ route('insertproduct') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-headline font-bold text-on-surface mb-2">Product Name</label>
                        <input type="text" name="product_name" class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition" placeholder="Enter product name" required>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-headline font-bold text-on-surface mb-2">Category</label>
                            <select name="cat_id" class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition" required>
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->cat_id }}">{{ $cat->cat_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-headline font-bold text-on-surface mb-2">Price (₹)</label>
                            <input type="number" name="product_price" step="0.01" class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition" placeholder="0.00" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-headline font-bold text-on-surface mb-2">Product Image</label>
                        <input type="file" name="p_img" class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition" accept="image/*" required>
                    </div>

                    <div>
                        <label class="block text-sm font-headline font-bold text-on-surface mb-2">Barcode</label>
                        <input type="text" name="product_barcode" class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition" placeholder="Enter unique barcode" required>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-primary to-secondary text-surface font-headline font-bold py-3 rounded-lg hover:shadow-lg transition">Add Product</button>
                        <a href="{{ route('home') }}" class="flex-1 bg-surface-container text-on-surface font-headline font-bold py-3 rounded-lg text-center hover:bg-surface-container/80 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>