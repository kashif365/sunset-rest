@extends('layouts.admin')

@section('title', 'Navigation Links')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group" aria-label="Filter by location">
            <a class="btn btn-outline-secondary {{ ! $location ? 'active' : '' }}" href="{{ route('admin.navigation-links.index') }}">All</a>
            <a class="btn btn-outline-secondary {{ $location === 'header' ? 'active' : '' }}" href="{{ route('admin.navigation-links.index', ['location' => 'header']) }}">Header</a>
            <a class="btn btn-outline-secondary {{ $location === 'footer' ? 'active' : '' }}" href="{{ route('admin.navigation-links.index', ['location' => 'footer']) }}">Footer</a>
        </div>
        <a href="{{ route('admin.navigation-links.create', ['location' => $location ?? 'header']) }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Link</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" data-reorder-table data-reorder-url="{{ route('admin.reorder', 'navigation-links') }}">
                <thead><tr><th style="width:90px;">Order</th><th>Location</th><th>Label</th><th>URL</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($links as $link)
                    <tr data-id="{{ $link->id }}">
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-light" data-move-up aria-label="Move up"><i class="bi bi-arrow-up"></i></button>
                            <button class="btn btn-sm btn-light" data-move-down aria-label="Move down"><i class="bi bi-arrow-down"></i></button>
                        </td>
                        <td><span class="badge text-bg-light border text-capitalize">{{ $link->location }}</span></td>
                        <td><a class="fw-bold" href="{{ route('admin.navigation-links.edit', $link) }}">{{ $link->label }}</a></td>
                        <td class="small text-body-secondary">{{ $link->url }}</td>
                        <td><span class="badge {{ $link->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $link->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.navigation-links.edit', $link) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.navigation-links.destroy', $link) }}" method="post" data-confirm="Delete this link?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-body-secondary py-4">No links yet — defaults will be shown on the site.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $links->links() }}</div>
@endsection
