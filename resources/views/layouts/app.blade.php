<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>euproxy - Proxy Management Dashboard</title>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet"/>
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f8fafc;
        }
        .navbar {
            background: white !important;
            border-bottom: 1px solid #e2e8f0;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-link {
            color: #64748b !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #2563eb !important;
        }
        .footer {
            background-color: white;
            border-top: 1px solid #e2e8f0;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            transition: all 0.2s;
        }
        .sidebar-link:hover {
            background-color: #f1f5f9;
            color: #2563eb;
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        .sidebar-link svg {
            margin-right: 12px;
            transition: all 0.2s;
        }
        .sidebar-link.active svg {
            stroke: white;
        }

        /* Mobile Responsive Improvements */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.25rem;
            }
            .stat-card {
                margin-bottom: 1rem;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }
            .page-header > a {
                width: 100%;
            }
            .action-btn {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
            .modern-table {
                font-size: 0.875rem;
            }
            .modern-table thead th,
            .modern-table tbody td {
                padding: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .stat-value {
                font-size: 1.5rem;
            }
            .stat-icon {
                width: 40px;
                height: 40px;
            }
            .stat-icon svg {
                width: 20px;
                height: 20px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/home') }}">
                    <span>eu</span>proxy
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @unless(request()->is('admin*'))
                    <ul class="navbar-nav me-auto">
                        @guest
                        @else
                            @can('create-user')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.search') }}">Users</a>
                            </li>
                            @endcan
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('ip.search',request()->user()->id) }}">Allow IPs</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('squiduser.search',request()->user()->id) }}">Squid User</a>
                            </li>
                        @endguest
                    </ul>
                    @endunless

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @if(request()->is('admin*') || request()->is('user*'))
        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 d-none d-md-block bg-white sidebar" style="min-height: calc(100vh - 56px); border-right:1px solid #e2e8f0; position: sticky; top: 56px; height: calc(100vh - 56px); overflow-y: auto;">
                    <div class="pt-4 pb-3 px-3">
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-semibold" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                                <div class="text-muted small">{{ Auth::user()->is_administrator ? 'Administrator' : 'User' }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            @if(Auth::user()->is_administrator)
                                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('user.dashboard') }}" class="sidebar-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" style="display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                    Dashboard
                                </a>
                            @endif
                        </div>

                        @if(!Auth::user()->is_administrator && $availableProxyTypes->count() > 0)
                        <div class="sidebar-section mb-3">
                            <div class="sidebar-section-title" style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; padding: 0 12px; margin-bottom: 8px;">
                                Proxies
                            </div>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="sidebar-link" data-bs-toggle="collapse" href="#proxiesCollapse" role="button" aria-expanded="{{ request()->is('user/proxies*') ? 'true' : 'false' }}" aria-controls="proxiesCollapse" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                        <div style="display: flex; align-items: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                            Proxy Services
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="chevron-icon"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </a>
                                    <div class="collapse {{ request()->is('user/proxies*') ? 'show' : '' }}" id="proxiesCollapse">
                                        <ul class="nav flex-column ms-4">
                                            @foreach($availableProxyTypes as $type)
                                            <li class="nav-item">
                                                <a href="{{ route('user.proxies.show', $type->slug) }}" class="sidebar-link {{ request()->is('user/proxies/'.$type->slug.'*') ? 'active' : '' }}" style="display: flex; align-items: center; padding: 8px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-size: 0.9rem; transition: all 0.2s;">
                                                    {{ $type->name }}
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        @endif

                        @if(Auth::user()->is_administrator)
                        <div class="sidebar-section mb-3">
                            <div class="sidebar-section-title" style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; padding: 0 12px; margin-bottom: 8px;">
                                Configuration
                            </div>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('admin.proxy-types.*') ? 'active' : '' }}" href="{{ route('admin.proxy-types.search') }}" style="display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                                        Proxy Types
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('admin.proxy-plans.*') ? 'active' : '' }}" href="{{ route('admin.proxy-plans.search') }}" style="display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                        Proxy Plans
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif

                        <div class="sidebar-section">
                            <div class="sidebar-section-title" style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; padding: 0 12px; margin-bottom: 8px;">
                                Management
                            </div>
                            <ul class="nav flex-column">
                                @can('create-user')
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('user.search') ? 'active' : '' }}" href="{{ route('user.search') }}" style="display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                        Users
                                    </a>
                                </li>
                                @endcan
                                <li class="nav-item">
                                    @if(Auth::user()->is_administrator)
                                        <a class="sidebar-link {{ request()->routeIs('squiduser.*') ? 'active' : '' }}" href="{{ route('squiduser.search',request()->user()->id) }}" style="display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                            Proxy Users
                                        </a>
                                    @else
                                        <a class="sidebar-link {{ request()->routeIs('user.squiduser.*') ? 'active' : '' }}" href="{{ route('user.squiduser.search') }}" style="display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                            Proxy Users
                                        </a>
                                    @endif
                                </li>
                                <li class="nav-item">
                                    @if(Auth::user()->is_administrator)
                                        <a class="sidebar-link {{ request()->routeIs('ip.*') ? 'active' : '' }}" href="{{ route('ip.search',request()->user()->id) }}" style="display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                            Allowed IPs
                                        </a>
                                    @else
                                        <a class="sidebar-link {{ request()->routeIs('user.ip.*') ? 'active' : '' }}" href="{{ route('user.ip.search') }}" style="display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; transition: all 0.2s;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                            Allowed IPs
                                        </a>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <main class="col-md-10 ms-sm-auto col-lg-10 px-4 py-4">
                    @yield('content')
                </main>
            </div>
        </div>
        @else
        <main class="py-4">
            @yield('content')
        </main>
        @endif

        <footer class="footer mt-5 py-4">
            <div class="container text-center">
                <p class="text-muted small mb-0">
                    &copy; 2024 euproxy. All rights reserved. | 
                    <a href="#" class="text-decoration-none text-muted-link" style="color: #6b7280;">Privacy</a> • 
                    <a href="#" class="text-decoration-none text-muted-link" style="color: #6b7280;">Terms</a> • 
                    <a href="#" class="text-decoration-none text-muted-link" style="color: #6b7280;">Support</a>
                </p>
            </div>
        </footer>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        @foreach ($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    </script>
    @stack('scripts')
</body>
</html>
