<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0; padding:0; background:#FFF5D8; font-family: Arial, Helvetica, sans-serif; color:#201713;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FFF5D8; padding:24px 12px;">
    <tr><td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#FFFDF7; border-radius:12px; overflow:hidden; border:1px solid #E6D2A9;">
            <tr>
                <td style="background:#69001F; padding:24px; text-align:center;">
                    <h1 style="margin:0; color:#FFFDF7; font-size:22px; text-transform:uppercase; letter-spacing:1px;">Sunset Bagel Exchange</h1>
                    <p style="margin:6px 0 0; color:#FFD21F; font-size:13px;">Hand rolled &bull; Kettle boiled &bull; Old fashioned</p>
                </td>
            </tr>
            <tr>
                <td style="padding:28px;">
                    <h2 style="margin:0 0 8px; color:#69001F;">Thanks, {{ explode(' ', $order->customer_name)[0] }} — your order is in! 🥯</h2>
                    <p style="margin:0 0 16px;">Order <strong>{{ $order->order_number }}</strong> will be ready for pickup:</p>
                    <p style="margin:0 0 20px; font-size:18px;"><strong>{{ $order->pickup_date->format('l, F j') }} at {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}</strong><br>
                        3316 Sunset Ave., Ocean, NJ 07712</p>

                    <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
                        @foreach($order->items as $item)
                            <tr style="border-bottom:1px solid #E6D2A9;">
                                <td valign="top">
                                    <strong>{{ $item->quantity }}× {{ $item->item_name }}</strong>
                                    @if($item->variation_name)<br><span style="color:#6D5B52;">{{ $item->variation_name }}</span>@endif
                                    @foreach($item->modifiers as $modifier)
                                        <br><span style="color:#6D5B52;">+ {{ $modifier->option_name }}</span>
                                    @endforeach
                                    @if($item->notes)<br><em style="color:#6D5B52;">"{{ $item->notes }}"</em>@endif
                                </td>
                                <td valign="top" align="right">${{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr><td align="right">Subtotal</td><td align="right">${{ number_format((float) $order->subtotal, 2) }}</td></tr>
                        @if((float) $order->discount > 0)
                            <tr><td align="right" style="color:#4F7C44;">Discount</td><td align="right" style="color:#4F7C44;">−${{ number_format((float) $order->discount, 2) }}</td></tr>
                        @endif
                        <tr><td align="right">Tax</td><td align="right">${{ number_format((float) $order->tax, 2) }}</td></tr>
                        @if((float) $order->tip > 0)
                            <tr><td align="right">Tip</td><td align="right">${{ number_format((float) $order->tip, 2) }}</td></tr>
                        @endif
                        <tr><td align="right" style="font-size:16px;"><strong>Total</strong></td><td align="right" style="font-size:16px;"><strong>${{ number_format((float) $order->total, 2) }}</strong></td></tr>
                    </table>

                    <p style="margin:20px 0 0; padding:12px; background:#FFF5D8; border-radius:8px; font-size:14px;">
                        <strong>Payment:</strong>
                        {{ $order->payment_method === 'phone' ? 'We will call you to confirm your order and take payment.' : 'Please pay at pickup (cash or card).' }}
                    </p>

                    @if($order->notes)
                        <p style="margin:16px 0 0; font-size:14px;"><strong>Your notes:</strong> {{ $order->notes }}</p>
                    @endif

                    <p style="margin:24px 0 0; font-size:14px;">Questions? Call us at <a href="tel:+17323618119" style="color:#B51F2A;">(732) 361-8119</a>.</p>
                </td>
            </tr>
            <tr>
                <td style="background:#201713; padding:16px; text-align:center; color:#FFFDF7; font-size:12px;">
                    &copy; {{ date('Y') }} Sunset Bagel Exchange • 3316 Sunset Ave., Ocean, NJ 07712
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
