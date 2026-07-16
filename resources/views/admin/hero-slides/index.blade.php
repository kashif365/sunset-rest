@extends('layouts.admin')

@section('title', 'Hero Slides')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">These appear in the homepage hero slider, in order.</p>
        <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Slide</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" data-reorder-table data-reorder-url="{{ route('admin.reorder', 'hero-slides') }}">
                <thead><tr><th style="width:90px;">Order</th><th>Image</th><th>Title</th><th>Buttons</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($slides as $slide)
                    <tr data-id="{{ $slide->id }}">
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-light" data-move-up aria-label="Move up"><i class="bi bi-arrow-up"></i></button>
                            <button class="btn btn-sm btn-light" data-move-down aria-label="Move down"><i class="bi bi-arrow-down"></i></button>
                        </td>
                        <td><img class="thumb-sm" src="{{ \App\Services\ImageService::thumbUrl($slide->image) }}" alt=""></td>
                        <td><a class="fw-bold" href="{{ route('admin.hero-slides.edit', $slide) }}">{{ $slide->title }}</a></td>
                        <td class="small">{{ $slide->button_text }}{{ $slide->button2_text ? ' / '.$slide->button2_text : '' }}</td>
                        <td><span class="badge {{ $slide->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $slide->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.hero-slides.edit', $slide) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.hero-slides.destroy', $slide) }}" method="post" data-confirm="Delete this slide?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-body-secondary py-4">No slides yet — add one to populate the homepage hero.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $slides->links() }}</div>
@endsection
