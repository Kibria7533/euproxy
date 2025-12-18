@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="text-center mb-4">
                <div class="mb-4">
                    <div style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    </div>
                </div>
                <h2 class="fw-bold mb-2" style="color: #1e293b;">Payment Cancelled</h2>
                <p class="text-muted mb-4">Your payment was cancelled and no charges were made to your account.</p>
            </div>

            @if($order)
            <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3" style="color: #475569;">Order Details</h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Plan:</span>
                        <span class="fw-semibold">{{ $order->proxyPlan->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Bandwidth:</span>
                        <span class="fw-semibold">{{ number_format($order->bandwidth_gb, 0) }} GB</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Amount:</span>
                        <span class="fw-bold" style="color: #1e293b;">${{ number_format($order->amount_paid, 2) }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0; background-color: #fffbeb;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3" style="color: #92400e;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        What Happened?
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">
                        The payment process was cancelled before completion. This could be because you clicked the back button, closed the payment window, or chose to cancel the transaction.
                    </p>
                </div>
            </div>

            <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3" style="color: #475569;">Ready to Try Again?</h6>
                    <p class="text-muted mb-3" style="font-size: 0.9rem;">
                        Your order is still available. You can complete the purchase at any time.
                    </p>

                    <div class="d-flex gap-2 flex-wrap">
                        @if($order)
                            <a href="{{ route('user.checkout.review', $order->proxy_plan_id) }}" class="btn btn-primary flex-grow-1" style="border-radius: 10px; padding: 10px 20px; font-weight: 500;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg>
                                Try Again
                            </a>
                            <a href="{{ route('user.proxies.buy', $order->proxyType->slug) }}" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 10px 20px; font-weight: 500;">
                                View Other Plans
                            </a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="btn btn-primary flex-grow-1" style="border-radius: 10px; padding: 10px 20px; font-weight: 500;">
                                Browse Plans
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-muted mb-2">Need help completing your purchase?</p>
                <a href="#" class="text-primary text-decoration-none fw-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: middle;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
