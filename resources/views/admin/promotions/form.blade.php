@extends('layouts.admin')

@section('title', $promotion->exists ? 'Edit Promotion' : 'New Promotion')

@section('content')
    <form action="{{ $promotion->exists ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}"
          method="post" enctype="multipart/form-data" class="row g-3" style="max-width: 60rem;">
        @csrf
        @if($promotion->exists) @method('PUT') @endif

        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="type">Type *</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="banner" @selected(old('type', $promotion->type) === 'banner')>Banner (large homepage strip)</option>
                            <option value="offer" @selected(old('type', $promotion->type) === 'offer')>Offer (card in offers grid)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="title">Title *</label>
                        <input class="form-control" id="title" name="title" value="{{ old('title', $promotion->title) }}" required maxlength="190">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="badge_text">Badge text</label>
                        <input class="form-control" id="badge_text" name="badge_text" value="{{ old('badge_text', $promotion->badge_text) }}" maxlength="60" placeholder="e.g. SAVE $4">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" maxlength="2000">{{ old('description', $promotion->description) }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="button_text">Button text</label>
                            <input class="form-control" id="button_text" name="button_text" value="{{ old('button_text', $promotion->button_text) }}" maxlength="60">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="button_url">Button URL</label>
                            <input class="form-control" id="button_url" name="button_url" value="{{ old('button_url', $promotion->button_url) }}" maxlength="500">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="starts_at">Starts</label>
                            <input class="form-control" id="starts_at" name="starts_at" type="date" value="{{ old('starts_at', optional($promotion->starts_at)->toDateString()) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ends_at">Ends</label>
                            <input class="form-control" id="ends_at" name="ends_at" type="date" value="{{ old('ends_at', optional($promotion->ends_at)->toDateString()) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <label class="form-label" for="image">Image</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-preview="#promo-preview">
                    <img id="promo-preview" class="img-fluid rounded mt-2" src="{{ \App\Services\ImageService::thumbUrl($promotion->image) }}" alt="Promotion preview">
                    <label class="form-label mt-2" for="image_alt">Alt text</label>
                    <input class="form-control" id="image_alt" name="image_alt" value="{{ old('image_alt', $promotion->image_alt) }}" maxlength="190">
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $promotion->exists ? $promotion->is_active : true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <label class="form-label" for="sort_order">Sort order</label>
                    <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $promotion->sort_order ?? 0) }}">
                </div>
            </div>
            <button class="btn btn-primary w-100" type="submit">{{ $promotion->exists ? 'Save Changes' : 'Create Promotion' }}</button>
            <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('admin.promotions.index') }}">Cancel</a>
        </div>
    </form>
@endsection
