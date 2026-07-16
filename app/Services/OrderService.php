<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Mail\OrderNotificationMail;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly SettingsService $settings,
    ) {}

    /**
     * Create an order from the current cart inside a transaction.
     *
     * @param array{
     *   customer_name: string, customer_email: string, customer_phone: string,
     *   pickup_date: string, pickup_time: string, payment_method: string,
     *   tip?: float|string|null, notes?: string|null
     * } $data
     */
    public function createFromCart(array $data): Order
    {
        $tip = round((float) ($data['tip'] ?? 0), 2);
        $totals = $this->cart->totals($tip);
        $coupon = $this->cart->coupon();
        $lines = $this->cart->items();

        $order = DB::transaction(function () use ($data, $totals, $coupon, $lines, $tip) {
            $customer = Customer::firstOrCreate(
                ['email' => strtolower(trim($data['customer_email']))],
                ['name' => $data['customer_name'], 'phone' => $data['customer_phone']]
            );
            $customer->update(['name' => $data['customer_name'], 'phone' => $data['customer_phone']]);

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $customer->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => strtolower(trim($data['customer_email'])),
                'customer_phone' => $data['customer_phone'],
                'status' => 'pending',
                'order_type' => 'pickup',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'pickup_date' => $data['pickup_date'],
                'pickup_time' => $data['pickup_time'],
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'tip' => $tip,
                'discount' => $totals['discount'],
                'total' => $totals['total'],
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                // Lock the row so concurrent checkouts can't oversell stock.
                $menuItem = MenuItem::lockForUpdate()->find($line['menu_item_id']);

                if ($menuItem && $menuItem->stock_quantity !== null) {
                    if ($menuItem->stock_quantity < $line['quantity']) {
                        throw new \RuntimeException("\"{$menuItem->name}\" no longer has enough stock.");
                    }
                    $menuItem->decrement('stock_quantity', $line['quantity']);
                }

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $line['menu_item_id'],
                    'item_name' => $line['name'],
                    'variation_name' => $line['variation_name'],
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => round($line['unit_price'] * $line['quantity'], 2),
                    'notes' => $line['notes'],
                ]);

                foreach ($line['modifiers'] as $modifier) {
                    $orderItem->modifiers()->create([
                        'group_name' => $modifier['group_name'],
                        'option_name' => $modifier['option_name'],
                        'price_adjustment' => $modifier['price_adjustment'],
                    ]);
                }
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $order;
        });

        $this->cart->clear();
        $this->sendNotifications($order);

        return $order;
    }

    private function sendNotifications(Order $order): void
    {
        $order->load('items.modifiers');

        try {
            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));

            $restaurantEmail = $this->settings->get('order_notification_email')
                ?: $this->settings->get('business_email');

            if ($restaurantEmail) {
                Mail::to($restaurantEmail)->send(new OrderNotificationMail($order));
            }
        } catch (\Throwable $e) {
            // An email failure must never lose a placed order.
            Log::error('Order notification email failed: '.$e->getMessage(), ['order' => $order->order_number]);
        }
    }
}
