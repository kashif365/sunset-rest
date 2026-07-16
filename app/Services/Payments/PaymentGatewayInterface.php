<?php

namespace App\Services\Payments;

use App\Models\Order;

/**
 * Contract for payment providers. Add a Stripe/Square implementation
 * later and register it in PaymentManager without touching checkout.
 */
interface PaymentGatewayInterface
{
    /** Machine name, e.g. "pay_at_pickup", "stripe". */
    public function identifier(): string;

    /** Human label shown at checkout. */
    public function label(): string;

    /** Whether this gateway is currently configured/usable. */
    public function isEnabled(): bool;

    /**
     * Begin payment for an order. Offline gateways simply return null;
     * online gateways may return a redirect URL to the provider.
     */
    public function initiate(Order $order): ?string;
}
