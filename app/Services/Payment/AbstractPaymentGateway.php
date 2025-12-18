<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use App\Models\ProxyOrder;
use Illuminate\Support\Facades\Log;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    /**
     * Log payment activity
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        Log::channel('daily')->$level("[{$this->getName()}] {$message}", $context);
    }

    /**
     * Create a payment transaction record
     *
     * @param ProxyOrder $order
     * @param string $transactionId
     * @param string $status
     * @param array $webhookPayload
     * @return PaymentTransaction
     */
    protected function createTransaction(
        ProxyOrder $order,
        string $transactionId,
        string $status,
        array $webhookPayload = []
    ): PaymentTransaction {
        return PaymentTransaction::create([
            'user_id' => $order->user_id,
            'proxy_order_id' => $order->id,
            'amount' => $order->amount_paid,
            'currency' => 'USD',
            'payment_gateway' => strtolower($this->getName()),
            'transaction_id' => $transactionId,
            'webhook_id' => $webhookPayload['id'] ?? null,
            'webhook_payload' => json_encode($webhookPayload),
            'type' => 'charge',
            'status' => $status,
        ]);
    }

    /**
     * Update existing transaction record
     *
     * @param string $transactionId
     * @param string $status
     * @param array $additionalData
     * @return PaymentTransaction|null
     */
    protected function updateTransaction(
        string $transactionId,
        string $status,
        array $additionalData = []
    ): ?PaymentTransaction {
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();

        if ($transaction) {
            $transaction->update(array_merge([
                'status' => $status,
            ], $additionalData));
        }

        return $transaction;
    }

    /**
     * Check for duplicate webhook processing
     *
     * @param string $webhookId
     * @return bool True if already processed
     */
    protected function isDuplicateWebhook(string $webhookId): bool
    {
        return PaymentTransaction::where('webhook_id', $webhookId)->exists();
    }

    /**
     * Format amount for gateway (e.g., Stripe uses cents)
     *
     * @param float $amount
     * @return int
     */
    protected function formatAmount(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Parse amount from gateway format to decimal
     *
     * @param int $amount
     * @return float
     */
    protected function parseAmount(int $amount): float
    {
        return round($amount / 100, 2);
    }
}
