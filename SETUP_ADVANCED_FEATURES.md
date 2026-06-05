# 🎉 Advanced Smart Billing System - SETUP INSTRUCTIONS

## 📋 Quick Start

### Step 1: Run Migrations
```bash
php artisan migrate
```

This creates:
- `stores` table (multi-store support)
- `recommendations` table (AI recommendations)
- `notifications` table (real-time notifications)
- Enhanced `bills` table (store tracking, offline sync)

### Step 2: Run Seeders
```bash
php artisan db:seed
```

This adds:
- ✅ 3 Sample Stores (Delhi, Mumbai, Bangalore)
- ✅ 1 Admin User (admin@example.com / password)
- ✅ 1 Customer User (customer@example.com / password)

### Step 3: Start Your Server
```bash
php artisan serve
```

---

## 🎯 Test Credentials

### Admin Account
- **Email**: admin@example.com
- **Password**: password
- **Role**: admin

### Customer Account
- **Email**: customer@example.com
- **Password**: password
- **Role**: customer

---

## 🚀 Features Overview

| Feature | Status | Details |
|---------|--------|---------|
| 📱 Scan & Go | ✅ Ready | Barcode scanning with auto-checkout option |
| ⚡ Express Mode | ✅ Ready | Skip cart review, pay instantly |
| 💡 AI Recommendations | ✅ Ready | Smart product suggestions based on history |
| 🎤 Voice Billing | ✅ Ready | Say "checkout" to pay (voice recognition) |
| 📴 Offline Mode | ✅ Ready | Cart syncs when back online |
| 🏪 Multi-Store | ✅ Ready | Select from 3 stores (expandable) |
| 🔔 Notifications | ✅ Ready | Real-time payment confirmations |
| 📋 Live Preview | ✅ Ready | See bill total while scanning |

---

## 🗂️ Important Folders

```
📁 app/Models/
├── Store.php (NEW)
├── Recommendation.php (NEW)
├── Notification.php (NEW)
└── Bill.php (UPDATED)

📁 database/migrations/
├── 2026_04_01_185509_create_stores_table.php (NEW)
├── 2026_04_01_185510_create_recommendations_table.php (NEW)
├── 2026_04_01_185510_create_notifications_table.php (NEW)
└── 2026_04_01_185528_add_store_and_sync_to_bills.php (NEW)

📁 database/seeders/
├── StoreSeeder.php (NEW)
├── UserSeeder.php (UPDATED)
└── DatabaseSeeder.php (UPDATED)

📁 resources/views/customer/
├── dashboard.blade.php (COMPLETELY REDESIGNED)
└── receipts.blade.php (existing)

📁 app/Http/Controllers/
└── BillingController.php (SIGNIFICANTLY ENHANCED)

📁 routes/
└── web.php (UPDATED with new endpoints)
```

---

## 📡 API Endpoints (NEW)

All endpoints require authentication and customer role.

### Notifications
```
GET  /notifications           - Get all unread notifications
POST /notifications/{id}/read - Mark notification as read
```

### Stores
```
GET  /stores - Get all active stores
```

### Offline Sync
```
POST /sync-offline - Sync offline cart with server
```

### Existing (Enhanced)
```
GET    /scan/{barcode}       - Scan product by barcode
GET    /cart                 - Get current cart
POST   /cart/update          - Update cart item quantity
POST   /cart/remove          - Remove item from cart
POST   /cart/clear           - Clear entire cart
POST   /checkout             - Complete purchase (now with store_id)
```

---

## 🎨 Dashboard Features Locator

### Top Header
- **Left**: "✦ Smart Billing Portal" title
- **Center**: Store selector dropdown
- **Right**: 🔔 Notifications, 👤 User Menu, ❓ Help

### Main Content Area
**Left Column (Large)**:
- 📱 Scan & Go section with barcode input
- 🎤 Voice button
- 💳 Pay Now button (Express mode)
- 📋 Live bill preview
- Available products grid
- 💡 Recommendations section

