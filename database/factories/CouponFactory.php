<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CODE###')),
            'type' => 'fixed',
            'value' => 5,
            'min_order' => null,
            'max_discount' => null,
            'starts_at' => null,
            'expires_at' => null,
            'usage_limit' => null,
            'used_count' => 0,
            'is_active' => true,
        ];
    }

    public function percent(float $value = 10): static
    {
        return $this->state(fn () => ['type' => 'percent', 'value' => $value]);
    }

    public function expired(): static
    {
        return $this->state(fn () => ['expires_at' => now()->subDay()]);
    }
}
