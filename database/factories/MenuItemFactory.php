<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuItemFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 3, 20),
            'discounted_price' => null,
            'image' => null,
            'image_alt' => null,
            'prep_time_minutes' => 10,
            'stock_quantity' => null,
            'low_stock_threshold' => 5,
            'is_available' => true,
            'is_sold_out' => false,
            'is_featured' => false,
            'is_bestseller' => false,
            'needs_verification' => false,
            'available_from' => null,
            'available_until' => null,
            'available_days' => null,
            'sort_order' => 0,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn () => ['is_available' => false]);
    }

    public function soldOut(): static
    {
        return $this->state(fn () => ['is_sold_out' => true]);
    }

    public function lowStock(): static
    {
        return $this->state(fn () => ['stock_quantity' => 2, 'low_stock_threshold' => 5]);
    }
}
