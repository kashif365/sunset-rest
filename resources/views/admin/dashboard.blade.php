@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <span class="stat-icon text-bg-primary"><i class="bi bi-receipt"></i></span>
                <div>
                    <div class="stat-value">{{ $todayOrders }}</div>
                    <div class="stat-label">Today's Orders</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <span class="stat-icon text-bg-warning"><i class="bi bi-hourglass-split"></i></span>
                <div>
                    <div class="stat-value">{{ $pendingOrders }}</div>
                    <div class="stat-label">Open / Pending</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <span class="stat-icon text-bg-success"><i class="bi bi-check2-circle"></i></span>
                <div>
                    <div class="stat-value">{{ $completedToday }}</div>
                    <div class="stat-label">Completed Today</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <span class="stat-icon text-bg-secondary"><i class="bi bi-x-circle"></i></span>
                <div>
                    <div class="stat-value">{{ $cancelledToday }}</div>
                    <div class="stat-label">Cancelled Today</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-4">
            <div class="stat-card">
                <div class="stat-label mb-1">Revenue Today</div>
                <div class="stat-value">${{ number_format($revenueToday, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="stat-card">
                <div class="stat-label mb-1">This Week</div>
                <div class="stat-value">${{ number_format($revenueWeek, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="stat-card">
                <div class="stat-label mb-1">This Month</div>
                <div class="stat-value">${{ number_format($revenueMonth, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-7">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <strong>Recent Orders</strong>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">All orders</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Order</th><th>Customer</th><th>Pickup</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                <td>{{ $order->customer_name }}</td>
                                <td class="text-nowrap">{{ $order->pickup_date->format('M j') }} {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}</td>
                                <td>${{ number_format((float) $order->total, 2) }}</td>
                                <td><span class="badge badge-status {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-body-secondary py-4">No orders yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Recent Messages</strong>
                    @if($unreadMessages > 0)
                        <span class="badge text-bg-danger">{{ $unreadMessages }} unread</span>
                    @endif
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($recentMessages as $message)
                        <li class="list-group-item d-flex justify-content-between gap-2">
                            <a href="{{ route('admin.contact-submissions.show', $message) }}" class="text-decoration-none {{ $message->is_read ? '' : 'fw-bold' }}">
                                {{ $message->name }} — {{ \Illuminate\Support\Str::limit($message->subject ?: $message->message, 50) }}
                            </a>
                            <span class="text-body-secondary small text-nowrap">{{ $message->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-body-secondary">No messages yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Most Ordered Products</strong></div>
                <ul class="list-group list-group-flush">
                    @forelse($topProducts as $product)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $product->item_name }}</span>
                            <span class="badge text-bg-primary rounded-pill">{{ $product->total_qty }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-body-secondary">No sales data yet.</li>
                    @endforelse
                </ul>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Low Stock</strong></div>
                <ul class="list-group list-group-flush">
                    @forelse($lowStock as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            @can('manage-content')
                                <a href="{{ route('admin.menu-items.edit', $item) }}">{{ $item->name }}</a>
                            @else
                                <span>{{ $item->name }}</span>
                            @endcan
                            <span class="badge {{ $item->stock_quantity <= 0 ? 'text-bg-danger' : 'text-bg-warning' }} rounded-pill">{{ $item->stock_quantity }} left</span>
                        </li>
                    @empty
                        <li class="list-group-item text-body-secondary">All stocked items look healthy.</li>
                    @endforelse
                </ul>
            </div>

            <div class="card">
                <div class="card-header bg-white"><strong>Quick Links</strong></div>
                <div class="card-body d-flex flex-wrap gap-2">
                    @can('manage-content')
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.menu-items.create') }}"><i class="bi bi-plus-circle me-1"></i>New menu item</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.hero-slides.index') }}"><i class="bi bi-images me-1"></i>Hero slides</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.coupons.create') }}"><i class="bi bi-ticket-perforated me-1"></i>New coupon</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.menu-items.index', ['flag' => 'needs_verification']) }}"><i class="bi bi-patch-question me-1"></i>Items needing verification</a>
                    @endcan
                    @can('manage-settings')
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.hours.edit') }}"><i class="bi bi-clock me-1"></i>Business hours</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.settings.edit', 'ordering') }}"><i class="bi bi-gear me-1"></i>Ordering settings</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
