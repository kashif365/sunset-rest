<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DietaryLabel;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::active()
            ->ordered()
            ->with([
                'menuItems' => fn ($q) => $q->where('is_available', true)
                    ->orderBy('sort_order')
                    ->with(['dietaryLabels', 'allergens', 'variations'])
                    ->withCount('modifierGroups'),
            ])
            ->get()
            ->filter(fn ($category) => $category->menuItems->isNotEmpty());

        return view('menu.index', [
            'categories' => $categories,
            'dietaryLabels' => DietaryLabel::orderBy('name')->get(),
            'search' => (string) $request->query('q', ''),
        ]);
    }

    public function category(Category $category)
    {
        abort_unless($category->is_active, 404);

        $category->load([
            'menuItems' => fn ($q) => $q->where('is_available', true)
                ->orderBy('sort_order')
                ->with(['dietaryLabels', 'allergens', 'variations'])
                ->withCount('modifierGroups'),
        ]);

        return view('menu.category', [
            'category' => $category,
            'categories' => Category::active()->ordered()->get(),
        ]);
    }

    public function show(MenuItem $menuItem)
    {
        abort_unless($menuItem->is_available && $menuItem->category?->is_active, 404);

        $menuItem->load([
            'category', 'variations', 'dietaryLabels', 'allergens',
            'modifierGroups.options' => fn ($q) => $q->where('is_available', true)->orderBy('sort_order'),
        ]);

        $related = MenuItem::available()
            ->with(['dietaryLabels', 'allergens', 'variations'])
            ->withCount('modifierGroups')
            ->where('category_id', $menuItem->category_id)
            ->where('id', '!=', $menuItem->id)
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        return view('menu.show', compact('menuItem', 'related'));
    }
}
