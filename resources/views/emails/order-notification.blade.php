<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"></head>
<body style="margin:0; padding:16px; font-family: Arial, Helvetica, sans-serif; color:#201713; background:#fff;">
    <h1 style="font-size:20px; color:#69001F; margin:0 0 4px;">🔔 New pickup order {{ $order->order_number }}</h1>
    <p style="margin:0 0 16px; font-size:15px;">
        <strong>Pickup:</strong> {{ $order->pickup_date->format('D, M j') }} at {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}<br>
        <strong>Customer:</strong> {{ $order->customer_name }} — {{ $order->customer_phone }} — {{ $order->customer_email }}<br>
        <strong>Payment:</strong> {{ str_replace('_', ' ', $order->payment_method) }} ({{ $order->payment_status }})
    </p>

    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; font-size:14px; border:1px solid #E6D2A9;">
        @foreach($order->items as $item)
            <tr style="border-bottom:1px solid #E6D2A9;">
                <td valign="top">
                    <strong>{{ $item->quantity }}× {{ $item->item_name }}</strong>
                    @if($item->variation_name)<br>{{ $item->variation_name }}@endif
                    @foreach($item->modifiers as $modifier)
                        <br>+ {{ $modifier->option_name }}
                    @endforeach
                    @if($item->notes)<br><em>"{{ $item->notes }}"</em>@endif
                </td>
                <td valign="top" align="right">${{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
        @endforeach
        <tr><td align="right"><strong>Total (incl. tax @if((float) $order->tip > 0) &amp; tip @endif)</strong></td>
            <td align="right"><strong>${{ number_format((float) $order->total, 2) }}</strong></td></tr>
    </table>

    @if($order->notes)
        <p style="font-size:14px;"><strong>Customer notes:</strong> {{ $order->notes }}</p>
    @endif

    <p style="font-size:14px;"><a href="{{ route('admin.orders.show', $order) }}" style="color:#B51F2A;">Open in admin panel →</a></p>
</body>
</html>
