<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(['code' => 'WELCOME10'], [
            'type' => 'percent',
            'value' => 10,
            'min_order' => 15,
            'max_discount' => 10,
            'starts_at' => null,
            'expires_at' => null,
            'usage_limit' => null,
            'is_active' => true,
        ]);

        Coupon::updateOrCreate(['code' => 'SAVE5'], [
            'type' => 'fixed',
            'value' => 5,
            'min_order' => 25,
            'max_discount' => null,
            'starts_at' => null,
            'expires_at' => null,
            'usage_limit' => null,
            'is_active' => true,
        ]);
    }
}
