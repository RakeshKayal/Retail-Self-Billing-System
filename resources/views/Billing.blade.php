<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Billing System - Ether POS</title>
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
                <h1 class="text-4xl font-headline font-bold mb-2">Billing System</h1>
                <p class="text-on-surface-variant">Create and manage customer bills</p>
            </div>

            <!-- Barcode Scanner Section -->
            <div class="mb-8 border border-secondary/30 rounded-xl bg-gradient-to-br from-surface-container-low to-surface bg-secondary/5 p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-secondary/20 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-secondary text-2xl">qr_code_2</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-headline font-bold text-on-surface">Barcode Scanner</h2>
                        <p class="text-sm text-on-surface-variant">Scan products using barcode or manual entry - connects to database</p>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        <div class="w-3 h-3 bg-secondary rounded-full animate-pulse"></div>
                        <span class="text-secondary text-sm font-bold">Ready to scan</span>
                    </div>
                </div>
                
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
                            <p class="text-xs text-on-surface-variant mb-1">Total Products in DB</p>
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
                    <button onclick="clearBarcodeInput()" class="px-4 py-2 bg-surface-container text-on-surface rounded-lg hover:bg-surface-container/80 transition font-bold text-sm">
                        <span class="material-symbols-outlined inline text-[18px] mr-1">restart_alt</span> Clear Input
                    </button>
                    <button onclick="disableScanning()" class="px-4 py-2 bg-error/20 text-error rounded-lg hover:bg-error/30 transition font-bold text-sm">
                        <span class="material-symbols-outlined inline text-[18px] mr-1">block</span> Disable Scanning
                    </button>
                </div>
            </div>

            <div class="border border-outline/10 rounded-xl bg-surface-container-low p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Products Section -->
                    <div class="lg:col-span-2">
                        <h2 class="text-2xl font-headline font-bold mb-6 text-on-surface">Available Products</h2>
                        
                        <div class="mb-6">
                            <input type="text" id="productSearch" placeholder="Search products by name or barcode..." class="w-full bg-[#0f141a] border border-outline/10 rounded-lg px-4 py-3 text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:border-secondary/50">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto pr-2">
                            @forelse($products as $product)
                                <div class="border border-outline/10 bg-surface-container/30 rounded-lg overflow-hidden hover:bg-surface-container/50 cursor-pointer transition" onclick="addToCart('{{ $product->product_id }}', '{{ $product->product_name }}', {{ $product->product_price }})">
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
                                        <p class="text-xs text-on-surface-variant mt-1 font-mono">{{ $product->product_barcode }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-8 text-on-surface-variant">
                                    No products available. <a href="{{ route('addProduct') }}" class="text-secondary font-bold hover:underline">Add products</a>
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
                                <span class="text-on-surface-variant">GST (18%):</span>
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
                                    <input type="radio" name="paymentMethod" value="cash" id="payment-cash" class="w-4 h-4" checked>
                                    <span class="material-symbols-outlined ml-3 text-primary">payments</span>
                                    <span class="ml-2 text-on-surface font-medium">Cash</span>
                                </label>
                                <label class="flex items-center p-3 border border-outline/10 rounded-lg cursor-pointer hover:bg-surface-container/50 transition">
                                    <input type="radio" name="paymentMethod" value="card" id="payment-card" class="w-4 h-4">
                                    <span class="material-symbols-outlined ml-3 text-secondary">credit_card</span>
                                    <span class="ml-2 text-on-surface font-medium">Card</span>
                                </label>
                                <label class="flex items-center p-3 border border-outline/10 rounded-lg cursor-pointer hover:bg-surface-container/50 transition">
                                    <input type="radio" name="paymentMethod" value="qr" id="payment-qr" class="w-4 h-4">
                                    <span class="material-symbols-outlined ml-3 text-primary">qr_code_2</span>
                                    <span class="ml-2 text-on-surface font-medium">QR Code</span>
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
    <script>
        let cart = [];
        let scanningEnabled = true;
        
        // Store products for barcode lookup
        const productsData = {!! json_encode($products->map(fn($p) => ['id' => $p->product_id, 'name' => $p->product_name, 'price' => $p->product_price, 'barcode' => $p->product_barcode])->keyBy('barcode')) !!};

        // Barcode Scanner Handler
        document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && scanningEnabled) {
                const barcode = this.value.trim();
                if (barcode) {
                    const product = productsData[barcode] || Object.values(productsData).find(p => p.id == barcode);
                    
                    if (product) {
                        addToCart(product.id, product.name, product.price);
                        
                        // Visual feedback
                        showScanSuccess();
                        this.value = '';
                        this.focus();
                    } else {
                        showScanError('Product not found!');
                        this.value = '';
                        this.focus();
                    }
                }
            }
        });

        function showScanSuccess() {
            const status = document.getElementById('scanStatus');
            status.textContent = '✓ Scanned!';
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
                pulse.classList.remove('hidden');
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
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({id, name, price, qty: 1});
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
                cartItems.innerHTML = cart.map(item => `
                    <div class="flex justify-between items-center p-2 bg-surface-container/30 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-bold text-on-surface">${item.name}</p>
                            <p class="text-xs text-on-surface-variant">₹${item.price.toFixed(2)} x ${item.qty}</p>
                        </div>
                        <div class="space-x-1">
                            <button onclick="removeFromCart('${item.id}')" class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-bold">✕</button>
                        </div>
                    </div>
                `).join('');
            }

            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const gst = subtotal * 0.18;
            const total = subtotal + gst;

            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('gst').textContent = gst.toFixed(2);
            document.getElementById('total').textContent = total.toFixed(2);
            
            // Update cart count and value display
            document.getElementById('cartCount').textContent = cart.length;
            document.getElementById('cartValue').textContent = total.toFixed(2);
        }

        async function generateBill() {
            if (cart.length === 0) {
                alert('Cart is empty');
                return;
            }

            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            const paymentText = {
                'cash': 'Cash',
                'card': 'Card',
                'qr': 'QR Code'
            }[paymentMethod];

            // Store bill data in localStorage for receipt
            const subtotal = parseFloat(document.getElementById('subtotal').textContent);
            const gst = parseFloat(document.getElementById('gst').textContent);
            const total = parseFloat(document.getElementById('total').textContent);

            const billData = {
                date: new Date().toLocaleString(),
                items: cart,
                subtotal: subtotal,
                gst: gst,
                total: total,
                paymentMethod: paymentText,
                payment_method: paymentMethod
            };

            localStorage.setItem('billData', JSON.stringify(billData));

            // Save to database
            try {
                const response = await fetch('/admin/create-cash-bill', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        items: cart,
                        total_amount: total,
                        payment_method: paymentMethod
                    })
                });

                const result = await response.json();
                if (result.success) {
                    console.log('Bill saved to database:', result);
                } else {
                    console.error('Failed to save bill:', result.message);
                }
            } catch (error) {
                console.error('Error saving bill:', error);
            }

            alert(`Bill generated successfully!\nPayment Method: ${paymentText}\nTotal: ₹${total.toFixed(2)}\n\nClick "Download Receipt" to download the bill.`);

            // Show download button
            document.getElementById('downloadBtn').classList.remove('hidden');
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
            updateCart();
        }

        // Focus on barcode input on page load
        window.addEventListener('load', function() {
            document.getElementById('barcodeInput').focus();
        });
    </script>
</body>
</html>