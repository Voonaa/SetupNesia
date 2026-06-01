<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Calculate checkout weights and shipping costs.
     *
     * @param \App\Models\User $user
     * @return array
     */
    public function calculateShipping(User $user): array
    {
        $cart = $this->cartService->getCartForUser($user);
        if (!$cart || $cart->items->count() === 0) {
            return ['weight' => 0, 'cost' => 0.00];
        }

        $totalWeight = 0; // grams
        foreach ($cart->items as $item) {
            if ($item->product) {
                $totalWeight += $item->product->weight * $item->quantity;
            }
        }

        // Mock JNE shipping rate: Rp 15,000 for every 1000 grams, minimum Rp 15,000
        $weightInKg = ceil($totalWeight / 1000);
        $shippingCost = max(15000.00, $weightInKg * 15000.00);

        return [
            'weight' => $totalWeight,
            'cost' => $shippingCost,
        ];
    }

    /**
     * Process checkout and place order.
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return \App\Models\Order
     * @throws \Exception
     */
    public function placeOrder(User $user, array $data): Order
    {
        $cart = $this->cartService->getCartForUser($user);

        if (!$cart || $cart->items->count() === 0) {
            throw new \Exception("Cannot place order. Your shopping cart is empty.");
        }

        return DB::transaction(function () use ($user, $cart, $data) {
            // 1. Validate stocks
            foreach ($cart->items as $item) {
                $product = $item->product;
                if (!$product->is_active) {
                    throw new \Exception("Product '{$product->name}' is no longer active in our store.");
                }
                if ($product->stock < $item->quantity) {
                    throw new \Exception("Insufficient stock for product '{$product->name}'. Only {$product->stock} units left.");
                }
            }

            // 2. Shipping calculations
            $shippingInfo = $this->calculateShipping($user);
            $shippingCost = $shippingInfo['cost'];
            $itemsTotal = $this->cartService->getCartSubtotal($user);
            $grandTotal = $itemsTotal + $shippingCost;

            // 3. Generate Order Number: ORD-YYYYMMDD-XXXXXX
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // 4. Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'total_price' => $grandTotal,
                'status' => 'pending',
                'shipping_address' => $data['shipping_address'],
                'shipping_cost' => $shippingCost,
                'notes' => $data['notes'] ?? null,
            ]);

            // 5. Create Order Items and Deduct Stock
            foreach ($cart->items as $item) {
                $product = $item->product;
                
                // Deduct stock
                $product->decrement('stock', $item->quantity);

                // Create OrderItem
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                ]);
            }

            // 6. Create Initial Payment
            Payment::create([
                'order_id' => $order->id,
                'gross_amount' => $grandTotal,
                'transaction_status' => 'pending',
            ]);

            // 7. Flush the Cart
            $this->cartService->clearCart($user);

            return $order;
        });
    }
}
