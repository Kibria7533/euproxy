@extends('layouts.app')

@section('content')
<style>
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
</style>

<div class="container-fluid">
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Welcome Section -->
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Welcome back, {{ Auth::user()->name }}!</h2>
        <p class="text-muted">Here's what's happening with your proxy system today.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Users -->
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                    </div>
                    <div class="stat-value mb-1">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
        </div>

        <!-- Total Squid Users -->
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        </div>
                    </div>
                    <div class="stat-value mb-1">{{ $stats['total_squid_users'] }}</div>
                    <div class="stat-label">Proxy Accounts</div>
                </div>
            </div>
        </div>

        <!-- Enabled Squid Users -->
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        </div>
                    </div>
                    <div class="stat-value mb-1">{{ $stats['enabled_squid_users'] }}</div>
                    <div class="stat-label">Active Proxies</div>
                </div>
            </div>
        </div>

        <!-- Total Allowed IPs -->
        <div class="col-md-3">
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

    <!-- Recent Activity -->
    <div class="row g-4">
        <!-- Recent Users -->
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Recent Users</h5>
                        <a href="{{ route('user.search') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    @if($stats['recent_users']->count() > 0)
                        <ul class="recent-list">
                            @foreach($stats['recent_users'] as $user)
                                <li class="recent-item">
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                    <div>
                                        <span class="badge bg-light text-dark">{{ $user->created_at->diffForHumans() }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center text-muted py-4">
                            <p>No users yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Proxy Users -->
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Recent Proxy Users</h5>
                        <a href="{{ route('squiduser.search', Auth::user()->id) }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    @if($stats['recent_squid_users']->count() > 0)
                        <ul class="recent-list">
                            @foreach($stats['recent_squid_users'] as $squidUser)
                                <li class="recent-item">
                                    <div>
                                        <div class="fw-semibold">{{ $squidUser->user }}</div>
                                        <div class="text-muted small">{{ $squidUser->fullname ?? 'No full name' }}</div>
                                    </div>
                                    <div>
                                        @if($squidUser->enabled)
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
                            <p>No proxy users yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Proxy Status Distribution</h5>
                    <canvas id="proxyStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">System Overview</h5>
                    <canvas id="systemOverviewChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Quick Actions</h5>
                    <div class="row g-3">
                        @can('create-user')
                        <div class="col-md-3">
                            <a href="{{ route('user.creator') }}" class="btn btn-outline-primary w-100 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                <div class="fw-semibold">Add User</div>
                            </a>
                        </div>
                        @endcan
                        <div class="col-md-3">
                            <a href="{{ route('squiduser.creator') }}" class="btn btn-outline-success w-100 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                <div class="fw-semibold">Add Proxy User</div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('ip.creator') }}" class="btn btn-outline-warning w-100 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                <div class="fw-semibold">Whitelist IP</div>
                            </a>
                        </div>
                        @can('create-user')
                        <div class="col-md-3">
                            <a href="{{ route('user.search') }}" class="btn btn-outline-secondary w-100 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <div class="fw-semibold">Manage Users</div>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Proxy Status Distribution Chart
    const proxyStatusCtx = document.getElementById('proxyStatusChart').getContext('2d');
    const proxyStatusChart = new Chart(proxyStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active Proxies', 'Disabled Proxies'],
            datasets: [{
                data: [{{ $stats['enabled_squid_users'] }}, {{ $stats['total_squid_users'] - $stats['enabled_squid_users'] }}],
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
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12,
                            family: 'Inter'
                        }
                    }
                }
            }
        }
    });

    // System Overview Chart
    const systemOverviewCtx = document.getElementById('systemOverviewChart').getContext('2d');
    const systemOverviewChart = new Chart(systemOverviewCtx, {
        type: 'bar',
        data: {
            labels: ['Users', 'Proxy Accounts', 'Whitelisted IPs'],
            datasets: [{
                label: 'Count',
                data: [{{ $stats['total_users'] }}, {{ $stats['total_squid_users'] }}, {{ $stats['total_allowed_ips'] }}],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)'
                ],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            family: 'Inter'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Inter'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection
