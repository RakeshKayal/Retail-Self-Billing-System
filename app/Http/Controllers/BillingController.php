<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Store;
use App\Models\Recommendation;
use App\Models\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function scan($barcode)
    {
        $product = Product::with('category')->where('product_barcode', $barcode)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Add to session cart
        $cart = session()->get('cart', []);
        $productId = $product->product_id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'name' => $product->product_name,
                'price' => $product->product_price,
                'quantity' => 1,
                'image' => $product->p_img,
            ];
        }

        session()->put('cart', $cart);

        // Return detailed product information
        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->product_id,
                'name' => $product->product_name,
                'price' => $product->product_price,
                'barcode' => $product->product_barcode,
                'category' => $product->category->cat_name ?? 'N/A',
                'image' => $product->p_img ? asset('product_img/' . $product->p_img) : null,
                'description' => 'Premium quality product',
            ],
            'cart' => $cart,
            'message' => 'Product added to cart!',
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product,product_id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $cart = session()->get('cart', []);
        $quantity = $request->quantity ?? 1;

        if (isset($cart[$product->product_id])) {
            $cart[$product->product_id]['quantity'] += $quantity;
        } else {
            $cart[$product->product_id] = [
                'product_id' => $product->product_id,
                'name' => $product->product_name,
                'price' => $product->product_price,
                'quantity' => $quantity,
                'image' => $product->p_img,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Added to cart!',
            'cart' => $cart,
        ]);
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Redirect to payment checkout instead of creating bill immediately
        return redirect()->route('payment.checkout');
    }

    // ── Mobile receipt page (opened via QR) ──
    public function showMobileReceipt($token)
    {
        $data = Cache::get("receipt:{$token}");

        if (!$data) {
            abort(404, 'Receipt expired or not found.');
        }

        return view('receipt.mobile', compact('data', 'token'));
    }

    public function getCart()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $total,
            'count' => count($cart),
        ]);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
        ]);

        session()->put('cart', $request->cart);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated!',
        ]);
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart' => $cart,
        ]);
    }

    public function clearCart()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
        ]);
    }

    public function customerDashboard()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();
        $cart = session()->get('cart', []);
        $products = Product::all();
        $stores = Store::query()->where('is_active', true)->get();

        // Get recommendations
        $recommendations = $this->getRecommendations($userId);
        
        // Get recent notifications
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('customer.dashboard', compact('cart', 'products', 'stores', 'recommendations', 'notifications'));
    }

    public function customerReceipts()
    {
        $bills = Bill::with(['items.product'])->orderBy('created_at', 'desc')->limit(20)->get();
        
        return view('customer.receipts', compact('bills'));
    }

    // ── AI-Based Recommendations ──
    private function getRecommendations($userId, $limit = 5)
    {
        // Get user's purchase history - products they have bought
        $purchasedProductIds = BillItem::whereHas('bill')
            ->pluck('product_id')->unique()->toArray();

        if (empty($purchasedProductIds)) {
            return Product::inRandomOrder()->limit($limit)->get();
        }

        // Get products in the same category as purchased items
        $recommendedProducts = Product::whereIn('cat_id', 
            Product::whereIn('product_id', $purchasedProductIds)->pluck('cat_id')
        )->whereNotIn('product_id', $purchasedProductIds)
        ->inRandomOrder()
        ->limit($limit)
        ->get();

        return $recommendedProducts;
    }

    private function recordPurchaseForRecommendation($userId, $productId, $quantity)
    {
        Recommendation::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            [
                'score' => DB::raw('COALESCE(score, 0) + ' . (10 * $quantity)),
                'reason' => 'Purchase history',
            ]
        );
    }

    // ── Offline Sync ──
    public function syncOfflineCart(Request $request)
    {
        $offlineCart = $request->input('cart', []);
        $storeId = $request->input('store_id', 1);

        if (empty($offlineCart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Process the offline cart
        $total = 0;
        foreach ($offlineCart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $bill = Bill::create([
            'total_amount' => $total,
            'store_id' => $storeId,
            'sync_status' => 'synced',
            'synced_at' => now(),
        ]);

        foreach ($offlineCart as $item) {
            BillItem::create([
                'bill_id' => $bill->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return response()->json([
            'success' => true,
            'bill_id' => $bill->id,
            'message' => 'Offline cart synced successfully',
        ]);
    }

    // ── Get Notifications ──
    public function getNotifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->where('read_at', null)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    // ── Mark Notification as Read ──
    public function markNotificationRead($id)
    {
        Notification::find($id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    // ── Get Stores ──
    public function getStores()
    {
        $stores = Store::where('is_active', true)->get();
        return response()->json($stores);
    }
}