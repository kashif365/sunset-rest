@extends('layouts.public')

@section('seo_title')Order Confirmed — Sunset Bagel Exchange@endsection

@section('content')
    <div class="container py-4 py-lg-5" style="max-width: 48rem;">
        <div class="text-center mb-4">
            <i class="bi bi-patch-check-fill display-1 text-success" aria-hidden="true"></i>
            <h1 class="display-heading h1 text-burgundy mt-2">Thanks, {{ explode(' ', $order->customer_name)[0] }}!</h1>
            <p class="lead mb-1">Your order is in. We'll have it ready for pickup:</p>
            <p class="fs-4 fw-bold text-burgundy mb-1">
                {{ $order->pickup_date->format('l, F j') }} at {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}
            </p>
            <p class="text-body-secondary">Order number: <strong>{{ $order->order_number }}</strong> — a confirmation email is on its way to {{ $order->customer_email }}.</p>
            <div class="d-flex justify-content-center flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('orders.receipt', $order->order_number) }}">
                    <i class="bi bi-printer me-1" aria-hidden="true"></i>Printable receipt
                </a>
                <a class="btn btn-brand" href="{{ route('menu.index') }}">Back to Menu</a>
            </div>
        </div>

        <div class="summary-card p-4">
            <h2 class="h5 fw-bold text-uppercase mb-3">Order Details</h2>
            @foreach($order->items as $item)
                <div class="d-flex justify-content-between gap-2 py-2 border-bottom">
                    <div>
                        <strong>{{ $item->quantity }}× {{ $item->item_name }}</strong>
                        @if($item->variation_name)<div class="small text-body-secondary">{{ $item->variation_name }}</div>@endif
                        @foreach($item->modifiers as $modifier)
                            <div class="small text-body-secondary">{{ $modifier->group_name }}: {{ $modifier->option_name }}</div>
                        @endforeach
                        @if($item->notes)<div class="small fst-italic text-body-secondary">"{{ $item->notes }}"</div>@endif
                    </div>
                    <span class="text-nowrap">${{ number_format((float) $item->line_total, 2) }}</span>
                </div>
            @endforeach

            <div class="summary-row mt-3"><span>Subtotal</span><span>${{ number_format((float) $order->subtotal, 2) }}</span></div>
            @if((float) $order->discount > 0)
                <div class="summary-row text-success"><span>Discount</span><span>−${{ number_format((float) $order->discount, 2) }}</span></div>
            @endif
            <div class="summary-row"><span>Tax</span><span>${{ number_format((float) $order->tax, 2) }}</span></div>
            @if((float) $order->tip > 0)
                <div class="summary-row"><span>Tip</span><span>${{ number_format((float) $order->tip, 2) }}</span></div>
            @endif
            <hr>
            <div class="summary-row summary-total"><span>Total</span><span>${{ number_format((float) $order->total, 2) }}</span></div>
            <p class="small text-body-secondary mb-0 mt-2">
                Payment: {{ $order->payment_method === 'phone' ? 'We will call you to confirm' : 'Pay at pickup' }} • {{ ucfirst($order->payment_status) }}
            </p>
        </div>
    </div>
@endsection
