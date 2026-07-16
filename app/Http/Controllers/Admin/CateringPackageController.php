<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CateringPackageRequest;
use App\Models\CateringPackage;
use App\Services\ImageService;

class CateringPackageController extends Controller
{
    public function __construct(private readonly ImageService $images) {}

    public function index()
    {
        return view('admin.catering.index', [
            'packages' => CateringPackage::orderBy('sort_order')->paginate(30),
        ]);
    }

    public function create()
    {
        return view('admin.catering.form', ['package' => new CateringPackage]);
    }

    public function store(CateringPackageRequest $request)
    {
        CateringPackage::create($this->payload($request));

        return redirect()->route('admin.catering-packages.index')->with('success', 'Catering package created.');
    }

    public function edit(CateringPackage $cateringPackage)
    {
        return view('admin.catering.form', ['package' => $cateringPackage]);
    }

    public function update(CateringPackageRequest $request, CateringPackage $cateringPackage)
    {
        $cateringPackage->update($this->payload($request, $cateringPackage));

        return redirect()->route('admin.catering-packages.index')->with('success', 'Catering package updated.');
    }

    public function destroy(CateringPackage $cateringPackage)
    {
        $this->images->delete($cateringPackage->image);
        $cateringPackage->delete();

        return redirect()->route('admin.catering-packages.index')->with('success', 'Catering package deleted.');
    }

    private function payload(CateringPackageRequest $request, ?CateringPackage $existing = null): array
    {
        $data = $request->safe()->except('image');
        $data['is_active'] = $request->boolean('is_active');
        $data['needs_verification'] = $request->boolean('needs_verification');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'catering', $existing?->image);
        }

        return $data;
    }
}
