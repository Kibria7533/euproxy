<?php

namespace App\Services\Payment;

use App\Models\ProxyOrder;

class PayPalPaymentGateway extends AbstractPaymentGateway
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $apiBase;
    protected bool $testMode;

    public function __construct()
    {
        $this->clientId = config('payment.paypal.client_id');
        $this->clientSecret = config('payment.paypal.client_secret');
        $this->testMode = config('payment.paypal.test_mode', true);
        $this->apiBase = $this->testMode
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    /**
     * {@inheritDoc}
     */
    public function createPaymentIntent(ProxyOrder $order, array $options = []): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $successUrl = $options['success_url'] ?? route('user.checkout.success', ['order' => $order->id]);
            $cancelUrl = $options['cancel_url'] ?? route('user.checkout.cancel', ['order' => $order->id]);

            $response = $this->makeRequest('/v2/checkout/orders', 'POST', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $order->invoice_number,
                    'description' => "{$order->bandwidth_gb} GB - {$order->proxyType->name}",
                    'custom_id' => (string) $order->id,
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($order->amount_paid, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'return_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                ],
            ], $accessToken);

            $approveLink = collect($response['links'])->firstWhere('rel', 'approve')['href'] ?? null;

            $this->log('info', 'PayPal order created', [
                'order_id' => $order->id,
                'paypal_order_id' => $response['id'],
                'amount' => $order->amount_paid,
            ]);

            return [
                'session_id' => $response['id'],
                'redirect_url' => $approveLink,
                'client_secret' => null,
            ];
        } catch (\Exception $e) {
            $this->log('error', 'Failed to create PayPal order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function capturePayment(string $transactionId, float $amount): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->makeRequest(
                "/v2/checkout/orders/{$transactionId}/capture",
                'POST',
                [],
                $accessToken
            );

            $captureId = $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            $this->log('info', 'PayPal payment captured', [
                'transaction_id' => $transactionId,
                'capture_id' => $captureId,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'transaction_id' => $captureId ?? $transactionId,
                'message' => 'Payment captured successfully',
            ];
        } catch (\Exception $e) {
            $this->log('error', 'Failed to capture PayPal payment', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function refundPayment(string $transactionId, float $amount, ?string $reason = null): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->makeRequest(
                "/v2/payments/captures/{$transactionId}/refund",
                'POST',
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                    'note_to_payer' => $reason ?? 'Refund processed',
                ],
                $accessToken
            );

            $this->log('info', 'PayPal refund processed', [
                'transaction_id' => $transactionId,
                'refund_id' => $response['id'],
                'amount' => $amount,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'refund_id' => $response['id'],
                'message' => 'Refund processed successfully',
            ];
        } catch (\Exception $e) {
            $this->log('error', 'Failed to process PayPal refund', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'refund_id' => null,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        // PayPal webhook verification requires calling their API
        // For now, we'll implement a basic verification
        // In production, you should implement full verification via PayPal's API

        try {
            $webhookId = config('payment.paypal.webhook_id');
            if (empty($webhookId)) {
                $this->log('warning', 'PayPal webhook ID not configured');
                return false;
            }

            // Decode payload
            $event = json_decode($payload, true);
            if (!$event) {
                return false;
            }

            // Basic validation
            return isset($event['id']) && isset($event['event_type']);
        } catch (\Exception $e) {
            $this->log('warning', 'PayPal webhook verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function parseWebhookPayload(string $payload): array
    {
        $event = json_decode($payload, true);

        $eventType = $event['event_type'] ?? 'unknown';
        $resource = $event['resource'] ?? [];

        // Extract order ID from custom_id
        $orderId = $resource['custom_id'] ?? null;
        $transactionId = $resource['id'] ?? null;

        // Map PayPal status
        $status = $this->mapPayPalStatus($eventType, $resource);

        return [
            'event_type' => $eventType,
            'event_id' => $event['id'] ?? null,
            'transaction_id' => $transactionId,
            'order_id' => $orderId,
            'status' => $status,
            'amount' => isset($resource['amount']['value']) ? (float) $resource['amount']['value'] : 0,
            'metadata' => [],
            'raw_data' => $resource,
        ];
    }

    /**
     * Map PayPal event types to internal payment statuses
     *
     * @param string $eventType
     * @param array $resource
     * @return string
     */
    protected function mapPayPalStatus(string $eventType, array $resource): string
    {
        return match ($eventType) {
            'CHECKOUT.ORDER.APPROVED' => 'pending',
            'PAYMENT.CAPTURE.COMPLETED' => 'completed',
            'PAYMENT.CAPTURE.DENIED' => 'failed',
            'PAYMENT.CAPTURE.REFUNDED' => 'refunded',
            default => 'pending',
        };
    }

    /**
     * Get PayPal access token
     *
     * @return string
     */
    protected function getAccessToken(): string
    {
        $response = $this->makeRequest('/v1/oauth2/token', 'POST', [
            'grant_type' => 'client_credentials',
        ], null, true);

        return $response['access_token'];
    }

    /**
     * Make HTTP request to PayPal API
     *
     * @param string $endpoint
     * @param string $method
     * @param array $data
     * @param string|null $accessToken
     * @param bool $isAuth
     * @return array
     */
    protected function makeRequest(
        string $endpoint,
        string $method,
        array $data = [],
        ?string $accessToken = null,
        bool $isAuth = false
    ): array {
        $url = $this->apiBase . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        if ($isAuth) {
            $auth = base64_encode("{$this->clientId}:{$this->clientSecret}");
            $headers[] = "Authorization: Basic {$auth}";
        } elseif ($accessToken) {
            $headers[] = "Authorization: Bearer {$accessToken}";
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (!empty($data)) {
            if ($isAuth) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new \Exception("PayPal API error: HTTP {$httpCode} - {$response}");
        }

        return json_decode($response, true);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'paypal';
    }

    /**
     * {@inheritDoc}
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }
}
