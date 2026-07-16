<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryImageRequest;
use App\Models\GalleryImage;
use App\Services\ImageService;

class GalleryImageController extends Controller
{
    public function __construct(private readonly ImageService $images) {}

    public function index()
    {
        return view('admin.gallery.index', [
            'imagesList' => GalleryImage::orderBy('sort_order')->paginate(36),
        ]);
    }

    public function create()
    {
        return view('admin.gallery.form', ['galleryImage' => new GalleryImage]);
    }

    public function store(GalleryImageRequest $request)
    {
        GalleryImage::create($this->payload($request));

        return redirect()->route('admin.gallery-images.index')->with('success', 'Image added to gallery.');
    }

    public function edit(GalleryImage $galleryImage)
    {
        return view('admin.gallery.form', compact('galleryImage'));
    }

    public function update(GalleryImageRequest $request, GalleryImage $galleryImage)
    {
        $galleryImage->update($this->payload($request, $galleryImage));

        return redirect()->route('admin.gallery-images.index')->with('success', 'Image updated.');
    }

    public function destroy(GalleryImage $galleryImage)
    {
        $this->images->delete($galleryImage->image);
        $galleryImage->delete();

        return redirect()->route('admin.gallery-images.index')->with('success', 'Image deleted.');
    }

    private function payload(GalleryImageRequest $request, ?GalleryImage $existing = null): array
    {
        $data = $request->safe()->except('image');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'gallery', $existing?->image);
        }

        return $data;
    }
}
