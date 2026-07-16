@extends('layouts.admin')

@section('title', $page->exists ? 'Edit Page: '.$page->title : 'New Page')

@section('content')
    <form action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" method="post" class="row g-3">
        @csrf
        @if($page->exists) @method('PUT') @endif

        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">Title *</label>
                        <input class="form-control" id="title" name="title" data-slug-source="#slug" value="{{ old('title', $page->title) }}" required maxlength="190">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug * <span class="text-body-secondary small">(page will be at /p/slug — use "privacy-policy" and "terms-and-conditions" for the required legal pages)</span></label>
                        <input class="form-control" id="slug" name="slug" value="{{ old('slug', $page->slug) }}" required maxlength="190">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="content">Content (HTML allowed — sanitized on save)</label>
                        <textarea class="form-control" id="content" name="content" rows="16" maxlength="60000">{{ old('content', $page->content) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $page->exists ? $page->is_active : true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <label class="form-label" for="seo_title">SEO title</label>
                    <input class="form-control mb-2" id="seo_title" name="seo_title" value="{{ old('seo_title', $page->seo_title) }}" maxlength="190">
                    <label class="form-label" for="meta_description">Meta description</label>
                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="500">{{ old('meta_description', $page->meta_description) }}</textarea>
                </div>
            </div>
            <button class="btn btn-primary w-100" type="submit">{{ $page->exists ? 'Save Changes' : 'Create Page' }}</button>
            <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('admin.pages.index') }}">Cancel</a>
        </div>
    </form>
@endsection
