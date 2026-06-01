<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test adding items to the cart.
     */
    public function test_customer_can_add_product_to_cart(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'stock' => 10,
            'price' => 100000.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->post(route('cart.store', $product), [
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('carts', [
            'user_id' => $customer->id,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /**
     * Test full checkout flow with stock reductions.
     */
    public function test_customer_can_checkout_successfully(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'stock' => 10,
            'price' => 100000.00,
            'weight' => 500,
            'is_active' => true,
        ]);

        // 1. Add to cart
        $this->actingAs($customer)->post(route('cart.store', $product), [
            'quantity' => 3,
        ]);

        // 2. Perform checkout
        $response = $this->actingAs($customer)->post(route('checkout.store'), [
            'shipping_address' => 'Jalan Pahlawan No. 45, Jakarta Barat',
            'notes' => 'Tolong dibungkus bubble wrap yang tebal.',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // 3. Assert database order created
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'status' => 'pending',
            'shipping_address' => 'Jalan Pahlawan No. 45, Jakarta Barat',
            'notes' => 'Tolong dibungkus bubble wrap yang tebal.',
        ]);

        // 4. Assert stock was decremented (10 - 3 = 7)
        $this->assertEquals(7, $product->fresh()->stock);

        // 5. Assert cart was cleared
        $cart = $customer->cart;
        $this->assertEquals(0, $cart->items()->count());

        // 6. Assert payment record created
        $order = Order::where('user_id', $customer->id)->first();
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'gross_amount' => $order->total_price,
            'transaction_status' => 'pending',
        ]);
    }
}