**Right Column (Sticky Sidebar)**:
- 🛒 Your Cart
- Item list with +/- controls
- Subtotal, Tax, Total
- Action buttons (Checkout, Clear Cart)
- Status indicator (Online/Offline)

### Notifications Panel
- Slide-out from right side
- Shows all notifications
- Click to mark as read
- Real-time updates

---

## 🛠️ Troubleshooting

### Voice Recognition Not Working
- ✓ Only works on Chrome, Edge, Safari
- ✓ Requires HTTPS in production
- ✓ Check microphone permissions

### Offline Sync Not Working
- ✓ Ensure network connection
- ✓ Check browser's IndexedDB/LocalStorage
- ✓ Check server logs: `php artisan logs:tail`

### Recommendations Not Showing
- ✓ Need at least 1 purchase history
- ✓ Check `recommendations` table is populated
- ✓ Verify `product_id` exists in products table

### Multiple Stores Not Showing
- ✓ Run: `php artisan db:seed --class=StoreSeeder`
- ✓ Check `stores` table has entries
- ✓ Verify `is_active = true` for each store

---

## 📊 Database Queries

### View all stores
```sql
SELECT * FROM stores WHERE is_active = true;
```

### View user recommendations
```sql
SELECT r.*, p.product_name, p.product_price 
FROM recommendations r
JOIN products p ON r.product_id = p.product_id
WHERE r.user_id = 2
ORDER BY r.score DESC;
```

### View user notifications
```sql
SELECT * FROM notifications 
WHERE user_id = 2 
ORDER BY created_at DESC
LIMIT 10;
```

### View bills and their stores
```sql
SELECT b.id, b.total_amount, s.store_name, b.sync_status
FROM bills b
LEFT JOIN stores s ON b.store_id = s.id
ORDER BY b.created_at DESC;
```

---

## 🔐 Security Notes

✅ All routes protected with `auth` middleware
✅ Customer routes require `role:customer` middleware
✅ CSRF protection on all POST requests
✅ Notifications filtered by user_id
✅ Bills associated with authenticated user

---

## 💡 Tips & Tricks

### For Faster Testing
1. Use keyboard shortcut to focus barcode input: Just start typing
2. Add multiple items quickly: Scan, scan, scan, then pay
3. Test offline: Open DevTools > Network > set to Offline
4. Test voice: Click 🎤, speak "pay" or "checkout"

### For Product Testing
1. Create products with barcodes in Admin dashboard
2. Barcode format: Any text string (e.g., "PROD-001")
3. Leave blank barcode if testing via product click

### Example Test Flow
1. Login as customer
2. See recommendations (or add them via admin first)
3. Click a product to add to cart
4. Add 2-3 more items
5. Click "Checkout"
6. Receive notification
7. View receipt

---

## 📝 Notes

- All timestamps use Laravel's `now()` (UTC)
- Prices are stored as decimal fields (2 decimal places)
- Store coordinates are for map integration (future)
- Recommendation scores increase with each purchase
- Tax rate is hardcoded to 5% (can be changed in controller)
- Voice recognition requires user permission

---

## 🎓 Learning Resources

**Files to Study:**
- `BillingController.php` - Main business logic
- `customer/dashboard.blade.php` - Frontend implementation
- `migrations/` - Database schema
- `seeders/` - Sample data generation

**Key JavaScript Functions:**
- `checkout()` - Process purchase
- `updateCart()` - Update cart display
- `toggleVoice()` - Voice recognition
- `fetchNotifications()` - Get notifications
- `toggleNotifications()` - Show notification panel

---

## 🎉 You're All Set!

The system is ready to use. Start by:
1. ✅ Running migrations
2. ✅ Running seeders
3. ✅ Logging in as customer
4. ✅ Testing the features!

For any issues, check the Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

---

**Version**: 2.0 - Smart Billing Pro
**Last Updated**: April 2, 2026
**Status**: Production Ready ✅
