<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $otherCustomer;
    private Category $category;
    private Product $product;
    private string $serverKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->otherCustomer = User::factory()->create(['role' => 'customer']);
        
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 10,
            'price' => 100000.00,
            'is_active' => true,
        ]);

        $this->serverKey = config('services.midtrans.server_key', 'SB-Mid-server-SetupNesiaDummyKey123');
    }

    /**
     * Helper to create an order.
     */
    private function createOrderForUser(User $user, int $quantity = 2): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-' . rand(100000, 999999),
            'user_id' => $user->id,
            'total_price' => $this->product->price * $quantity + 15000.00,
            'status' => 'pending',
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
            'transaction_status' => 'pending',
        ]);

        // Deduct stock manually
        $this->product->decrement('stock', $quantity);

        return $order;
    }

    /**
     * Test authorized customer can retrieve Snap Token successfully.
     */
    public function test_customer_can_retrieve_snap_token(): void
    {
        $order = $this->createOrderForUser($this->customer);

        // Fake Midtrans Snap API Response
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'mock-snap-token-xyz-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v1/transactions/mock-snap-token-xyz-123'
            ], 201)
        ]);

        $response = $this->actingAs($this->customer)->postJson(route('payment.snap-token', $order));

        $response->assertStatus(200);
        $response->assertJsonPath('token', 'mock-snap-token-xyz-123');
    }

    /**
     * Test unauthorized customer cannot retrieve Snap Token of another customer's order.
     */
    public function test_customer_cannot_retrieve_snap_token_for_other_user_order(): void
    {
        $order = $this->createOrderForUser($this->otherCustomer);

        $response = $this->actingAs($this->customer)->postJson(route('payment.snap-token', $order));

        $response->assertStatus(403);
    }

    /**
     * Test webhook signature validation: valid settlement sets order as paid.
     */
    public function test_webhook_valid_signature_settlement_updates_order_to_paid(): void
    {
        $order = $this->createOrderForUser($this->customer, 2);

        // Setup notification payload
        $statusCode = '200';
        $grossAmount = number_format($order->total_price, 2, '.', '');
        
        // Calculate signature key: SHA512 of (order_id + status_code + gross_amount + server_key)
        $signatureKey = hash('sha512', $order->order_number . $statusCode . $grossAmount . $this->serverKey);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ];

        $response = $this->postJson(route('payment.callback'), $payload);

        $response->assertStatus(200);
        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals('settlement', $order->fresh()->payment->transaction_status);
        
        // Stock remains decremented (10 - 2 = 8)
        $this->assertEquals(8, $this->product->fresh()->stock);
    }

    /**
     * Test webhook signature validation: expire/cancel updates order to cancelled and restocks.
     */
    public function test_webhook_expire_updates_order_to_cancelled_and_restocks(): void
    {
        // Initial stock 10. After createOrder(qty=3), stock is 7.
        $order = $this->createOrderForUser($this->customer, 3);
        $this->assertEquals(7, $this->product->fresh()->stock);

        $statusCode = '200'; // statusCode doesn't strictly have to match transaction status, signature calculates off statusCode parameter
        $grossAmount = number_format($order->total_price, 2, '.', '');
        $signatureKey = hash('sha512', $order->order_number . $statusCode . $grossAmount . $this->serverKey);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
            'fraud_status' => 'accept',
        ];

        $response = $this->postJson(route('payment.callback'), $payload);

        $response->assertStatus(200);
        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals('expire', $order->fresh()->payment->transaction_status);

        // Restocked stock: (7 + 3 = 10)
        $this->assertEquals(10, $this->product->fresh()->stock);
    }

    /**
     * Test webhook with invalid signature is strictly rejected.
     */
    public function test_webhook_invalid_signature_is_rejected(): void
    {
        $order = $this->createOrderForUser($this->customer);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => number_format($order->total_price, 2, '.', ''),
            'signature_key' => 'invalid-fake-signature-hash-here',
            'transaction_status' => 'settlement',
        ];

        $response = $this->postJson(route('payment.callback'), $payload);

        $response->assertStatus(400);
        $this->assertEquals('pending', $order->fresh()->status);
    }
}
