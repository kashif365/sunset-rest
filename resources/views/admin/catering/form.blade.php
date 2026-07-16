@extends('layouts.admin')

@section('title', $package->exists ? 'Edit Package: '.$package->name : 'New Catering Package')

@section('content')
    <form action="{{ $package->exists ? route('admin.catering-packages.update', $package) : route('admin.catering-packages.store') }}"
          method="post" enctype="multipart/form-data" class="row g-3" style="max-width: 60rem;">
        @csrf
        @if($package->exists) @method('PUT') @endif

        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="name">Name *</label>
                        <input class="form-control" id="name" name="name" data-slug-source="#slug" value="{{ old('name', $package->name) }}" required maxlength="190">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug *</label>
                        <input class="form-control" id="slug" name="slug" value="{{ old('slug', $package->slug) }}" required maxlength="190">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" maxlength="4000">{{ old('description', $package->description) }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="price">Price ($)</label>
                            <input class="form-control" id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $package->price) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="price_label">Price label</label>
                            <input class="form-control" id="price_label" name="price_label" value="{{ old('price_label', $package->price_label) }}" maxlength="60" placeholder="per person">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="serves">Serves</label>
                            <input class="form-control" id="serves" name="serves" value="{{ old('serves', $package->serves) }}" maxlength="120" placeholder="10-12 people">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <label class="form-label" for="image">Image</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-preview="#package-preview">
                    <img id="package-preview" class="img-fluid rounded mt-2" src="{{ \App\Services\ImageService::thumbUrl($package->image) }}" alt="Package preview">
                    <label class="form-label mt-2" for="image_alt">Alt text</label>
                    <input class="form-control" id="image_alt" name="image_alt" value="{{ old('image_alt', $package->image_alt) }}" maxlength="190">
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $package->exists ? $package->is_active : true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="needs_verification" value="0">
                        <input class="form-check-input" type="checkbox" id="needs_verification" name="needs_verification" value="1" @checked(old('needs_verification', $package->needs_verification))>
                        <label class="form-check-label" for="needs_verification">Needs price verification (shows "Call for pricing")</label>
                    </div>
                    <label class="form-label" for="sort_order">Sort order</label>
                    <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $package->sort_order ?? 0) }}">
                </div>
            </div>
            <button class="btn btn-primary w-100" type="submit">{{ $package->exists ? 'Save Changes' : 'Create Package' }}</button>
            <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('admin.catering-packages.index') }}">Cancel</a>
        </div>
    </form>
@endsection
