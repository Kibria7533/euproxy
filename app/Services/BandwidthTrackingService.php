<?php

namespace App\Services;

use App\Models\ProxyRequest;
use App\Models\ProxySubscription;
use App\Models\ProxySubscriptionUsage;
use App\Models\SquidUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BandwidthTrackingService
{
    /**
     * Track bandwidth usage for a proxy request
     *
     * @param ProxyRequest $request
     * @return bool
     */
    public function trackRequest(ProxyRequest $request): bool
    {
        // Get SquidUser associated with this request via username
        $squidUser = SquidUser::where('user', $request->username)->first();

        if (!$squidUser || !$squidUser->proxy_subscription_id) {
            // No subscription linked to this SquidUser
            Log::warning('Proxy request without subscription', [
                'request_id' => $request->id,
                'username' => $request->username,
            ]);
            return false;
        }

        // Find active subscription
        $subscription = ProxySubscription::where('id', $squidUser->proxy_subscription_id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            Log::warning('Subscription not active', [
                'subscription_id' => $squidUser->proxy_subscription_id,
                'request_id' => $request->id,
            ]);
            return false;
        }

        // Check if this request was already tracked
        $existingUsage = ProxySubscriptionUsage::where('proxy_request_id', $request->id)->first();
        if ($existingUsage) {
            Log::info('Request already tracked', ['request_id' => $request->id]);
            return true;
        }

        return DB::transaction(function () use ($request, $subscription) {
            // Get bytes consumed from request
            $bytesConsumed = $request->bytes ?? 0;

            // Deduct bandwidth from subscription
            $subscription->deductBandwidth($bytesConsumed);

            // Create usage record
            ProxySubscriptionUsage::create([
                'proxy_subscription_id' => $subscription->id,
                'proxy_request_id' => $request->id,
                'bytes_consumed' => $bytesConsumed,
                'consumed_at' => $request->ts ? \Carbon\Carbon::createFromTimestamp($request->ts) : now(),
            ]);

            Log::info('Bandwidth tracked', [
                'request_id' => $request->id,
                'subscription_id' => $subscription->id,
                'bytes_consumed' => $bytesConsumed,
                'remaining_gb' => $subscription->bandwidth_remaining_gb,
            ]);

            return true;
        });
    }

    /**
     * Get active subscription for user by proxy type
     *
     * @param int $userId
     * @param int $proxyTypeId
     * @return ProxySubscription|null
     */
    public function getActiveSubscription(int $userId, int $proxyTypeId): ?ProxySubscription
    {
        return ProxySubscription::where('user_id', $userId)
            ->where('proxy_type_id', $proxyTypeId)
            ->where('status', 'active')
            ->where('bandwidth_remaining_bytes', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('bandwidth_remaining_bytes', 'desc') // Use subscription with most bandwidth first
            ->first();
    }

    /**
     * Check if user has sufficient bandwidth
     *
     * @param int $userId
     * @param int $proxyTypeId
     * @param int $requiredBytes
     * @return bool
     */
    public function hasSufficientBandwidth(int $userId, int $proxyTypeId, int $requiredBytes): bool
    {
        $subscription = $this->getActiveSubscription($userId, $proxyTypeId);

        if (!$subscription) {
            return false;
        }

        return $subscription->bandwidth_remaining_bytes >= $requiredBytes;
    }

    /**
     * Get bandwidth usage summary for user
     *
     * @param int $userId
     * @return array
     */
    public function getUserBandwidthSummary(int $userId): array
    {
        $subscriptions = ProxySubscription::where('user_id', $userId)->get();

        return [
            'total_bandwidth_gb' => $subscriptions->sum('bandwidth_total_gb'),
            'used_bandwidth_gb' => $subscriptions->sum('bandwidth_used_gb'),
            'remaining_bandwidth_gb' => $subscriptions->where('status', 'active')->sum('bandwidth_remaining_gb'),
            'active_subscriptions' => $subscriptions->where('status', 'active')->count(),
            'depleted_subscriptions' => $subscriptions->where('status', 'depleted')->count(),
        ];
    }

    /**
     * Find subscriptions with low bandwidth
     *
     * @param float $threshold Percentage threshold (e.g., 90 for 90%)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findLowBandwidthSubscriptions(float $threshold = 90)
    {
        return ProxySubscription::where('status', 'active')
            ->where('notify_low_bandwidth', true)
            ->get()
            ->filter(function ($subscription) use ($threshold) {
                return $subscription->bandwidth_usage_percentage >= $threshold;
            });
    }

    /**
     * Find expired subscriptions
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findExpiredSubscriptions()
    {
        return ProxySubscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();
    }

    /**
     * Mark subscription as expired
     *
     * @param ProxySubscription $subscription
     * @return void
     */
    public function markAsExpired(ProxySubscription $subscription): void
    {
        $hasRemainingBandwidth = $subscription->bandwidth_remaining_bytes > 0;

        $subscription->update([
            'status' => $hasRemainingBandwidth ? 'expired_with_balance' : 'expired',
            'suspended_at' => now(),
            'suspension_reason' => 'Subscription expired',
        ]);

        Log::info('Subscription marked as expired', [
            'subscription_id' => $subscription->id,
            'had_remaining_bandwidth' => $hasRemainingBandwidth,
            'remaining_gb' => $subscription->bandwidth_remaining_gb,
        ]);
    }
}
