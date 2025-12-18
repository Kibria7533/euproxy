<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bandwidth Alert Thresholds
    |--------------------------------------------------------------------------
    |
    | Define at what percentage of bandwidth usage alerts should be sent
    | to users. These are cumulative - once alerted at a threshold,
    | no more alerts for that threshold will be sent.
    |
    */

    'bandwidth_alerts' => [
        'low' => 90,     // Alert at 90% usage
        'critical' => 95, // Alert at 95% usage
        'depleted' => 100, // Alert when fully depleted
    ],

    /*
    |--------------------------------------------------------------------------
    | Refund Policy Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the refund policy rules for proxy orders
    |
    */

    'refund_policy' => [
        // Full refund conditions
        'full_refund' => [
            'max_hours' => 24,           // Within 24 hours
            'max_usage_percentage' => 5,  // Less than 5% usage
        ],

        // Partial refund conditions
        'partial_refund' => [
            'max_days' => 7,              // Within 7 days
            // Amount = (remaining_bandwidth_percentage / 100) * original_price
        ],

        // No refund after
        'no_refund_after_days' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Auto-Renewal
    |--------------------------------------------------------------------------
    |
    | Configure auto-renewal behavior for subscriptions
    |
    */

    'auto_renewal' => [
        'enabled' => env('PROXY_AUTO_RENEWAL_ENABLED', false),
        'days_before_expiry' => 3,  // Attempt renewal 3 days before expiry
    ],

    /*
    |--------------------------------------------------------------------------
    | Bandwidth Tracking
    |--------------------------------------------------------------------------
    |
    | Configuration for bandwidth tracking and usage monitoring
    |
    */

    'bandwidth' => [
        'track_enabled' => true,
        'deduct_on_request' => true,  // Deduct bandwidth immediately on proxy request
        'suspend_on_depletion' => true, // Auto-suspend when bandwidth depleted
    ],

    /*
    |--------------------------------------------------------------------------
    | Proxy Request Limits
    |--------------------------------------------------------------------------
    |
    | Global rate limiting and request throttling
    |
    */

    'request_limits' => [
        'max_concurrent_requests' => 100,  // Per user
        'requests_per_second' => 10,       // Per user (can be overridden by plan features)
    ],

    /*
    |--------------------------------------------------------------------------
    | Expiry Check Schedule
    |--------------------------------------------------------------------------
    |
    | How often to check for expired subscriptions (in minutes)
    | This will be used by the scheduled command
    |
    */

    'expiry_check_interval' => 60, // Check every hour

    /*
    |--------------------------------------------------------------------------
    | Low Bandwidth Check Schedule
    |--------------------------------------------------------------------------
    |
    | How often to check for low bandwidth and send alerts (in minutes)
    |
    */

    'low_bandwidth_check_interval' => 120, // Check every 2 hours

];
