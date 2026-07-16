<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NavigationLinkRequest;
use App\Models\NavigationLink;
use Illuminate\Http\Request;

class NavigationLinkController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.navigation.index', [
            'links' => NavigationLink::query()
                ->when($request->query('location'), fn ($q, $l) => $q->where('location', $l))
                ->orderBy('location')->orderBy('sort_order')
                ->paginate(50)
                ->withQueryString(),
            'location' => $request->query('location'),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.navigation.form', [
            'link' => new NavigationLink(['location' => $request->query('location', 'header')]),
        ]);
    }

    public function store(NavigationLinkRequest $request)
    {
        NavigationLink::create($this->payload($request));

        return redirect()->route('admin.navigation-links.index')->with('success', 'Link created.');
    }

    public function edit(NavigationLink $navigationLink)
    {
        return view('admin.navigation.form', ['link' => $navigationLink]);
    }

    public function update(NavigationLinkRequest $request, NavigationLink $navigationLink)
    {
        $navigationLink->update($this->payload($request));

        return redirect()->route('admin.navigation-links.index')->with('success', 'Link updated.');
    }

    public function destroy(NavigationLink $navigationLink)
    {
        $navigationLink->delete();

        return redirect()->route('admin.navigation-links.index')->with('success', 'Link deleted.');
    }

    private function payload(NavigationLinkRequest $request): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['new_tab'] = $request->boolean('new_tab');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
