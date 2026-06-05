<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Store::create([
            'store_name' => 'LUXE Main Store',
            'store_code' => 'LUXE-001',
            'location' => 'New Delhi',
            'phone' => '+91-9999-9999',
            'address' => '123 Premium Street, New Delhi',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'is_active' => true,
        ]);

        \App\Models\Store::create([
            'store_name' => 'LUXE Mumbai',
            'store_code' => 'LUXE-002',
            'location' => 'Mumbai',
            'phone' => '+91-8888-8888',
            'address' => '456 Luxury Ave, Mumbai',
            'latitude' => 19.0760,
            'longitude' => 72.8777,
            'is_active' => true,
        ]);

        \App\Models\Store::create([
            'store_name' => 'LUXE Bangalore',
            'store_code' => 'LUXE-003',
            'location' => 'Bangalore',
            'phone' => '+91-7777-7777',
            'address' => '789 Elite Plaza, Bangalore',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'is_active' => true,
        ]);
    }
}
