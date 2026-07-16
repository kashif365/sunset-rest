@extends('layouts.admin')

@section('title', 'Promotions')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group" aria-label="Filter by type">
            <a class="btn btn-outline-secondary {{ ! $type ? 'active' : '' }}" href="{{ route('admin.promotions.index') }}">All</a>
            <a class="btn btn-outline-secondary {{ $type === 'banner' ? 'active' : '' }}" href="{{ route('admin.promotions.index', ['type' => 'banner']) }}">Banners</a>
            <a class="btn btn-outline-secondary {{ $type === 'offer' ? 'active' : '' }}" href="{{ route('admin.promotions.index', ['type' => 'offer']) }}">Offers</a>
        </div>
        <a href="{{ route('admin.promotions.create', ['type' => $type ?? 'banner']) }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Promotion</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" data-reorder-table data-reorder-url="{{ route('admin.reorder', 'promotions') }}">
                <thead><tr><th style="width:90px;">Order</th><th>Type</th><th>Title</th><th>Dates</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($promotions as $promo)
                    <tr data-id="{{ $promo->id }}">
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-light" data-move-up aria-label="Move up"><i class="bi bi-arrow-up"></i></button>
                            <button class="btn btn-sm btn-light" data-move-down aria-label="Move down"><i class="bi bi-arrow-down"></i></button>
                        </td>
                        <td><span class="badge text-bg-light border text-capitalize">{{ $promo->type }}</span></td>
                        <td><a class="fw-bold" href="{{ route('admin.promotions.edit', $promo) }}">{{ $promo->title }}</a></td>
                        <td class="small">
                            @if($promo->starts_at || $promo->ends_at)
                                {{ $promo->starts_at?->format('M j') ?? 'Always' }} – {{ $promo->ends_at?->format('M j') ?? 'Ongoing' }}
                            @else
                                Always on
                            @endif
                        </td>
                        <td><span class="badge {{ $promo->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $promo->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.promotions.edit', $promo) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.promotions.destroy', $promo) }}" method="post" data-confirm="Delete '{{ $promo->title }}'?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-body-secondary py-4">No promotions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $promotions->links() }}</div>
@endsection
