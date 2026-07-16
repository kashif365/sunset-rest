<?php

namespace App\Services\Payments;

use App\Models\Order;

class PhoneOrderGateway implements PaymentGatewayInterface
{
    public function identifier(): string
    {
        return 'phone';
    }

    public function label(): string
    {
        return 'Order inquiry — we will call you to confirm & take payment';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function initiate(Order $order): ?string
    {
        return null;
    }
}
