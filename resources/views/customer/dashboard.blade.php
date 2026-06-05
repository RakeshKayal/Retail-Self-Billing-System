<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shopping Portal - Ether POS</title>
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
    <script src="https://cdn.jsdelivr.net/npm/@zxing/library@0.19.0/umd.min.js"></script>
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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        #cameraPreview { width: 100%; height: auto; border-radius: 12px; max-height: 400px; }
    </style>
</head>
<body class="bg-surface min-h-screen">
    <!-- Sidebar for customer -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 z-50">
        <div class="px-6 mb-10">
            <h1 class="text-2xl font-bold bg-gradient-to-br from-primary to-[#7e51ff] bg-clip-text text-transparent font-headline">Ether POS</h1>
            <p class="text-xs text-on-surface-variant mt-1 uppercase tracking-widest">Customer Portal</p>
        </div>
        <nav class="flex-1 px-3 space-y-2">
            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-secondary bg-surface-container font-medium border-l-4 border-secondary scale-105 transition-all duration-300">
                <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
                <span>Shopping</span>
            </a>
            <a href="{{ route('customer.receipts') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant font-medium hover:bg-surface-container hover:text-primary transition-all duration-300">
                <span class="material-symbols-outlined text-[20px]">receipt</span>
                <span>My Receipts</span>
            </a>
        </nav>
        <div class="px-6 mt-auto pt-6 border-t border-on-surface-variant/10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full border border-primary/20 bg-gradient-to-br from-primary to-[#7e51ff] flex items-center justify-center text-white font-headline font-bold text-sm">
                {{ (auth()->user()->name ?? 'C')[0] }}
            </div>
            <div>
                <p class="text-sm font-medium text-on-surface">{{ auth()->user()->name ?? 'Customer' }}</p>
                <form action="{{ route('logout') }}" method="POST" class="inline" id="logoutForm">
                    @csrf
                    <button type="button" onclick="document.getElementById('logoutForm').submit()" class="text-xs text-on-surface-variant hover:text-secondary transition cursor-pointer">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="ml-64">
        <!-- Topbar -->
        <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 z-40 bg-surface-container-low/60 backdrop-blur-xl flex justify-between items-center px-8 border-b border-on-surface-variant/10 shadow-lg">
            <div class="flex items-center gap-4">
                <h2 class="text-xl font-headline font-bold text-on-surface">Shopping Center</h2>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleTheme()" class="p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-full transition-colors duration-200">
                    <span class="material-symbols-outlined" id="themeIcon">light_mode</span>
                </button>
                <button class="p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-full transition-colors duration-200 relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-secondary rounded-full"></span>
                </button>
            </div>
        </header>

        <main class="pt-20 p-8">
            <div class="mb-8">
                <h1 class="text-4xl font-headline font-bold mb-2">Shopping Portal</h1>
                <p class="text-on-surface-variant">Browse products and complete your purchase</p>
            </div>

            <!-- Barcode Scanner Section -->
            <div class="mb-8 border border-secondary/30 rounded-xl bg-gradient-to-br from-surface-container-low to-surface bg-secondary/5 p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-secondary/20 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-secondary text-2xl">qr_code_2</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-headline font-bold text-on-surface">Barcode Scanner</h2>
                        <p class="text-sm text-on-surface-variant">Scan products using barcode or camera - connects to database</p>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        <div class="w-3 h-3 bg-secondary rounded-full animate-pulse"></div>
                        <span class="text-secondary text-sm font-bold">Ready to scan</span>
                    </div>
                </div>
                
                <!-- Scanner Input -->
                <div class="relative mb-4">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-secondary text-2xl">qr_code</span>
                    <input 
                        type="text" 
                        id="barcodeInput" 
                        placeholder="Scan barcode or enter product code..." 
                        class="w-full bg-[#0f141a] border-2 border-secondary/50 rounded-lg pl-14 pr-4 py-3 text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:border-secondary transition text-lg font-mono"
                        autofocus
                    >
                    <span id="scanStatus" class="absolute right-4 top-1/2 -translate-y-1/2 text-secondary text-sm font-bold"></span>
                </div>

                <!-- Scanner Status Panel -->
                <div class="bg-surface-container/30 rounded-lg p-4 mb-4 border border-outline/10">
                    <div class="flex gap-6">
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Total Products</p>
                            <p class="text-2xl font-bold text-secondary">{{ $products->count() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Items in Cart</p>
                            <p class="text-2xl font-bold text-primary" id="cartCount">0</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Total Value</p>
                            <p class="text-2xl font-bold text-emerald-400">₹<span id="cartValue">0.00</span></p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button onclick="toggleCamera()" class="px-4 py-2 bg-secondary text-surface rounded-lg hover:bg-secondary/90 transition font-bold text-sm">
                        <span class="material-symbols-outlined inline text-[18px] mr-1">photo_camera</span> Open Camera
                    </button>
                    <button onclick="openFileSelector()" class="px-4 py-2 bg-secondary/20 text-secondary rounded-lg hover:bg-secondary/40 transition font-bold text-sm">
                        <span class="material-symbols-outlined inline text-[18px] mr-1">upload_file</span> Upload Image
                    </button>
                    <button onclick="clearBarcodeInput()" class="px-4 py-2 bg-surface-container text-on-surface rounded-lg hover:bg-surface-container/80 transition font-bold text-sm">
                        <span class="material-symbols-outlined inline text-[18px] mr-1">restart_alt</span> Clear Input
                    </button>
                    <button onclick="disableScanning()" class="px-4 py-2 bg-error/20 text-error rounded-lg hover:bg-error/30 transition font-bold text-sm">
                        <span class="material-symbols-outlined inline text-[18px] mr-1">block</span> Disable Scanning
                    </button>
                </div>
                <input type="file" id="barcodeFileInput" accept="image/*" class="hidden" onchange="handleBarcodeFile(event)">
            </div>

            <!-- Camera Modal -->
            <div id="cameraModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                <div class="bg-surface-container-low rounded-xl p-6 max-w-md w-full max-h-96 overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-headline font-bold text-on-surface">Camera Scanner</h3>
                        <button onclick="toggleCamera()" class="text-on-surface-variant hover:text-on-surface">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <video id="cameraPreview" class="w-full rounded-lg mb-4 bg-surface"></video>
                    <div id="scannerStatus" class="w-full bg-secondary/10 text-secondary rounded-lg p-4 text-center text-sm font-bold">📷 Camera ready - scanning...</div>
                </div>
            </div>

            <div class="border border-outline/10 rounded-xl bg-surface-container-low p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Products Section -->
                    <div class="lg:col-span-2">
                        <h2 class="text-2xl font-headline font-bold mb-2 text-on-surface">Available Products</h2>
                        <p class="text-xs text-on-surface-variant mb-4">Note: Products are listed for browse only. To add items to cart, scan barcode using the input or camera scanner.</p>
                        <div class="mb-6">
                            <input type="text" id="productSearch" placeholder="Search products by name or barcode..." class="w-full bg-[#0f141a] border border-outline/10 rounded-lg px-4 py-3 text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:border-secondary/50">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto pr-2">
                            @forelse($products as $product)
                                <div class="border border-outline/10 bg-surface-container/30 rounded-lg overflow-hidden hover:bg-surface-container/50 transition" data-barcode="{{ $product->product_barcode }}">
                                    @if($product->p_img)
                                        <img src="{{ asset('product_img/' . $product->p_img) }}" alt="{{ $product->product_name }}" class="w-full h-32 object-cover">
                                    @else
                                        <div class="w-full h-32 bg-surface-container flex items-center justify-center">
                                            <span class="material-symbols-outlined text-4xl text-on-surface-variant">image_not_supported</span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="font-headline font-bold text-on-surface mb-1">{{ $product->product_name }}</h3>
                                        <p class="text-sm text-on-surface-variant mb-2">{{ $product->category->cat_name ?? 'N/A' }}</p>
                                        <p class="text-secondary font-bold">₹{{ number_format($product->product_price, 2) }}</p>
                                        
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-8 text-on-surface-variant">
                                    No products available. Please try again later.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Bill Summary -->
                    <div class="border border-outline/10 bg-surface-container-low rounded-xl p-6 sticky top-24 h-fit max-h-[calc(100vh-150px)] overflow-y-auto">
                        <h2 class="text-xl font-headline font-bold mb-6 text-on-surface">Bill Summary</h2>
                        
                        <div class="space-y-4 mb-6 max-h-40 overflow-y-auto pr-2" id="cartItems">
                            <p class="text-on-surface-variant text-sm text-center py-4">Cart is empty</p>
                        </div>

                        <div class="border-t border-outline/10 pt-4 space-y-3 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">Subtotal:</span>
                                <span class="text-on-surface font-bold">₹<span id="subtotal">0.00</span></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">GST (6-8%):</span>
                                <span class="text-on-surface font-bold">₹<span id="gst">0.00</span></span>
                            </div>
                            <div class="flex justify-between text-lg border-t border-outline/10 pt-3">
                                <span class="font-headline font-bold text-on-surface">Total:</span>
                                <span class="text-secondary font-bold">₹<span id="total">0.00</span></span>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="border-t border-outline/10 pt-6 mb-6">
                            <label class="block text-sm font-bold text-on-surface mb-3">Payment Method</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border border-outline/10 rounded-lg cursor-pointer hover:bg-surface-container/50 transition">
                                    <input type="radio" name="paymentMethod" value="netbanking" id="payment-netbanking" class="w-4 h-4" checked>
                                    <span class="material-symbols-outlined ml-3 text-secondary">account_balance</span>
                                    <span class="ml-2 text-on-surface font-medium">Net Banking</span>
                                </label>
                                <label class="flex items-center p-3 border border-outline/10 rounded-lg cursor-pointer hover:bg-surface-container/50 transition">
                                    <input type="radio" name="paymentMethod" value="card" id="payment-card" class="w-4 h-4">
                                    <span class="material-symbols-outlined ml-3 text-primary">credit_card</span>
                                    <span class="ml-2 text-on-surface font-medium">Credit/Debit Card</span>
                                </label>
                            </div>
                        </div>

                        <button onclick="generateBill()" class="w-full bg-gradient-to-r from-primary to-secondary text-surface font-headline font-bold py-3 rounded-lg hover:shadow-lg transition">Generate Bill</button>
                        <button onclick="downloadReceipt()" id="downloadBtn" class="w-full mt-2 bg-surface-container text-on-surface font-headline font-bold py-3 rounded-lg hover:bg-surface-container/80 transition hidden">
                            <span class="material-symbols-outlined inline text-[18px] mr-1">download</span> Download Receipt
                        </button>
                        <button onclick="clearCart()" class="w-full mt-2 bg-surface-container text-on-surface font-headline font-bold py-3 rounded-lg hover:bg-surface-container/80 transition">Clear Cart</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <link href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        let cart = [];
        let scanningEnabled = true;
        let codeReader = null;
        let cropper = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Store products for barcode lookup
        const productsData = {!! json_encode($products->map(fn($p) => ['id' => $p->product_id, 'name' => $p->product_name, 'price' => $p->product_price, 'barcode' => $p->product_barcode])->keyBy('barcode')) !!};

        // Load cart from server on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCartFromServer();
            updateCart();
        });

        async function loadCartFromServer() {
            try {
                const response = await fetch('/cart', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });
                const data = await response.json();
                if (data.success && data.cart) {
                    // Convert server cart format to client format
                    cart = Object.values(data.cart).map(item => ({
                        id: item.product_id,
                        name: item.name,
                        price: parseFloat(item.price),
                        qty: item.quantity
                    }));
                    updateCart();
                }
            } catch (error) {
                console.log('Cart load info:', error);
            }
        }

        // Barcode Scanner Handler
        document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && scanningEnabled) {
                const barcode = this.value.trim();
                if (barcode) {
                    searchByBarcode();
                }
            }
        });

        function toggleCamera() {
            const modal = document.getElementById('cameraModal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                initializeCamera();
            } else {
                stopCamera();
            }
        }

        function initializeCamera() {
            const videoElement = document.getElementById('cameraPreview');
            const statusElement = document.getElementById('scannerStatus');
            
            if (!ZXing || !ZXing.BrowserMultiFormatReader) {
                statusElement.textContent = '❌ Camera library not loaded';
                statusElement.classList.remove('bg-secondary/10', 'text-secondary');
                statusElement.classList.add('bg-red-500/10', 'text-red-400');
                return;
            }

            codeReader = new ZXing.BrowserMultiFormatReader();
            statusElement.textContent = '📷 Camera ready - scanning...';
            statusElement.classList.remove('bg-red-500/10', 'text-red-400');
            statusElement.classList.add('bg-secondary/10', 'text-secondary');

            codeReader.decodeFromVideoDevice(undefined, videoElement, (result, err) => {
                if (result) {
                    const barcode = result.text;
                    statusElement.textContent = '✓ Barcode detected: ' + barcode;
                    statusElement.classList.remove('bg-secondary/10', 'text-secondary');
                    statusElement.classList.add('bg-emerald-500/10', 'text-emerald-400');
                    
                    // Fetch and display product details
                    fetch(`/scan/${barcode}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                statusElement.textContent = '❌ Product not found';
                                statusElement.classList.remove('bg-emerald-500/10', 'text-emerald-400');
                                statusElement.classList.add('bg-red-500/10', 'text-red-400');
                            } else {
                                addToCart(data.product.id, data.product.name, data.product.price);
                                showScanSuccess();
                                toggleCamera();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            statusElement.textContent = '❌ Error scanning';
                            statusElement.classList.remove('bg-emerald-500/10', 'text-emerald-400');
                            statusElement.classList.add('bg-red-500/10', 'text-red-400');
                        });
                }
            });
        }

        function stopCamera() {
            if (codeReader) {
                try {
                    codeReader.reset();
                } catch (err) {
                    console.warn('Failed to stop camera reader', err);
                }
                codeReader = null;
            }
        }

        function openFileSelector() {
            const fileInput = document.getElementById('barcodeFileInput');
            fileInput.value = null;
            fileInput.click();
        }

        function handleBarcodeFile(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            stopCamera();

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.id = 'crop-image-temp';
                img.src = e.target.result;

                img.onload = function() {
                    // Show crop container
                    const cropContainer = document.createElement('div');
                    cropContainer.id = 'crop-modal';
                    cropContainer.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);display:flex;align-items:center;justify-content:center;z-index:9999;padding:20px;';

                    cropContainer.innerHTML = `
                        <div style="background:#151a21;border-radius:12px;padding:20px;max-width:500px;width:100%;max-height:80vh;overflow-y:auto;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
                                <h3 style="color:#f1f3fc;font-size:18px;font-weight:bold;margin:0;">Crop Barcode Area</h3>
                                <button onclick="document.getElementById('crop-modal').remove();" style="background:none;border:none;color:#a8abb3;font-size:20px;cursor:pointer;">✕</button>
                            </div>
                            <img id="crop-img-to-crop" src="${e.target.result}" style="max-width:100%;border-radius:8px;margin-bottom:15px;">
                            <div style="display:flex;gap:10px;">
                                <button id="btnRotateCrop" style="flex:1;padding:10px;background:#00e3fd;color:#0a0e14;border:none;border-radius:8px;font-weight:bold;cursor:pointer;">↻ Rotate</button>
                                <button id="btnCropAndScan" style="flex:1;padding:10px;background:#b6a0ff;color:#0a0e14;border:none;border-radius:8px;font-weight:bold;cursor:pointer;">✓ Scan Barcode</button>
                            </div>
                        </div>
                    `;

                    document.body.appendChild(cropContainer);

                    const cropImg = document.getElementById('crop-img-to-crop');
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(cropImg, {
                        viewMode: 1,
                        autoCropArea: 0.85,
                        aspectRatio: 4,
                        responsive: true,
                        guides: true,
                        highlight: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnClick: true,
                    });

                    document.getElementById('btnRotateCrop').onclick = function() {
                        cropper.rotate(90);
                    };

                    document.getElementById('btnCropAndScan').onclick = function() {
                        cropAndScanBarcode();
                    };
                };
            };
            reader.readAsDataURL(file);
        }

        function cropAndScanBarcode() {
            const statusElement = document.getElementById('scannerStatus');
            statusElement.textContent = '🔍 Processing image…';
            statusElement.classList.remove('bg-red-500/10', 'text-red-400', 'bg-emerald-500/10', 'text-emerald-400');
            statusElement.classList.add('bg-secondary/10', 'text-secondary');

            try {
                const canvas = cropper.getCroppedCanvas({
                    width: 1200,
                    height: 300
                });

                const ctx = canvas.getContext('2d');
                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imgData.data;

                // Grayscale + threshold for better barcode contrast
                for (let i = 0; i < data.length; i += 4) {
                    const gray = (data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114);
                    const val = gray > 128 ? 255 : 0;
                    data[i] = data[i + 1] = data[i + 2] = val;
                }
                ctx.putImageData(imgData, 0, 0);

                console.log('Canvas processed, converting to blob...');

                canvas.toBlob(function(blob) {
                    console.log('Blob created:', blob);
                    const imgFile = new File([blob], 'barcode.png', { type: 'image/png' });

                    // Create hidden container for Html5Qrcode if it doesn't exist
                    let container = document.getElementById('qrcode-reader-temp');
                    if (!container) {
                        container = document.createElement('div');
                        container.id = 'qrcode-reader-temp';
                        container.style.display = 'none';
                        document.body.appendChild(container);
                    }

                    statusElement.textContent = '📱 Scanning barcode…';
                    const tempReader = new Html5Qrcode('qrcode-reader-temp');

                    tempReader.scanFile(imgFile, true)
                        .then(function(decodedText) {
                            console.log('✓ Barcode detected:', decodedText);
                            tempReader.clear();
                            statusElement.textContent = '✓ Found: ' + decodedText;
                            statusElement.classList.remove('bg-secondary/10', 'text-secondary');
                            statusElement.classList.add('bg-emerald-500/10', 'text-emerald-400');

                            // Close crop modal
                            const modal = document.getElementById('crop-modal');
                            if (modal) modal.remove();

                            // Small delay before fetching
                            setTimeout(() => {
                                console.log('Fetching product for barcode:', decodedText);
                                fetch(`/scan/${encodeURIComponent(decodedText)}`, {
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json',
                                    }
                                })
                                    .then(response => {
                                        console.log('Response status:', response.status);
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Product data received:', data);
                                        if (data.error) {
                                            showScanError('Product not found');
                                        } else if (data.success && data.cart) {
                                            // Sync local cart with server cart (don't add again)
                                            cart = Object.values(data.cart).map(item => ({
                                                id: item.product_id,
                                                name: item.name,
                                                price: parseFloat(item.price),
                                                qty: item.quantity
                                            }));
                                            updateCart();
                                            showScanSuccess();
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Fetch error:', error);
                                        showScanError('Error: ' + error.message);
                                    });
                            }, 500);
                        })
                        .catch(function(err) {
                            console.error('Scan error:', err);
                            tempReader.clear();
                            showScanError('❌ Barcode not detected — try crop or better lighting');
                        });
                }, 'image/png');
            } catch (error) {
                console.error('Crop error:', error);
                showScanError('Error processing image: ' + error.message);
            }
        }

        function searchByBarcode() {
            const barcode = document.getElementById('barcodeInput').value.trim();
            if (barcode) {
                // Fetch product details from backend
                fetch(`/scan/${barcode}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            showScanError('Product not found!');
                        } else if (data.success) {
                            // Update local cart from server response
                            if (data.cart) {
                                cart = Object.values(data.cart).map(item => ({
                                    id: item.product_id,
                                    name: item.name,
                                    price: parseFloat(item.price),
                                    qty: item.quantity
                                }));
                            }
                            updateCart();
                            showScanSuccess();
                            document.getElementById('barcodeInput').value = '';
                            document.getElementById('barcodeInput').focus();
                        } else {
                            showScanError('Error scanning product');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showScanError('Error scanning product');
                    });
            }
        }

        function showScanSuccess() {
            const status = document.getElementById('scanStatus');
            status.textContent = '✓ Added to cart!';
            status.classList.remove('text-error');
            status.classList.add('text-secondary');
            
            const input = document.getElementById('barcodeInput');
            input.classList.add('border-secondary');
            input.classList.remove('border-error');
            
            setTimeout(() => {
                status.textContent = '';
                input.classList.remove('border-secondary');
            }, 1500);
        }

        function showScanError(message) {
            const status = document.getElementById('scanStatus');
            status.textContent = '✗ ' + message;
            status.classList.remove('text-secondary');
            status.classList.add('text-error');
            
            const input = document.getElementById('barcodeInput');
            input.classList.remove('border-secondary');
            input.classList.add('border-error');
            
            setTimeout(() => {
                status.textContent = '';
                input.classList.remove('border-error');
                input.classList.add('border-secondary/50');
            }, 2000);
        }

        function clearBarcodeInput() {
            document.getElementById('barcodeInput').value = '';
            document.getElementById('barcodeInput').focus();
        }

        function disableScanning() {
            scanningEnabled = !scanningEnabled;
            const btn = event.target.closest('button');
            const input = document.getElementById('barcodeInput');
            const pulse = document.querySelector('[class*="animate-pulse"]');
            
            if (scanningEnabled) {
                btn.classList.remove('bg-error/20', 'text-error');
                btn.classList.add('bg-surface-container', 'text-on-surface');
                btn.innerHTML = '<span class="material-symbols-outlined inline text-[18px] mr-1">block</span> Disable Scanning';
                input.disabled = false;
                pulse.parentElement.innerHTML = '<div class="w-3 h-3 bg-secondary rounded-full animate-pulse"></div><span class="text-secondary text-sm font-bold">Ready to scan</span>';
            } else {
                btn.classList.add('bg-error/20', 'text-error');
                btn.classList.remove('bg-surface-container', 'text-on-surface');
                btn.innerHTML = '<span class="material-symbols-outlined inline text-[18px] mr-1">check_circle</span> Enable Scanning';
                input.disabled = true;
                pulse.parentElement.innerHTML = '<div class="w-3 h-3 bg-error rounded-full"></div><span class="text-error text-sm font-bold">Scanning disabled</span>';
            }
        }

        function addToCart(id, name, price) {
            const numPrice = parseFloat(price); // Convert to number
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({id, name, price: numPrice, qty: 1});
            }
            updateCart();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCart();
        }

        function updateQty(id, qty) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.qty = Math.max(1, qty);
                updateCart();
            }
        }

        function updateCart() {
            const cartItems = document.getElementById('cartItems');
            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="text-on-surface-variant text-sm text-center py-4">Cart is empty</p>';
            } else {
                cartItems.innerHTML = cart.map((item, index) => `
                    <div class="flex flex-col p-3 bg-surface-container/30 rounded border border-outline/10" data-item-id="${item.id}">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm font-bold text-on-surface">${item.name}</p>
                            <button class="cart-remove px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-bold hover:bg-red-500/30" type="button">✕</button>
                        </div>
                        <p class="text-xs text-on-surface-variant mb-2">₹${item.price.toFixed(2)} per item</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 bg-surface-container rounded">
                                <button class="cart-minus px-2 py-1 text-secondary font-bold hover:bg-surface-container/80" type="button">−</button>
                                <span class="px-3 py-1 text-on-surface font-semibold cart-qty">${item.qty}</span>
                                <button class="cart-plus px-2 py-1 text-secondary font-bold hover:bg-surface-container/80" type="button">+</button>
                            </div>
                            <span class="text-sm font-bold text-emerald-400">₹${(item.price * item.qty).toFixed(2)}</span>
                        </div>
                    </div>
                `).join('');

                // Re-attach event listeners after rendering
                attachCartListeners();
            }

            // Calculate subtotal and GST (7.5%)
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const gst = subtotal * 0.075; // Assuming 7.5% GST
            const total = subtotal + gst;

            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('gst').textContent = gst.toFixed(2);
            document.getElementById('total').textContent = total.toFixed(2);

            document.getElementById('cartCount').textContent = cart.length;
            document.getElementById('cartValue').textContent = total.toFixed(2);
        }

        // Attach event listener to cart items container
        function attachCartListeners() {
            const cartItemsContainer = document.getElementById('cartItems');
            if (!cartItemsContainer) return;

            cartItemsContainer.onclick = function(e) {
                // Find the button that was clicked
                const plusBtn = e.target.closest('.cart-plus');
                const minusBtn = e.target.closest('.cart-minus');
                const removeBtn = e.target.closest('.cart-remove');

                if (!plusBtn && !minusBtn && !removeBtn) return;

                // Get the item container
                const itemDiv = e.target.closest('[data-item-id]');
                if (!itemDiv) return;

                const itemId = itemDiv.dataset.itemId;
                console.log('Button clicked - itemId:', itemId, 'Plus:', !!plusBtn, 'Minus:', !!minusBtn, 'Remove:', !!removeBtn);

                const item = cart.find(i => String(i.id) === String(itemId));
                if (!item) {
                    console.log('Item not found in cart:', itemId);
                    return;
                }

                if (plusBtn) {
                    console.log('PLUS clicked for', itemId, 'old qty:', item.qty);
                    item.qty++;
                    updateCart();
                    return false;
                }

                if (minusBtn) {
                    console.log('MINUS clicked for', itemId, 'old qty:', item.qty);
                    if (item.qty > 1) {
                        item.qty--;
                        updateCart();
                    }
                    return false;
                }

                if (removeBtn) {
                    console.log('REMOVE clicked for', itemId);
                    removeFromCart(itemId);
                    return false;
                }
            };
        }

        // Attach listeners when page loads
        window.addEventListener('load', attachCartListeners);

        function generateBill() {
            if (cart.length === 0) {
                alert('Cart is empty');
                return;
            }
            
            // Save cart to server session then redirect to checkout
            const cartData = cart.map(item => ({
                product_id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.qty
            }));

            fetch('{{ route("cart.update") }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    cart: Object.fromEntries(cart.map(item => [item.id, {
                        product_id: item.id,
                        name: item.name,
                        price: item.price,
                        quantity: item.qty,
                        image: null
                    }]))
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("payment.checkout") }}';
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Error saving cart. Please try again.');
            });
        }

        function downloadReceipt() {
            const billData = JSON.parse(localStorage.getItem('billData'));
            
            if (!billData) {
                alert('No bill data found');
                return;
            }
            
            const receiptHTML = `
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
                        .receipt { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 15px; margin-bottom: 20px; }
                        .logo { font-size: 28px; font-weight: bold; color: #b6a0ff; margin-bottom: 5px; }
                        .tagline { font-size: 12px; color: #666; }
                        .info { text-align: center; font-size: 12px; color: #666; margin-bottom: 20px; }
                        .items { margin-bottom: 20px; }
                        .item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; font-size: 13px; }
                        .item-name { font-weight: bold; }
                        .totals { border-top: 2px dashed #333; border-bottom: 2px dashed #333; padding: 15px 0; margin: 15px 0; }
                        .total-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; }
                        .grand-total { display: flex; justify-content: space-between; font-weight: bold; font-size: 16px; color: #b6a0ff; padding: 10px 0; }
                        .footer { text-align: center; font-size: 12px; color: #666; margin-top: 20px; }
                        .payment-method { text-align: center; padding: 10px; background: #f0f0f0; border-radius: 4px; margin-bottom: 10px; font-weight: bold; }
                        @media print { body { padding: 0; background: white; } .receipt { box-shadow: none; max-width: 100%; } }
                    </style>
                </head>
                <body>
                    <div class="receipt">
                        <div class="header">
                            <div class="logo">🧿 ETHER POS</div>
                            <div class="tagline">Smart Retail Billing System</div>
                        </div>
                        
                        <div class="info">
                            <div>${billData.date}</div>
                            <div style="margin-top: 8px;">Receipt</div>
                        </div>
                        
                        <div class="items">
                            ${billData.items.map(item => `
                                <div class="item">
                                    <div>
                                        <div class="item-name">${item.name}</div>
                                        <div style="font-size: 11px; color: #999;">₹${item.price.toFixed(2)} × ${item.qty}</div>
                                    </div>
                                    <div style="text-align: right; font-weight: bold;">₹${(item.price * item.qty).toFixed(2)}</div>
                                </div>
                            `).join('')}
                        </div>
                        
                        <div class="totals">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span>₹${billData.subtotal.toFixed(2)}</span>
                            </div>
                            <div class="total-row">
                                <span>GST (18%):</span>
                                <span>₹${billData.gst.toFixed(2)}</span>
                            </div>
                            <div class="grand-total">
                                <span>Total:</span>
                                <span>₹${billData.total.toFixed(2)}</span>
                            </div>
                        </div>
                        
                        <div class="payment-method">
                            Payment: ${billData.paymentMethod}
                        </div>
                        
                        <div class="footer">
                            <div style="margin-bottom: 10px;">Thank you for your purchase!</div>
                            <div>www.etherpos.com</div>
                        </div>
                    </div>
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(receiptHTML);
            printWindow.document.close();
            
            // Auto print after a short delay
            setTimeout(() => {
                printWindow.print();
            }, 250);
        }

        function clearCart() {
            cart = [];
            document.getElementById('downloadBtn').classList.add('hidden');
            updateCart();
        }

        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = localStorage.getItem('theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            localStorage.setItem('theme', newTheme);
            html.classList.toggle('dark', newTheme === 'dark');
            html.classList.toggle('light', newTheme === 'light');
            
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const icon = document.getElementById('themeIcon');
            icon.textContent = isDark ? 'light_mode' : 'dark_mode';
        }

        // Focus on barcode input on page load
        window.addEventListener('load', function() {
            updateThemeIcon();
            document.getElementById('barcodeInput').focus();
        });
    </script>
</body>
</html>
