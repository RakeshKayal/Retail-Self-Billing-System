<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Ether POS</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body { background-color: #0a0e14; color: #f1f3fc; transition: all 0.3s ease; }
        html.light body { background-color: #f9fafb; color: #1f2937; }
    </style>
</head>
<body class="bg-[#0a0e14] min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2 text-white">Checkout</h1>
            <p class="text-gray-400">Complete your purchase</p>
        </div>

        <div class="grid grid-cols-3 gap-8">
            <!-- Order Items -->
            <div class="col-span-2">
                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-white">Order Items</h2>
                    <div class="space-y-3">
                        @foreach($items as $item)
                            <div class="flex justify-between items-center p-3 bg-[#0f141a] rounded">
                                <div>
                                    <p class="font-semibold text-white">{{ $item['product']->product_name }}</p>
                                    <p class="text-sm text-gray-400">Qty: {{ $item['quantity'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-blue-400">₹{{ number_format($item['subtotal'], 2) }}</p>
                                    <p class="text-sm text-gray-400">@ ₹{{ number_format($item['product']->product_price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="col-span-1">
                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6 sticky top-8">
                    <h2 class="text-xl font-bold mb-6 text-white">Order Summary</h2>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-300">
                            <span>Subtotal:</span>
                            <span>₹{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Tax (7.5%):</span>
                            <span>₹{{ number_format($total * 0.075, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-600 pt-4 flex justify-between text-xl font-bold text-white">
                            <span>Total:</span>
                            <span id="totalAmount">₹{{ number_format($total + ($total * 0.075), 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-white mb-3">Payment Method:</label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 border border-gray-600 rounded cursor-pointer hover:border-blue-500 transition" style="background-color: #0f141a;">
                                <input type="radio" name="payment_method" value="online" class="mr-3" checked>
                                <span class="text-white">💳 Online (Razorpay)</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-600 rounded cursor-pointer hover:border-blue-500 transition" style="background-color: #0f141a;">
                                <input type="radio" name="payment_method" value="card" class="mr-3">
                                <span class="text-white">🏦 Credit/Debit Card</span>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <button 
                        id="payBtn" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition mb-3">
                        Pay ₹{{ number_format($total + ($total * 0.075), 2) }}
                    </button>

                    <button 
                        onclick="window.history.back()" 
                        class="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 rounded-lg transition">
                        Back to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const razorpayKeyId = '{{ $razorpayKeyId }}';
        const totalAmount = {{ $total  + ($total * 0.075) }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        document.getElementById('payBtn').addEventListener('click', async function() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            try {
                // Create order
                const orderResponse = await fetch('{{ route("payment.createOrder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        amount: totalAmount,
                        payment_method: paymentMethod,
                    }),
                });

                const orderData = await orderResponse.json();

                if (!orderData.success) {
                    alert('Failed to create order: ' + orderData.message);
                    return;
                }

                // Open Razorpay checkout
                const options = {
                    key: razorpayKeyId,
                    amount: Math.round(totalAmount * 100),
                    currency: 'INR',
                    name: 'Ether POS',
                    description: 'Customer Purchase',
                    order_id: orderData.order_id,
                    handler: async function(response) {
                        // Verify payment
                        const verifyResponse = await fetch('{{ route("payment.verify") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature,
                                payment_method: paymentMethod,
                            }),
                        });

                        const verifyData = await verifyResponse.json();

                        if (verifyData.success) {
                            alert('Payment successful! Bill ID: ' + verifyData.bill_id);
                            window.location.href = '/receipts?bill_id=' + verifyData.bill_id;
                        } else {
                            alert('Payment verification failed: ' + verifyData.message);
                        }
                    },
                    prefill: {
                        name: '{{ auth()->user()->name }}',
                        email: '{{ auth()->user()->email }}',
                    },
                    theme: {
                        color: '#b6a0ff',
                    },
                };

                const rzp = new Razorpay(options);
                rzp.open();
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
            }
        });
    </script>
</body>
</html>
