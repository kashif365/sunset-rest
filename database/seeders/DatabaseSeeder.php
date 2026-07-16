<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            BusinessHoursSeeder::class,
            UserSeeder::class,
            NavigationSeeder::class,
            TaxonomySeeder::class,
            ModifierSeeder::class,
            CategorySeeder::class,
            MenuItemSeeder::class,
            HeroSlideSeeder::class,
            PromotionSeeder::class,
            CateringPackageSeeder::class,
            CouponSeeder::class,
            FaqSeeder::class,
            PageSeeder::class,
            GalleryImageSeeder::class,
        ]);
    }
}
