@extends('layouts.admin')

@section('title', $group->exists ? 'Edit Group: '.$group->name : 'New Modifier Group')

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><strong>Group Details</strong></div>
                <div class="card-body">
                    <form action="{{ $group->exists ? route('admin.modifier-groups.update', $group) : route('admin.modifier-groups.store') }}" method="post">
                        @csrf
                        @if($group->exists) @method('PUT') @endif
                        <div class="mb-3">
                            <label class="form-label" for="name">Name *</label>
                            <input class="form-control" id="name" name="name" value="{{ old('name', $group->name) }}" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="selection_type">Selection type *</label>
                            <select class="form-select" id="selection_type" name="selection_type" required>
                                <option value="single" @selected(old('selection_type', $group->selection_type) === 'single')>Single choice (radio)</option>
                                <option value="multiple" @selected(old('selection_type', $group->selection_type) === 'multiple')>Multiple choice (checkboxes)</option>
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-4">
                                <label class="form-label" for="min_select">Min select</label>
                                <input class="form-control" id="min_select" name="min_select" type="number" min="0" value="{{ old('min_select', $group->min_select ?? 0) }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label" for="max_select">Max select</label>
                                <input class="form-control" id="max_select" name="max_select" type="number" min="1" value="{{ old('max_select', $group->max_select) }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label" for="sort_order">Sort order</label>
                                <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $group->sort_order ?? 0) }}">
                            </div>
                        </div>
                        <div class="form-check form-switch my-3">
                            <input type="hidden" name="is_required" value="0">
                            <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1" @checked(old('is_required', $group->is_required))>
                            <label class="form-check-label" for="is_required">Required (customer must choose)</label>
                        </div>
                        <button class="btn btn-primary" type="submit">{{ $group->exists ? 'Save Changes' : 'Create Group' }}</button>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.modifier-groups.index') }}">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            @if($group->exists)
                <div class="card">
                    <div class="card-header bg-white"><strong>Options</strong></div>
                    <ul class="list-group list-group-flush">
                        @forelse($group->options as $option)
                            <li class="list-group-item">
                                <form action="{{ route('admin.modifier-options.update', $option) }}" method="post" class="row g-2 align-items-center">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="modifier_group_id" value="{{ $group->id }}">
                                    <div class="col-4"><input class="form-control form-control-sm" name="name" value="{{ $option->name }}" required maxlength="150" aria-label="Option name"></div>
                                    <div class="col-3"><input class="form-control form-control-sm" name="price_adjustment" type="number" step="0.01" value="{{ $option->price_adjustment }}" aria-label="Price adjustment"></div>
                                    <div class="col-2 form-check">
                                        <input type="hidden" name="is_available" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_available" value="1" @checked($option->is_available) id="avail-{{ $option->id }}">
                                        <label class="form-check-label small" for="avail-{{ $option->id }}">On</label>
                                    </div>
                                    <div class="col-3 d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Save</button>
                                    </div>
                                </form>
                                <form action="{{ route('admin.modifier-options.destroy', $option) }}" method="post" class="mt-1" data-confirm="Remove '{{ $option->name }}'?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0" type="submit">Remove</button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-body-secondary">No options yet — add one below.</li>
                        @endforelse
                    </ul>
                    <div class="card-body">
                        <form action="{{ route('admin.modifier-options.store') }}" method="post" class="row g-2">
                            @csrf
                            <input type="hidden" name="modifier_group_id" value="{{ $group->id }}">
                            <div class="col-5"><input class="form-control" name="name" placeholder="Option name" required maxlength="150" aria-label="New option name"></div>
                            <div class="col-3"><input class="form-control" name="price_adjustment" type="number" step="0.01" placeholder="0.00" aria-label="New option price"></div>
                            <div class="col-4"><button class="btn btn-outline-primary w-100" type="submit">Add Option</button></div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info">Save the group first, then add its options.</div>
            @endif
        </div>
    </div>
@endsection
