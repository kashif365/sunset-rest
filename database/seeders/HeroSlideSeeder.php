<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'title' => 'Hand Rolled. Kettle Boiled. Old Fashioned.',
                'subtitle' => 'Fresh bagels, breakfast, lunch and coffee — made the old-school way, every single morning.',
                'button_text' => 'Order Online', 'button_url' => '/menu',
                'button2_text' => 'View Menu', 'button2_url' => '/menu',
            ],
            [
                'title' => 'Breakfast, Lunch & Coffee All Day',
                'subtitle' => 'From two-egg sandwiches to stacked Boar\'s Head classics — there\'s something for everyone.',
                'button_text' => 'See Full Menu', 'button_url' => '/menu',
                'button2_text' => 'Call Ahead', 'button2_url' => 'tel:+17323618119',
            ],
            [
                'title' => 'Catering For Your Next Gathering',
                'subtitle' => 'Family gathering or business meeting — let us help make it a success.',
                'button_text' => 'Explore Catering', 'button_url' => '/catering',
                'button2_text' => 'Contact Us', 'button2_url' => '/contact',
            ],
        ];

        foreach ($slides as $index => $slide) {
            HeroSlide::updateOrCreate(
                ['title' => $slide['title']],
                $slide + ['image' => '/images/hero-default.svg', 'image_alt' => $slide['title'], 'is_active' => true, 'sort_order' => $index]
            );
        }
    }
}
