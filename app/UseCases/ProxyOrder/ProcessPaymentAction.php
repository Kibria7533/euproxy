<?php

namespace App\UseCases\ProxyOrder;

use App\Models\ProxyOrder;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPaymentAction
{
    /**
     * Process payment webhook and update order status
     *
     * @param ProxyOrder $order
     * @param array $webhookData Parsed webhook data from payment gateway
     * @return bool
     */
    public function execute(ProxyOrder $order, array $webhookData): bool
    {
        return DB::transaction(function () use ($order, $webhookData) {
            $status = $webhookData['status'];
            $transactionId = $webhookData['transaction_id'];
            $eventId = $webhookData['event_id'] ?? null;

            // Check for duplicate webhook processing
            if ($eventId && PaymentTransaction::where('webhook_id', $eventId)->exists()) {
                Log::info("Duplicate webhook detected", [
                    'event_id' => $eventId,
                    'order_id' => $order->id,
                ]);
                return false;
            }

            // Update order status based on payment status
            $orderStatus = match ($status) {
                'completed' => 'completed',
                'failed' => 'failed',
                'refunded' => 'refunded',
                default => 'pending',
            };

            $order->update([
                'payment_status' => $orderStatus,
                'payment_transaction_id' => $transactionId,
            ]);

            // Create payment transaction record
            PaymentTransaction::create([
                'user_id' => $order->user_id,
                'proxy_order_id' => $order->id,
                'amount' => $webhookData['amount'],
                'currency' => 'USD',
                'payment_gateway' => $webhookData['gateway'] ?? 'unknown',
                'transaction_id' => $transactionId,
                'webhook_id' => $eventId,
                'webhook_payload' => json_encode($webhookData['raw_data'] ?? []),
                'type' => 'charge',
                'status' => $status === 'completed' ? 'success' : ($status === 'failed' ? 'failed' : 'pending'),
            ]);

            Log::info("Payment processed", [
                'order_id' => $order->id,
                'status' => $orderStatus,
                'transaction_id' => $transactionId,
            ]);

            return true;
        });
    }
}
