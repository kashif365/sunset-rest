<?php

namespace Database\Seeders;

use App\Models\Allergen;
use App\Models\DietaryLabel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Vegetarian' => 'bi-flower1', 'Low Sodium' => 'bi-heart-pulse', 'High Protein' => 'bi-lightning-charge'] as $name => $icon) {
            DietaryLabel::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'icon' => $icon]);
        }

        foreach (['Gluten' => 'bi-exclamation-triangle', 'Dairy' => 'bi-cup-straw', 'Egg' => 'bi-egg', 'Peanuts' => 'bi-exclamation-circle', 'Tree Nuts' => 'bi-tree'] as $name => $icon) {
            Allergen::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'icon' => $icon]);
        }
    }
}
