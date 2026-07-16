@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    <form class="row g-2 mb-3" method="get">
        <div class="col-auto">
            <label class="visually-hidden" for="filter-q">Search</label>
            <input id="filter-q" class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Order #, name, email, phone…">
        </div>
        <div class="col-auto">
            <label class="visually-hidden" for="filter-status">Status</label>
            <select id="filter-status" class="form-select" name="status">
                <option value="">All statuses</option>
                @foreach(\App\Models\Order::STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="visually-hidden" for="filter-date">Pickup date</label>
            <input id="filter-date" class="form-control" type="date" name="date" value="{{ request('date') }}">
        </div>
        <div class="col-auto"><button class="btn btn-outline-secondary" type="submit">Filter</button></div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Order</th><th>Customer</th><th>Pickup</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a class="fw-bold" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a><br><span class="small text-body-secondary">{{ $order->created_at->format('M j, g:i A') }}</span></td>
                        <td>{{ $order->customer_name }}<br><span class="small text-body-secondary">{{ $order->customer_phone }}</span></td>
                        <td class="text-nowrap">{{ $order->pickup_date->format('M j') }}<br>{{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}</td>
                        <td>{{ $order->items->sum('quantity') }}</td>
                        <td>${{ number_format((float) $order->total, 2) }}</td>
                        <td class="small text-capitalize">{{ str_replace('_', ' ', $order->payment_method) }}<br>{{ ucfirst($order->payment_status) }}</td>
                        <td><span class="badge badge-status {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.orders.show', $order) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-body-secondary py-4">No orders match.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
@endsection
