<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModifierGroupRequest;
use App\Http\Requests\Admin\ModifierOptionRequest;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;

class ModifierGroupController extends Controller
{
    public function index()
    {
        return view('admin.modifiers.index', [
            'groups' => ModifierGroup::withCount(['options', 'menuItems'])->orderBy('sort_order')->paginate(30),
        ]);
    }

    public function create()
    {
        return view('admin.modifiers.form', ['group' => new ModifierGroup]);
    }

    public function store(ModifierGroupRequest $request)
    {
        $group = ModifierGroup::create($this->payload($request));

        return redirect()->route('admin.modifier-groups.edit', $group)->with('success', 'Modifier group created — now add options.');
    }

    public function edit(ModifierGroup $modifierGroup)
    {
        $modifierGroup->load('options');

        return view('admin.modifiers.form', ['group' => $modifierGroup]);
    }

    public function update(ModifierGroupRequest $request, ModifierGroup $modifierGroup)
    {
        $modifierGroup->update($this->payload($request));

        return redirect()->route('admin.modifier-groups.edit', $modifierGroup)->with('success', 'Modifier group updated.');
    }

    public function destroy(ModifierGroup $modifierGroup)
    {
        $modifierGroup->delete();

        return redirect()->route('admin.modifier-groups.index')->with('success', 'Modifier group deleted.');
    }

    // ---- Options (nested) ---------------------------------------

    public function storeOption(ModifierOptionRequest $request)
    {
        ModifierOption::create($this->optionPayload($request));

        return back()->with('success', 'Option added.');
    }

    public function updateOption(ModifierOptionRequest $request, ModifierOption $option)
    {
        $option->update($this->optionPayload($request));

        return back()->with('success', 'Option updated.');
    }

    public function destroyOption(ModifierOption $option)
    {
        abort_unless(auth()->user()->can('manage-content'), 403);

        $option->delete();

        return back()->with('success', 'Option removed.');
    }

    private function payload(ModifierGroupRequest $request): array
    {
        $data = $request->validated();
        $data['is_required'] = $request->boolean('is_required');
        $data['min_select'] = (int) ($data['min_select'] ?? 0);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }

    private function optionPayload(ModifierOptionRequest $request): array
    {
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default');
        $data['is_available'] = $request->boolean('is_available');
        $data['price_adjustment'] = (float) ($data['price_adjustment'] ?? 0);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
