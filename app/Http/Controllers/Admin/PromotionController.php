<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromotionRequest;
use App\Models\Promotion;
use App\Services\ImageService;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function __construct(private readonly ImageService $images) {}

    public function index(Request $request)
    {
        return view('admin.promotions.index', [
            'promotions' => Promotion::query()
                ->when($request->query('type'), fn ($q, $t) => $q->where('type', $t))
                ->orderBy('sort_order')
                ->paginate(30)
                ->withQueryString(),
            'type' => $request->query('type'),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.promotions.form', [
            'promotion' => new Promotion(['type' => $request->query('type', 'banner')]),
        ]);
    }

    public function store(PromotionRequest $request)
    {
        Promotion::create($this->payload($request));

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion created.');
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.form', compact('promotion'));
    }

    public function update(PromotionRequest $request, Promotion $promotion)
    {
        $promotion->update($this->payload($request, $promotion));

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion updated.');
    }

    public function destroy(Promotion $promotion)
    {
        $this->images->delete($promotion->image);
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion deleted.');
    }

    private function payload(PromotionRequest $request, ?Promotion $existing = null): array
    {
        $data = $request->safe()->except('image');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'promotions', $existing?->image);
        }

        return $data;
    }
}
