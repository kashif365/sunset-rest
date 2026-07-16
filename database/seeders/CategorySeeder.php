<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /** Order matches the printed menu layout; slug maps to the generated /images/categories/{slug}.svg art. */
    public static array $categories = [
        'Hand Rolled Bagels' => 'Old fashioned, kettle boiled bagels — hand rolled fresh every morning.',
        'Breakfast Sandwiches' => "Served with 2 eggs on a bagel or roll.",
        'Egg Omelettes' => 'Made with 3 eggs, served with home fries and toast.',
        'French Toast' => 'Griddled fresh, every morning.',
        'Sides & More' => 'Perfect additions to any order.',
        'Cold Lunch Classics' => "We proudly serve Boar's Head premium cold cuts.",
        'Hot Lunch Classics' => 'Burgers, melts and grilled favorites.',
        'Chicken Sandwiches' => 'Stacked foot-long sandwiches with french fries for $2.',
        'A Little Healthier' => 'Keeping Ocean Township lean and mean.',
        'Fresh Garden Salads' => 'Served with your choice of dressing.',
        'Homemade Salads' => 'Served on a bagel or by the pound.',
        'Little Taste of Latin Culture' => 'Burritos, quesadillas and tacos — a Sunset original.',
        'Fresh Bakery' => 'Muffins, cookies, croissants and more, baked fresh daily.',
        'For the Kids' => 'Little plates for little appetites.',
        'Burgers' => 'Hand-pressed and grilled to order.',
        'Family Specials' => "Big savings for the whole family.",
        'Catering' => 'Breakfast and lunch spreads for your next gathering.',
    ];

    public function run(): void
    {
        foreach (array_keys(self::$categories) as $index => $name) {
            $slug = Str::slug($name);

            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => self::$categories[$name],
                    'image' => "/images/categories/{$slug}.svg",
                    'image_alt' => $name.' at Sunset Bagel Exchange',
                    'is_active' => true,
                    'is_featured' => in_array($name, [
                        'Hand Rolled Bagels', 'Breakfast Sandwiches', 'Hot Lunch Classics',
                        'Chicken Sandwiches', 'Little Taste of Latin Culture', 'Fresh Bakery',
                    ], true),
                    'sort_order' => $index,
                    'seo_title' => $name.' — Sunset Bagel Exchange',
                    'meta_description' => self::$categories[$name],
                ]
            );
        }
    }
}
