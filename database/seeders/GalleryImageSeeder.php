<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use Illuminate\Database\Seeder;

class GalleryImageSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            'Fresh Bagels', 'Breakfast Sandwich', 'Morning Coffee', 'Lunch Platter',
            'Bakery Case', 'Catering Spread', 'Storefront', 'Bagel Boil',
        ];

        foreach ($titles as $index => $title) {
            GalleryImage::updateOrCreate(['title' => $title], [
                'image' => '/images/placeholder-food.svg',
                'image_alt' => $title.' at Sunset Bagel Exchange',
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }
    }
}
