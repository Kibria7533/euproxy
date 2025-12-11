<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - euproxy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9fafb;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            margin: 0;
            padding: 0;
        }

        .header {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            margin: 0;
        }

        .header-links {
            display: flex;
            gap: 20px;
            align-items: center;
            margin: 0;
            list-style: none;
            padding: 0;
        }

        .header-links a {
            text-decoration: none;
            color: #6b7280;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .header-links a:hover {
            color: #7c3aed;
        }

        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            display: flex;
            flex: 1;
            width: 100%;
            background: #f9fafb;
        }

        .left-panel {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #1f2937;
            padding: 40px;
            text-align: center;
            background: #ffffff;
        }

        @media (min-width: 992px) {
            .left-panel {
                display: flex;
            }
        }

        .logo-section {
            margin-bottom: 30px;
        }

        .logo-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-section p {
            font-size: 1.1rem;
            opacity: 0.7;
            font-weight: 300;
            color: #6b7280;
        }

        .features-list {
            margin-top: 40px;
            text-align: left;
            display: inline-block;
        }

        .features-list .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 0.95rem;
            color: #6b7280;
        }

        .features-list .feature-item svg {
            width: 24px;
            height: 24px;
            margin-right: 15px;
            flex-shrink: 0;
            color: #7c3aed;
            stroke: #7c3aed;
        }

        .right-panel {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #ffffff;
            padding: 40px 20px;
            min-height: calc(100vh - 60px);
        }

        .login-form {
            width: 100%;
            max-width: 420px;
            background: transparent;
            border-radius: 12px;
            padding: 0;
            box-shadow: none;
            border: none;
        }

        .login-form h2 {
            font-size: 1.8rem;
            margin-bottom: 8px;
            color: #1f2937;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .form-subtitle {
            color: #6b7280;
            margin-bottom: 28px;
            font-size: 0.95rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.3px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f9fafb;
            font-family: inherit;
        }

        .form-group input:hover {
            border-color: #d1d5db;
            background: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: #7c3aed;
            background: white;
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .password-input-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #6b7280;
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
        }

        .form-check {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
        }

        .form-check input {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #7c3aed;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .form-check label {
            margin: 0;
            font-size: 0.9rem;
            color: #6b7280;
            cursor: pointer;
            font-weight: 400;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(124, 58, 237, 0.35);
            background: linear-gradient(135deg, #6d28d9 0%, #5b21b6 100%);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.2);
        }

        .form-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }

        .form-links a {
            display: block;
            color: #7c3aed;
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .form-links a:hover {
            color: #6d28d9;
            text-decoration: underline;
        }

        .admin-link {
            text-align: center;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }

        .admin-link a {
            color: #9ca3af;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.3s;
        }

        .admin-link a:hover {
            color: #374151;
        }

        .alert {
            margin-bottom: 20px;
            padding: 12px 14px;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
            }

            .left-panel {
                display: none !important;
            }

            .right-panel {
                flex: 1;
                padding: 20px;
                min-height: auto;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .login-form {
                width: 100%;
                max-width: 100%;
                padding: 20px;
            }

            .login-form h2 {
                font-size: 1.5rem;
                margin-bottom: 8px;
            }

            .form-subtitle {
                font-size: 0.9rem;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-group input {
                padding: 10px 12px;
                font-size: 16px;
            }

            .btn-login {
                padding: 10px;
                font-size: 0.9rem;
                margin-bottom: 12px;
            }

            .form-links {
                margin-top: 15px;
            }

            .form-links a {
                font-size: 0.85rem;
                margin-bottom: 6px;
            }
        }

        @media (max-width: 480px) {
            .login-form {
                padding: 16px;
            }

            .login-form h2 {
                font-size: 1.3rem;
            }

            .form-group label {
                font-size: 0.85rem;
            }

            .form-group input {
                padding: 10px 10px;
                font-size: 16px;
            }

            .password-toggle {
                right: 8px;
            }

            .form-check label {
                font-size: 0.85rem;
            }

            .btn-login {
                padding: 10px;
                font-size: 0.9rem;
            }

            .admin-link {
                margin-top: 10px;
                padding-top: 10px;
            }

            .admin-link a {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1 class="header-logo">euproxy</h1>
            <ul class="header-links">
                <li><a href="{{ route('user.login') }}">Login</a></li>
                <li><a href="{{ route('user.register') }}">Register</a></li>
            </ul>
        </div>
    </header>

    <div class="login-wrapper">
    <div class="login-container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="logo-section">
                <h1>euproxy</h1>
                <p>Secure Proxy Management</p>
                
                <div class="features-list">
                    <div class="feature-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Manage your proxy accounts</span>
                    </div>
                    <div class="feature-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Secure access control</span>
                    </div>
                    <div class="feature-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Fast and reliable</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="login-form">
                <h2>Welcome back</h2>
                <p class="form-subtitle">Sign in to your account to continue</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('user.login.post') }}" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="••••••••" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg id="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="btn-login">Sign In</button>

                    <div class="form-links">
                        <a href="{{ route('user.register') }}">Don't have an account? Sign up</a>
                    </div>

                    <div class="admin-link">
                        <a href="{{ route('admin.login') }}">Admin Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm6.633 10.632a10.053 10.053 0 01-9.147 5.555m5.595-3.856V5.25a3.375 3.375 0 00-6.75 0v12.882a3.39 3.39 0 001.946 3.010A10.05 10.05 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.064 10.064 0 01-3.996 5.9m-9.833 2.526c1.3-.26 2.552-.676 3.740-1.254"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
    </div>
    </div>
</body>
</html>
