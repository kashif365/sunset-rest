<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\Coupon;
use App\Models\MenuItem;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function index()
    {
        return view('cart.index', [
            'items' => $this->cart->items(),
            'totals' => $this->cart->totals(),
            'coupon' => $this->cart->coupon(),
        ]);
    }

    public function add(AddToCartRequest $request, MenuItem $menuItem)
    {
        if (! $menuItem->isOrderable()) {
            return back()->with('error', 'Sorry, this item is not available right now.');
        }

        $this->cart->add(
            $menuItem,
            (int) $request->validated('quantity'),
            $request->validated('variation_id') ? (int) $request->validated('variation_id') : null,
            array_map('intval', $request->validated('modifiers', []) ?? []),
            $request->validated('notes'),
        );

        return redirect()->route('cart.index')
            ->with('success', $menuItem->name.' added to your cart.');
    }

    public function update(Request $request, string $line)
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:25']]);

        $this->cart->updateQuantity($line, (int) $request->input('quantity'));

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(string $line)
    {
        $this->cart->remove($line);

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        $this->cart->clear();

        return redirect()->route('cart.index')->with('success', 'Your cart is now empty.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:40']]);

        $coupon = Coupon::where('code', strtoupper(trim($request->input('code'))))->first();

        if (! $coupon || ! $coupon->isRedeemable($this->cart->subtotal())) {
            return back()->with('error', 'That coupon code is not valid for this order.');
        }

        $this->cart->applyCoupon($coupon);

        return back()->with('success', 'Coupon applied!');
    }

    public function removeCoupon()
    {
        $this->cart->removeCoupon();

        return back()->with('success', 'Coupon removed.');
    }
}
