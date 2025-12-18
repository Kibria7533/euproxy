<div class="row">
    @forelse($plans as $plan)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100" style="border-radius: 16px; border: 2px solid {{ $plan->is_popular ? '#3b82f6' : '#e2e8f0' }}; transition: transform 0.2s, box-shadow 0.2s; position: relative; overflow: visible;">
                @if($plan->is_popular)
                    <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 6px 20px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);">
                        Most Popular
                    </div>
                @endif

                @if($plan->is_free_trial)
                    <div style="position: absolute; top: -12px; right: 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 6px 16px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">
                        Free Trial
                    </div>
                @endif

                <div class="card-body p-4" style="padding-top: {{ $plan->is_popular || $plan->is_free_trial ? '2rem' : '1.5rem' }} !important;">
                    <!-- Plan Name -->
                    <h5 class="fw-bold mb-3" style="color: #1e293b;">{{ $plan->name }}</h5>

                    <!-- Bandwidth -->
                    <div class="mb-3">
                        <div class="d-flex align-items-baseline">
                            <span class="display-4 fw-bold" style="color: #3b82f6; line-height: 1;">{{ number_format($plan->bandwidth_gb, 0) }}</span>
                            <span class="h5 ms-2 text-muted">GB</span>
                        </div>
                        <div class="text-muted small">Bandwidth</div>
                    </div>

                    <!-- Pricing -->
                    <div class="mb-4">
                        @if($plan->discount_percentage > 0)
                            <div class="mb-1">
                                <span class="text-muted" style="text-decoration: line-through; font-size: 1rem;">${{ number_format($plan->base_price, 2) }}</span>
                                <span class="badge bg-danger ms-2" style="font-size: 0.75rem; padding: 4px 8px;">-{{ $plan->discount_percentage }}% OFF</span>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <span class="h3 fw-bold mb-0" style="color: #059669;">${{ number_format($plan->final_price, 2) }}</span>
                                <span class="text-muted ms-2 small">one-time</span>
                            </div>
                        @else
                            <div class="d-flex align-items-baseline">
                                <span class="h3 fw-bold mb-0" style="color: #1e293b;">${{ number_format($plan->base_price, 2) }}</span>
                                <span class="text-muted ms-2 small">one-time</span>
                            </div>
                        @endif
                        <div class="text-muted small mt-1">${{ number_format($plan->price_per_gb, 2) }} per GB</div>
                    </div>

                    <!-- Features -->
                    @if($plan->features->count() > 0)
                        <div class="mb-4">
                            <ul class="list-unstyled mb-0">
                                @foreach($plan->features->take(5) as $feature)
                                    <li class="mb-2 d-flex align-items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981; margin-right: 8px; flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <span style="font-size: 0.9rem; color: #475569;">{{ $feature->display_label }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Validity -->
                    @if($plan->validity_days)
                        <div class="mb-3 p-2 rounded" style="background-color: #f1f5f9;">
                            <small class="text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Valid for {{ $plan->validity_days }} days
                            </small>
                        </div>
                    @endif

                    <!-- Buy Button -->
                    <a href="{{ route('user.checkout.review', $plan->id) }}" class="btn btn-primary w-100 fw-semibold" style="border-radius: 10px; padding: 12px; background: {{ $plan->is_popular ? 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)' : '#3b82f6' }}; border: none; transition: transform 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        Buy Now
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info" style="border-radius: 12px; border: none; background-color: #e0f2fe; color: #0369a1;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                No plans available for this proxy type at the moment.
            </div>
        </div>
    @endforelse
</div>

@if($plans->count() > 0)
<!-- Feature Comparison Table -->
<div class="card mt-5" style="border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
    <div class="card-body p-4">
        <h4 class="fw-bold mb-4" style="color: #1e293b;">Plan Comparison</h4>
        <div class="table-responsive">
            <table class="table table-borderless" style="border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background-color: #f8fafc; border-radius: 8px;">
                        <th style="padding: 16px; color: #475569; font-weight: 600;">Feature</th>
                        @foreach($plans->take(5) as $plan)
                            <th class="text-center" style="padding: 16px; color: #475569; font-weight: 600;">
                                {{ $plan->name }}
                                @if($plan->is_popular)
                                    <span class="badge bg-primary ms-1" style="font-size: 0.65rem;">Popular</span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 16px; font-weight: 500; color: #64748b;">Bandwidth</td>
                        @foreach($plans->take(5) as $plan)
                            <td class="text-center" style="padding: 16px;">
                                <span class="fw-bold" style="color: #3b82f6;">{{ number_format($plan->bandwidth_gb, 0) }} GB</span>
                            </td>
                        @endforeach
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 16px; font-weight: 500; color: #64748b;">Price</td>
                        @foreach($plans->take(5) as $plan)
                            <td class="text-center" style="padding: 16px;">
                                <span class="fw-bold">${{ number_format($plan->final_price, 2) }}</span>
                                @if($plan->discount_percentage > 0)
                                    <br><small class="text-muted" style="text-decoration: line-through;">${{ number_format($plan->base_price, 2) }}</small>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 16px; font-weight: 500; color: #64748b;">Price per GB</td>
                        @foreach($plans->take(5) as $plan)
                            <td class="text-center" style="padding: 16px;">
                                <span class="text-muted">${{ number_format($plan->price_per_gb, 2) }}</span>
                            </td>
                        @endforeach
                    </tr>
                    @if($plan->validity_days)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 16px; font-weight: 500; color: #64748b;">Validity</td>
                        @foreach($plans->take(5) as $plan)
                            <td class="text-center" style="padding: 16px;">
                                <span class="text-muted">{{ $plan->validity_days ?? 'Unlimited' }} days</span>
                            </td>
                        @endforeach
                    </tr>
                    @endif
                    @php
                        // Get all unique feature keys
                        $allFeatureKeys = $plans->take(5)->flatMap(function($plan) {
                            return $plan->features->pluck('feature_key');
                        })->unique();
                    @endphp
                    @foreach($allFeatureKeys as $featureKey)
                        @php
                            $firstPlan = $plans->take(5)->first(function($p) use ($featureKey) {
                                return $p->features->where('feature_key', $featureKey)->isNotEmpty();
                            });
                            $featureLabel = $firstPlan->features->where('feature_key', $featureKey)->first()->display_label ?? $featureKey;
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 16px; font-weight: 500; color: #64748b;">{{ $featureLabel }}</td>
                            @foreach($plans->take(5) as $plan)
                                @php
                                    $feature = $plan->features->where('feature_key', $featureKey)->first();
                                @endphp
                                <td class="text-center" style="padding: 16px;">
                                    @if($feature)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <br><small class="text-muted">{{ $feature->feature_value }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<style>
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
    transform: scale(1.02);
}
</style>
