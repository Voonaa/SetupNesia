<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    /**
     * Get all orders with user information.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllOrders(): Collection
    {
        return Order::with('user')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get order details by ID.
     *
     * @param int $id
     * @return \App\Models\Order
     */
    public function getOrderById(int $id): Order
    {
        return Order::with(['user', 'items.product.primaryImage', 'payment'])->findOrFail($id);
    }

    /**
     * Get orders placed by a specific customer.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCustomerOrders(\App\Models\User $user): Collection
    {
        return Order::with(['items.product.primaryImage', 'payment'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Cancel a customer's order.
     *
     * @param \App\Models\Order $order
     * @param \App\Models\User $user
     * @return \App\Models\Order
     * @throws \Exception
     */
    public function cancelOrder(Order $order, \App\Models\User $user): Order
    {
        if ($order->user_id !== $user->id) {
            throw new \Exception("Unauthorized to cancel this order.");
        }

        if (!in_array($order->status, ['pending', 'paid'])) {
            throw new \Exception("Order cannot be cancelled. Only pending or paid orders can be cancelled.");
        }

        return $this->updateOrderStatus($order, 'cancelled');
    }

    /**
     * Update order status with dynamic restocking logic on cancellation.
     *
     * @param \App\Models\Order $order
     * @param string $status
     * @return \App\Models\Order
     * @throws \Exception
     */
    public function updateOrderStatus(Order $order, string $status): Order
    {
        $validStatuses = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \Exception("Invalid order status: {$status}");
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $status) {
            $oldStatus = $order->status;
            
            $order->update([
                'status' => $status,
            ]);

            // Restock items if order transitioned to cancelled
            if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                // Sync payment transaction status if exists
                if ($order->payment) {
                    $order->payment->update([
                        'transaction_status' => 'cancelled',
                    ]);
                }
            }
        });

        return $order->fresh(['user', 'items.product.primaryImage', 'payment']);
    }
}
