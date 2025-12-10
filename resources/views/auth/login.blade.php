@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; display: flex; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
    <!-- Left Side - Branding (Hidden on mobile, visible on lg screens) -->
    <div style="flex: 1; display: none; padding: 2rem; color: white; justify-content: center; align-items: center;" class="d-none d-lg-flex">
        <div style="text-align: center;">
            <div style="margin-bottom: 2rem;">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 1rem;">
                    <circle cx="40" cy="40" r="38" stroke="white" stroke-width="2"/>
                    <path d="M30 40C30 34.48 34.48 30 40 30C45.52 30 50 34.48 50 40C50 45.52 45.52 50 40 50C34.48 50 30 45.52 30 40Z" fill="white" opacity="0.8"/>
                    <path d="M40 35V45M35 40H45" stroke="#7c3aed" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">euproxy</h1>
            <p style="font-size: 1.25rem; margin-bottom: 1rem;">Professional Proxy Management Solution</p>
            <p style="opacity: 0.8;">Secure, fast, and reliable proxy authentication system</p>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem;">
        <div style="width: 100%; max-width: 450px; background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h2 style="font-weight: 700; color: #000; margin-bottom: 0.5rem; font-size: 1.75rem;">Welcome Back</h2>
                <p style="color: #9ca3af;">Sign in to euproxy Dashboard</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="needs-validation">
                @csrf

                <!-- Email Field -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="email" style="font-size: 14px; font-weight: 500; color: #000; margin-bottom: 0.5rem; display: block;">Email Address</label>
                    <div style="display: flex; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden;">
                        <span style="background: #f3f4f6; padding: 10px 12px; display: flex; align-items: center; color: #6b7280;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </span>
                        <input 
                            id="email" 
                            type="email" 
                            class="form-control border-0 @error('email') is-invalid @enderror" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="Enter your email"
                            required 
                            autocomplete="email" 
                            autofocus
                            style="border: none; padding: 10px 12px; font-size: 14px;"
                        >
                    </div>
                    @error('email')
                        <div style="color: #dc2626; font-size: 13px; margin-top: 4px;">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="password" style="font-size: 14px; font-weight: 500; color: #000; margin-bottom: 0.5rem; display: block;">Password</label>
                    <div style="display: flex; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden;">
                        <span style="background: #f3f4f6; padding: 10px 12px; display: flex; align-items: center; color: #6b7280;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input 
                            id="password" 
                            type="password" 
                            class="form-control border-0 @error('password') is-invalid @enderror" 
                            name="password" 
                            placeholder="Enter your password"
                            required 
                            autocomplete="current-password"
                            style="border: none; padding: 10px 12px; font-size: 14px; flex: 1;"
                        >
                        <button class="btn btn-light border-0" type="button" id="togglePassword" style="cursor: pointer; background: #f3f4f6; border: none; padding: 10px 12px; display: flex; align-items: center; color: #6b7280;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div style="color: #dc2626; font-size: 13px; margin-top: 4px;">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center;">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="width: 18px; height: 18px; margin-right: 0.5rem;">
                        <label class="form-check-label" for="remember" style="color: #9ca3af; font-size: 14px; margin: 0;">
                            Keep me signed in
                        </label>
                    </div>
                </div>

                <!-- Login Button -->
                <button type="submit" style="width: 100%; padding: 10px; margin-bottom: 1rem; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <polyline points="9 21 3 21 3 15"></polyline>
                        <line x1="21" y1="3" x2="3" y2="21"></line>
                    </svg>
                    Sign In
                </button>

                <!-- Forgot Password Link -->
                @if (Route::has('password.request'))
                    <div style="text-align: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                        <a href="{{ route('password.request') }}" style="color: #2563eb; text-decoration: none; font-weight: 500; font-size: 14px;">
                            Forgot your password?
                        </a>
                    </div>
                @endif

                <!-- Admin Login Link -->
                <div style="text-align: center;">
                    <p style="color: #9ca3af; font-size: 14px; margin-bottom: 0.5rem;">Administrator access?</p>
                    <a href="{{ route('admin.login') }}" style="color: #6b7280; border: 1px solid #e5e7eb; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block; transition: all 0.3s ease;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; margin-right: 4px; vertical-align: -1px;">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        Admin Login
                    </a>
                </div>
            </form>

            <!-- Footer -->
            <div style="text-align: center; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                <p style="color: #9ca3af; font-size: 13px;">
                    Protected by euproxy &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    #togglePassword:hover {
        background: #e5e7eb !important;
    }

    button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);
        background: linear-gradient(135deg, #6d28d9 0%, #5b21b6 100%) !important;
    }

    .form-control.is-invalid {
        border-color: #dc2626 !important;
    }
</style>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('svg');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            passwordInput.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    });
</script>
@endsection
