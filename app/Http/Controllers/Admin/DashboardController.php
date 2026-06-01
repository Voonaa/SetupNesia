<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\DashboardStatisticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardStatisticsService $statsService;

    public function __construct(DashboardStatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Display the main administrator panel dashboard.
     */
    public function index(): View
    {
        $metrics = $this->statsService->getMetrics();
        
        // Fetch 5 most recent orders with customer data
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('metrics', 'recentOrders'));
    }
}
