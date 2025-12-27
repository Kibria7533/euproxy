@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <a href="{{ route('user.proxies.buy', $plan->proxyType->slug) }}" class="text-decoration-none text-muted d-inline-flex align-items-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Back to Plans
                </a>
                <h3 class="fw-bold mb-1">Review Your Order</h3>
                <p class="text-muted mb-0">Review your plan details and select a payment method</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('user.checkout.process') }}" id="checkoutForm">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                <!-- Order Summary Card -->
                <div class="card mb-4" style="border-radius: 16px; border: 2px solid #3b82f6;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #1e293b;">Order Summary</h5>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <h6 class="fw-semibold mb-1" style="color: #475569;">{{ $plan->name }}</h6>
                                <p class="text-muted small mb-2">{{ $plan->proxyType->name }}</p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge bg-primary">{{ number_format($plan->bandwidth_gb, 0) }} GB Bandwidth</span>
                                    @if($plan->validity_days)
                                        <span class="badge bg-secondary">{{ $plan->validity_days }} Days Validity</span>
                                    @endif
                                    @if($plan->is_popular)
                                        <span class="badge bg-warning text-dark">Most Popular</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                @if($plan->discount_percentage > 0)
                                    <div class="mb-1">
                                        <span class="text-muted" style="text-decoration: line-through; font-size: 1.1rem;">${{ number_format($plan->base_price, 2) }}</span>
                                        <span class="badge bg-danger ms-2">-{{ $plan->discount_percentage }}%</span>
                                    </div>
                                    <div class="h4 fw-bold mb-0" style="color: #059669;">${{ number_format($plan->final_price, 2) }}</div>
                                @else
                                    <div class="h4 fw-bold mb-0" style="color: #1e293b;">${{ number_format($plan->base_price, 2) }}</div>
                                @endif
                                <small class="text-muted">${{ number_format($plan->price_per_gb, 2) }} per GB</small>
                            </div>
                        </div>

                        @if($plan->features->count() > 0)
                        <div class="border-top pt-3 mt-3">
                            <h6 class="fw-semibold mb-3" style="color: #475569; font-size: 0.9rem;">Included Features:</h6>
                            <div class="row">
                                @foreach($plan->features as $feature)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981; margin-right: 8px; flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            <span style="font-size: 0.9rem; color: #64748b;">{{ $feature->display_label }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #1e293b;">Select Payment Method</h5>

                        <div class="payment-methods">
                            <!-- Stripe -->
                            <div class="form-check p-3 rounded border" style="cursor: pointer;" onclick="document.getElementById('payment_stripe').checked = true;">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_stripe" value="stripe" checked style="width: 1.25em; height: 1.25em;">
                                <label class="form-check-label ms-3" for="payment_stripe" style="cursor: pointer; width: 100%;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-semibold mb-1" style="color: #1e293b;">Credit/Debit Card</div>
                                            <small class="text-muted">Pay securely with Stripe</small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <img src="https://js.stripe.com/v3/fingerprinted/img/visa-729c05c240c4bdb47b03ac81d9945bfe.svg" alt="Visa" style="height: 24px;">
                                            <img src="https://js.stripe.com/v3/fingerprinted/img/mastercard-4d8844094130711885b5e41b28c9848f.svg" alt="Mastercard" style="height: 24px;">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @error('payment_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div class="card-body p-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" value="1" required style="width: 1.25em; height: 1.25em;">
                            <label class="form-check-label ms-2" for="terms_accepted" style="cursor: pointer;">
                                I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Refund Policy</a>
                            </label>
                        </div>
                        @error('terms_accepted')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror

                        <div class="alert alert-info mt-3 mb-0" style="border-radius: 10px; border: none; background-color: #dbeafe; color: #1e40af; font-size: 0.9rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            <strong>Refund Policy:</strong> Full refund within 24 hours if you've used less than 5% bandwidth. Partial refunds available within 7 days.
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-3 mb-4">
                    <button type="submit" class="btn btn-primary btn-lg flex-grow-1" style="border-radius: 12px; padding: 14px; font-weight: 600; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                        Proceed to Payment
                    </button>
                    <a href="{{ route('user.proxies.buy', $plan->proxyType->slug) }}" class="btn btn-outline-secondary btn-lg" style="border-radius: 12px; padding: 14px; font-weight: 600; min-width: 120px;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.payment-methods .form-check {
    transition: all 0.2s;
}

.payment-methods .form-check:hover {
    background-color: #f8fafc;
    border-color: #3b82f6 !important;
}

.payment-methods input[type="radio"]:checked + label {
    font-weight: 600;
}

.payment-methods .form-check:has(input:checked) {
    border-color: #3b82f6 !important;
    background-color: #eff6ff;
}
</style>
@endsection
