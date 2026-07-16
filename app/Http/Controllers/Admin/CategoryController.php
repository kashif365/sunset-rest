<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\ImageService;

class CategoryController extends Controller
{
    public function __construct(private readonly ImageService $images) {}

    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('menuItems')->ordered()->paginate(30),
        ]);
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function store(CategoryRequest $request)
    {
        $data = $this->payload($request);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($this->payload($request, $category));

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        abort_unless(auth()->user()->can('manage-content'), 403);

        $this->images->delete($category->image);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }

    private function payload(CategoryRequest $request, ?Category $existing = null): array
    {
        $data = $request->safe()->except('image');
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'categories', $existing?->image);
        }

        return $data;
    }
}
