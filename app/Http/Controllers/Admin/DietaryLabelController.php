<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaxonomyRequest;
use App\Models\DietaryLabel;

class DietaryLabelController extends Controller
{
    public function index()
    {
        return view('admin.taxonomies.index', [
            'records' => DietaryLabel::orderBy('name')->paginate(50),
            'type' => 'dietary-labels',
            'title' => 'Dietary Labels',
        ]);
    }

    public function create()
    {
        return view('admin.taxonomies.form', [
            'record' => new DietaryLabel, 'type' => 'dietary-labels', 'title' => 'Dietary Label',
        ]);
    }

    public function store(TaxonomyRequest $request)
    {
        DietaryLabel::create($request->validated());

        return redirect()->route('admin.dietary-labels.index')->with('success', 'Dietary label created.');
    }

    public function edit(DietaryLabel $dietaryLabel)
    {
        return view('admin.taxonomies.form', [
            'record' => $dietaryLabel, 'type' => 'dietary-labels', 'title' => 'Dietary Label',
        ]);
    }

    public function update(TaxonomyRequest $request, DietaryLabel $dietaryLabel)
    {
        $dietaryLabel->update($request->validated());

        return redirect()->route('admin.dietary-labels.index')->with('success', 'Dietary label updated.');
    }

    public function destroy(DietaryLabel $dietaryLabel)
    {
        $dietaryLabel->delete();

        return redirect()->route('admin.dietary-labels.index')->with('success', 'Dietary label deleted.');
    }
}
