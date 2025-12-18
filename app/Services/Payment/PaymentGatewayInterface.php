<?php

namespace App\Services\Payment;

use App\Models\ProxyOrder;

interface PaymentGatewayInterface
{
    /**
     * Create a payment intent/session for the order
     *
     * @param ProxyOrder $order
     * @param array $options Additional gateway-specific options
     * @return array ['session_id' => string, 'redirect_url' => string, 'client_secret' => string|null]
     */
    public function createPaymentIntent(ProxyOrder $order, array $options = []): array;

    /**
     * Capture/complete a pending payment
     *
     * @param string $transactionId
     * @param float $amount
     * @return array ['success' => bool, 'transaction_id' => string, 'message' => string]
     */
    public function capturePayment(string $transactionId, float $amount): array;

    /**
     * Process a refund for a completed payment
     *
     * @param string $transactionId
     * @param float $amount
     * @param string|null $reason
     * @return array ['success' => bool, 'refund_id' => string, 'message' => string]
     */
    public function refundPayment(string $transactionId, float $amount, ?string $reason = null): array;

    /**
     * Verify webhook signature to ensure authenticity
     *
     * @param string $payload Raw webhook payload
     * @param string $signature Webhook signature header
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool;

    /**
     * Parse webhook payload into standardized format
     *
     * @param string $payload Raw webhook payload
     * @return array ['event_type' => string, 'transaction_id' => string, 'status' => string, 'amount' => float, 'metadata' => array]
     */
    public function parseWebhookPayload(string $payload): array;

    /**
     * Get payment gateway name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Check if gateway is in test mode
     *
     * @return bool
     */
    public function isTestMode(): bool;
}
