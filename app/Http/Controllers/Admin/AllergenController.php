<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaxonomyRequest;
use App\Models\Allergen;

class AllergenController extends Controller
{
    public function index()
    {
        return view('admin.taxonomies.index', [
            'records' => Allergen::orderBy('name')->paginate(50),
            'type' => 'allergens',
            'title' => 'Allergens',
        ]);
    }

    public function create()
    {
        return view('admin.taxonomies.form', [
            'record' => new Allergen, 'type' => 'allergens', 'title' => 'Allergen',
        ]);
    }

    public function store(TaxonomyRequest $request)
    {
        Allergen::create($request->validated());

        return redirect()->route('admin.allergens.index')->with('success', 'Allergen created.');
    }

    public function edit(Allergen $allergen)
    {
        return view('admin.taxonomies.form', [
            'record' => $allergen, 'type' => 'allergens', 'title' => 'Allergen',
        ]);
    }

    public function update(TaxonomyRequest $request, Allergen $allergen)
    {
        $allergen->update($request->validated());

        return redirect()->route('admin.allergens.index')->with('success', 'Allergen updated.');
    }

    public function destroy(Allergen $allergen)
    {
        $allergen->delete();

        return redirect()->route('admin.allergens.index')->with('success', 'Allergen deleted.');
    }
}
