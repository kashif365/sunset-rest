@extends('layouts.admin')

@section('title', $category->exists ? 'Edit Category: '.$category->name : 'New Category')

@section('content')
    <form action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
          method="post" enctype="multipart/form-data" class="row g-3" style="max-width: 60rem;">
        @csrf
        @if($category->exists) @method('PUT') @endif

        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="name">Name *</label>
                        <input class="form-control" id="name" name="name" data-slug-source="#slug"
                               value="{{ old('name', $category->name) }}" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug *</label>
                        <input class="form-control" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" required maxlength="160">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" maxlength="2000">{{ old('description', $category->description) }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="seo_title">SEO title</label>
                            <input class="form-control" id="seo_title" name="seo_title" value="{{ old('seo_title', $category->seo_title) }}" maxlength="190">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="meta_description">Meta description</label>
                            <input class="form-control" id="meta_description" name="meta_description" value="{{ old('meta_description', $category->meta_description) }}" maxlength="500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <label class="form-label" for="image">Image (jpg/png/webp, max 4 MB)</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-preview="#image-preview">
                    <img id="image-preview" class="img-fluid rounded mt-2" style="max-height: 140px;"
                         src="{{ \App\Services\ImageService::thumbUrl($category->image) }}" alt="Category image preview">
                    <div class="mt-2">
                        <label class="form-label" for="image_alt">Image alt text</label>
                        <input class="form-control" id="image_alt" name="image_alt" value="{{ old('image_alt', $category->image_alt) }}" maxlength="190">
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))>
                        <label class="form-check-label" for="is_active">Active (visible on site)</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="is_featured" value="0">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $category->is_featured))>
                        <label class="form-check-label" for="is_featured">Featured on homepage</label>
                    </div>
                    <label class="form-label" for="sort_order">Sort order</label>
                    <input class="form-control" type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0">
                </div>
            </div>

            <button class="btn btn-primary w-100" type="submit">{{ $category->exists ? 'Save Changes' : 'Create Category' }}</button>
            <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('admin.categories.index') }}">Cancel</a>
        </div>
    </form>
@endsection
