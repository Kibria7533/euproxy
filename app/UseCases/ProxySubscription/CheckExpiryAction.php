<?php

namespace App\UseCases\ProxySubscription;

use App\Services\BandwidthTrackingService;
use Illuminate\Support\Facades\Log;

class CheckExpiryAction
{
    protected BandwidthTrackingService $bandwidthService;

    public function __construct(BandwidthTrackingService $bandwidthService)
    {
        $this->bandwidthService = $bandwidthService;
    }

    /**
     * Check for expired subscriptions and mark them accordingly
     *
     * @return array
     */
    public function execute(): array
    {
        $expiredSubscriptions = $this->bandwidthService->findExpiredSubscriptions();

        $results = [
            'checked' => $expiredSubscriptions->count(),
            'expired' => 0,
            'expired_with_balance' => 0,
        ];

        foreach ($expiredSubscriptions as $subscription) {
            $hadBalance = $subscription->bandwidth_remaining_bytes > 0;

            // Mark as expired
            $this->bandwidthService->markAsExpired($subscription);

            if ($hadBalance) {
                $results['expired_with_balance']++;

                Log::info('Subscription expired with remaining bandwidth', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'remaining_gb' => $subscription->bandwidth_remaining_gb,
                ]);

                // TODO: Send email notification about expired subscription with balance
                // event(new SubscriptionExpiredWithBalanceEvent($subscription));
            } else {
                $results['expired']++;

                Log::info('Subscription expired', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                ]);

                // TODO: Send email notification about expired subscription
                // event(new SubscriptionExpiredEvent($subscription));
            }
        }

        return $results;
    }
}
