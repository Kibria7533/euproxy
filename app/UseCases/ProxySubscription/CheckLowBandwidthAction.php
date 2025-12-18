<?php

namespace App\UseCases\ProxySubscription;

use App\Services\BandwidthTrackingService;
use Illuminate\Support\Facades\Log;

class CheckLowBandwidthAction
{
    protected BandwidthTrackingService $bandwidthService;

    public function __construct(BandwidthTrackingService $bandwidthService)
    {
        $this->bandwidthService = $bandwidthService;
    }

    /**
     * Check for subscriptions with low bandwidth and send alerts
     *
     * @return array
     */
    public function execute(): array
    {
        $thresholds = config('proxy.bandwidth_alerts', [
            'low' => 90,
            'critical' => 95,
        ]);

        $results = [
            'checked' => 0,
            'alerted_90' => 0,
            'alerted_95' => 0,
        ];

        // Check 90% threshold
        $subscriptions90 = $this->bandwidthService->findLowBandwidthSubscriptions($thresholds['low']);
        $results['checked'] += $subscriptions90->count();

        foreach ($subscriptions90 as $subscription) {
            $usagePercentage = $subscription->bandwidth_usage_percentage;

            // Only alert between 90-94.99%
            if ($usagePercentage >= $thresholds['low'] && $usagePercentage < $thresholds['critical']) {
                Log::info('Low bandwidth alert sent', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'usage_percentage' => $usagePercentage,
                    'remaining_gb' => $subscription->bandwidth_remaining_gb,
                ]);

                // TODO: Send email notification
                // event(new LowBandwidthAlertEvent($subscription, $thresholds['low']));

                $results['alerted_90']++;
            }
        }

        // Check 95% threshold
        $subscriptions95 = $this->bandwidthService->findLowBandwidthSubscriptions($thresholds['critical']);

        foreach ($subscriptions95 as $subscription) {
            $usagePercentage = $subscription->bandwidth_usage_percentage;

            // Only alert at 95% or higher
            if ($usagePercentage >= $thresholds['critical']) {
                Log::info('Critical bandwidth alert sent', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'usage_percentage' => $usagePercentage,
                    'remaining_gb' => $subscription->bandwidth_remaining_gb,
                ]);

                // TODO: Send critical email notification
                // event(new CriticalBandwidthAlertEvent($subscription, $thresholds['critical']));

                $results['alerted_95']++;
            }
        }

        return $results;
    }
}
