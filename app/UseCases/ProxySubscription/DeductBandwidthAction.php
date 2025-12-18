<?php

namespace App\UseCases\ProxySubscription;

use App\Models\ProxySubscription;
use App\Models\ProxySubscriptionUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeductBandwidthAction
{
    /**
     * Deduct bandwidth from subscription
     *
     * @param ProxySubscription $subscription
     * @param int $bytes
     * @param int|null $proxyRequestId
     * @return bool
     */
    public function execute(ProxySubscription $subscription, int $bytes, ?int $proxyRequestId = null): bool
    {
        if ($subscription->status !== 'active') {
            Log::warning('Attempted to deduct bandwidth from inactive subscription', [
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
            ]);
            return false;
        }

        return DB::transaction(function () use ($subscription, $bytes, $proxyRequestId) {
            // Deduct bandwidth
            $subscription->deductBandwidth($bytes);

            // Create usage record if proxy request ID provided
            if ($proxyRequestId) {
                ProxySubscriptionUsage::create([
                    'proxy_subscription_id' => $subscription->id,
                    'proxy_request_id' => $proxyRequestId,
                    'bytes_consumed' => $bytes,
                    'consumed_at' => now(),
                ]);
            }

            // Check if subscription is depleted
            if ($subscription->bandwidth_remaining_bytes <= 0) {
                $this->handleDepletedSubscription($subscription);
            }

            // Check for low bandwidth alerts
            $this->checkLowBandwidthAlerts($subscription);

            return true;
        });
    }

    /**
     * Handle depleted subscription
     *
     * @param ProxySubscription $subscription
     * @return void
     */
    protected function handleDepletedSubscription(ProxySubscription $subscription): void
    {
        Log::info('Subscription depleted', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
        ]);

        // TODO: Send email notification to user
        // event(new SubscriptionDepletedEvent($subscription));
    }

    /**
     * Check and send low bandwidth alerts
     *
     * @param ProxySubscription $subscription
     * @return void
     */
    protected function checkLowBandwidthAlerts(ProxySubscription $subscription): void
    {
        $usagePercentage = $subscription->bandwidth_usage_percentage;
        $thresholds = config('proxy.bandwidth_alerts', [
            'low' => 90,
            'critical' => 95,
        ]);

        // Alert at 90% usage
        if ($usagePercentage >= $thresholds['low'] && $usagePercentage < $thresholds['critical']) {
            Log::info('Low bandwidth alert (90%)', [
                'subscription_id' => $subscription->id,
                'usage_percentage' => $usagePercentage,
            ]);
            // TODO: Send alert email
            // event(new LowBandwidthAlertEvent($subscription, 90));
        }

        // Alert at 95% usage
        if ($usagePercentage >= $thresholds['critical']) {
            Log::info('Critical bandwidth alert (95%)', [
                'subscription_id' => $subscription->id,
                'usage_percentage' => $usagePercentage,
            ]);
            // TODO: Send critical alert email
            // event(new CriticalBandwidthAlertEvent($subscription, 95));
        }
    }
}
