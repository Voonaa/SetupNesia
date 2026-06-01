<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);
        
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 20,
            'price' => 150000.00,
            'is_active' => true,
        ]);
    }

    /**
     * Helper to create a paid order on a specific date.
     */
    private function createPaidOrder(User $user, string $createdAt, int $quantity = 2): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-' . rand(100000, 999999),
            'user_id' => $user->id,
            'total_price' => $this->product->price * $quantity + 15000.00,
            'status' => 'paid',
            'shipping_address' => 'Jalan Menteng Raya No. 10, Jakarta',
            'shipping_cost' => 15000.00,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => $quantity,
            'price' => $this->product->price,
        ]);

        // Force dates in database directly to bypass Eloquent timestamp overrides
        \Illuminate\Support\Facades\DB::table('orders')->where('id', $order->id)->update([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        \Illuminate\Support\Facades\DB::table('order_items')->where('order_id', $order->id)->update([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        return $order->fresh(['items.product', 'user']);
    }

    /**
     * Test customers are strictly forbidden from accessing reports.
     */
    public function test_customer_cannot_access_reports(): void
    {
        // Index
        $response = $this->actingAs($this->customer)->get(route('admin.reports.index'));
        $response->assertStatus(403);

        // Export
        $response = $this->actingAs($this->customer)->get(route('admin.reports.export'));
        $response->assertStatus(403);

        // Print
        $response = $this->actingAs($this->customer)->get(route('admin.reports.print'));
        $response->assertStatus(403);
    }

    /**
     * Test admin can view reports index and see aggregated statistics.
     */
    public function test_admin_can_access_reports_index_and_filter_daily(): void
    {
        // Create an order paid today
        $orderToday = $this->createPaidOrder($this->customer, date('Y-m-d H:i:s'), 2);

        // Create an order paid 2 months ago
        $orderPast = $this->createPaidOrder($this->customer, date('Y-m-d H:i:s', strtotime('-2 months')), 1);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'type' => 'daily',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Daily Report');
        $response->assertSee($orderToday->order_number);
        $response->assertDontSee($orderPast->order_number);
        
        // Assert sums: 2 units of 150000 + 15000 shipping = 315000
        $response->assertSee('Rp 315.000');
    }

    /**
     * Test admin can filter monthly reports.
     */
    public function test_admin_can_filter_monthly_reports(): void
    {
        $targetDate = '2026-05-15 14:00:00';
        $orderTarget = $this->createPaidOrder($this->customer, $targetDate, 1); // May 2026
        
        $otherDate = '2026-06-01 10:00:00';
        $orderOther = $this->createPaidOrder($this->customer, $otherDate, 2); // June 2026

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'type' => 'monthly',
            'year' => 2026,
            'month' => 5,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Monthly Report (May 2026)');
        $response->assertSee($orderTarget->order_number);
        $response->assertDontSee($orderOther->order_number);
    }

    /**
     * Test admin can filter yearly reports.
     */
    public function test_admin_can_filter_yearly_reports(): void
    {
        $targetDate = '2025-10-01 12:00:00';
        $orderTarget = $this->createPaidOrder($this->customer, $targetDate, 1); // 2025
        
        $otherDate = '2026-02-01 12:00:00';
        $orderOther = $this->createPaidOrder($this->customer, $otherDate, 2); // 2026

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'type' => 'yearly',
            'year' => 2025,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Yearly Report (2025)');
        $response->assertSee($orderTarget->order_number);
        $response->assertDontSee($orderOther->order_number);
    }

    /**
     * Test admin can export reports as Excel/CSV streaming.
     */
    public function test_admin_can_export_csv_stream(): void
    {
        $order = $this->createPaidOrder($this->customer, date('Y-m-d H:i:s'), 2);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.export', [
            'type' => 'daily',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type') ?? $response->headers->get('Content-type'));
        $response->assertHeader('Content-Disposition', 'attachment; filename=daily_report__' . date('Y-m-d') . '_to_' . date('Y-m-d') . '__report.csv');
        
        // Assert streamed content contains order number and customer info
        $this->assertStringContainsString($order->order_number, $response->streamedContent());
        $this->assertStringContainsString($this->customer->name, $response->streamedContent());
        $this->assertStringContainsString('Total Revenue (Rp)', $response->streamedContent());
    }

    /**
     * Test admin can load print report page correctly.
     */
    public function test_admin_can_load_print_report_page(): void
    {
        $order = $this->createPaidOrder($this->customer, date('Y-m-d H:i:s'), 1);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.print', [
            'type' => 'daily',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Transaction Details Log');
        $response->assertSee($order->order_number);
        $response->assertSee('window.print()');
    }
}
