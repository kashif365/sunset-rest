<?php

namespace Database\Seeders;

use App\Models\CateringPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * The printed menu only had a call-out box mentioning catering was
 * available ("Sales rep? Let us help make your meeting a success!")
 * with no packages, headcounts or prices listed. Per instructions we
 * never invent prices — these placeholder packages are all flagged
 * needs_verification so the client can supply real pricing and details.
 */
class CateringPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Breakfast Bagel Platter',
                'description' => 'An assortment of hand rolled bagels with plain and flavored cream cheeses. Ask us to tailor it to your headcount.',
                'serves' => '10-12 people',
            ],
            [
                'name' => 'Continental Breakfast Spread',
                'description' => 'Bagels, muffins, pastries, fresh fruit and coffee for your morning meeting.',
                'serves' => '10-15 people',
            ],
            [
                'name' => 'Cold Cut Lunch Platter',
                'description' => "Boar's Head deli platter with a selection of breads, cheeses and condiments.",
                'serves' => '10-12 people',
            ],
        ];

        foreach ($packages as $index => $package) {
            CateringPackage::updateOrCreate(
                ['slug' => Str::slug($package['name'])],
                $package + [
                    'price' => null,
                    'price_label' => null,
                    'image' => null,
                    'image_alt' => $package['name'],
                    'needs_verification' => true,
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
