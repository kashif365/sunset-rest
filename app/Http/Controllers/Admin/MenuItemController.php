<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemRequest;
use App\Models\Allergen;
use App\Models\Category;
use App\Models\DietaryLabel;
use App\Models\MenuItem;
use App\Models\ModifierGroup;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuItemController extends Controller
{
    public function __construct(private readonly ImageService $images) {}

    public function index(Request $request)
    {
        $items = MenuItem::with('category')
            ->when($request->query('category'), fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->query('q'), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->when($request->query('flag') === 'needs_verification', fn ($q) => $q->where('needs_verification', true))
            ->when($request->query('flag') === 'sold_out', fn ($q) => $q->where('is_sold_out', true))
            ->when($request->query('flag') === 'low_stock', fn ($q) => $q->whereNotNull('stock_quantity')
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold'))
            ->orderBy('category_id')->orderBy('sort_order')
            ->paginate(30)
            ->withQueryString();

        return view('admin.menu-items.index', [
            'items' => $items,
            'categories' => Category::ordered()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.menu-items.form', [
            'item' => new MenuItem,
            'categories' => Category::ordered()->get(),
            'dietaryLabels' => DietaryLabel::orderBy('name')->get(),
            'allergens' => Allergen::orderBy('name')->get(),
            'modifierGroups' => ModifierGroup::orderBy('sort_order')->get(),
        ]);
    }

    public function store(MenuItemRequest $request)
    {
        $item = DB::transaction(function () use ($request) {
            $item = MenuItem::create($this->payload($request));
            $this->syncRelations($request, $item);

            return $item;
        });

        return redirect()->route('admin.menu-items.edit', $item)->with('success', 'Menu item created.');
    }

    public function edit(MenuItem $menuItem)
    {
        $menuItem->load(['variations', 'dietaryLabels', 'allergens', 'modifierGroups']);

        return view('admin.menu-items.form', [
            'item' => $menuItem,
            'categories' => Category::ordered()->get(),
            'dietaryLabels' => DietaryLabel::orderBy('name')->get(),
            'allergens' => Allergen::orderBy('name')->get(),
            'modifierGroups' => ModifierGroup::orderBy('sort_order')->get(),
        ]);
    }

    public function update(MenuItemRequest $request, MenuItem $menuItem)
    {
        DB::transaction(function () use ($request, $menuItem) {
            $menuItem->update($this->payload($request, $menuItem));
            $this->syncRelations($request, $menuItem);
        });

        return redirect()->route('admin.menu-items.edit', $menuItem)->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem)
    {
        abort_unless(auth()->user()->can('manage-content'), 403);

        $this->images->delete($menuItem->image);
        $menuItem->delete();

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item deleted.');
    }

    private function payload(MenuItemRequest $request, ?MenuItem $existing = null): array
    {
        $data = $request->safe()->except(['image', 'dietary_labels', 'allergens', 'modifier_groups', 'variations']);

        foreach (['is_available', 'is_sold_out', 'is_featured', 'is_bestseller', 'needs_verification'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['low_stock_threshold'] = (int) ($data['low_stock_threshold'] ?? 5);
        $data['available_days'] = $request->input('available_days') ?: null;

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'menu-items', $existing?->image);
        }

        return $data;
    }

    private function syncRelations(MenuItemRequest $request, MenuItem $item): void
    {
        $item->dietaryLabels()->sync($request->input('dietary_labels', []));
        $item->allergens()->sync($request->input('allergens', []));

        $groups = collect($request->input('modifier_groups', []))
            ->values()
            ->mapWithKeys(fn ($id, $index) => [(int) $id => ['sort_order' => $index]]);
        $item->modifierGroups()->sync($groups);

        // Inline variations editor: keep submitted rows, drop the rest.
        $submitted = collect($request->input('variations', []))
            ->filter(fn ($v) => filled($v['name'] ?? null) && ($v['price'] ?? '') !== '');

        $keepIds = [];
        foreach ($submitted->values() as $index => $row) {
            $variation = $item->variations()->updateOrCreate(
                ['id' => $row['id'] ?? null],
                ['name' => $row['name'], 'price' => $row['price'], 'sort_order' => $index]
            );
            $keepIds[] = $variation->id;
        }

        $item->variations()->whereNotIn('id', $keepIds)->delete();
    }
}
