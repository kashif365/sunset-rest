<?php

namespace Tests\Unit;

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixed_discount_is_capped_at_subtotal(): void
    {
        $coupon = Coupon::factory()->create(['type' => 'fixed', 'value' => 50]);

        $this->assertSame(10.0, $coupon->discountFor(10));
    }

    public function test_percent_discount_calculates_correctly(): void
    {
        $coupon = Coupon::factory()->percent(20)->create();

        $this->assertSame(4.0, $coupon->discountFor(20));
    }

    public function test_percent_discount_respects_max_discount_cap(): void
    {
        $coupon = Coupon::factory()->percent(50)->create(['max_discount' => 5]);

        $this->assertSame(5.0, $coupon->discountFor(100));
    }

    public function test_coupon_below_minimum_order_is_not_redeemable(): void
    {
        $coupon = Coupon::factory()->create(['min_order' => 25]);

        $this->assertFalse($coupon->isRedeemable(10));
        $this->assertTrue($coupon->isRedeemable(30));
    }

    public function test_inactive_coupon_is_not_redeemable(): void
    {
        $coupon = Coupon::factory()->create(['is_active' => false]);

        $this->assertFalse($coupon->isRedeemable(100));
    }

    public function test_coupon_past_usage_limit_is_not_redeemable(): void
    {
        $coupon = Coupon::factory()->create(['usage_limit' => 1, 'used_count' => 1]);

        $this->assertFalse($coupon->isRedeemable(100));
    }
}
