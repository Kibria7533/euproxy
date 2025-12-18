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
                            <div class="form-check mb-3 p-3 rounded border" style="cursor: pointer;" onclick="document.getElementById('payment_stripe').checked = true;">
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

                            <!-- PayPal -->
                            <div class="form-check p-3 rounded border" style="cursor: pointer;" onclick="document.getElementById('payment_paypal').checked = true;">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_paypal" value="paypal" style="width: 1.25em; height: 1.25em;">
                                <label class="form-check-label ms-3" for="payment_paypal" style="cursor: pointer; width: 100%;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-semibold mb-1" style="color: #1e293b;">PayPal</div>
                                            <small class="text-muted">Pay with your PayPal account</small>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="24" viewBox="0 0 124 33" fill="none">
                                            <path fill="#253B80" d="M46.211 6.749h-6.839a.95.95 0 0 0-.939.802l-2.766 17.537a.57.57 0 0 0 .564.658h3.265a.95.95 0 0 0 .939-.803l.746-4.73a.95.95 0 0 1 .938-.803h2.165c4.505 0 7.105-2.18 7.784-6.5.306-1.89.013-3.375-.872-4.415-.972-1.142-2.696-1.746-4.985-1.746zM47 13.154c-.374 2.454-2.249 2.454-4.062 2.454h-1.032l.724-4.583a.57.57 0 0 1 .563-.481h.473c1.235 0 2.4 0 3.002.704.359.42.468 1.044.332 1.906zM66.654 13.075h-3.275a.57.57 0 0 0-.563.481l-.145.916-.229-.332c-.709-1.029-2.29-1.373-3.868-1.373-3.619 0-6.71 2.741-7.312 6.586-.313 1.918.132 3.752 1.22 5.031.998 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .562.66h2.95a.95.95 0 0 0 .939-.803l1.77-11.209a.568.568 0 0 0-.561-.658zm-4.565 6.374c-.316 1.871-1.801 3.127-3.695 3.127-.951 0-1.711-.305-2.199-.883-.484-.574-.668-1.391-.514-2.301.295-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.499.589.697 1.411.554 2.317zM84.096 13.075h-3.291a.954.954 0 0 0-.787.417l-4.539 6.686-1.924-6.425a.953.953 0 0 0-.912-.678h-3.234a.57.57 0 0 0-.541.754l3.625 10.638-3.408 4.811a.57.57 0 0 0 .465.9h3.287a.949.949 0 0 0 .781-.408l10.946-15.8a.57.57 0 0 0-.468-.895z"/>
                                            <path fill="#179BD7" d="M94.992 6.749h-6.84a.95.95 0 0 0-.938.802l-2.766 17.537a.569.569 0 0 0 .562.658h3.51a.665.665 0 0 0 .656-.562l.785-4.971a.95.95 0 0 1 .938-.803h2.164c4.506 0 7.105-2.18 7.785-6.5.307-1.89.012-3.375-.873-4.415-.971-1.142-2.694-1.746-4.983-1.746zm.789 6.405c-.373 2.454-2.248 2.454-4.062 2.454h-1.031l.725-4.583a.568.568 0 0 1 .562-.481h.473c1.234 0 2.4 0 3.002.704.359.42.468 1.044.331 1.906zM115.434 13.075h-3.273a.567.567 0 0 0-.562.481l-.145.916-.23-.332c-.709-1.029-2.289-1.373-3.867-1.373-3.619 0-6.709 2.741-7.311 6.586-.312 1.918.131 3.752 1.219 5.031 1 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .564.66h2.949a.95.95 0 0 0 .938-.803l1.771-11.209a.571.571 0 0 0-.565-.658zm-4.565 6.374c-.314 1.871-1.801 3.127-3.695 3.127-.949 0-1.711-.305-2.199-.883-.484-.574-.666-1.391-.514-2.301.297-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.501.589.699 1.411.554 2.317zM119.295 7.23l-2.807 17.858a.569.569 0 0 0 .562.658h2.822c.469 0 .867-.34.939-.803l2.768-17.536a.57.57 0 0 0-.562-.659h-3.16a.571.571 0 0 0-.562.482z"/>
                                            <path fill="#253B80" d="M7.266 29.154l.523-3.322-1.165-.027H1.061L4.927 1.292a.316.316 0 0 1 .314-.268h9.38c3.114 0 5.263.648 6.385 1.927.526.6.861 1.227 1.023 1.917.17.724.173 1.589.007 2.644l-.012.077v.676l.526.298a3.69 3.69 0 0 1 1.065.812c.45.513.741 1.165.864 1.938.127.795.085 1.741-.123 2.812-.24 1.232-.628 2.305-1.152 3.183a6.547 6.547 0 0 1-1.825 2c-.696.494-1.523.869-2.458 1.109-.906.236-1.939.355-3.072.355h-.73c-.522 0-1.029.188-1.427.525a2.21 2.21 0 0 0-.744 1.328l-.055.299-.924 5.855-.042.215c-.011.068-.03.102-.058.125a.155.155 0 0 1-.096.035H7.266z"/>
                                            <path fill="#179BD7" d="M23.048 7.667c-.028.179-.06.362-.096.55-1.237 6.351-5.469 8.545-10.874 8.545H9.326c-.661 0-1.218.48-1.321 1.132L6.596 26.83l-.399 2.533a.704.704 0 0 0 .695.814h4.881c.578 0 1.069-.42 1.16-.99l.048-.248.919-5.832.059-.32c.09-.572.582-.992 1.16-.992h.73c4.729 0 8.431-1.92 9.513-7.476.452-2.321.218-4.259-.978-5.622a4.667 4.667 0 0 0-1.336-1.03z"/>
                                            <path fill="#222D65" d="M21.754 7.151a9.757 9.757 0 0 0-1.203-.267 15.284 15.284 0 0 0-2.426-.177h-7.352a1.172 1.172 0 0 0-1.159.992L8.05 17.605l-.045.289a1.336 1.336 0 0 1 1.321-1.132h2.752c5.405 0 9.637-2.195 10.874-8.545.037-.188.068-.371.096-.55a6.594 6.594 0 0 0-1.017-.429 9.045 9.045 0 0 0-.277-.087z"/>
                                            <path fill="#253B80" d="M9.614 7.699a1.169 1.169 0 0 1 1.159-.991h7.352c.871 0 1.684.057 2.426.177a9.757 9.757 0 0 1 1.481.353c.365.121.704.264 1.017.429.368-2.347-.003-3.945-1.272-5.392C20.378.682 17.853 0 14.622 0h-9.38c-.66 0-1.223.48-1.325 1.133L.01 25.898a.806.806 0 0 0 .795.932h5.791l1.454-9.225 1.564-9.906z"/>
                                        </svg>
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
