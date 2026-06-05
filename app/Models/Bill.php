<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BillItem;

class Bill extends Model
{
    protected $fillable = ['total_amount', 'store_id', 'sync_status', 'synced_at', 'payment_method', 'razorpay_order_id', 'razorpay_payment_id', 'status', 'user_id'];

    // One bill has many items
    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}