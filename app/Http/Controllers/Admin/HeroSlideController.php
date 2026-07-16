<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeroSlideRequest;
use App\Models\HeroSlide;
use App\Services\ImageService;

class HeroSlideController extends Controller
{
    public function __construct(private readonly ImageService $images) {}

    public function index()
    {
        return view('admin.hero-slides.index', [
            'slides' => HeroSlide::orderBy('sort_order')->paginate(30),
        ]);
    }

    public function create()
    {
        return view('admin.hero-slides.form', ['slide' => new HeroSlide]);
    }

    public function store(HeroSlideRequest $request)
    {
        HeroSlide::create($this->payload($request));

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide created.');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.form', ['slide' => $heroSlide]);
    }

    public function update(HeroSlideRequest $request, HeroSlide $heroSlide)
    {
        $heroSlide->update($this->payload($request, $heroSlide));

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide updated.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        $this->images->delete($heroSlide->image);
        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide deleted.');
    }

    private function payload(HeroSlideRequest $request, ?HeroSlide $existing = null): array
    {
        $data = $request->safe()->except('image');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'hero-slides', $existing?->image, 1600);
        }

        return $data;
    }
}
