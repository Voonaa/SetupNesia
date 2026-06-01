<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders.
     */
    public function index(): View
    {
        $orders = $this->orderService->getAllOrders();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order): View
    {
        $order = $this->orderService->getOrderById($order->id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the order status.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,processing,shipped,completed,cancelled',
        ]);

        try {
            $this->orderService->updateOrderStatus($order, $request->status);
            return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.show', $order)->with('error', $e->getMessage());
        }
    }
}
