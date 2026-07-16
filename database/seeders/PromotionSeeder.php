<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        Promotion::updateOrCreate(
            ['title' => "Tacos Menu — Afternoons Only"],
            [
                'type' => 'banner',
                'description' => 'Ask about our Tacos Menu, available after 12:00 PM, Monday through Saturday.',
                'badge_text' => 'AFTERNOON SPECIAL',
                'button_text' => 'See the Menu', 'button_url' => '/menu',
                'is_active' => true, 'sort_order' => 0,
            ]
        );

        $offers = [
            [
                'title' => "The Family Special",
                'description' => "Baker's dozen of bagels with a half pound each of plain and flavored cream cheese — you save about $4.",
                'badge_text' => 'SAVE $4', 'button_text' => 'Order Now', 'button_url' => '/menu',
            ],
            [
                'title' => 'Build Your Own Breakfast Sandwich',
                'description' => 'Two eggs, your choice of meat, cheese and bread — made your way, every morning.',
                'badge_text' => 'MORNING FAVORITE', 'button_text' => 'Order Now', 'button_url' => '/menu',
            ],
        ];

        foreach ($offers as $index => $offer) {
            Promotion::updateOrCreate(
                ['title' => $offer['title']],
                $offer + ['type' => 'offer', 'is_active' => true, 'sort_order' => $index]
            );
        }
    }
}
