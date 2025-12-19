<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - euproxy</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            margin: 0;
        }

        .admin-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-container {
            display: flex;
            flex: 1;
            width: 100%;
            background: #f9fafb;
        }

        .admin-left {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            background: #ffffff;
        }

        @media (min-width: 992px) {
            .admin-left {
                display: flex;
            }
        }

        .admin-logo {
            margin-bottom: 30px;
        }

        .admin-logo h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .admin-logo p {
            font-size: 1.1rem;
            color: #6b7280;
            opacity: 0.9;
            font-weight: 300;
        }

        .admin-features {
            margin-top: 40px;
            text-align: left;
            display: inline-block;
        }

        .admin-features .feature {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 0.95rem;
            color: #6b7280;
        }

        .admin-features .feature svg {
            width: 24px;
            height: 24px;
            margin-right: 15px;
            flex-shrink: 0;
            color: #dc2626;
            stroke: #dc2626;
        }

        .admin-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #ffffff;
            padding: 40px 20px;
            min-height: calc(100vh - 60px);
        }

        .admin-form {
            width: 100%;
            max-width: 420px;
            background: transparent;
            border-radius: 12px;
            padding: 0;
            box-shadow: none;
            border: none;
        }

        .admin-form h2 {
            font-size: 1.8rem;
            margin-bottom: 8px;
            color: #1f2937;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .admin-badge {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .admin-badge svg {
            width: 14px;
            height: 14px;
            display: inline-block;
            margin-right: 6px;
            vertical-align: -1px;
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
            border-color: #dc2626;
            background: white;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .form-group input.is-invalid {
            border-color: #dc2626 !important;
        }

        .invalid-feedback {
            display: block;
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 4px;
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
            accent-color: #dc2626;
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

        .btn-admin {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.35);
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        }

        .btn-admin:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);
        }

        .form-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }

        .form-links a {
            color: #dc2626;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
            font-weight: 500;
        }

        .form-links a:hover {
            color: #b91c1c;
            text-decoration: underline;
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
            .admin-container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
            }

            .admin-right {
                flex: 1;
                padding: 20px;
                min-height: auto;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .admin-form {
                width: 100%;
                max-width: 100%;
            }

            .admin-form h2 {
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

            .btn-admin {
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
            .admin-form {
                padding: 0;
            }

            .admin-form h2 {
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

            .btn-admin {
                padding: 10px;
                font-size: 0.9rem;
            }

            .form-links {
                margin-top: 10px;
                padding-top: 10px;
            }

            .form-links a {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="{{ route('landing') }}" class="header-logo" style="text-decoration: none;">euproxy</a>
        </div>
    </header>

    <div class="admin-wrapper">
        <div class="admin-container">
            <!-- Left Panel -->
            <div class="admin-left">
                <div class="admin-logo">
                    <h1>euproxy</h1>
                    <p>Administrator Console</p>
                    
                    <div class="admin-features">
                        <div class="feature">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span>Enterprise-grade security</span>
                        </div>
                        <div class="feature">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Full system management</span>
                        </div>
                        <div class="feature">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Secure access control</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="admin-right">
                <div class="admin-form">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <span class="admin-badge">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            Administrator Access
                        </span>
                    </div>

                    <h2>Admin Login</h2>
                    <p class="form-subtitle">Enter your administrator credentials</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.post') }}" novalidate>
                        @csrf

                        <div class="form-group">
                            <label for="email">Administrator Email</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
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

                        <button type="submit" class="btn-admin">Sign In</button>

                        <div class="form-links">
                            <a href="{{ route('user.login') }}">← Back to User Login</a>
                        </div>
                    </form>
                </div>
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
</body>
</html>
