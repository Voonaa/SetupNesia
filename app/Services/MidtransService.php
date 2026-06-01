<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapBaseUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key', '');
        $this->clientKey = config('services.midtrans.client_key', '');
        $this->isProduction = (bool) config('services.midtrans.is_production', false);
        
        $this->snapBaseUrl = $this->isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Request a payment Snap Token from Midtrans.
     *
     * @param \App\Models\Order $order
     * @return string
     * @throws \Exception
     */
    public function getSnapToken(Order $order): string
    {
        if (empty($this->serverKey)) {
            throw new \Exception("Midtrans Server Key is not configured. Please check your services.php config.");
        }

        // Formulate Item Details
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id' => (string) ($item->product_id ?? 'deleted'),
                'price' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name' => substr($item->product ? $item->product->name : 'N/A', 0, 50),
            ];
        }

        // Add Shipping Cost as an item detail if greater than 0
        if ($order->shipping_cost > 0) {
            $itemDetails[] = [
                'id' => 'shipping',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Shipping Cost',
            ];
        }

        // Formulate Payload
        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_price,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'enabled_payments' => ['qris'], // Direct restriction to QRIS as per spec
        ];

        // Send POST request utilizing Laravel's Http client
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
        ->withBasicAuth($this->serverKey, '')
        ->post($this->snapBaseUrl, $payload);

        if ($response->failed()) {
            Log::error('Midtrans API Request Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'order' => $order->order_number,
            ]);
            throw new \Exception("Midtrans Error: " . ($response->json('error_messages')[0] ?? 'Failed to communicate with payment gateway.'));
        }

        $token = $response->json('token');
        if (empty($token)) {
            throw new \Exception("Midtrans Error: Snap token not returned in response payload.");
        }

        return $token;
    }

    /**
     * Verify Midtrans Callback Signature.
     *
     * @param array $payload
     * @return bool
     */
    public function verifyCallbackSignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        if (empty($orderId) || empty($statusCode) || empty($grossAmount) || empty($signatureKey)) {
            return false;
        }

        // Verification Formula: hash('sha512', order_id + status_code + gross_amount + server_key)
        $calculatedHash = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return hash_equals($calculatedHash, $signatureKey);
    }
}
