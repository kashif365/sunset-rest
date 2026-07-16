<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;
use App\Models\ModifierOption;
use Illuminate\Support\Str;

/**
 * Session-backed shopping cart.
 *
 * Each line: id, menu_item_id, name, variation_id, variation_name,
 * unit_price, quantity, notes, modifiers[] (group/option/price).
 */
class CartService
{
    private const SESSION_KEY = 'sbe.cart';

    private const COUPON_KEY = 'sbe.cart.coupon';

    public function __construct(private readonly SettingsService $settings) {}

    /** @return array<string, array<string, mixed>> */
    public function items(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return array_sum(array_column($this->items(), 'quantity'));
    }

    public function isEmpty(): bool
    {
        return $this->items() === [];
    }

    /**
     * @param  array<int>  $modifierOptionIds
     */
    public function add(
        MenuItem $item,
        int $quantity = 1,
        ?int $variationId = null,
        array $modifierOptionIds = [],
        ?string $notes = null,
    ): array {
        $quantity = max(1, min($quantity, 25));

        $variation = null;
        if ($variationId) {
            $variation = MenuItemVariation::where('menu_item_id', $item->id)->findOrFail($variationId);
        }

        $unitPrice = $variation ? (float) $variation->price : $item->effectivePrice();

        $modifiers = [];
        if ($modifierOptionIds !== []) {
            $options = ModifierOption::with('group')
                ->whereIn('id', $modifierOptionIds)
                ->where('is_available', true)
                ->whereHas('group.menuItems', fn ($q) => $q->where('menu_items.id', $item->id))
                ->get();

            foreach ($options as $option) {
                $modifiers[] = [
                    'option_id' => $option->id,
                    'group_name' => $option->group->name,
                    'option_name' => $option->name,
                    'price_adjustment' => (float) $option->price_adjustment,
                ];
                $unitPrice += (float) $option->price_adjustment;
            }
        }

        $line = [
            'menu_item_id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'image' => $item->image,
            'variation_id' => $variation?->id,
            'variation_name' => $variation?->name,
            'unit_price' => round($unitPrice, 2),
            'quantity' => $quantity,
            'notes' => $notes !== null ? Str::limit(trim($notes), 400, '') : null,
            'modifiers' => $modifiers,
        ];

        $cart = $this->items();

        // Merge identical configurations into one line.
        $signature = md5(json_encode([
            $line['menu_item_id'], $line['variation_id'],
            array_column($modifiers, 'option_id'), $line['notes'],
        ]));

        if (isset($cart[$signature])) {
            $cart[$signature]['quantity'] = min(25, $cart[$signature]['quantity'] + $quantity);
        } else {
            $line['id'] = $signature;
            $cart[$signature] = $line;
        }

        session([self::SESSION_KEY => $cart]);

        return $cart[$signature];
    }

    public function updateQuantity(string $lineId, int $quantity): void
    {
        $cart = $this->items();
        if (! isset($cart[$lineId])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$lineId]);
        } else {
            $cart[$lineId]['quantity'] = min($quantity, 25);
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function remove(string $lineId): void
    {
        $cart = $this->items();
        unset($cart[$lineId]);
        session([self::SESSION_KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget([self::SESSION_KEY, self::COUPON_KEY]);
    }

    // ---- Coupon -------------------------------------------------

    public function applyCoupon(Coupon $coupon): void
    {
        session([self::COUPON_KEY => $coupon->code]);
    }

    public function removeCoupon(): void
    {
        session()->forget(self::COUPON_KEY);
    }

    public function coupon(): ?Coupon
    {
        $code = session(self::COUPON_KEY);
        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon || ! $coupon->isRedeemable($this->subtotal())) {
            return null;
        }

        return $coupon;
    }

    // ---- Totals -------------------------------------------------

    public function subtotal(): float
    {
        return round(array_reduce(
            $this->items(),
            fn ($carry, $line) => $carry + $line['unit_price'] * $line['quantity'],
            0.0
        ), 2);
    }

    public function discount(): float
    {
        $coupon = $this->coupon();

        return $coupon ? $coupon->discountFor($this->subtotal()) : 0.0;
    }

    public function taxableBase(): float
    {
        return max(0, $this->subtotal() - $this->discount());
    }

    public function tax(): float
    {
        $rate = $this->settings->float('tax_rate', 0.0);

        return round($this->taxableBase() * $rate / 100, 2);
    }

    public function total(float $tip = 0.0): float
    {
        return round($this->taxableBase() + $this->tax() + max(0, $tip), 2);
    }

    /** @return array<string, float> */
    public function totals(float $tip = 0.0): array
    {
        return [
            'subtotal' => $this->subtotal(),
            'discount' => $this->discount(),
            'tax' => $this->tax(),
            'tip' => round(max(0, $tip), 2),
            'total' => $this->total($tip),
        ];
    }
}
