<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'totalOrders' => Order::count(),
            'totalUsers' => User::where('role', 'cliente')->count(),
            'pendingOrders' => Order::pendientes()->count(),
            'todayOrders' => Order::whereDate('created_at', today())->count(),
            'monthRevenue' => Order::whereMonth('created_at', now()->month)
                ->where('status', '!=', Order::STATUS_CANCELADO)
                ->sum('total'),
        ];

        $recentOrders = Order::with('user')
            ->recent()
            ->take(5)
            ->get();

        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }
}