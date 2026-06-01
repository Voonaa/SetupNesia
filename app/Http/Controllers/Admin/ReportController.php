<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display administrative sales reports dashboard.
     */
    public function index(Request $request)
    {
        $type = $request->input('type', 'daily');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $month = (int) $request->input('month', date('m'));
        $year = (int) ($request->filled('year_only') ? $request->input('year_only') : $request->input('year', date('Y')));

        if ($type === 'daily') {
            $report = $this->reportService->generateDailyReport($startDate, $endDate);
        } elseif ($type === 'monthly') {
            $report = $this->reportService->generateMonthlyReport($year, $month);
        } else {
            $report = $this->reportService->generateYearlyReport($year);
        }

        return view('admin.reports.index', compact('report', 'type', 'startDate', 'endDate', 'month', 'year'));
    }

    /**
     * Stream CSV download of matching sales reports for Excel.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'daily');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $month = (int) $request->input('month', date('m'));
        $year = (int) ($request->filled('year_only') ? $request->input('year_only') : $request->input('year', date('Y')));

        if ($type === 'daily') {
            $report = $this->reportService->generateDailyReport($startDate, $endDate);
        } elseif ($type === 'monthly') {
            $report = $this->reportService->generateMonthlyReport($year, $month);
        } else {
            $report = $this->reportService->generateYearlyReport($year);
        }

        $fileName = str_replace([' ', '(', ')'], '_', strtolower($report['title'])) . '_report.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            // Header summary meta
            fputcsv($file, [$report['title']]);
            fputcsv($file, []);
            fputcsv($file, ['Total Orders', $report['total_orders']]);
            fputcsv($file, ['Total Revenue (Rp)', $report['total_revenue']]);
            fputcsv($file, ['Total Items Sold', $report['total_items_sold']]);
            fputcsv($file, ['Average Order Value (Rp)', $report['average_order_value']]);
            fputcsv($file, []);

            // Column names
            fputcsv($file, [
                'Order Number',
                'Date Placed',
                'Customer Name',
                'Customer Email',
                'Items Details',
                'Shipping Cost (Rp)',
                'Grand Total (Rp)'
            ]);

            // Body data rows
            foreach ($report['orders'] as $order) {
                $items = [];
                foreach ($order->items as $item) {
                    $items[] = ($item->product ? $item->product->name : 'N/A') . ' (x' . $item->quantity . ')';
                }
                $itemsStr = implode(', ', $items);

                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user->name,
                    $order->user->email,
                    $itemsStr,
                    (int) $order->shipping_cost,
                    (int) $order->total_price,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display print-optimized report page.
     */
    public function printReport(Request $request)
    {
        $type = $request->input('type', 'daily');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $month = (int) $request->input('month', date('m'));
        $year = (int) ($request->filled('year_only') ? $request->input('year_only') : $request->input('year', date('Y')));

        if ($type === 'daily') {
            $report = $this->reportService->generateDailyReport($startDate, $endDate);
        } elseif ($type === 'monthly') {
            $report = $this->reportService->generateMonthlyReport($year, $month);
        } else {
            $report = $this->reportService->generateYearlyReport($year);
        }

        return view('admin.reports.print', compact('report', 'type', 'startDate', 'endDate', 'month', 'year'));
    }
}
