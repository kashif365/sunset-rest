@extends('layouts.admin')

@section('title', 'Catering Packages')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Shown on the public Catering page.</p>
        <a href="{{ route('admin.catering-packages.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Package</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" data-reorder-table data-reorder-url="{{ route('admin.reorder', 'catering-packages') }}">
                <thead><tr><th style="width:90px;">Order</th><th>Image</th><th>Name</th><th>Price</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($packages as $package)
                    <tr data-id="{{ $package->id }}">
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-light" data-move-up aria-label="Move up"><i class="bi bi-arrow-up"></i></button>
                            <button class="btn btn-sm btn-light" data-move-down aria-label="Move down"><i class="bi bi-arrow-down"></i></button>
                        </td>
                        <td><img class="thumb-sm" src="{{ \App\Services\ImageService::thumbUrl($package->image) }}" alt=""></td>
                        <td><a class="fw-bold" href="{{ route('admin.catering-packages.edit', $package) }}">{{ $package->name }}</a>
                            @if($package->needs_verification)<span class="badge text-bg-warning ms-1">Needs verification</span>@endif
                        </td>
                        <td>{{ $package->needs_verification || $package->price === null ? 'Call for pricing' : '$'.number_format((float) $package->price, 2) }}</td>
                        <td><span class="badge {{ $package->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $package->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.catering-packages.edit', $package) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.catering-packages.destroy', $package) }}" method="post" data-confirm="Delete '{{ $package->name }}'?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-body-secondary py-4">No catering packages yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $packages->links() }}</div>
@endsection
