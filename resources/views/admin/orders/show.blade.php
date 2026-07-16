@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to orders</a>
        <a href="{{ route('admin.orders.receipt', $order) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print receipt</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-white d-flex justify-content-between">
                    <strong>{{ $order->order_number }}</strong>
                    <span class="badge badge-status {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Customer:</strong> {{ $order->customer_name }} — {{ $order->customer_phone }} — {{ $order->customer_email }}</p>
                    <p class="mb-1"><strong>Pickup:</strong> {{ $order->pickup_date->format('l, F j') }} at {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}</p>
                    <p class="mb-1"><strong>Payment:</strong> {{ str_replace('_', ' ', $order->payment_method) }} ({{ $order->payment_status }})</p>
                    @if($order->notes)<p class="mb-0"><strong>Customer notes:</strong> {{ $order->notes }}</p>@endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white"><strong>Items</strong></div>
                <ul class="list-group list-group-flush">
                    @foreach($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between gap-2">
                            <div>
                                <strong>{{ $item->quantity }}× {{ $item->item_name }}</strong>
                                @if($item->variation_name)<div class="small text-body-secondary">{{ $item->variation_name }}</div>@endif
                                @foreach($item->modifiers as $modifier)
                                    <div class="small text-body-secondary">{{ $modifier->group_name }}: {{ $modifier->option_name }}</div>
                                @endforeach
                                @if($item->notes)<div class="small fst-italic text-body-secondary">"{{ $item->notes }}"</div>@endif
                            </div>
                            <span class="text-nowrap">${{ number_format((float) $item->line_total, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="card-body">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span>${{ number_format((float) $order->subtotal, 2) }}</span></div>
                    @if((float) $order->discount > 0)
                        <div class="d-flex justify-content-between text-success"><span>Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</span><span>−${{ number_format((float) $order->discount, 2) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between"><span>Tax</span><span>${{ number_format((float) $order->tax, 2) }}</span></div>
                    @if((float) $order->tip > 0)
                        <div class="d-flex justify-content-between"><span>Tip</span><span>${{ number_format((float) $order->tip, 2) }}</span></div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span>${{ number_format((float) $order->total, 2) }}</span></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-white"><strong>Update Order</strong></div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order) }}" method="post">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status">
                                @foreach(\App\Models\Order::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="payment_status">Payment status</label>
                            <select class="form-select" id="payment_status" name="payment_status">
                                @foreach(['unpaid', 'paid', 'refunded'] as $status)
                                    <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="admin_notes">Internal notes</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" maxlength="2000">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
