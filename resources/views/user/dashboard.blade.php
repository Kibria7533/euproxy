<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>euproxy - User Dashboard</title>

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
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
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
            color: #8b5cf6 !important;
        }
        .sidebar {
            background: white;
            border-right: 1px solid #e2e8f0;
            min-height: calc(100vh - 56px);
            position: sticky;
            top: 56px;
            height: calc(100vh - 56px);
            overflow-y: auto;
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
            color: #8b5cf6;
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }
        .sidebar-link svg {
            margin-right: 12px;
            transition: all 0.2s;
        }
        .sidebar-link.active svg {
            stroke: white;
        }
        .stat-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }
        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .recent-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .recent-item {
            padding: 12px;
            border-radius: 8px;
            transition: background-color 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .recent-item:hover {
            background-color: #f1f5f9;
        }
        .badge-status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .footer {
            background-color: white;
            border-top: 1px solid #e2e8f0;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.25rem;
            }
            .stat-card {
                margin-bottom: 1rem;
            }
            .stat-value {
                font-size: 1.5rem;
            }
            .stat-icon {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('user.dashboard') }}">
                    <span>eu</span>proxy
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('user.logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-2 d-none d-md-block sidebar">
                    <div class="pt-4 pb-3 px-3">
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-semibold" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                                <div class="text-muted small">User</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('user.dashboard') }}" class="sidebar-link active">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                Dashboard
                            </a>
                        </div>

                        <div class="sidebar-section">
                            <div class="sidebar-section-title" style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; padding: 0 12px; margin-bottom: 8px;">
                                Management
                            </div>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="sidebar-link" href="{{ route('user.squiduser.search') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                        Proxy Users
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-link" href="{{ route('user.ip.search') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                        Allowed IPs
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="col-md-10 ms-sm-auto col-lg-10 px-4 py-4">
                    <div class="container-fluid">
                        <!-- Welcome Section -->
                        <div class="mb-4">
                            <h2 class="fw-bold mb-1">Welcome back, {{ Auth::user()->name }}!</h2>
                            <p class="text-muted">Here's your proxy management overview.</p>
                        </div>

                        <!-- Session Messages -->
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <strong>Info:</strong> {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Bandwidth Warnings -->
                        @if($stats['bandwidth_data']->where('is_over_limit', true)->isNotEmpty())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Warning!</strong> Some proxy users exceeded their bandwidth limits.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @elseif($stats['bandwidth_data']->where('usage_percentage', '>=', 90)->isNotEmpty())
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Notice:</strong> Some proxy users are approaching their limits.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Statistics Cards -->
                        <div class="row g-4 mb-4">
                            <!-- Total Proxy Users -->
                            <div class="col-md-4">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                            </div>
                                        </div>
                                        <div class="stat-value mb-1">{{ $stats['total_proxy_users'] }}</div>
                                        <div class="stat-label">My Proxy Accounts</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enabled Proxy Users -->
                            <div class="col-md-4">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                            </div>
                                        </div>
                                        <div class="stat-value mb-1">{{ $stats['enabled_proxy_users'] }}</div>
                                        <div class="stat-label">Active Proxies</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Allowed IPs -->
                            <div class="col-md-4">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                            </div>
                                        </div>
                                        <div class="stat-value mb-1">{{ $stats['total_allowed_ips'] }}</div>
                                        <div class="stat-label">Whitelisted IPs</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bandwidth Usage Overview -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-3">Bandwidth Usage Overview</h5>
                                        @if($stats['bandwidth_data']->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Proxy User</th>
                                                            <th>Used</th>
                                                            <th>Limit</th>
                                                            <th>Usage</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($stats['bandwidth_data'] as $bw)
                                                            <tr>
                                                                <td><strong>{{ $bw['username'] }}</strong></td>
                                                                <td>{{ $bw['total_bandwidth_gb'] }} GB</td>
                                                                <td>
                                                                    @if($bw['bandwidth_limit_gb'])
                                                                        {{ $bw['bandwidth_limit_gb'] }} GB
                                                                    @else
                                                                        <span class="text-muted">Unlimited</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($bw['bandwidth_limit_gb'])
                                                                        <div class="progress" style="height: 20px; min-width: 150px;">
                                                                            <div class="progress-bar {{ $bw['usage_percentage'] >= 90 ? 'bg-danger' : ($bw['usage_percentage'] >= 75 ? 'bg-warning' : 'bg-success') }}"
                                                                                 style="width: {{ min($bw['usage_percentage'], 100) }}%">
                                                                                {{ $bw['usage_percentage'] }}%
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">N/A</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($bw['is_over_limit'])
                                                                        <span class="badge bg-danger">Over Limit</span>
                                                                    @elseif($bw['usage_percentage'] >= 90)
                                                                        <span class="badge bg-warning text-dark">Near Limit</span>
                                                                    @else
                                                                        <span class="badge bg-success">Normal</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-center text-muted py-4">No bandwidth data available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-3">Proxy Status</h5>
                                        <div style="position: relative; height: 200px; width: 100%;">
                                            <canvas id="proxyStatusChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-3">Quick Stats</h5>
                                        <div class="d-flex justify-content-around align-items-center" style="height: 200px;">
                                            <div class="text-center">
                                                <div style="font-size: 2rem; font-weight: 700; color: #8b5cf6;">{{ $stats['total_proxy_users'] }}</div>
                                                <div class="text-muted">Total Proxies</div>
                                            </div>
                                            <div class="text-center">
                                                <div style="font-size: 2rem; font-weight: 700; color: #10b981;">{{ $stats['enabled_proxy_users'] }}</div>
                                                <div class="text-muted">Active</div>
                                            </div>
                                            <div class="text-center">
                                                <div style="font-size: 2rem; font-weight: 700; color: #f59e0b;">{{ $stats['total_allowed_ips'] }}</div>
                                                <div class="text-muted">IPs</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Bandwidth Chart with Filters -->
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="fw-bold mb-0">Bandwidth Usage Analytics</h5>
                                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                                <!-- Time Range Filter -->
                                                <select id="timeRangeFilter" class="form-select form-select-sm" style="width: auto; min-width: 120px;">
                                                    <option value="hour">Last Hour</option>
                                                    <option value="today">Today</option>
                                                    <option value="7days" selected>Last 7 Days</option>
                                                    <option value="30days">Last 30 Days</option>
                                                </select>

                                                <!-- Proxy User Filter -->
                                                @if($stats['bandwidth_data']->isNotEmpty())
                                                <select id="proxyUserFilter" class="form-select form-select-sm" style="width: auto; min-width: 180px;">
                                                    <option value="all" selected>All Proxy Users</option>
                                                    @foreach($stats['bandwidth_data'] as $bw)
                                                    <option value="{{ $bw['username'] }}">{{ $bw['username'] }}</option>
                                                    @endforeach
                                                </select>
                                                @endif

                                                <button id="refreshChart" class="btn btn-sm btn-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                                    Refresh
                                                </button>
                                            </div>
                                        </div>

                                        <div id="chartLoadingIndicator" class="text-center py-4" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="text-muted mt-2">Loading bandwidth data...</p>
                                        </div>

                                        <div style="position: relative; height: 350px; width: 100%;">
                                            <canvas id="bandwidthChart"></canvas>
                                        </div>

                                        <div class="mt-3 text-muted small">
                                            <strong>Tip:</strong> Use the dropdown above to view bandwidth for a specific proxy user, or select "All Proxy Users" to see combined usage across all your proxies.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="row g-4 mb-4">
                            <!-- Recent Proxy Users -->
                            <div class="col-md-6">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold mb-0">Recent Proxy Users</h5>
                                            <a href="{{ route('user.squiduser.search') }}" class="btn btn-sm btn-outline-primary">View All</a>
                                        </div>
                                        @if($stats['recent_proxy_users']->count() > 0)
                                            <ul class="recent-list">
                                                @foreach($stats['recent_proxy_users'] as $proxyUser)
                                                    <li class="recent-item">
                                                        <div>
                                                            <div class="fw-semibold">{{ $proxyUser->user }}</div>
                                                            <div class="text-muted small">{{ $proxyUser->fullname ?? 'No full name' }}</div>
                                                        </div>
                                                        <div>
                                                            @if($proxyUser->enabled)
                                                                <span class="badge-status" style="background-color: #d1fae5; color: #065f46;">Active</span>
                                                            @else
                                                                <span class="badge-status" style="background-color: #fee2e2; color: #991b1b;">Disabled</span>
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-center text-muted py-4">
                                                <p>No proxy users yet. Create your first one!</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Allowed IPs -->
                            <div class="col-md-6">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold mb-0">Recent Whitelisted IPs</h5>
                                            <a href="{{ route('user.ip.search') }}" class="btn btn-sm btn-outline-primary">View All</a>
                                        </div>
                                        @if($stats['recent_allowed_ips']->count() > 0)
                                            <ul class="recent-list">
                                                @foreach($stats['recent_allowed_ips'] as $allowedIp)
                                                    <li class="recent-item">
                                                        <div class="d-flex align-items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 10px; color: #f59e0b;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                                            <code class="fw-bold" style="background-color: #fef3c7; padding: 4px 8px; border-radius: 4px; color: #92400e;">{{ $allowedIp->ip }}</code>
                                                        </div>
                                                        <div>
                                                            <span class="badge bg-light text-dark">{{ $allowedIp->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-center text-muted py-4">
                                                <p>No whitelisted IPs yet. Add one to get started!</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-3">Quick Actions</h5>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <a href="{{ route('user.squiduser.creator') }}" class="btn btn-outline-primary w-100 py-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                                    <div class="fw-semibold">Add Proxy User</div>
                                                </a>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="{{ route('user.ip.creator') }}" class="btn btn-outline-warning w-100 py-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                                    <div class="fw-semibold">Whitelist IP</div>
                                                </a>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="{{ route('user.squiduser.search') }}" class="btn btn-outline-success w-100 py-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                                    <div class="fw-semibold">View Proxies</div>
                                                </a>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="{{ route('user.ip.search') }}" class="btn btn-outline-secondary w-100 py-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                                    <div class="fw-semibold">View IPs</div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Proxy Status Distribution Chart
        const proxyStatusCtx = document.getElementById('proxyStatusChart');
        if (proxyStatusCtx) {
            const proxyStatusChart = new Chart(proxyStatusCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Active Proxies', 'Disabled Proxies'],
                    datasets: [{
                        data: [{{ $stats['enabled_proxy_users'] }}, {{ $stats['total_proxy_users'] - $stats['enabled_proxy_users'] }}],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgba(16, 185, 129, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12,
                                    family: 'Inter'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Dynamic Bandwidth Chart
        const bandwidthCtxElement = document.getElementById('bandwidthChart');
        let bandwidthChart = null;

        if (bandwidthCtxElement) {
            const bandwidthCtx = bandwidthCtxElement.getContext('2d');
            bandwidthChart = new Chart(bandwidthCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Bandwidth (GB)',
                        data: [],
                        backgroundColor: 'rgba(139, 92, 246, 0.2)',
                        borderColor: 'rgba(139, 92, 246, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: 'rgba(139, 92, 246, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 750
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'Inter',
                                    size: 11
                                },
                                usePointStyle: true,
                                padding: 12
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 10,
                            titleFont: {
                                size: 13,
                                family: 'Inter'
                            },
                            bodyFont: {
                                size: 12,
                                family: 'Inter'
                            },
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' GB';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    family: 'Inter',
                                    size: 10
                                },
                                callback: function(value) {
                                    return value.toFixed(1) + ' GB';
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    family: 'Inter',
                                    size: 10
                                },
                                maxRotation: 45,
                                minRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 20
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Function to load bandwidth data via AJAX
        function loadBandwidthData() {
            if (!bandwidthChart) {
                console.error('Bandwidth chart not initialized');
                return;
            }

            const timeRange = document.getElementById('timeRangeFilter').value;
            const proxyUserSelect = document.getElementById('proxyUserFilter');
            let selectedUsers = [];

            if (proxyUserSelect) {
                const selectedValue = proxyUserSelect.value;

                // If "all" is selected or nothing is selected, get all users
                if (!selectedValue || selectedValue === 'all') {
                    // Collect all usernames except the "all" option
                    for (let i = 1; i < proxyUserSelect.options.length; i++) {
                        selectedUsers.push(proxyUserSelect.options[i].value);
                    }
                } else {
                    // Single user selected
                    selectedUsers.push(selectedValue);
                }
            }

            console.log('Loading bandwidth data for users:', selectedUsers, 'Range:', timeRange);

            // Show loading indicator
            const loadingIndicator = document.getElementById('chartLoadingIndicator');
            const chartCanvas = document.getElementById('bandwidthChart');

            if (loadingIndicator) loadingIndicator.style.display = 'block';
            if (chartCanvas) chartCanvas.style.opacity = '0.3';

            // Build query parameters
            const params = new URLSearchParams();
            params.append('range', timeRange);

            // Add each username as a separate parameter
            if (selectedUsers.length > 0) {
                selectedUsers.forEach(user => {
                    params.append('usernames[]', user);
                });
            }

            // Fetch data via AJAX
            fetch('{{ route('user.bandwidth.data') }}?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);

                if (data.success && bandwidthChart) {
                    // Update chart
                    bandwidthChart.data.labels = data.labels || [];
                    bandwidthChart.data.datasets[0].data = data.values || [];
                    bandwidthChart.update('none'); // Use 'none' for instant update without animation

                    // Hide loading indicator
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                    if (chartCanvas) chartCanvas.style.opacity = '1';

                    console.log('Chart updated successfully');
                } else {
                    console.error('Data fetch failed:', data);
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                    if (chartCanvas) chartCanvas.style.opacity = '1';
                    if (typeof toastr !== 'undefined' && data.message) {
                        toastr.error(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading bandwidth data:', error);
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                if (chartCanvas) chartCanvas.style.opacity = '1';
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to load bandwidth data: ' + error.message);
                }
            });
        }

        // Event listeners for filters
        const timeRangeFilter = document.getElementById('timeRangeFilter');
        const proxyUserFilter = document.getElementById('proxyUserFilter');
        const refreshButton = document.getElementById('refreshChart');

        if (timeRangeFilter) {
            timeRangeFilter.addEventListener('change', loadBandwidthData);
        }
        if (proxyUserFilter) {
            proxyUserFilter.addEventListener('change', loadBandwidthData);
        }
        if (refreshButton) {
            refreshButton.addEventListener('click', loadBandwidthData);
        }

        // Load initial data
        if (bandwidthChart) {
            loadBandwidthData();
        }
    </script>
</body>
</html>
