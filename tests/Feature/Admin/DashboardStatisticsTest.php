<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\DashboardStatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatisticsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that DashboardStatisticsService computes KPIs correctly.
     */
    public function test_dashboard_statistics_service_calculates_metrics_correctly(): void
    {
        // 1. Create Users
        User::factory()->create(['role' => 'admin']);
        User::factory()->count(3)->create(['role' => 'customer']);

        // 2. Create Products
        $category = Category::factory()->create();
        Product::factory()->count(4)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        // 3. Create Orders with different statuses
        $customer = User::where('role', 'customer')->first();
        
        // Paid/Completed Orders (Should be summed in revenue)
        Order::factory()->create([
            'user_id' => $customer->id,
            'total_price' => 1500000.00,
            'status' => 'paid',
        ]);
        Order::factory()->create([
            'user_id' => $customer->id,
            'total_price' => 2500000.00,
            'status' => 'completed',
        ]);

        // Pending/Cancelled Orders (Should NOT be summed in revenue)
        Order::factory()->create([
            'user_id' => $customer->id,
            'total_price' => 500000.00,
            'status' => 'pending',
        ]);
        Order::factory()->create([
            'user_id' => $customer->id,
            'total_price' => 900000.00,
            'status' => 'cancelled',
        ]);

        // 4. Run calculations
        $service = new DashboardStatisticsService();
        $metrics = $service->getMetrics();

        // 5. Assertions
        $this->assertEquals(3, $metrics['total_customers']);
        $this->assertEquals(4, $metrics['total_products']);
        $this->assertEquals(4, $metrics['total_orders']);
        $this->assertEquals(4000000.00, $metrics['total_revenue']); // 1.5M + 2.5M
    }
}
