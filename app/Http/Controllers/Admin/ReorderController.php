<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Generic sort-order persistence for reorderable admin tables.
 */
class ReorderController extends Controller
{
    private const TYPES = [
        'categories' => \App\Models\Category::class,
        'menu-items' => \App\Models\MenuItem::class,
        'hero-slides' => \App\Models\HeroSlide::class,
        'promotions' => \App\Models\Promotion::class,
        'catering-packages' => \App\Models\CateringPackage::class,
        'faqs' => \App\Models\Faq::class,
        'gallery-images' => \App\Models\GalleryImage::class,
        'navigation-links' => \App\Models\NavigationLink::class,
        'modifier-groups' => \App\Models\ModifierGroup::class,
        'modifier-options' => \App\Models\ModifierOption::class,
    ];

    public function __invoke(Request $request, string $type)
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        abort_unless($request->user()->can('manage-content'), 403);

        $data = $request->validate([
            'ids' => ['required', 'array', 'max:500'],
            'ids.*' => ['integer'],
        ]);

        $model = self::TYPES[$type];

        DB::transaction(function () use ($model, $data) {
            foreach (array_values($data['ids']) as $index => $id) {
                $model::whereKey($id)->update(['sort_order' => $index]);
            }
        });

        return response()->json(['ok' => true]);
    }
}
