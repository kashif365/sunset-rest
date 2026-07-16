@extends('layouts.admin')

@section('title', $galleryImage->exists ? 'Edit Gallery Image' : 'New Gallery Image')

@section('content')
    <form action="{{ $galleryImage->exists ? route('admin.gallery-images.update', $galleryImage) : route('admin.gallery-images.store') }}"
          method="post" enctype="multipart/form-data" class="row g-3" style="max-width: 40rem;">
        @csrf
        @if($galleryImage->exists) @method('PUT') @endif

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <label class="form-label" for="image">Image {{ $galleryImage->exists ? '' : '*' }}</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-preview="#gallery-preview" @required(! $galleryImage->exists)>
                    <img id="gallery-preview" class="img-fluid rounded mt-2" style="max-height: 200px;" src="{{ \App\Services\ImageService::thumbUrl($galleryImage->image) }}" alt="Gallery preview">

                    <div class="mt-3">
                        <label class="form-label" for="title">Title</label>
                        <input class="form-control" id="title" name="title" value="{{ old('title', $galleryImage->title) }}" maxlength="190">
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="image_alt">Alt text</label>
                        <input class="form-control" id="image_alt" name="image_alt" value="{{ old('image_alt', $galleryImage->image_alt) }}" maxlength="190">
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $galleryImage->exists ? $galleryImage->is_active : true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit">{{ $galleryImage->exists ? 'Save Changes' : 'Add Image' }}</button>
            <a class="btn btn-outline-secondary mt-3" href="{{ route('admin.gallery-images.index') }}">Cancel</a>
        </div>
    </form>
@endsection
