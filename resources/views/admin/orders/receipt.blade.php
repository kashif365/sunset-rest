@extends('layouts.admin')

@section('title', 'Receipt '.$order->order_number)

@section('content')
    <div class="text-end mb-3 no-print">
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    </div>

    <div class="card receipt-card mx-auto" style="max-width: 42rem;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h1 class="h3" style="color:#69001F;">Sunset Bagel Exchange</h1>
                <p class="small mb-0">3316 Sunset Ave., Ocean, NJ 07712 • (732) 361-8119</p>
            </div>

            <div class="d-flex justify-content-between small border-top border-bottom py-2 mb-3">
                <span><strong>Order:</strong> {{ $order->order_number }}</span>
                <span><strong>Placed:</strong> {{ $order->created_at->format('M j, Y g:i A') }}</span>
            </div>

            <p class="mb-1"><strong>Pickup:</strong> {{ $order->pickup_date->format('l, F j') }} at {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}</p>
            <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
            <p class="mb-3"><strong>Phone:</strong> {{ $order->customer_phone }}</p>

            <table class="table table-sm">
                <thead><tr><th>Item</th><th class="text-center">Qty</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->item_name }}
                            @if($item->variation_name)<div class="small text-body-secondary">{{ $item->variation_name }}</div>@endif
                            @foreach($item->modifiers as $modifier)
                                <div class="small text-body-secondary">+ {{ $modifier->option_name }}</div>
                            @endforeach
                            @if($item->notes)<div class="small fst-italic">"{{ $item->notes }}"</div>@endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">${{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="2" class="text-end">Subtotal</td><td class="text-end">${{ number_format((float) $order->subtotal, 2) }}</td></tr>
                    @if((float) $order->discount > 0)
                        <tr><td colspan="2" class="text-end">Discount</td><td class="text-end">−${{ number_format((float) $order->discount, 2) }}</td></tr>
                    @endif
                    <tr><td colspan="2" class="text-end">Tax</td><td class="text-end">${{ number_format((float) $order->tax, 2) }}</td></tr>
                    @if((float) $order->tip > 0)
                        <tr><td colspan="2" class="text-end">Tip</td><td class="text-end">${{ number_format((float) $order->tip, 2) }}</td></tr>
                    @endif
                    <tr class="fw-bold fs-5"><td colspan="2" class="text-end">Total</td><td class="text-end">${{ number_format((float) $order->total, 2) }}</td></tr>
                </tfoot>
            </table>

            @if($order->notes)<p class="small mb-0"><strong>Customer notes:</strong> {{ $order->notes }}</p>@endif
            @if($order->admin_notes)<p class="small mb-0"><strong>Internal notes:</strong> {{ $order->admin_notes }}</p>@endif
        </div>
    </div>
@endsection
