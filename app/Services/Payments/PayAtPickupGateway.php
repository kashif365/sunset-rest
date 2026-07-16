<?php

namespace App\Services\Payments;

use App\Models\Order;

class PayAtPickupGateway implements PaymentGatewayInterface
{
    public function identifier(): string
    {
        return 'pay_at_pickup';
    }

    public function label(): string
    {
        return 'Pay at pickup (cash or card in store)';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function initiate(Order $order): ?string
    {
        return null; // Nothing to do; the order is settled in store.
    }
}
