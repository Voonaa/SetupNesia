<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class DashboardStatisticsService
{
    /**
     * Get aggregated statistics for the admin dashboard.
     *
     * @return array
     */
    public function getMetrics(): array
    {
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();

        // Calculate total revenue from successful/paid orders
        $totalRevenue = Order::whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->sum('total_price');

        return [
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'total_revenue' => (float) $totalRevenue,
        ];
    }
}
