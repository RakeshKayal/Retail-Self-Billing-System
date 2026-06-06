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
        // Product model uses a non-default primary key name (`product_id`),
        // so explicitly specify the foreign key and owner key to avoid
        // Eloquent inferring `product_product_id` as the foreign key.
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}