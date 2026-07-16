<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todayOrders = Order::today()->count();
        $pendingOrders = Order::whereIn('status', ['pending', 'confirmed', 'preparing'])->count();
        $completedToday = Order::today()->status('completed')->count();
        $cancelledToday = Order::today()->status('cancelled')->count();

        $revenueToday = (float) Order::today()->where('status', '!=', 'cancelled')->sum('total');
        $revenueWeek = (float) Order::where('created_at', '>=', now()->startOfWeek())
            ->where('status', '!=', 'cancelled')->sum('total');
        $revenueMonth = (float) Order::where('created_at', '>=', now()->startOfMonth())
            ->where('status', '!=', 'cancelled')->sum('total');

        $topProducts = OrderItem::select('item_name', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->take(8)
            ->get();

        $lowStock = MenuItem::whereNotNull('stock_quantity')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->take(8)
            ->get();

        return view('admin.dashboard', [
            'todayOrders' => $todayOrders,
            'pendingOrders' => $pendingOrders,
            'completedToday' => $completedToday,
            'cancelledToday' => $cancelledToday,
            'revenueToday' => $revenueToday,
            'revenueWeek' => $revenueWeek,
            'revenueMonth' => $revenueMonth,
            'topProducts' => $topProducts,
            'lowStock' => $lowStock,
            'recentOrders' => Order::latest()->take(8)->get(),
            'recentMessages' => ContactSubmission::latest()->take(5)->get(),
            'unreadMessages' => ContactSubmission::where('is_read', false)->count(),
        ]);
    }
}
