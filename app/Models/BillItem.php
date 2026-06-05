<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bill;
use App\Models\Product;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id',
        'product_id',
        'quantity',
        'price'
    ];

    // Each item belongs to a bill
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Each item belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}