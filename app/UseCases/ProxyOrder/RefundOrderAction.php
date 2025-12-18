<?php

namespace App\UseCases\ProxyOrder;

use App\Models\ProxyOrder;
use App\Models\User;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundOrderAction
{
    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Process refund for an order based on policy
     *
     * Refund Policy:
     * - Full refund: < 24 hours + < 5% usage
     * - Partial refund: Remaining bandwidth % × Original price (if < 7 days)
     * - No refund: > 7 days
     *
     * @param ProxyOrder $order
     * @param User $refundedBy Admin processing the refund
     * @param string|null $reason
     * @return array ['success' => bool, 'amount' => float, 'message' => string]
     */
    public function execute(ProxyOrder $order, User $refundedBy, ?string $reason = null): array
    {
        if ($order->payment_status !== 'completed') {
            return [
                'success' => false,
                'amount' => 0,
                'message' => 'Only completed orders can be refunded',
            ];
        }

        if ($order->payment_status === 'refunded') {
            return [
                'success' => false,
                'amount' => 0,
                'message' => 'Order already refunded',
            ];
        }

        $subscription = $order->subscription;
        if (!$subscription) {
            return [
                'success' => false,
                'amount' => 0,
                'message' => 'No subscription found for this order',
            ];
        }

        // Calculate refund amount based on policy
        $refundCalculation = $this->calculateRefundAmount($order, $subscription);

        if (!$refundCalculation['eligible']) {
            return [
                'success' => false,
                'amount' => 0,
                'message' => $refundCalculation['reason'],
            ];
        }

        return DB::transaction(function () use ($order, $subscription, $refundCalculation, $refundedBy, $reason) {
            $refundAmount = $refundCalculation['amount'];

            // Process refund through payment gateway
            $result = $this->paymentGateway->refundPayment(
                $order->payment_transaction_id,
                $refundAmount,
                $reason
            );

            if (!$result['success']) {
                return [
                    'success' => false,
                    'amount' => 0,
                    'message' => "Gateway error: {$result['message']}",
                ];
            }

            // Update order
            $order->update([
                'payment_status' => 'refunded',
                'refunded_by' => $refundedBy->id,
                'refunded_at' => now(),
                'refund_amount' => $refundAmount,
                'refund_reason' => $reason ?? $refundCalculation['type'],
            ]);

            // Cancel subscription
            $subscription->update([
                'status' => 'cancelled',
                'suspended_at' => now(),
                'suspension_reason' => "Order refunded: {$reason}",
            ]);

            Log::info("Order refunded", [
                'order_id' => $order->id,
                'refund_amount' => $refundAmount,
                'refund_type' => $refundCalculation['type'],
                'refunded_by' => $refundedBy->id,
            ]);

            return [
                'success' => true,
                'amount' => $refundAmount,
                'message' => "Refund processed: $" . number_format($refundAmount, 2) . " ({$refundCalculation['type']})",
            ];
        });
    }

    /**
     * Calculate refund amount based on policy
     *
     * @param ProxyOrder $order
     * @param \App\Models\ProxySubscription $subscription
     * @return array
     */
    protected function calculateRefundAmount(ProxyOrder $order, $subscription): array
    {
        $hoursSincePurchase = $order->created_at->diffInHours(now());
        $daysSincePurchase = $order->created_at->diffInDays(now());

        // Calculate usage percentage
        $usagePercentage = $subscription->bandwidth_usage_percentage;

        // Full refund: < 24 hours + < 5% usage
        if ($hoursSincePurchase < 24 && $usagePercentage < 5) {
            return [
                'eligible' => true,
                'amount' => $order->amount_paid,
                'type' => 'full',
                'reason' => 'Full refund (< 24 hours, < 5% usage)',
            ];
        }

        // No refund: > 7 days
        if ($daysSincePurchase > 7) {
            return [
                'eligible' => false,
                'amount' => 0,
                'type' => 'none',
                'reason' => 'Refund not available after 7 days',
            ];
        }

        // Partial refund: Remaining bandwidth % × Original price
        $remainingPercentage = 100 - $usagePercentage;
        $partialRefundAmount = ($remainingPercentage / 100) * $order->amount_paid;

        return [
            'eligible' => true,
            'amount' => round($partialRefundAmount, 2),
            'type' => 'partial',
            'reason' => "Partial refund ({$remainingPercentage}% bandwidth remaining)",
        ];
    }
}
