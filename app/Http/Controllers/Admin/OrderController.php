<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('date'), fn ($q, $d) => $q->whereDate('pickup_date', $d))
            ->when($request->query('q'), fn ($q, $term) => $q->where(function ($query) use ($term) {
                $query->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_email', 'like', "%{$term}%")
                    ->orWhere('customer_phone', 'like', "%{$term}%");
            }))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.modifiers', 'customer', 'coupon']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(OrderStatusRequest $request, Order $order)
    {
        $data = $request->validated();

        $order->update([
            'status' => $data['status'],
            'payment_status' => $data['payment_status'] ?? $order->payment_status,
            'admin_notes' => $data['admin_notes'] ?? $order->admin_notes,
        ]);

        return back()->with('success', "Order {$order->order_number} updated.");
    }

    public function receipt(Order $order)
    {
        $order->load('items.modifiers');

        return view('admin.orders.receipt', compact('order'));
    }
}
