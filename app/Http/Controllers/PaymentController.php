<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Product;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class PaymentController extends Controller
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(
            env('RAZORPAY_KEY_ID'),
            env('RAZORPAY_KEY_SECRET')
        );
    }

    /**
     * Show checkout page with cart items
     */
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect('/customer')->with('error', 'Cart is empty!');
        }

        $total = 0;
        $items = [];

        foreach ($cart as $productId => $cartItem) {
            $product = Product::find($productId);
            $quantity = isset($cartItem['quantity']) ? (int)$cartItem['quantity'] : 1;
            if ($product && $quantity > 0) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->product_price * $quantity,
                ];
                $total += $product->product_price * $quantity;
            }
        }

        return view('checkout', [
            'items' => $items,
            'total' => $total,
            'razorpayKeyId' => env('RAZORPAY_KEY_ID'),
        ]);
    }

    /**
     * Create Razorpay order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:online,card',
        ]);

        try {
            $amountInPaisa = (int)($request->amount * 100); // Convert to paisa

            $order = $this->api->order->create([
                'amount' => $amountInPaisa,
                'currency' => 'INR',
                'receipt' => 'receipt_' . time(),
                'notes' => [
                    'payment_method' => $request->payment_method,
                ],
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order['id'],
                'order_data' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify payment and create bill
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'payment_method' => 'required|in:online,card',
        ]);

        Log::info('Payment Verification Request:', $request->all());

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            Log::info('Verifying Payment Signature:', $attributes);

            // Verify payment signature
            $this->api->utility->verifyPaymentSignature($attributes);

            Log::info('Payment Signature Verified Successfully');

            // Get cart from session
            $cart = session()->get('cart', []);
            $subtotal = 0;
            $billItems = [];
            $taxRate = 0.075;

            // Create bill
            $bill = new Bill();
            $bill->user_id = auth()->id();
            $bill->payment_method = $request->payment_method;
            $bill->razorpay_order_id = $request->razorpay_order_id;
            $bill->razorpay_payment_id = $request->razorpay_payment_id;
            $bill->status = 'completed';

            // Calculate subtotal and prepare items
            foreach ($cart as $productId => $cartItem) {
                $product = Product::find($productId);
                $quantity = isset($cartItem['quantity']) ? (int)$cartItem['quantity'] : 1;
                if ($product && $quantity > 0) {
                    $itemSubtotal = $product->product_price * $quantity;
                    $subtotal += $itemSubtotal;

                    $billItems[] = [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'unit_price' => $product->product_price,
                        'subtotal' => $itemSubtotal,
                    ];
                }
            }

            $taxAmount = round($subtotal * $taxRate, 2);
            $bill->total_amount = round($subtotal + $taxAmount, 2);
            $bill->save();

            // Save bill items
            foreach ($billItems as $item) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['unit_price'],
                ]);
            }

            // Clear cart
            session()->forget('cart');

            // Send invoice email to customer
            try {
                $bill = Bill::with(['items.product', 'user'])->find($bill->id);
                // Ensure each item has the `product` relation set — some environments
                // may not correctly infer relations when primary keys use non-standard
                // names, so explicitly attach the product model.
                if ($bill && $bill->items) {
                    foreach ($bill->items as $item) {
                        $product = \App\Models\Product::find($item->product_id);
                        if ($product) {
                            $item->setRelation('product', $product);
                        }
                    }
                }

                if ($bill && $bill->user && $bill->user->email) {
                    Mail::to($bill->user->email)->send(new InvoiceMail($bill));
                }
            } catch (\Exception $e) {
                Log::error('Invoice Email Error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and bill created!',
                'bill_id' => $bill->id,
            ]);
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            Log::error('Razorpay Signature Verification Error: ' . $e->getMessage());
            Log::error('Full Error: ', ['error' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Payment signature verification failed. Please contact support.',
                'debug_error' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error('Payment Verification Error: ' . $e->getMessage());
            Log::error('Full Error Stack: ', ['error' => $e, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage(),
                'debug_error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order status
     */
    public function getOrderStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
        ]);

        try {
            $order = $this->api->order->fetch($request->order_id);

            return response()->json([
                'success' => true,
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all payments for admin dashboard
     */
    public function getPaymentsForAdmin()
    {
        $bills = Bill::with(['user', 'items.product'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'bills' => $bills,
        ]);
    }

    /**
     * Create cash bill directly (no payment gateway)
     */
    public function createCashBill(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,qr',
        ]);

        try {
            $bill = new Bill();
            $bill->user_id = auth()->id();
            $bill->payment_method = $request->payment_method;
            $bill->status = 'completed';
            $bill->total_amount = $request->total_amount;
            $bill->save();

            // Create bill items
            foreach ($request->items as $item) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Send invoice email to customer if available
            try {
                $bill = Bill::with(['items.product', 'user'])->find($bill->id);
                if ($bill && $bill->items) {
                    foreach ($bill->items as $item) {
                        $product = \App\Models\Product::find($item->product_id);
                        if ($product) {
                            $item->setRelation('product', $product);
                        }
                    }
                }
                if ($bill && $bill->user && $bill->user->email) {
                    Mail::to($bill->user->email)->send(new InvoiceMail($bill));
                }
            } catch (\Exception $e) {
                Log::error('Cash Invoice Email Error: ' . $e->getMessage());
            }

            Log::info('Cash Bill Created:', ['bill_id' => $bill->id, 'amount' => $request->total_amount]);

            return response()->json([
                'success' => true,
                'bill_id' => $bill->id,
                'message' => 'Bill created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Cash Bill Creation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bill: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getRevenueStats()
    {
        $today = (float)Bill::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $thisMonth = (float)Bill::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $online = (float)Bill::where('status', 'completed')
            ->whereDate('created_at', today())
            ->whereIn('payment_method', ['online', 'card'])
            ->sum('total_amount');

        $cash = (float)Bill::where('status', 'completed')
            ->whereDate('created_at', today())
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        return response()->json([
            'success' => true,
            'today' => $today,
            'this_month' => $thisMonth,
            'online_sales' => $online,
            'cash_sales' => $cash,
            'total_sales' => $online + $cash,
        ]);
    }
}
