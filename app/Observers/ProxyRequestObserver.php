<?php

namespace App\Observers;

use App\Models\ProxyRequest;
use App\Services\BandwidthTrackingService;
use Illuminate\Support\Facades\Log;

class ProxyRequestObserver
{
    protected BandwidthTrackingService $bandwidthService;

    public function __construct(BandwidthTrackingService $bandwidthService)
    {
        $this->bandwidthService = $bandwidthService;
    }

    /**
     * Handle the ProxyRequest "created" event.
     *
     * This is triggered whenever a new proxy request is logged.
     * We track bandwidth usage and deduct from user's subscription.
     *
     * @param ProxyRequest $proxyRequest
     * @return void
     */
    public function created(ProxyRequest $proxyRequest): void
    {
        // Only track if bandwidth tracking is enabled
        if (!config('proxy.bandwidth.track_enabled', true)) {
            return;
        }

        // Only track if deduction on request is enabled
        if (!config('proxy.bandwidth.deduct_on_request', true)) {
            return;
        }

        try {
            $tracked = $this->bandwidthService->trackRequest($proxyRequest);

            if (!$tracked) {
                Log::debug('Bandwidth not tracked for request', [
                    'request_id' => $proxyRequest->id,
                    'username' => $proxyRequest->username,
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to track bandwidth for proxy request', [
                'request_id' => $proxyRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the ProxyRequest "updated" event.
     *
     * If bytes_in or bytes_out change, we may need to adjust bandwidth
     *
     * @param ProxyRequest $proxyRequest
     * @return void
     */
    public function updated(ProxyRequest $proxyRequest): void
    {
        // Check if bandwidth-related fields changed
        if ($proxyRequest->isDirty('bytes')) {
            Log::info('Proxy request bandwidth updated', [
                'request_id' => $proxyRequest->id,
                'old_bytes' => $proxyRequest->getOriginal('bytes'),
                'new_bytes' => $proxyRequest->bytes,
            ]);

            // TODO: Implement bandwidth adjustment logic if needed
            // For now, we only track on creation to avoid complications
        }
    }
}
