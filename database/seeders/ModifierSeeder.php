<?php

namespace Database\Seeders;

use App\Models\ModifierGroup;
use Illuminate\Database\Seeder;

class ModifierSeeder extends Seeder
{
    /** @var array<string, ModifierGroup> */
    public static array $groups = [];

    public function run(): void
    {
        $this->group('Bagel Flavor', 'single', true, 0, null, [
            'Plain', 'Everything', 'Poppy', 'Sesame', 'Whole Wheat', 'Multigrain',
            'Cinnamon Raisin', 'French Toast', 'Onion', 'Salt', 'Garlic',
        ]);

        $this->group('Cream Cheese / Spread', 'single', false, 0, 1, [
            ['None', 0],
            ['Butter', 2.00],
            ['Plain Cream Cheese', 3.75],
            ['Flavored Cream Cheese', 4.25],
            ['Bacon & Cream Cheese', 5.25],
            ['Fresh Kiwi & Cream Cheese', 5.25],
        ]);

        $this->group('Meat Choice', 'single', true, 1, 1, ['Bacon', 'Pork Roll', 'Sausage']);

        $this->group('Cheese Choice', 'single', false, 0, 1, [
            'American', 'Swiss', 'Cheddar', 'Provolone', 'Pepper Jack',
        ]);

        $this->group('Bread / Roll Choice', 'single', true, 1, 1, ['Bagel', 'Roll', 'Wrap']);

        $this->group('Extras / Add-ons', 'multiple', false, 0, 4, [
            ['Add Cheese', 1.00],
            ['Add Meat', 2.50],
            ['Add Hash Browns', 1.50],
            ['Add Avocado', 2.50],
        ]);

        $this->group('Add French Fries', 'single', false, 0, 1, [
            ['No Fries', 0],
            ['Add French Fries', 2.00],
        ]);

        $this->group('Dressing Choice', 'single', true, 1, 1, [
            'Balsamic', 'Russian', 'Ranch', 'Caesar', 'Italian',
        ]);

        $this->group('Sub Roll Upgrade', 'single', false, 0, 1, [
            ['Regular Bread', 0],
            ['Sub Roll (+$3.00)', 3.00],
        ]);

        $this->group('Salad Add-in', 'single', false, 0, 1, [
            ['None', 0],
            ['Add Tuna Salad', 3.50],
            ['Add Chicken Salad', 3.50],
            ['Add Egg Salad', 3.50],
        ]);
    }

    /**
     * @param  array<int, string|array{0:string,1:float}>  $options
     */
    private function group(string $name, string $type, bool $required, int $min, ?int $max, array $options): void
    {
        $group = ModifierGroup::updateOrCreate(
            ['name' => $name],
            [
                'selection_type' => $type,
                'is_required' => $required,
                'min_select' => $min,
                'max_select' => $max,
                'sort_order' => count(self::$groups),
            ]
        );

        foreach ($options as $index => $option) {
            [$optName, $price] = is_array($option) ? $option : [$option, 0];

            $group->options()->updateOrCreate(
                ['name' => $optName],
                [
                    'price_adjustment' => $price,
                    'is_default' => $index === 0 && $type === 'single',
                    'is_available' => true,
                    'sort_order' => $index,
                ]
            );
        }

        self::$groups[$name] = $group;
    }
}
