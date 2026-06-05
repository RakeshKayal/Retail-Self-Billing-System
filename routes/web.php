<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Homec;
use App\Http\Controllers\ProductC;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;





// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showLogin'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function () {
        return redirect('/dashboard');
    })->middleware('role:admin|staff')->name('admin.dashboard');

    // Customer routes
    Route::middleware('role:customer')->group(function () {
        Route::get('/customer', [BillingController::class, 'customerDashboard'])->name('customer.dashboard');
        Route::get('/scan/{barcode}', [BillingController::class, 'scan'])->name('scan');
        Route::post('/cart/add', [BillingController::class, 'addToCart'])->name('cart.add');
        Route::get('/cart', [BillingController::class, 'getCart']);
        Route::put('/cart/update', [BillingController::class, 'updateCart'])->name('cart.update');
        Route::post('/cart/remove', [BillingController::class, 'removeItem']);
        Route::post('/cart/clear', [BillingController::class, 'clearCart']);
        Route::post('/checkout', [BillingController::class, 'checkout'])->name('checkout');
        Route::get('/receipts', [BillingController::class, 'customerReceipts'])->name('customer.receipts');
        
        // New features
        Route::post('/sync-offline', [BillingController::class, 'syncOfflineCart'])->name('sync.offline');
        Route::get('/notifications', [BillingController::class, 'getNotifications'])->name('notifications');
        Route::post('/notifications/{id}/read', [BillingController::class, 'markNotificationRead'])->name('notification.read');
        Route::get('/stores', [BillingController::class, 'getStores'])->name('stores');

        // Payment routes
        Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
        Route::post('/payment/create-order', [PaymentController::class, 'createOrder'])->name('payment.createOrder');
        Route::post('/payment/verify', [PaymentController::class, 'verifyPayment'])->name('payment.verify');
        Route::get('/payment/status', [PaymentController::class, 'getOrderStatus'])->name('payment.status');
    });

    // Admin/staff routes for payment dashboard
    Route::middleware('role:admin|staff')->group(function () {
        Route::get('/admin/payments', [PaymentController::class, 'getPaymentsForAdmin'])->name('admin.payments');
        Route::get('/admin/revenue-stats', [PaymentController::class, 'getRevenueStats'])->name('admin.revenue');
        Route::post('/admin/create-cash-bill', [PaymentController::class, 'createCashBill'])->name('admin.createCashBill');
    });
});

// Profile: change password (any authenticated user)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/password', [\App\Http\Controllers\PasswordController::class, 'edit'])->name('profile.password.edit');
    Route::post('/profile/password', [\App\Http\Controllers\PasswordController::class, 'update'])->name('profile.password.update');
});

// Debug endpoint - remove after testing
Route::get('/debug/bills', function() {
    $bills = \App\Models\Bill::all();
    return response()->json([
        'total_bills' => $bills->count(),
        'bills' => $bills,
        'today_date' => today(),
        'now' => now(),
    ]);
});

Route::get('/debug/revenue', function() {
    $today = \App\Models\Bill::where('status', 'completed')
        ->whereDate('created_at', today())
        ->sum('total_amount');

    $thisMonth = \App\Models\Bill::where('status', 'completed')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_amount');

    $online = \App\Models\Bill::where('status', 'completed')
        ->whereDate('created_at', today())
        ->whereIn('payment_method', ['online', 'card'])
        ->sum('total_amount');

    $cash = \App\Models\Bill::where('status', 'completed')
        ->whereDate('created_at', today())
        ->where('payment_method', 'cash')
        ->sum('total_amount');

    return response()->json([
        'success' => true,
        'today' => (float)$today,
        'this_month' => (float)$thisMonth,
        'online_sales' => (float)$online,
        'cash_sales' => (float)$cash,
        'today_date' => today(),
    ]);
});

// Route::get('/', function () {
//     return view('welcome');
// });

// view  
Route::get('/',  [Homec::class, 'dashboard']  )->name('home')->middleware('auth');
Route::get('/dashboard',  [Homec::class, 'dashboard']  )->name('dashboard')->middleware('auth');

// Admin routes - protected with auth and admin/staff role
Route::middleware(['auth', 'role:admin|staff'])->group(function () {
    Route::get('/addProduct',    [Homec::class, 'add']   )->name('addProduct');
    Route::get('/addcat',    [Homec::class, 'addcat']   )->name('addcat');
    Route::get('/bill',    [Homec::class, 'bill']   )->name('bill');
    Route::post('/add',[ProductC::class,'addpro'])->name('insertproduct');
    Route::get('/showProduct',   [ProductC::class, 'show']  )->name('showProduct');
    Route::get('/manageProduct', [ProductC::class, 'manage'])->name('manageProduct');
    Route::get('/manageProduct/{id}/edit',[ProductC::class, 'edit'])->name('editproduct');
    Route::patch('/manageProduct/{id}/update',[ProductC::class, 'update'] )->name('product.update');
    Route::delete('/manageProduct/{id}/delete',[ProductC::class, 'destroy'])->name('deleteproduct');
    Route::post('/add_cate',[ProductC::class,'add_cate'])->name('insertcat');
    Route::delete('/delete_cate/{id}',[ProductC::class,'delete_cate'])->name('deletecat');
});

// Staff management - admin only
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/staff', [\App\Http\Controllers\StaffController::class, 'index'])->name('admin.staff.index');
    Route::post('/admin/staff/send-otp', [\App\Http\Controllers\StaffController::class, 'sendOtp'])->name('admin.staff.sendOtp');
    Route::post('/admin/staff/verify-otp', [\App\Http\Controllers\StaffController::class, 'verifyOtp'])->name('admin.staff.verifyOtp');
    Route::post('/admin/staff', [\App\Http\Controllers\StaffController::class, 'store'])->name('admin.staff.store');
    Route::delete('/admin/staff/{id}', [\App\Http\Controllers\StaffController::class, 'destroy'])->name('admin.staff.destroy');
});
