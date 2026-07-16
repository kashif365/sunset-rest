<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payments\PaymentManager;
use App\Services\PickupSlotService;
use Illuminate\Support\Carbon;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PickupSlotService $slots,
        private readonly PaymentManager $payments,
    ) {}

    public function show()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('menu.index')->with('error', 'Your cart is empty — add something tasty first!');
        }

        if (! $this->slots->orderingAvailable()) {
            return view('checkout.closed');
        }

        $dates = $this->slots->orderableDates();

        return view('checkout.index', [
            'items' => $this->cart->items(),
            'totals' => $this->cart->totals(),
            'coupon' => $this->cart->coupon(),
            'dates' => $dates,
            'slotsByDate' => collect($dates)->mapWithKeys(
                fn ($d) => [$d => $this->slots->slotsFor(Carbon::parse($d))]
            ),
            'gateways' => $this->payments->enabled(),
            'minOrder' => app(\App\Services\SettingsService::class)->float('min_order_amount', 0),
        ]);
    }

    public function store(CheckoutRequest $request, OrderService $orders)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('menu.index')->with('error', 'Your cart is empty.');
        }

        if (! $this->slots->orderingAvailable()) {
            return back()->with('error', 'Online ordering is currently closed.');
        }

        $minOrder = app(\App\Services\SettingsService::class)->float('min_order_amount', 0);
        if ($this->cart->subtotal() < $minOrder) {
            return back()->with('error', sprintf('Minimum order is $%.2f.', $minOrder))->withInput();
        }

        try {
            $order = $orders->createFromCart($request->validated());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        // Online gateways may redirect to the provider here.
        $redirect = $this->payments->get($order->payment_method)->initiate($order);
        if ($redirect) {
            return redirect()->away($redirect);
        }

        return redirect()
            ->route('orders.confirmation', $order->order_number)
            ->with('success', 'Your order has been placed!');
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::with('items.modifiers')->where('order_number', $orderNumber)->firstOrFail();

        return view('checkout.confirmation', compact('order'));
    }

    public function receipt(string $orderNumber)
    {
        $order = Order::with('items.modifiers')->where('order_number', $orderNumber)->firstOrFail();

        return view('checkout.receipt', compact('order'));
    }
}
