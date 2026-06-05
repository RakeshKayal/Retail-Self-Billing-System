<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Ether POS</title>
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
                <h1 class="text-4xl font-headline font-bold mb-2">Edit Product</h1>
                <p class="text-on-surface-variant">Update product details and information</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 border border-error/30 rounded-lg p-4 bg-error/5">
                    <p class="text-error font-bold mb-2">Error validating form:</p>
                    <ul class="text-error text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="max-w-2xl">
                <div class="border border-outline/10 rounded-xl bg-surface-container-low p-8">
                    <form action="{{ route('product.update', $product->product_id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-on-surface font-headline font-bold mb-3">Product Name</label>
                            <input type="text" name="product_name" value="{{ $product->product_name }}" required class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition">
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-on-surface font-headline font-bold mb-3">Category</label>
                                <select name="cat_id" required class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->cat_id }}" {{ $product->cat_id == $cat->cat_id ? 'selected' : '' }}>{{ $cat->cat_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-on-surface font-headline font-bold mb-3">Price</label>
                                <input type="number" name="product_price" value="{{ $product->product_price }}" step="0.01" required class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-on-surface font-headline font-bold mb-3">Barcode</label>
                            <input type="text" name="product_barcode" value="{{ $product->product_barcode }}" required class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition">
                        </div>

                        <div>
                            <label class="block text-on-surface font-headline font-bold mb-3">Product Image</label>
                            @if($product->p_img)
                                <div class="mb-4 p-4 border border-primary/30 rounded-lg bg-primary/5">
                                    <p class="text-sm text-on-surface-variant mb-2">Current Image:</p>
                                    <img src="{{ asset('product_img/' . $product->p_img) }}" alt="{{ $product->product_name }}" class="max-h-40 rounded">
                                </div>
                            @endif
                            <input type="file" name="p_img" class="w-full bg-[#0f141a] border border-outline/10 rounded-lg py-2 px-4 text-on-surface focus:outline-none focus:border-secondary/50 transition">
                            <p class="text-xs text-on-surface-variant mt-2">Leave empty to keep current image</p>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="flex-1 bg-gradient-to-r from-primary to-secondary text-surface font-headline font-bold py-3 rounded-lg hover:shadow-lg transition">
                                Update Product
                            </button>
                            <a href="{{ route('manageProduct') }}" class="flex-1 bg-surface-container border border-on-surface-variant/30 text-on-surface font-headline font-bold py-3 rounded-lg hover:bg-surface-container/80 transition text-center">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
