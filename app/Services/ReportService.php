<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate Daily sales report.
     *
     * @param string $startDate (YYYY-MM-DD)
     * @param string $endDate (YYYY-MM-DD)
     * @return array
     */
    public function generateDailyReport(string $startDate, string $endDate): array
    {
        $query = Order::with(['user', 'items.product'])
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'asc');

        return $this->buildReportData($query->get(), "Daily Report ({$startDate} to {$endDate})");
    }

    /**
     * Generate Monthly sales report.
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function generateMonthlyReport(int $year, int $month): array
    {
        $query = Order::with(['user', 'items.product'])
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'asc');

        $monthName = date('F', mktime(0, 0, 0, $month, 10));
        return $this->buildReportData($query->get(), "Monthly Report ({$monthName} {$year})");
    }

    /**
     * Generate Yearly sales report.
     *
     * @param int $year
     * @return array
     */
    public function generateYearlyReport(int $year): array
    {
        $query = Order::with(['user', 'items.product'])
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'asc');

        return $this->buildReportData($query->get(), "Yearly Report ({$year})");
    }

    /**
     * Build report summaries and arrays.
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders
     * @param string $title
     * @return array
     */
    protected function buildReportData(Collection $orders, string $title): array
    {
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_price');
        
        $totalItemsSold = 0;
        foreach ($orders as $order) {
            $totalItemsSold += $order->items->sum('quantity');
        }

        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            'title' => $title,
            'orders' => $orders,
            'total_orders' => $totalOrders,
            'total_revenue' => (float) $totalRevenue,
            'total_items_sold' => $totalItemsSold,
            'average_order_value' => (float) $averageOrderValue,
        ];
    }
}
