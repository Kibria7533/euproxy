<div class="row">
    @forelse($subscriptions as $subscription)
        <div class="col-lg-6 mb-4">
            <div class="card h-100" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #1e293b;">{{ $subscription->order->proxyPlan->name }}</h5>
                            <small class="text-muted">Order #{{ $subscription->order->invoice_number }}</small>
                        </div>
                        <div>
                            @if($subscription->status === 'active')
                                <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background-color: #d1fae5; color: #065f46;">
                                    Active
                                </span>
                            @elseif($subscription->status === 'depleted')
                                <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background-color: #fee2e2; color: #991b1b;">
                                    Depleted
                                </span>
                            @elseif($subscription->status === 'expired')
                                <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background-color: #fef3c7; color: #92400e;">
                                    Expired
                                </span>
                            @else
                                <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background-color: #f1f5f9; color: #64748b;">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Bandwidth Usage -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold" style="color: #475569;">Bandwidth Usage</span>
                            <span class="fw-bold" style="color: #3b82f6;">{{ number_format($subscription->bandwidth_usage_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 12px; border-radius: 6px; background-color: #f1f5f9;">
                            <div class="progress-bar" role="progressbar"
                                 style="width: {{ min($subscription->bandwidth_usage_percentage, 100) }}%;
                                        background: {{ $subscription->bandwidth_usage_percentage >= 95 ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : ($subscription->bandwidth_usage_percentage >= 90 ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' : 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)') }};
                                        border-radius: 6px;"
                                 aria-valuenow="{{ $subscription->bandwidth_usage_percentage }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">Used: {{ number_format($subscription->bandwidth_used_gb, 2) }} GB</small>
                            <small class="text-muted">Total: {{ number_format($subscription->bandwidth_total_gb, 2) }} GB</small>
                        </div>
                        <div class="mt-1">
                            <small class="fw-semibold" style="color: #059669;">Remaining: {{ number_format($subscription->bandwidth_remaining_gb, 2) }} GB</small>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3 rounded" style="background-color: #f8fafc;">
                                <small class="text-muted d-block mb-1">Started</small>
                                <span class="fw-semibold" style="color: #475569; font-size: 0.9rem;">{{ $subscription->started_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        @if($subscription->expires_at)
                        <div class="col-6">
                            <div class="p-3 rounded" style="background-color: #f8fafc;">
                                <small class="text-muted d-block mb-1">Expires</small>
                                <span class="fw-semibold" style="color: #475569; font-size: 0.9rem;">{{ $subscription->expires_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="col-6">
                            <div class="p-3 rounded" style="background-color: #f8fafc;">
                                <small class="text-muted d-block mb-1">Amount Paid</small>
                                <span class="fw-bold" style="color: #059669; font-size: 0.9rem;">${{ number_format($subscription->order->amount_paid, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background-color: #f8fafc;">
                                <small class="text-muted d-block mb-1">Last Used</small>
                                <span class="fw-semibold" style="color: #475569; font-size: 0.9rem;">
                                    {{ $subscription->last_used_at ? $subscription->last_used_at->diffForHumans() : 'Never' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts -->
                    @if($subscription->status === 'active' && $subscription->bandwidth_usage_percentage >= 90)
                        <div class="alert alert-warning mb-3" style="border-radius: 10px; border: none; background-color: #fef3c7; color: #92400e; padding: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <strong>Low Bandwidth!</strong> You've used {{ number_format($subscription->bandwidth_usage_percentage, 1) }}% of your allocation.
                        </div>
                    @endif

                    @if($subscription->status === 'depleted')
                        <div class="alert alert-danger mb-3" style="border-radius: 10px; border: none; background-color: #fee2e2; color: #991b1b; padding: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                            <strong>Bandwidth Depleted!</strong> Purchase a new plan to continue using the service.
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        @if($subscription->status === 'depleted' || $subscription->status === 'expired')
                            <a href="{{ route('user.proxies.buy', $proxyType->slug) }}" class="btn btn-primary flex-grow-1" style="border-radius: 8px; font-weight: 500;">
                                Renew Plan
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px; font-weight: 500;" onclick="alert('View details coming soon')">
                            Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card" style="border-radius: 16px; border: 2px dashed #cbd5e1; background-color: #f8fafc;">
                <div class="card-body p-5 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="color: #cbd5e1; margin-bottom: 1rem;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    <h5 class="fw-bold mb-2" style="color: #475569;">No Active Subscriptions</h5>
                    <p class="text-muted mb-4">You don't have any subscriptions for {{ $proxyType->name }} yet. Purchase a plan to get started!</p>
                    <a href="{{ route('user.proxies.buy', $proxyType->slug) }}" class="btn btn-primary" style="border-radius: 10px; padding: 12px 32px; font-weight: 500;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        Browse Plans
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($subscriptions->count() > 0)
<!-- Usage Summary -->
<div class="card mt-4" style="border-radius: 16px; border: 1px solid #e2e8f0; background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4" style="color: #1e293b;">Usage Summary</h5>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <div class="h2 fw-bold mb-1" style="color: #3b82f6;">{{ $subscriptions->where('status', 'active')->count() }}</div>
                    <div class="text-muted small">Active Subscriptions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="h2 fw-bold mb-1" style="color: #059669;">{{ number_format($subscriptions->sum('bandwidth_total_gb'), 2) }} GB</div>
                    <div class="text-muted small">Total Bandwidth</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="h2 fw-bold mb-1" style="color: #ef4444;">{{ number_format($subscriptions->sum('bandwidth_used_gb'), 2) }} GB</div>
                    <div class="text-muted small">Bandwidth Used</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="h2 fw-bold mb-1" style="color: #10b981;">{{ number_format($subscriptions->where('status', 'active')->sum('bandwidth_remaining_gb'), 2) }} GB</div>
                    <div class="text-muted small">Remaining</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
