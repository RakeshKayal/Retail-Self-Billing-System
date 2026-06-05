<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'store_name',
        'store_code',
        'location',
        'phone',
        'address',
        'latitude',
        'longitude',
        'is_active',
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
