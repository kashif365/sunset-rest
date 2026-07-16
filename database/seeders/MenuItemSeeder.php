<?php

namespace Database\Seeders;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\DietaryLabel;
use App\Models\MenuItem;
use App\Models\ModifierGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed data transcribed from the two printed Sunset Bagel Exchange menu
 * photos supplied by the client. Every price/name below was read directly
 * off the printed menu. A handful of entries were hard to read clearly
 * (faded print, ambiguous wording, or overlapping columns) — those are
 * marked needs_verification => true and MUST be confirmed with the client
 * before going live. Nothing here is invented; ambiguous items keep the
 * closest legible reading and are flagged rather than guessed away.
 */
class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');
        $groups = ModifierGroup::pluck('id', 'name');
        $dietary = DietaryLabel::pluck('id', 'slug');
        $allergens = Allergen::pluck('id', 'slug');

        $sort = [];
        $nextSort = fn (string $cat) => $sort[$cat] = ($sort[$cat] ?? -1) + 1;

        $make = function (
            string $categorySlug,
            string $name,
            ?string $shortDescription,
            float $price,
            array $opts = []
        ) use ($categories, $groups, $dietary, $allergens, $nextSort) {
            $slug = Str::slug($name);
            $suffix = 1;
            $baseSlug = $slug;
            while (MenuItem::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.(++$suffix);
            }

            $item = MenuItem::create([
                'category_id' => $categories[$categorySlug],
                'name' => $name,
                'slug' => $slug,
                'short_description' => $shortDescription,
                'description' => $opts['description'] ?? $shortDescription,
                'price' => $price,
                'discounted_price' => $opts['discounted_price'] ?? null,
                'image' => null,
                'image_alt' => $name.' — Sunset Bagel Exchange',
                'prep_time_minutes' => $opts['prep'] ?? 10,
                'stock_quantity' => null,
                'low_stock_threshold' => 5,
                'is_available' => true,
                'is_sold_out' => false,
                'is_featured' => $opts['featured'] ?? false,
                'is_bestseller' => $opts['bestseller'] ?? false,
                'needs_verification' => $opts['needs_verification'] ?? false,
                'available_from' => $opts['from'] ?? null,
                'available_until' => $opts['until'] ?? null,
                'available_days' => null,
                'sort_order' => $nextSort($categorySlug),
                'seo_title' => $name.' — Sunset Bagel Exchange',
                'meta_description' => $shortDescription,
            ]);

            foreach ($opts['modifiers'] ?? [] as $groupName) {
                if (isset($groups[$groupName])) {
                    $item->modifierGroups()->attach($groups[$groupName], ['sort_order' => 0]);
                }
            }

            foreach ($opts['dietary'] ?? [] as $slugKey) {
                if (isset($dietary[$slugKey])) {
                    $item->dietaryLabels()->attach($dietary[$slugKey]);
                }
            }

            foreach ($opts['allergens'] ?? [] as $slugKey) {
                if (isset($allergens[$slugKey])) {
                    $item->allergens()->attach($allergens[$slugKey]);
                }
            }

            foreach ($opts['variations'] ?? [] as $index => [$vName, $vPrice]) {
                $item->variations()->create([
                    'name' => $vName,
                    'price' => $vPrice,
                    'is_default' => $index === 0,
                    'sort_order' => $index,
                ]);
            }

            return $item;
        };

        // ---------------------------------------------------------
        // Hand Rolled Bagels
        // ---------------------------------------------------------
        $make('hand-rolled-bagels', 'Hand Rolled Bagel', 'Old fashioned, kettle boiled. Choose your flavor.',
            1.50, [
                'description' => 'Plain, Everything, Poppy, Sesame, Whole Wheat, Multigrain, Cinnamon Raisin, French Toast, Onion, Salt or Garlic — hand rolled and kettle boiled fresh every morning.',
                'modifiers' => ['Bagel Flavor', 'Cream Cheese / Spread'],
                'allergens' => ['gluten'],
                'featured' => true, 'bestseller' => true,
            ]);
        $make('hand-rolled-bagels', 'Half Dozen Bagels', 'Six hand rolled bagels, any flavors.', 8.50, [
            'modifiers' => ['Bagel Flavor'],
            'allergens' => ['gluten'],
        ]);
        $make('hand-rolled-bagels', "Baker's Dozen Bagels", 'Thirteen hand rolled bagels, any flavors.', 16.00, [
            'modifiers' => ['Bagel Flavor'],
            'allergens' => ['gluten'],
            'featured' => true,
        ]);

        // ---------------------------------------------------------
        // Breakfast Sandwiches — served with 2 eggs on a bagel or roll
        // ---------------------------------------------------------
        $make('breakfast-sandwiches', 'Egg Sandwich', 'Two eggs on a bagel or roll.', 4.40, [
            'modifiers' => ['Bread / Roll Choice', 'Extras / Add-ons'],
            'allergens' => ['egg', 'gluten'],
        ]);
        $make('breakfast-sandwiches', 'Meat & Egg', 'Two eggs with your choice of meat.', 6.40, [
            'modifiers' => ['Bread / Roll Choice', 'Meat Choice', 'Extras / Add-ons'],
            'allergens' => ['egg', 'gluten'],
            'bestseller' => true,
        ]);
        $make('breakfast-sandwiches', 'Egg & Cheese', 'Two eggs with your choice of cheese.', 6.40, [
            'modifiers' => ['Bread / Roll Choice', 'Cheese Choice', 'Extras / Add-ons'],
            'allergens' => ['egg', 'gluten', 'dairy'],
        ]);
        $make('breakfast-sandwiches', 'Meat, Egg & Cheese', 'Two eggs, your choice of meat and cheese.', 7.45, [
            'modifiers' => ['Bread / Roll Choice', 'Meat Choice', 'Cheese Choice', 'Extras / Add-ons'],
            'allergens' => ['egg', 'gluten', 'dairy'],
            'featured' => true, 'bestseller' => true,
        ]);
        $make('breakfast-sandwiches', 'Potatoes & Egg', 'Two eggs with home fries.', 5.40, [
            'modifiers' => ['Bread / Roll Choice', 'Extras / Add-ons'],
            'allergens' => ['egg', 'gluten'],
        ]);

        // ---------------------------------------------------------
        // Egg Omelettes — 3 eggs, served with home fries and toast
        // ---------------------------------------------------------
        $make('egg-omelettes', 'Cheese Omelette', 'Your choice of cheese: Swiss, White American, Cheddar or Provolone.', 10.35, [
            'modifiers' => ['Cheese Choice'], 'allergens' => ['egg', 'dairy', 'gluten'],
        ]);
        $make('egg-omelettes', 'Vegetable Omelette', 'With spinach, bell peppers, mushrooms and onions.', 12.20, [
            'allergens' => ['egg', 'gluten'], 'dietary' => ['vegetarian'],
        ]);
        $make('egg-omelettes', 'Ham and Cheese Omelette', 'Classic ham and cheese.', 12.20, [
            'allergens' => ['egg', 'dairy', 'gluten'],
        ]);
        $make('egg-omelettes', 'Western Omelette', 'With ham, peppers and onions.', 12.20, [
            'allergens' => ['egg', 'gluten'],
        ]);
        $make('egg-omelettes', 'Greek Omelette', 'With spinach, feta cheese, onions and tomatoes.', 12.20, [
            'allergens' => ['egg', 'dairy', 'gluten'], 'dietary' => ['vegetarian'],
        ]);
        $make('egg-omelettes', 'Lorraine Omelette', 'With bacon, onions and Swiss cheese.', 12.20, [
            'allergens' => ['egg', 'dairy', 'gluten'],
        ]);
        $make('egg-omelettes', 'Belly Buster Omelette', 'With bacon, sausage, ham and cheese.', 14.05, [
            'allergens' => ['egg', 'dairy', 'gluten'], 'bestseller' => true,
        ]);
        $make('egg-omelettes', 'Chorizo Omelette', 'Chorizo, jalapeño, onions and pepper jack cheese.', 12.20, [
            'allergens' => ['egg', 'dairy', 'gluten'],
        ]);
        $make('egg-omelettes', 'Your Way Egg Platter', 'Build your own — egg whites available for +$1.00.', 11.40, [
            'needs_verification' => true,
            'description' => 'Build your own egg platter. Printed menu notes egg whites are available as a +$1.00 upgrade — exact build-your-own options to be confirmed with client.',
            'allergens' => ['egg'],
        ]);

        // ---------------------------------------------------------
        // French Toast
        // ---------------------------------------------------------
        $make('french-toast', 'French Toast', 'Griddled fresh.', 6.50, ['allergens' => ['egg', 'gluten', 'dairy']]);
        $make('french-toast', 'French Toast With Meat', 'Served with 3 slices of your choice of meat.', 8.50, [
            'modifiers' => ['Meat Choice'], 'allergens' => ['egg', 'gluten', 'dairy'],
        ]);
        $make('french-toast', 'French Toast Platter', '2 bacon, 2 pork roll, 2 sausage.', 8.50, [
            'allergens' => ['egg', 'gluten', 'dairy'], 'featured' => true,
        ]);
        $make('french-toast', 'Sweet & Salty', 'French toast with 1 bacon or pork roll and 1 egg.', 8.50, [
            'needs_verification' => true,
            'description' => 'French toast, 1 bacon or pork roll, and 1 egg. Confirm whether sausage is also offered here.',
            'allergens' => ['egg', 'gluten', 'dairy'],
        ]);

        // ---------------------------------------------------------
        // Sides & More (per order)
        // ---------------------------------------------------------
        $make('sides-more', 'Hash Browns', null, 3.50);
        $make('sides-more', 'Home Fries', null, 3.50);
        $make('sides-more', 'Two Eggs', null, 3.50, ['allergens' => ['egg']]);
        $make('sides-more', 'Bacon, Pork Roll or Sausage', null, 3.50, ['modifiers' => ['Meat Choice']]);
        $make('sides-more', 'French Fries', null, 3.50);
        $make('sides-more', 'Cheese Fries', null, 4.50, ['allergens' => ['dairy']]);
        $make('sides-more', 'Bacon Cheese Fries', null, 6.00, ['allergens' => ['dairy']]);

        // ---------------------------------------------------------
        // Cold Lunch Classics — Boar's Head
        // ---------------------------------------------------------
        $make('cold-lunch-classics', 'Our Famous BLT', "Fresh slices of Boar's Head bacon, shredded lettuce and fresh tomatoes.", 8.75, [
            'modifiers' => ['Sub Roll Upgrade'], 'allergens' => ['gluten'], 'bestseller' => true,
        ]);
        $make('cold-lunch-classics', 'Turkey BLT', 'Boar\'s Head turkey sliced thin and stacked high, topped with bacon, lettuce and tomatoes.', 9.25, [
            'modifiers' => ['Sub Roll Upgrade'], 'allergens' => ['gluten'],
        ]);
        $make('cold-lunch-classics', 'Ham, Salami & Provolone', 'The Italian cold cut classic that hits the spot.', 9.75, [
            'modifiers' => ['Sub Roll Upgrade'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('cold-lunch-classics', 'American', 'Triple threat of roast beef, turkey and ham with your choice of cheese, lettuce and tomatoes.', 10.25, [
            'modifiers' => ['Sub Roll Upgrade', 'Cheese Choice'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('cold-lunch-classics', 'Turkey Deluxe', "Boar's Head turkey, provolone, sun-dried tomatoes and fresh basil.", 10.25, [
            'needs_verification' => true,
            'description' => 'Name partially illegible on the printed menu (read as "Turkey Eagel") — likely "Turkey Deluxe". Please confirm exact name.',
            'modifiers' => ['Sub Roll Upgrade'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('cold-lunch-classics', 'Turkey & Swiss', null, 9.25, [
            'modifiers' => ['Sub Roll Upgrade'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('cold-lunch-classics', 'Roast Beef & Cheddar', null, 10.25, [
            'modifiers' => ['Sub Roll Upgrade'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('cold-lunch-classics', 'Roast Beef, Mozzarella & Roasted Peppers', null, 10.25, [
            'modifiers' => ['Sub Roll Upgrade'], 'allergens' => ['gluten', 'dairy'],
        ]);

        // ---------------------------------------------------------
        // Hot Lunch Classics — add french fries for $2.00
        // ---------------------------------------------------------
        $make('hot-lunch-classics', 'Original 1/2 Pound Cheeseburger', null, 11.50, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'], 'bestseller' => true,
        ]);
        $make('hot-lunch-classics', 'Bacon Cheeseburger', null, 11.25, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('hot-lunch-classics', 'Smothered Burger', 'With sautéed mushrooms, onions and melted Swiss cheese.', 11.25, [
            'needs_verification' => true,
            'description' => 'Name partially illegible on the printed menu (read as "Southered Burger") — likely "Smothered Burger". Please confirm.',
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('hot-lunch-classics', 'Philly Cheesesteak', 'Topped with American cheese, sautéed peppers and onions.', 11.50, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'], 'bestseller' => true,
        ]);
        $make('hot-lunch-classics', 'Tuna Melt', 'Homemade tuna, tomato and melted American cheese.', 9.50, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('hot-lunch-classics', 'Grilled Cheese', null, 6.00, ['allergens' => ['gluten', 'dairy'], 'dietary' => ['vegetarian']]);
        $make('hot-lunch-classics', 'Grilled Cheese With Ham or Bacon', null, 7.00, ['allergens' => ['gluten', 'dairy']]);
        $make('hot-lunch-classics', 'Peter Luger', 'Grilled house beef, fresh mozzarella, onions and tomatoes, topped with Peter Luger sauce.', 11.50, [
            'needs_verification' => true,
            'description' => 'Description transcribed from a partially worn section of the printed menu — please confirm ingredients and sauce name.',
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('hot-lunch-classics', 'BBQ Roast Beef', 'Grilled roast beef, melted provolone, grilled onions and BBQ sauce.', 11.50, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('hot-lunch-classics', 'Pastrami', 'Hot pastrami, Swiss cheese, mustard and two fried eggs.', 11.25, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy', 'egg'],
        ]);
        $make('hot-lunch-classics', 'Reuben', 'Hot pastrami, sauerkraut, Russian dressing on rye bread.', 11.25, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);

        // ---------------------------------------------------------
        // Chicken Sandwiches — foot-long, add fries for $2.00
        // ---------------------------------------------------------
        $make('chicken-sandwiches', 'Original Chicken Wrap', 'Crispy bacon, lettuce, tomatoes, mayo.', 10.75, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten'],
        ]);
        $make('chicken-sandwiches', 'Chicken Teriyaki', 'With mushrooms and teriyaki sauce.', 12.75, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten'],
        ]);
        $make('chicken-sandwiches', 'Balsamic Chicken', 'Fresh mozzarella, roasted peppers, spinach and balsamic dressing.', 12.75, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('chicken-sandwiches', 'Buffalo Chicken', 'With hot sauce, blue cheese, lettuce and tomatoes.', 12.75, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'], 'bestseller' => true,
        ]);
        $make('chicken-sandwiches', 'Chicken Caesar', 'With romaine lettuce, grated Parmesan cheese and Caesar sauce.', 12.75, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy', 'egg'],
        ]);
        $make('chicken-sandwiches', 'BBQ Chicken', 'With crispy bacon, cheddar cheese and BBQ sauce.', 12.75, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('chicken-sandwiches', 'Crispy Chicken Sandwich', 'Breaded chicken, pepper jack cheese, lettuce, tomatoes, chipotle mayo on a roll.', 10.65, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);
        $make('chicken-sandwiches', 'BBQ Crispy Chicken Sandwich', 'With crispy bacon, cheddar cheese, on your favorite wrap.', 11.70, [
            'modifiers' => ['Add French Fries'], 'allergens' => ['gluten', 'dairy'],
        ]);

        // ---------------------------------------------------------
        // A Little Healthier
        // ---------------------------------------------------------
        $make('a-little-healthier', 'Turkey, Egg Whites & Spinach Wrap', 'Turkey with low sodium Swiss and egg whites on a scooped out whole wheat bagel.', 9.05, [
            'allergens' => ['gluten', 'dairy', 'egg'], 'dietary' => ['low-sodium', 'high-protein'],
        ]);
        $make('a-little-healthier', 'Vegetable Egg White Omelette', 'With spinach, mushrooms, peppers and onions.', 12.80, [
            'allergens' => ['egg'], 'dietary' => ['vegetarian', 'high-protein'],
        ]);
        $make('a-little-healthier', 'Muscle Builder Wrap', 'With grilled chicken, egg whites and a touch of feta cheese.', 10.65, [
            'allergens' => ['gluten', 'dairy', 'egg'], 'dietary' => ['high-protein'],
        ]);
        $make('a-little-healthier', 'Papaya Wrap', 'Turkey, spinach, egg whites and a touch of feta cheese.', 9.60, [
            'needs_verification' => true,
            'description' => 'Printed name ("Papaya Wrap") does not match the listed ingredients (turkey, spinach, egg whites, feta) — please confirm correct name.',
            'allergens' => ['gluten', 'dairy', 'egg'],
        ]);
        $make('a-little-healthier', 'Fit Wrap', 'Turkey bacon, egg whites and roasted peppers.', 9.60, [
            'allergens' => ['gluten', 'egg'], 'dietary' => ['high-protein'],
        ]);
        $make('a-little-healthier', 'Veggie Wrap', 'Sautéed onions and peppers over spinach and fresh mozzarella with a balsamic drizzle.', 9.60, [
            'allergens' => ['gluten', 'dairy'], 'dietary' => ['vegetarian'],
        ]);
        $make('a-little-healthier', 'Veggie Omelette', 'With lettuce, tomatoes and onions.', 9.60, [
            'allergens' => ['egg'], 'dietary' => ['vegetarian'],
        ]);
        $make('a-little-healthier', 'Egg Avocado Toast', 'Fresh baked bagel topped with avocado and two fried eggs.', 9.60, [
            'allergens' => ['gluten', 'egg'], 'dietary' => ['vegetarian'], 'featured' => true,
        ]);

        // ---------------------------------------------------------
        // Fresh Garden Salads
        // ---------------------------------------------------------
        $make('fresh-garden-salads', 'House Tossed Salad', 'Bed of mixed greens, tomatoes, cucumber, onions.', 9.05, [
            'modifiers' => ['Dressing Choice', 'Salad Add-in'], 'dietary' => ['vegetarian'],
        ]);
        $make('fresh-garden-salads', 'Greek Salad', 'Mixed greens, feta cheese, Kalamata olives. Add tuna, chicken or egg salad for +$3.50.', 9.60, [
            'modifiers' => ['Dressing Choice', 'Salad Add-in'], 'allergens' => ['dairy'], 'dietary' => ['vegetarian'],
        ]);
        $make('fresh-garden-salads', 'Grilled Chicken Salad', 'Grilled chicken strips, chopped romaine lettuce.', 13.30, [
            'modifiers' => ['Dressing Choice'], 'dietary' => ['high-protein'],
        ]);
        $make('fresh-garden-salads', 'Chicken Caesar Salad', 'Seasoned chicken strips, grated Parmesan cheese, croutons.', 13.30, [
            'modifiers' => ['Dressing Choice'], 'allergens' => ['gluten', 'dairy'],
        ]);

        // ---------------------------------------------------------
        // Homemade Salads — sandwich or by the pound
        // ---------------------------------------------------------
        $make('homemade-salads', 'Tuna Salad', 'Served on a bagel or by the pound.', 8.50, [
            'variations' => [['Sandwich', 8.50], ['Per Lb', 12.00]],
        ]);
        $make('homemade-salads', 'Chicken Salad', 'Served on a bagel or by the pound.', 8.50, [
            'variations' => [['Sandwich', 8.50], ['Per Lb', 12.00]],
        ]);
        $make('homemade-salads', 'Egg Salad', 'Served on a bagel or by the pound.', 8.00, [
            'variations' => [['Sandwich', 8.00], ['Per Lb', 10.00]], 'allergens' => ['egg'],
        ]);
        $make('homemade-salads', 'Macaroni Salad', 'Sold by the pound.', 6.00, [
            'variations' => [['Per Lb', 6.00]], 'allergens' => ['gluten', 'egg'],
        ]);

        // ---------------------------------------------------------
        // Little Taste of Latin Culture
        // ---------------------------------------------------------
        $make('little-taste-of-latin-culture', 'California Burrito', 'Grilled chicken or steak, lettuce, tomatoes, avocado, chipotle mayo and pepper jack cheese.', 10.65, [
            'allergens' => ['gluten', 'dairy'], 'bestseller' => true,
        ]);
        $make('little-taste-of-latin-culture', 'Sunset Burrito', 'Grilled steak or chicken, beans, Spanish style rice, avocado, jalapeño, on your roll.', 10.65, [
            'allergens' => ['gluten'], 'featured' => true,
        ]);
        $make('little-taste-of-latin-culture', 'Chorizo Breakfast Burrito', 'With potatoes, onions, two scrambled eggs and mixed cheese.', 10.65, [
            'allergens' => ['gluten', 'egg', 'dairy'],
        ]);
        $make('little-taste-of-latin-culture', 'Steak Breakfast Burrito', 'With potatoes, onions, two scrambled eggs and mixed cheese, side of sour cream.', 10.65, [
            'allergens' => ['gluten', 'egg', 'dairy'],
        ]);
        $make('little-taste-of-latin-culture', 'Huevos Rancheros Burrito', 'Sausage, scrambled eggs, Spanish style rice, beans, salsa and mixed cheese.', 10.65, [
            'allergens' => ['gluten', 'egg', 'dairy'],
        ]);
        $make('little-taste-of-latin-culture', 'Chicken Quesadilla', 'Seasoned chicken or steak, mixed cheese, lettuce, tomatoes, side of salsa and sour cream.', 12.65, [
            'allergens' => ['gluten', 'dairy'],
        ]);
        $make('little-taste-of-latin-culture', 'Steak Quesadilla', 'Seasoned steak, mixed cheese, lettuce, tomatoes, side of sour cream.', 12.65, [
            'allergens' => ['gluten', 'dairy'],
        ]);
        $make('little-taste-of-latin-culture', 'Chorizo With Eggs Torta', 'Served with lettuce, tomato, mayo, guacamole and side of fries.', 13.30, [
            'needs_verification' => true,
            'description' => 'Price and exact section placement uncertain — this item appeared near the Fresh Garden Salads section on the printed menu but is not a salad. Please confirm price.',
            'allergens' => ['gluten', 'egg'],
        ]);
        $make('little-taste-of-latin-culture', 'Chorizo Breakfast Tacos', 'With 3 flour tortillas, eggs, mixed cheese, chipotle mayo and side of salsa.', 13.30, [
            'needs_verification' => true,
            'description' => 'Price uncertain — printed in the same ambiguous section as the Chorizo Torta. Please confirm price.',
            'allergens' => ['gluten', 'egg', 'dairy'],
        ]);
        $make('little-taste-of-latin-culture', 'Chicken Bacon Salad', 'With candied bacon, cheddar cheese, avocado, guacamole and side of fries.', 13.30, [
            'needs_verification' => true,
            'description' => 'Printed name was hard to read (looked like "Chicken Banana Salad") which does not match the listed ingredients — likely "Chicken Bacon Salad". Please confirm name and price.',
            'allergens' => ['dairy'],
        ]);

        // ---------------------------------------------------------
        // Fresh Bakery
        // ---------------------------------------------------------
        $make('fresh-bakery', 'Muffins', 'Corn, blueberry, chocolate chip or banana nut.', 3.75, ['allergens' => ['gluten', 'egg', 'dairy', 'tree-nuts']]);
        $make('fresh-bakery', 'Crumb Cake', null, 3.75, ['allergens' => ['gluten', 'egg', 'dairy']]);
        $make('fresh-bakery', 'Black and White Cookies', null, 3.75, ['allergens' => ['gluten', 'egg', 'dairy']]);
        $make('fresh-bakery', 'Croissants', null, 3.75, ['allergens' => ['gluten', 'dairy']]);
        $make('fresh-bakery', 'Cheese Danish', null, 3.75, ['allergens' => ['gluten', 'egg', 'dairy']]);

        // ---------------------------------------------------------
        // For the Kids
        // ---------------------------------------------------------
        $make('for-the-kids', 'Sunshine Plate', null, 5.50, [
            'needs_verification' => true,
            'description' => 'Contents not specified on the printed menu beyond the name — please confirm what is included.',
        ]);
        $make('for-the-kids', 'Chicken Nuggets & Fries', null, 6.50, ['allergens' => ['gluten']]);
        $make('for-the-kids', 'Sunrise Plate', null, 4.50, [
            'needs_verification' => true,
            'description' => 'Contents not specified on the printed menu beyond the name — please confirm what is included.',
        ]);
        $make('for-the-kids', 'Kids Grilled Cheese', null, 5.30, ['allergens' => ['gluten', 'dairy']]);
        $make('for-the-kids', 'Kids Sweet & Salty', 'Kid-sized French toast with a side of meat and egg.', 5.30, [
            'needs_verification' => true,
            'description' => 'A "Sweet & Salty" also appears in French Toast at $8.50 — please confirm this kids-menu price/portion is correct and intentionally different.',
            'allergens' => ['gluten', 'egg', 'dairy'],
        ]);

        // ---------------------------------------------------------
        // Burgers
        // ---------------------------------------------------------
        $make('burgers', 'Big Boy Burger', '8 oz burger, bacon, pork roll, fried egg and melted white American.', 14.50, [
            'allergens' => ['gluten', 'egg', 'dairy'], 'featured' => true, 'bestseller' => true,
        ]);

        // ---------------------------------------------------------
        // Family Specials
        // ---------------------------------------------------------
        $make('family-specials', 'The Family Special', "1/2 lb plain cream cheese + 1/2 lb any flavored cream cheese with a baker's dozen of bagels — you save about $4.", 24.00, [
            'allergens' => ['gluten', 'dairy'], 'featured' => true,
        ]);

        // Catering: no items/prices were printed on the menu for this
        // category — it is intentionally left empty. Client-supplied
        // catering packages are seeded separately as CateringPackage
        // records (see CateringPackageSeeder), not as menu items.
    }
}
