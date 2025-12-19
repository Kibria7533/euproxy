<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>euproxy - Premium Proxy Services & Management Platform</title>
    <meta name="description" content="Professional proxy management platform with residential, datacenter, and rotating proxies. Advanced dashboard for seamless proxy control.">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #ffffff;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar-landing {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.75rem;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            color: #64748b;
            font-weight: 500;
            transition: color 0.3s;
            margin: 0 0.5rem;
        }

        .nav-link:hover {
            color: #6366f1;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            color: white;
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
            color: white;
        }

        .btn-outline-gradient {
            border: 2px solid #6366f1;
            color: #6366f1;
            background: transparent;
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-outline-gradient:hover {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border-color: transparent;
        }

        /* Hero Section */
        .hero-section {
            padding: 100px 0 80px;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.8;
        }

        .hero-image {
            max-width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: white;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1rem;
            text-align: center;
        }

        .section-subtitle {
            font-size: 1.125rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 4rem;
        }

        .feature-card {
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
            height: 100%;
            background: white;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: #6366f1;
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }

        .feature-description {
            color: #64748b;
            line-height: 1.7;
        }

        /* Pricing Section */
        .pricing-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
        }

        /* Pricing Tabs */
        .pricing-tabs-container {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .pricing-tabs {
            background: white;
            border-radius: 50px;
            padding: 0.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .pricing-tabs .nav-link {
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            color: #64748b;
            font-weight: 600;
            transition: all 0.3s;
            margin: 0 0.25rem;
        }

        .pricing-tabs .nav-link:hover {
            color: #6366f1;
            background: #f1f5f9;
        }

        .pricing-tabs .nav-link.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        /* Custom 5-column layout */
        .col-lg-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }

        @media (max-width: 1199px) {
            .col-lg-2-4 {
                width: 33.333%;
            }
        }

        @media (max-width: 767px) {
            .col-lg-2-4 {
                width: 100%;
            }
        }

        .pricing-card {
            background: white;
            border-radius: 16px;
            padding: 2rem 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            height: 100%;
            border: 2px solid #e2e8f0;
            position: relative;
            text-align: center;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-color: #6366f1;
        }

        .pricing-card.popular {
            border-color: #ef4444;
            border-width: 3px;
        }

        .popular-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.375rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .discount-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.375rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .pricing-bandwidth-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin: 1rem 0;
        }

        .pricing-price-wrapper {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 0.25rem;
            margin: 0.5rem 0;
        }

        .pricing-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
        }

        .pricing-period {
            font-size: 1rem;
            color: #64748b;
            font-weight: 500;
        }

        .original-price {
            color: #94a3b8;
            text-decoration: line-through;
            font-size: 1.125rem;
            margin: 0.25rem 0;
        }

        .price-per-gb {
            color: #64748b;
            font-size: 1rem;
            margin: 0.5rem 0 1rem;
        }

        .pricing-features {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0 0;
            text-align: left;
        }

        .pricing-features li {
            padding: 0.5rem 0;
            color: #64748b;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }

        .pricing-features li::before {
            content: '✓';
            color: #10b981;
            font-weight: bold;
            margin-right: 0.5rem;
            font-size: 1rem;
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .btn-white {
            background: white;
            color: #6366f1;
            padding: 1rem 2.5rem;
            border-radius: 10px;
            font-weight: 700;
            border: none;
            transition: all 0.3s;
        }

        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
            color: #6366f1;
        }

        /* Footer */
        .footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 60px 0 30px;
        }

        .footer-title {
            color: white;
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.125rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #334155;
            margin-top: 3rem;
            padding-top: 2rem;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .pricing-card.popular {
                transform: scale(1);
                border-width: 2px;
            }

            .pricing-tabs {
                flex-wrap: wrap;
                justify-content: center;
            }

            .pricing-tabs .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                margin: 0.25rem;
            }

            .pricing-bandwidth-title {
                font-size: 1.75rem;
            }

            .pricing-price {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-landing navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <span>eu</span>proxy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-primary-gradient ms-2">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="hero-title">Premium Proxy Management Platform</h1>
                    <p class="hero-subtitle">
                        Professional-grade proxy services with an advanced management dashboard.
                        Residential, datacenter, and rotating proxies for all your needs.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-primary-gradient btn-lg">
                            Start Free Trial
                        </a>
                        <a href="#pricing" class="btn btn-outline-gradient btn-lg">
                            View Pricing
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400" class="hero-image">
                            <defs>
                                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#6366f1;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <rect width="500" height="400" fill="#f8fafc" rx="20"/>
                            <rect x="50" y="50" width="400" height="60" fill="url(#grad1)" rx="10" opacity="0.2"/>
                            <rect x="70" y="65" width="120" height="30" fill="url(#grad1)" rx="5"/>
                            <circle cx="380" cy="80" r="20" fill="url(#grad1)"/>
                            <rect x="50" y="140" width="180" height="220" fill="white" rx="15" style="filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1))"/>
                            <rect x="70" y="160" width="140" height="20" fill="url(#grad1)" rx="5" opacity="0.3"/>
                            <rect x="70" y="195" width="140" height="40" fill="url(#grad1)" rx="5"/>
                            <rect x="70" y="250" width="140" height="12" fill="#e2e8f0" rx="3"/>
                            <rect x="70" y="272" width="100" height="12" fill="#e2e8f0" rx="3"/>
                            <rect x="70" y="294" width="120" height="12" fill="#e2e8f0" rx="3"/>
                            <rect x="260" y="140" width="180" height="220" fill="white" rx="15" style="filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1))"/>
                            <circle cx="350" cy="210" r="40" fill="url(#grad1)" opacity="0.2"/>
                            <text x="350" y="220" font-size="24" font-weight="bold" fill="url(#grad1)" text-anchor="middle">99%</text>
                            <rect x="280" y="270" width="140" height="12" fill="#e2e8f0" rx="3"/>
                            <rect x="280" y="292" width="110" height="12" fill="#e2e8f0" rx="3"/>
                            <rect x="280" y="314" width="130" height="12" fill="#e2e8f0" rx="3"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose euproxy?</h2>
            <p class="section-subtitle">Powerful features designed for professionals</p>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="9" y1="9" x2="15" y2="9"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                        </div>
                        <h3 class="feature-title">Advanced Dashboard</h3>
                        <p class="feature-description">
                            Intuitive management interface with real-time analytics, bandwidth monitoring, and comprehensive proxy controls.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <h3 class="feature-title">99.9% Uptime</h3>
                        <p class="feature-description">
                            Reliable infrastructure with enterprise-grade servers ensuring your proxies are always available when you need them.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                        </div>
                        <h3 class="feature-title">Multiple Proxy Types</h3>
                        <p class="feature-description">
                            Choose from residential, datacenter, rotating, and static proxies tailored to your specific use case.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                        <h3 class="feature-title">Bandwidth Control</h3>
                        <p class="feature-description">
                            Track usage in real-time, set limits, and receive alerts. Full control over your bandwidth allocation.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <h3 class="feature-title">Secure & Private</h3>
                        <p class="feature-description">
                            Bank-level encryption and authentication. Your data and connections are completely secure and anonymous.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"></path>
                                <path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"></path>
                                <path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z"></path>
                                <path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z"></path>
                                <path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z"></path>
                                <path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"></path>
                                <path d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z"></path>
                                <path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z"></path>
                            </svg>
                        </div>
                        <h3 class="feature-title">API Integration</h3>
                        <p class="feature-description">
                            RESTful API for seamless integration with your applications and automated workflows.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing-section">
        <div class="container">
            <h2 class="section-title">Simple, Transparent Pricing</h2>
            <p class="section-subtitle">Choose the perfect plan for your needs</p>

            @if($proxyTypes->isNotEmpty())
            <!-- Pricing Tabs -->
            <div class="pricing-tabs-container mb-5">
                <ul class="nav nav-pills pricing-tabs justify-content-center" role="tablist">
                    @foreach($proxyTypes as $index => $proxyType)
                        @if($proxyType->plans->isNotEmpty())
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                    id="tab-{{ $proxyType->slug }}"
                                    data-bs-toggle="pill"
                                    data-bs-target="#content-{{ $proxyType->slug }}"
                                    type="button"
                                    role="tab">
                                {{ $proxyType->name }}
                            </button>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                @foreach($proxyTypes as $index => $proxyType)
                    @if($proxyType->plans->isNotEmpty())
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                         id="content-{{ $proxyType->slug }}"
                         role="tabpanel">
                        <div class="row g-4 justify-content-center">
                            @foreach($proxyType->plans->take(5) as $plan)
                            <div class="col-lg-2-4 col-md-4 col-sm-6">
                                <div class="pricing-card {{ $plan->is_popular ? 'popular' : '' }}">
                                    @if($plan->is_popular)
                                        <div class="popular-badge">Most Popular</div>
                                    @endif

                                    @if($plan->discount_percentage > 0)
                                    <div class="discount-badge">
                                        {{ number_format($plan->discount_percentage, 0) }}% OFF
                                    </div>
                                    @endif

                                    <h4 class="pricing-bandwidth-title">{{ number_format($plan->bandwidth_gb, 0) }} GB</h4>

                                    <div class="pricing-price-wrapper">
                                        <div class="pricing-price">${{ number_format($plan->final_price, 0) }}</div>
                                        <div class="pricing-period">/30 Days</div>
                                    </div>

                                    @if($plan->discount_percentage > 0)
                                    <div class="original-price">${{ number_format($plan->base_price, 0) }}</div>
                                    @endif

                                    <div class="price-per-gb">${{ number_format($plan->price_per_gb, 2) }}/GB</div>

                                    <a href="{{ route('register') }}" class="btn {{ $plan->is_popular ? 'btn-primary-gradient' : 'btn-outline-gradient' }} w-100 mt-3">
                                        Buy Plan
                                    </a>

                                    <ul class="pricing-features mt-4">
                                        @foreach($plan->features->take(3) as $feature)
                                            <li>{{ $feature->display_label }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Get Started?</h2>
            <p class="cta-subtitle">Join thousands of satisfied customers using our proxy services</p>
            <a href="{{ route('register') }}" class="btn btn-white btn-lg">
                Start Your Free Trial
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h3 style="color: white; font-weight: 800; font-size: 1.5rem; margin-bottom: 1rem;">
                        <span style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">eu</span><span style="color: white;">proxy</span>
                    </h3>
                    <p>Professional proxy management platform for businesses and individuals.</p>
                </div>
                <div class="col-lg-2 col-md-3 mb-4 mb-lg-0">
                    <h4 class="footer-title">Product</h4>
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="#">Documentation</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3 mb-4 mb-lg-0">
                    <h4 class="footer-title">Company</h4>
                    <ul class="footer-links">
                        <li><a href="#">About</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3 mb-4 mb-lg-0">
                    <h4 class="footer-title">Legal</h4>
                    <ul class="footer-links">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Refund Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3">
                    <h4 class="footer-title">Account</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">&copy; 2024 euproxy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
