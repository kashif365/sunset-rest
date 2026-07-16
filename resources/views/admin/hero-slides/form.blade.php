@extends('layouts.admin')

@section('title', $slide->exists ? 'Edit Slide' : 'New Hero Slide')

@section('content')
    <form action="{{ $slide->exists ? route('admin.hero-slides.update', $slide) : route('admin.hero-slides.store') }}"
          method="post" enctype="multipart/form-data" class="row g-3" style="max-width: 60rem;">
        @csrf
        @if($slide->exists) @method('PUT') @endif

        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">Title *</label>
                        <input class="form-control" id="title" name="title" value="{{ old('title', $slide->title) }}" required maxlength="190">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="subtitle">Subtitle</label>
                        <textarea class="form-control" id="subtitle" name="subtitle" rows="2" maxlength="500">{{ old('subtitle', $slide->subtitle) }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="button_text">Button 1 text</label>
                            <input class="form-control" id="button_text" name="button_text" value="{{ old('button_text', $slide->button_text) }}" maxlength="60">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="button_url">Button 1 URL</label>
                            <input class="form-control" id="button_url" name="button_url" value="{{ old('button_url', $slide->button_url) }}" maxlength="500" placeholder="/menu">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="button2_text">Button 2 text</label>
                            <input class="form-control" id="button2_text" name="button2_text" value="{{ old('button2_text', $slide->button2_text) }}" maxlength="60">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="button2_url">Button 2 URL</label>
                            <input class="form-control" id="button2_url" name="button2_url" value="{{ old('button2_url', $slide->button2_url) }}" maxlength="500" placeholder="/catering">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <label class="form-label" for="image">Slide image {{ $slide->exists ? '' : '*' }} <span class="text-body-secondary small">(wide, 1600px+ recommended)</span></label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-preview="#slide-preview" @required(! $slide->exists)>
                    <img id="slide-preview" class="img-fluid rounded mt-2" src="{{ \App\Services\ImageService::url($slide->image, '/images/hero-default.svg') }}" alt="Slide preview">
                    <label class="form-label mt-2" for="image_alt">Alt text</label>
                    <input class="form-control" id="image_alt" name="image_alt" value="{{ old('image_alt', $slide->image_alt) }}" maxlength="190">
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $slide->exists ? $slide->is_active : true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <label class="form-label" for="sort_order">Sort order</label>
                    <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $slide->sort_order ?? 0) }}">
                </div>
            </div>
            <button class="btn btn-primary w-100" type="submit">{{ $slide->exists ? 'Save Changes' : 'Create Slide' }}</button>
            <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('admin.hero-slides.index') }}">Cancel</a>
        </div>
    </form>
@endsection
