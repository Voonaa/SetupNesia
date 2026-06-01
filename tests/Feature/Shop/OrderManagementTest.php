<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $otherCustomer;
    private User $admin;
    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->otherCustomer = User::factory()->create(['role' => 'customer']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 10,
            'price' => 100000.00,
            'is_active' => true,
        ]);
    }

    /**
     * Helper to create an order.
     */
    private function createOrderForUser(User $user, string $status = 'pending', int $quantity = 2): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-' . rand(100000, 999999),
            'user_id' => $user->id,
            'total_price' => $this->product->price * $quantity + 15000.00,
            'status' => $status,
            'shipping_address' => 'Jalan Kebagusan Raya No. 12, Jakarta',
            'shipping_cost' => 15000.00,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => $quantity,
            'price' => $this->product->price,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gross_amount' => $order->total_price,
            'transaction_status' => $status === 'paid' ? 'settlement' : 'pending',
        ]);

        // Reduce stock manually since we bypass checkout
        $this->product->decrement('stock', $quantity);

        return $order;
    }

    /**
     * Test customers can view their order history list.
     */
    public function test_customer_can_view_orders_index(): void
    {
        $order = $this->createOrderForUser($this->customer);

        $response = $this->actingAs($this->customer)->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }

    /**
     * Test customers can view their own order details.
     */
    public function test_customer_can_view_own_order_details(): void
    {
        $order = $this->createOrderForUser($this->customer);

        $response = $this->actingAs($this->customer)->get(route('orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }

    /**
     * Test customers cannot view another customer's order.
     */
    public function test_customer_cannot_view_other_customer_order(): void
    {
        $order = $this->createOrderForUser($this->otherCustomer);

        $response = $this->actingAs($this->customer)->get(route('orders.show', $order));

        $response->assertStatus(403);
    }

    /**
     * Test customer can cancel a pending or paid order and stock is restocked.
     */
    public function test_customer_can_cancel_pending_order_and_stock_is_restocked(): void
    {
        // Initial stock is 10. After createOrderForUser(qty=2), stock is 8.
        $order = $this->createOrderForUser($this->customer, 'pending', 2);
        $this->assertEquals(8, $this->product->fresh()->stock);

        $response = $this->actingAs($this->customer)->post(route('orders.cancel', $order));

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('success');

        // Check order and payment status
        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals('cancelled', $order->fresh()->payment->transaction_status);

        // Assert stock was restocked (8 + 2 = 10)
        $this->assertEquals(10, $this->product->fresh()->stock);
    }

    /**
     * Test customer cannot cancel an order in processing, shipped, or completed state.
     */
    public function test_customer_cannot_cancel_processing_order(): void
    {
        $order = $this->createOrderForUser($this->customer, 'processing', 2);
        
        $response = $this->actingAs($this->customer)->post(route('orders.cancel', $order));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('processing', $order->fresh()->status);
    }

    /**
     * Test admin can cancel any order and trigger restocking.
     */
    public function test_admin_can_cancel_order_and_trigger_restocking(): void
    {
        $order = $this->createOrderForUser($this->customer, 'processing', 3);
        $this->assertEquals(7, $this->product->fresh()->stock);

        // Admin updates status to cancelled
        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'status' => 'cancelled',
        ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $order->fresh()->status);
        
        // Assert stock was restocked (7 + 3 = 10)
        $this->assertEquals(10, $this->product->fresh()->stock);
    }
}
