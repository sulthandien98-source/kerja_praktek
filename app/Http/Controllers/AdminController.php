<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalOrders   = Order::count();

        $pendingOrders = Order::where('status', Order::STATUS_PENDING)
            ->orWhere('status', Order::STATUS_WAITING)
            ->count();

        $completedOrders = Order::where('status', Order::STATUS_SELESAI)->count();

        $totalRevenue = Order::where('status', Order::STATUS_SELESAI)
            ->where('payment_status', Order::PAYMENT_APPROVED)
            ->sum('total_price');

        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();

        $todayRevenue = Order::whereDate('created_at', Carbon::today())
            ->where('status', Order::STATUS_SELESAI)
            ->where('payment_status', Order::PAYMENT_APPROVED)
            ->sum('total_price');

        $outOfStock = Product::where('stock', '<=', 0)->count();

        $pendingPaymentCount = Order::where('payment_status', Order::PAYMENT_UPLOADED)->count();

        $statusCounts = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $latestOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalRevenue',
            'todayOrders',
            'todayRevenue',
            'outOfStock',
            'pendingPaymentCount',
            'statusCounts',
            'latestOrders'
        ));
    }
}