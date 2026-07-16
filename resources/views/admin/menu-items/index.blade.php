@extends('layouts.admin')

@section('title', 'Menu Items')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form class="d-flex flex-wrap gap-2" method="get">
            <label class="visually-hidden" for="filter-q">Search</label>
            <input id="filter-q" class="form-control" style="max-width: 220px;" type="search" name="q" value="{{ request('q') }}" placeholder="Search items…">
            <label class="visually-hidden" for="filter-category">Category</label>
            <select id="filter-category" class="form-select" style="max-width: 220px;" name="category">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <label class="visually-hidden" for="filter-flag">Flag</label>
            <select id="filter-flag" class="form-select" style="max-width: 200px;" name="flag">
                <option value="">All flags</option>
                <option value="needs_verification" @selected(request('flag') === 'needs_verification')>Needs verification</option>
                <option value="sold_out" @selected(request('flag') === 'sold_out')>Sold out</option>
                <option value="low_stock" @selected(request('flag') === 'low_stock')>Low stock</option>
            </select>
            <button class="btn btn-outline-secondary" type="submit">Filter</button>
        </form>
        <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Item</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Flags</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td><img class="thumb-sm" src="{{ \App\Services\ImageService::thumbUrl($item->image) }}" alt=""></td>
                        <td>
                            <a class="fw-bold" href="{{ route('admin.menu-items.edit', $item) }}">{{ $item->name }}</a>
                            @if($item->needs_verification)
                                <span class="badge text-bg-warning ms-1" title="Extracted from the printed menu; price/details need client confirmation">Needs verification</span>
                            @endif
                        </td>
                        <td>{{ $item->category->name }}</td>
                        <td class="text-nowrap">
                            @if($item->discounted_price !== null)
                                <s class="text-body-secondary">${{ number_format((float) $item->price, 2) }}</s>
                                ${{ number_format((float) $item->discounted_price, 2) }}
                            @else
                                ${{ number_format((float) $item->price, 2) }}
                            @endif
                        </td>
                        <td>
                            @if($item->stock_quantity === null)
                                <span class="text-body-secondary">—</span>
                            @else
                                <span class="badge {{ $item->isLowStock() ? 'text-bg-danger' : 'text-bg-light border' }}">{{ $item->stock_quantity }}</span>
                            @endif
                        </td>
                        <td>
                            @if($item->is_featured)<i class="bi bi-star-fill text-warning" title="Featured"></i>@endif
                            @if($item->is_bestseller)<i class="bi bi-fire text-danger" title="Bestseller"></i>@endif
                            @if($item->is_sold_out)<span class="badge text-bg-dark">Sold out</span>@endif
                        </td>
                        <td><span class="badge {{ $item->is_available ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_available ? 'Available' : 'Hidden' }}</span></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.menu-items.edit', $item) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.menu-items.destroy', $item) }}" method="post" data-confirm="Delete '{{ $item->name }}'?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-body-secondary py-4">No menu items match.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
@endsection
