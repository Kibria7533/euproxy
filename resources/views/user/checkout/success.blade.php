@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            @if($order && $order->payment_status === 'completed')
                <!-- Success State -->
                <div class="text-center mb-4">
                    <div class="mb-4">
                        <div style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2" style="color: #1e293b;">Payment Successful!</h2>
                    <p class="text-muted mb-4">Thank you for your purchase. Your proxy service is now active.</p>
                </div>

                <!-- Order Details Card -->
                <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0" style="color: #1e293b;">Order Confirmation</h5>
                            <span class="badge bg-success" style="padding: 8px 16px; font-size: 0.9rem;">Paid</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background-color: #f8fafc;">
                                    <small class="text-muted d-block mb-1">Order Number</small>
                                    <span class="fw-bold" style="color: #1e293b; font-family: monospace;">{{ $order->invoice_number }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background-color: #f8fafc;">
                                    <small class="text-muted d-block mb-1">Order Date</small>
                                    <span class="fw-semibold" style="color: #1e293b;">{{ $order->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-4 mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #475569;">Plan Details</h6>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="fw-semibold mb-1" style="color: #1e293b;">{{ $order->proxyPlan->name }}</div>
                                    <small class="text-muted">{{ $order->proxyType->name }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: #1e293b;">${{ number_format($order->amount_paid, 2) }}</div>
                                    <small class="text-muted">{{ number_format($order->bandwidth_gb, 0) }} GB</small>
                                </div>
                            </div>
                        </div>

                        @if($order->subscription)
                        <div class="border-top pt-4">
                            <h6 class="fw-semibold mb-3" style="color: #475569;">Subscription Status</h6>
                            <div class="alert alert-success mb-0" style="border-radius: 12px; border: none; background-color: #d1fae5; color: #065f46;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <strong>Your subscription is now active!</strong>
                                <br>
                                <small>You have {{ number_format($order->subscription->bandwidth_remaining_gb, 2) }} GB of bandwidth available.</small>
                                @if($order->subscription->expires_at)
                                    <br>
                                    <small>Expires on {{ $order->subscription->expires_at->format('M d, Y') }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Next Steps Card -->
                <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #1e293b;">What's Next?</h5>

                        <div class="d-flex align-items-start mb-3">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; flex-shrink: 0; margin-right: 16px; font-size: 0.9rem;">
                                1
                            </div>
                            <div>
                                <div class="fw-semibold mb-1" style="color: #475569;">Get Your Credentials</div>
                                <p class="text-muted small mb-0">View your proxy credentials in the "My Subscriptions" tab</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; flex-shrink: 0; margin-right: 16px; font-size: 0.9rem;">
                                2
                            </div>
                            <div>
                                <div class="fw-semibold mb-1" style="color: #475569;">Configure Your Application</div>
                                <p class="text-muted small mb-0">Check the "Using Configuration" tab for setup instructions and code examples</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; flex-shrink: 0; margin-right: 16px; font-size: 0.9rem;">
                                3
                            </div>
                            <div>
                                <div class="fw-semibold mb-1" style="color: #475569;">Monitor Your Usage</div>
                                <p class="text-muted small mb-0">Track your bandwidth consumption in real-time through your dashboard</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mb-4">
                    <a href="{{ route('user.proxies.subscriptions', $order->proxyType->slug) }}" class="btn btn-primary btn-lg" style="border-radius: 12px; padding: 12px 32px; font-weight: 600;">
                        View My Subscriptions
                    </a>
                    <a href="{{ route('user.proxies.configuration', $order->proxyType->slug) }}" class="btn btn-outline-primary btn-lg" style="border-radius: 12px; padding: 12px 32px; font-weight: 600;">
                        Setup Guide
                    </a>
                </div>

            @else
                <!-- Processing/Pending State -->
                <div class="text-center mb-4">
                    <div class="mb-4">
                        <div class="spinner-border text-primary" role="status" style="width: 60px; height: 60px;">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2" style="color: #1e293b;">Processing Your Payment</h2>
                    <p class="text-muted mb-4">{{ $message ?? 'Please wait while we confirm your payment...' }}</p>

                    <div class="alert alert-info" style="border-radius: 12px; border: none; background-color: #dbeafe; color: #1e40af; max-width: 500px; margin: 0 auto;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        This may take a few moments. You'll receive an email once your order is confirmed.
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary" style="border-radius: 10px; padding: 10px 24px;">
                            Return to Dashboard
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
