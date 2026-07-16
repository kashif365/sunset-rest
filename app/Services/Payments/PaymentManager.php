<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class PaymentManager
{
    /** @var array<string, PaymentGatewayInterface> */
    private array $gateways = [];

    public function __construct()
    {
        // Register offline gateways. When Stripe/Square credentials are
        // supplied, add e.g. $this->register(new StripeGateway(...)).
        $this->register(new PayAtPickupGateway);
        $this->register(new PhoneOrderGateway);
    }

    public function register(PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$gateway->identifier()] = $gateway;
    }

    /** @return array<string, PaymentGatewayInterface> */
    public function enabled(): array
    {
        return array_filter($this->gateways, fn ($g) => $g->isEnabled());
    }

    public function get(string $identifier): PaymentGatewayInterface
    {
        $gateway = $this->gateways[$identifier] ?? null;

        if (! $gateway || ! $gateway->isEnabled()) {
            throw new InvalidArgumentException("Payment method [{$identifier}] is not available.");
        }

        return $gateway;
    }

    public function isValid(string $identifier): bool
    {
        return isset($this->enabled()[$identifier]);
    }
}
