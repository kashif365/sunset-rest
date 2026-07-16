<?php

namespace Tests\Feature;

use App\Models\BusinessHour;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\MenuItem;
use App\Models\Order;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingsSeeder::class);

        // Open every day 00:00–23:59 so pickup slots are always available in tests.
        for ($day = 0; $day <= 6; $day++) {
            BusinessHour::create([
                'day_of_week' => $day, 'open_time' => '00:00', 'close_time' => '23:59', 'is_closed' => false,
            ]);
        }
    }

    private function addItemToCart(float $price = 10.00, int $qty = 1): MenuItem
    {
        $item = MenuItem::factory()->for(Category::factory())->create(['price' => $price]);
        $this->post(route('cart.add', $item), ['quantity' => $qty]);

        return $item;
    }

    public function test_checkout_page_redirects_when_cart_is_empty(): void
    {
        $this->get(route('checkout.show'))->assertRedirect(route('menu.index'));
    }

    public function test_placing_a_valid_order_creates_it_and_sends_emails(): void
    {
        Mail::fake();
        $this->addItemToCart(10.00, 2);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '732-555-1234',
            'pickup_date' => today()->addDay()->toDateString(),
            'pickup_time' => '12:00',
            'payment_method' => 'pay_at_pickup',
            'tip' => 2,
            'notes' => 'No onions please',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.confirmation', $order->order_number));

        $this->assertSame('Jane Doe', $order->customer_name);
        $this->assertSame(20.00, (float) $order->subtotal);
        $this->assertSame(2.00, (float) $order->tip);
        $this->assertEmpty(session('sbe_cart', []));

        Mail::assertSent(\App\Mail\OrderConfirmationMail::class);
        Mail::assertSent(\App\Mail\OrderNotificationMail::class);
    }

    public function test_checkout_rejects_invalid_data(): void
    {
        $this->addItemToCart();

        $response = $this->post(route('checkout.store'), [
            'customer_name' => '',
            'customer_email' => 'not-an-email',
            'customer_phone' => '',
            'pickup_date' => 'not-a-date',
            'pickup_time' => 'not-a-time',
            'payment_method' => 'bitcoin',
        ]);

        $response->assertSessionHasErrors([
            'customer_name', 'customer_email', 'customer_phone', 'pickup_date', 'pickup_time', 'payment_method',
        ]);
        $this->assertSame(0, Order::count());
    }

    public function test_checkout_rejects_a_pickup_date_outside_the_advance_order_window(): void
    {
        $this->addItemToCart();

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '732-555-1234',
            'pickup_date' => today()->addYears(2)->toDateString(),
            'pickup_time' => '12:00',
            'payment_method' => 'pay_at_pickup',
        ]);

        $response->assertSessionHasErrors('pickup_date');
        $this->assertSame(0, Order::count());
    }

    public function test_ordering_is_blocked_when_disabled_in_settings(): void
    {
        app(\App\Services\SettingsService::class)->set('ordering_enabled', '0', 'ordering');
        $this->addItemToCart();

        $response = $this->get(route('checkout.show'));

        $response->assertOk();
        $response->assertViewIs('checkout.closed');
    }

    public function test_coupon_discount_is_applied_to_order_total(): void
    {
        Mail::fake();
        Coupon::factory()->create([
            'code' => 'SAVE5', 'type' => 'fixed', 'value' => 5, 'min_order' => 5,
        ]);
        $this->addItemToCart(20.00, 1);

        $this->post(route('cart.coupon.apply'), ['code' => 'SAVE5'])->assertSessionHas('success');

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '732-555-1234',
            'pickup_date' => today()->addDay()->toDateString(),
            'pickup_time' => '12:00',
            'payment_method' => 'pay_at_pickup',
        ]);
        $response->assertRedirect();

        $order = Order::first();
        $this->assertSame(20.00, (float) $order->subtotal);
        $this->assertSame(5.00, (float) $order->discount);
        $this->assertSame('SAVE5', $order->coupon_code);

        $coupon = Coupon::where('code', 'SAVE5')->first();
        $this->assertSame(1, $coupon->used_count);
    }

    public function test_invalid_coupon_code_is_rejected(): void
    {
        $this->addItemToCart(20.00, 1);

        $response = $this->post(route('cart.coupon.apply'), ['code' => 'DOESNOTEXIST']);

        $response->assertSessionHas('error');
    }

    public function test_expired_coupon_cannot_be_applied(): void
    {
        Coupon::factory()->expired()->create(['code' => 'OLDCODE']);
        $this->addItemToCart(20.00, 1);

        $this->post(route('cart.coupon.apply'), ['code' => 'OLDCODE'])->assertSessionHas('error');
    }

    public function test_order_creation_decrements_tracked_stock(): void
    {
        Mail::fake();
        $item = MenuItem::factory()->for(Category::factory())->create(['price' => 10, 'stock_quantity' => 5]);
        $this->post(route('cart.add', $item), ['quantity' => 3]);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '732-555-1234',
            'pickup_date' => today()->addDay()->toDateString(),
            'pickup_time' => '12:00',
            'payment_method' => 'pay_at_pickup',
        ]);

        $this->assertSame(2, $item->fresh()->stock_quantity);
    }
}
