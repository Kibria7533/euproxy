<?php

namespace App\UseCases\ProxyOrder;

use App\Models\ProxyOrder;
use App\Models\ProxySubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FulfillOrderAction
{
    /**
     * Fulfill a completed order by creating subscription
     *
     * @param ProxyOrder $order
     * @param bool $autoCreateSquidUser Whether to auto-create SquidUser entries
     * @return ProxySubscription
     */
    public function execute(ProxyOrder $order, bool $autoCreateSquidUser = false): ProxySubscription
    {
        if ($order->payment_status !== 'completed') {
            throw new \Exception("Cannot fulfill order #{$order->id} with payment status: {$order->payment_status}");
        }

        if ($order->subscription()->exists()) {
            throw new \Exception("Order #{$order->id} already has a subscription");
        }

        return DB::transaction(function () use ($order, $autoCreateSquidUser) {
            $plan = $order->proxyPlan;

            // Calculate expiry date if validity_days is set
            $expiresAt = $plan->validity_days
                ? now()->addDays($plan->validity_days)
                : null;

            // Convert GB to bytes for precise tracking
            $bandwidthBytes = (int) round($order->bandwidth_gb * 1073741824); // 1 GB = 1073741824 bytes

            // Create subscription
            $subscription = ProxySubscription::create([
                'user_id' => $order->user_id,
                'proxy_order_id' => $order->id,
                'proxy_type_id' => $order->proxy_type_id,
                'bandwidth_total_gb' => $order->bandwidth_gb,
                'bandwidth_remaining_bytes' => $bandwidthBytes,
                'bandwidth_used_gb' => 0,
                'status' => 'active',
                'auto_renew' => $plan->is_renewable ? 'disabled' : 'disabled', // Default to disabled
                'started_at' => now(),
                'expires_at' => $expiresAt,
                'notify_low_bandwidth' => true,
            ]);

            Log::info("Order fulfilled - Subscription created", [
                'order_id' => $order->id,
                'subscription_id' => $subscription->id,
                'bandwidth_gb' => $order->bandwidth_gb,
                'expires_at' => $expiresAt,
            ]);

            // Optionally auto-create SquidUser entries
            if ($autoCreateSquidUser) {
                $this->createSquidUserForSubscription($subscription);
            }

            return $subscription;
        });
    }

    /**
     * Optionally create SquidUser entries for the subscription
     *
     * @param ProxySubscription $subscription
     * @return void
     */
    protected function createSquidUserForSubscription(ProxySubscription $subscription): void
    {
        // This is optional - you can implement auto-creation of SquidUser entries
        // based on the subscription. For now, we'll leave this as a placeholder.
        // Users can manually create SquidUser entries via the UI and link them to subscriptions.

        Log::info("Auto-create SquidUser triggered (not implemented)", [
            'subscription_id' => $subscription->id,
        ]);
    }
}
