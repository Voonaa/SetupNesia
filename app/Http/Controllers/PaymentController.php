<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected MidtransService $midtransService;
    protected OrderService $orderService;

    public function __construct(MidtransService $midtransService, OrderService $orderService)
    {
        $this->midtransService = $midtransService;
        $this->orderService = $orderService;
    }

    /**
     * Generate a Snap payment token for the given order.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSnapToken(Order $order): JsonResponse
    {
        // 1. Authorization: Ensure order belongs to logged in user
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // 2. Validate current order state
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'This order is not eligible for payment. Current status: ' . $order->status], 422);
        }

        try {
            // Load items.product and user relationships to satisfy MidtransService
            $order = $this->orderService->getOrderById($order->id);
            
            $token = $this->midtransService->getSnapToken($order);

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle webhook callback notifications from Midtrans.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(Request $request): JsonResponse
    {
        $payload = $request->all();
        
        Log::info('Midtrans Webhook Callback Received', $payload);

        // 1. Signature Authentication Check
        if (!$this->midtransService->verifyCallbackSignature($payload)) {
            Log::warning('Midtrans Webhook Callback Rejected: Invalid Signature', [
                'order_id' => $payload['order_id'] ?? 'unknown',
            ]);
            return response()->json(['message' => 'Invalid transaction signature key.'], 400);
        }

        $orderNumber = $payload['order_id'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';

        // 2. Query matching order records
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            Log::error('Midtrans Webhook Callback: Order Not Found', ['order_id' => $orderNumber]);
            return response()->json(['message' => 'Order corresponding to order number not found.'], 404);
        }

        try {
            // 3. Process status maps
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                if ($transactionStatus === 'capture' && $fraudStatus === 'challenge') {
                    // Challenge state: keep pending or set failed
                    $this->orderService->updateOrderStatus($order, 'pending');
                    if ($order->payment) {
                        $order->payment->update(['transaction_status' => 'challenge']);
                    }
                } else {
                    // Settled / Captured: set paid
                    $this->orderService->updateOrderStatus($order, 'paid');
                    if ($order->payment) {
                        $order->payment->update(['transaction_status' => 'settlement']);
                    }
                }
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                // Denied / Expired / Cancelled: set cancelled and trigger automatic stock restocking!
                $this->orderService->updateOrderStatus($order, 'cancelled');
                if ($order->payment) {
                    $order->payment->update(['transaction_status' => $transactionStatus]);
                }
            } elseif ($transactionStatus === 'pending') {
                // Pending status
                $this->orderService->updateOrderStatus($order, 'pending');
                if ($order->payment) {
                    $order->payment->update(['transaction_status' => 'pending']);
                }
            }

            return response()->json(['message' => 'Notification callback handled successfully.']);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Execution Error', [
                'order' => $orderNumber,
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error handling notification callback.'], 500);
        }
    }

    /**
     * Webhook Landing Points: Successful payment redirect.
     */
    public function finish(Request $request): RedirectResponse
    {
        $order = Order::where('order_number', $request->query('order_id'))->first();
        $route = $order ? route('orders.show', $order->id) : route('orders.index');
        
        return redirect($route)->with('success', 'Your payment was completed successfully!');
    }

    /**
     * Webhook Landing Points: Pending payment redirect.
     */
    public function unfinish(Request $request): RedirectResponse
    {
        $order = Order::where('order_number', $request->query('order_id'))->first();
        $route = $order ? route('orders.show', $order->id) : route('orders.index');
        
        return redirect($route)->with('error', 'Your payment is pending or incomplete. Please finish the payment inside the QRIS popup.');
    }

    /**
     * Webhook Landing Points: Errored payment redirect.
     */
    public function error(Request $request): RedirectResponse
    {
        $order = Order::where('order_number', $request->query('order_id'))->first();
        $route = $order ? route('orders.show', $order->id) : route('orders.index');
        
        return redirect($route)->with('error', 'An error occurred during payment processing. Please try again.');
    }
}
