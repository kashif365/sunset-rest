@extends('layouts.admin')

@section('title', 'Menu Categories')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Use the arrows to reorder how categories appear on the site.</p>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Category</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" data-reorder-table data-reorder-url="{{ route('admin.reorder', 'categories') }}">
                <thead><tr><th style="width:90px;">Order</th><th>Image</th><th>Name</th><th>Items</th><th>Featured</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($categories as $category)
                    <tr data-id="{{ $category->id }}">
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-light" data-move-up aria-label="Move {{ $category->name }} up"><i class="bi bi-arrow-up"></i></button>
                            <button class="btn btn-sm btn-light" data-move-down aria-label="Move {{ $category->name }} down"><i class="bi bi-arrow-down"></i></button>
                        </td>
                        <td><img class="thumb-sm" src="{{ \App\Services\ImageService::thumbUrl($category->image) }}" alt=""></td>
                        <td><a href="{{ route('admin.categories.edit', $category) }}" class="fw-bold">{{ $category->name }}</a><br><span class="small text-body-secondary">/{{ $category->slug }}</span></td>
                        <td>{{ $category->menu_items_count }}</td>
                        <td>@if($category->is_featured)<i class="bi bi-star-fill text-warning"></i>@endif</td>
                        <td>
                            <span class="badge {{ $category->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $category->is_active ? 'Active' : 'Hidden' }}</span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.categories.destroy', $category) }}" method="post"
                                  data-confirm="Delete '{{ $category->name }}' and ALL {{ $category->menu_items_count }} items in it?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-body-secondary py-4">No categories yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $categories->links() }}</div>
@endsection
