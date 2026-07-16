@extends('layouts.admin')

@section('title', $link->exists ? 'Edit Link' : 'New Navigation Link')

@section('content')
    <form action="{{ $link->exists ? route('admin.navigation-links.update', $link) : route('admin.navigation-links.store') }}" method="post" class="row g-3" style="max-width: 40rem;">
        @csrf
        @if($link->exists) @method('PUT') @endif

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="location">Location *</label>
                        <select class="form-select" id="location" name="location" required>
                            <option value="header" @selected(old('location', $link->location) === 'header')>Header</option>
                            <option value="footer" @selected(old('location', $link->location) === 'footer')>Footer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="label">Label *</label>
                        <input class="form-control" id="label" name="label" value="{{ old('label', $link->label) }}" required maxlength="120">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="url">URL *</label>
                        <input class="form-control" id="url" name="url" value="{{ old('url', $link->url) }}" required maxlength="500" placeholder="/menu or https://...">
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="new_tab" value="0">
                                <input class="form-check-input" type="checkbox" id="new_tab" name="new_tab" value="1" @checked(old('new_tab', $link->new_tab))>
                                <label class="form-check-label" for="new_tab">Open in new tab</label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $link->exists ? $link->is_active : true))>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="sort_order">Sort order</label>
                            <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $link->sort_order ?? 0) }}">
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit">{{ $link->exists ? 'Save Changes' : 'Create Link' }}</button>
            <a class="btn btn-outline-secondary mt-3" href="{{ route('admin.navigation-links.index') }}">Cancel</a>
        </div>
    </form>
@endsection
