<div class="row">
    <div class="col-lg-8">
        <!-- Getting Started -->
        <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-3" style="color: #1e293b;">Getting Started with {{ $proxyType->name }}</h4>
                <p class="text-muted mb-4">This guide will help you understand how to use {{ $proxyType->name }} effectively in your applications.</p>

                <!-- Step 1 -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; margin-right: 16px;">
                            1
                        </div>
                        <h5 class="mb-0 fw-semibold" style="color: #475569;">Purchase a Plan</h5>
                    </div>
                    <p class="text-muted mb-0" style="margin-left: 56px;">Choose from our flexible bandwidth packages in the "Buy Plans" tab. Select a plan that matches your usage requirements.</p>
                </div>

                <!-- Step 2 -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; margin-right: 16px;">
                            2
                        </div>
                        <h5 class="mb-0 fw-semibold" style="color: #475569;">Get Your Credentials</h5>
                    </div>
                    <p class="text-muted mb-0" style="margin-left: 56px;">After purchase, you'll receive your unique username and password. Find them in the "My Subscriptions" tab.</p>
                </div>

                <!-- Step 3 -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; margin-right: 16px;">
                            3
                        </div>
                        <h5 class="mb-0 fw-semibold" style="color: #475569;">Configure Your Application</h5>
                    </div>
                    <p class="text-muted mb-0" style="margin-left: 56px;">Use the code examples in the "Using Configuration" tab to integrate {{ $proxyType->name }} into your application.</p>
                </div>

                <!-- Step 4 -->
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; margin-right: 16px;">
                            4
                        </div>
                        <h5 class="mb-0 fw-semibold" style="color: #475569;">Monitor Your Usage</h5>
                    </div>
                    <p class="text-muted mb-0" style="margin-left: 56px;">Track your bandwidth consumption in real-time through the "My Subscriptions" tab. Get alerts when you're running low.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4" style="color: #1e293b;">Frequently Asked Questions</h4>

                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item mb-3" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="background-color: #f8fafc; color: #475569;">
                                What happens when I run out of bandwidth?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background-color: white;">
                                When your bandwidth is depleted, your proxy access will be suspended. You'll need to purchase a new plan to continue using the service. We'll send you alerts at 90% and 95% usage to help you plan ahead.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="background-color: #f8fafc; color: #475569;">
                                Can I use the same credentials on multiple devices?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background-color: white;">
                                Yes! You can use your proxy credentials across multiple devices and applications simultaneously. Your bandwidth will be shared across all connections.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="background-color: #f8fafc; color: #475569;">
                                How is bandwidth calculated?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background-color: white;">
                                Bandwidth is measured in gigabytes (GB) based on the total data transferred through the proxy. Both request and response data count towards your allocation.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="background-color: #f8fafc; color: #475569;">
                                Do unused bandwidth rollover?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background-color: white;">
                                No, unused bandwidth does not roll over to the next billing period. Each plan is independent with its own bandwidth allocation.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" style="background-color: #f8fafc; color: #475569;">
                                What's your refund policy?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background-color: white;">
                                We offer full refunds within 24 hours if you've used less than 5% of your bandwidth. Partial refunds are available within 7 days based on remaining bandwidth. No refunds after 7 days.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Practices -->
        <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4" style="color: #1e293b;">Best Practices</h4>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2" style="color: #475569;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981; margin-right: 8px; vertical-align: middle;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Connection Pooling
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 0.9rem; margin-left: 34px;">
                        Reuse connections when possible to reduce overhead and improve performance. Most HTTP libraries support connection pooling out of the box.
                    </p>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2" style="color: #475569;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981; margin-right: 8px; vertical-align: middle;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Error Handling
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 0.9rem; margin-left: 34px;">
                        Implement proper retry logic with exponential backoff. Network issues can happen - graceful error handling ensures your application stays robust.
                    </p>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2" style="color: #475569;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981; margin-right: 8px; vertical-align: middle;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Monitor Bandwidth
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 0.9rem; margin-left: 34px;">
                        Regularly check your bandwidth usage in the dashboard. Set up alerts to notify you when you're approaching your limit.
                    </p>
                </div>

                <div>
                    <h6 class="fw-semibold mb-2" style="color: #475569;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981; margin-right: 8px; vertical-align: middle;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Secure Credentials
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 0.9rem; margin-left: 34px;">
                        Never commit your proxy credentials to version control. Use environment variables or secure configuration management.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Need More Help?</h5>
                <p class="mb-4" style="opacity: 0.9;">Our support team is here to help you succeed. Get in touch if you have any questions or issues.</p>
                <a href="#" class="btn btn-light w-100 fw-semibold" style="border-radius: 8px; color: #3b82f6;">
                    Contact Support
                </a>
            </div>
        </div>

        <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color: #1e293b;">Quick Links</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <a href="{{ route('user.proxies.buy', $proxyType->slug) }}" class="text-decoration-none" style="color: #3b82f6; font-size: 0.9rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                            View Pricing Plans
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('user.proxies.configuration', $proxyType->slug) }}" class="text-decoration-none" style="color: #3b82f6; font-size: 0.9rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                            Configuration Guide
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.proxies.subscriptions', $proxyType->slug) }}" class="text-decoration-none" style="color: #3b82f6; font-size: 0.9rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            My Subscriptions
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
