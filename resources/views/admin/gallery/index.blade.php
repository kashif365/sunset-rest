@extends('layouts.admin')

@section('title', 'Gallery')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Photos shown in the homepage gallery / Instagram-style section.</p>
        <a href="{{ route('admin.gallery-images.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Image</a>
    </div>

    <div class="row g-3" data-reorder-table data-reorder-url="{{ route('admin.reorder', 'gallery-images') }}">
        @forelse($imagesList as $image)
            <div class="col-6 col-md-4 col-lg-3" data-id="{{ $image->id }}">
                <div class="card h-100">
                    <img class="card-img-top object-cover" style="aspect-ratio: 1/1;" src="{{ \App\Services\ImageService::thumbUrl($image->image) }}" alt="{{ $image->image_alt }}">
                    <div class="card-body p-2 d-flex justify-content-between align-items-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-light" data-move-up aria-label="Move up"><i class="bi bi-arrow-left"></i></button>
                            <button class="btn btn-light" data-move-down aria-label="Move down"><i class="bi bi-arrow-right"></i></button>
                        </div>
                        <span class="badge {{ $image->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $image->is_active ? 'On' : 'Off' }}</span>
                    </div>
                    <div class="card-footer p-2 d-flex justify-content-between">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.gallery-images.edit', $image) }}">Edit</a>
                        <form action="{{ route('admin.gallery-images.destroy', $image) }}" method="post" data-confirm="Delete this image?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-body-secondary py-4">No gallery images yet.</p>
        @endforelse
    </div>
    <div class="mt-3">{{ $imagesList->links() }}</div>
@endsection
