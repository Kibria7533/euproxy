@extends('layouts.app')

@push('styles')
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

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .stat-value {
            font-size: 1.5rem;
        }
        .stat-icon {
            width: 40px;
            height: 40px;
        }
    }
</style>
@endpush

@section('content')
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
                                                                <td>
                                                                    {{ $bw['total_bandwidth_gb'] }} GB
                                                                    <span class="text-muted small">({{ round($bw['total_bandwidth_gb'] * 1024, 2) }} MB)</span>
                                                                </td>
                                                                <td>
                                                                    @if($bw['bandwidth_limit_gb'])
                                                                        {{ $bw['bandwidth_limit_gb'] }} GB
                                                                        <span class="text-muted small">({{ round($bw['bandwidth_limit_gb'] * 1024, 2) }} MB)</span>
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

                        <!-- Blocked Users -->
                        @if($stats['blocked_users']->isNotEmpty())
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <div class="card stat-card border-danger" style="border-width: 1px !important;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold mb-0 text-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                                                Blocked Proxy Users
                                                <span class="badge bg-danger ms-1">{{ $stats['blocked_users']->count() }}</span>
                                            </h5>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Username</th>
                                                        <th>Reason</th>
                                                        <th>Used</th>
                                                        <th>Current Limit</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($stats['blocked_users'] as $bu)
                                                    <tr id="blocked-row-{{ $bu['id'] }}">
                                                        <td><strong>{{ $bu['username'] }}</strong></td>
                                                        <td>
                                                            @if($bu['reason'] === 'quota_exceeded')
                                                                <span class="badge bg-danger">Quota Exceeded</span>
                                                            @else
                                                                <span class="badge bg-secondary">Manually Blocked</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $bu['used_gb'] }} GB</td>
                                                        <td>{{ $bu['bandwidth_limit_gb'] > 0 ? $bu['bandwidth_limit_gb'] . ' GB' : '—' }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-success"
                                                                onclick="openUnblockModal({{ $bu['id'] }}, '{{ $bu['username'] }}')">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><rect x="8" y="11" width="13" height="13" rx="2" ry="2"></rect><path d="M5 11V7a7 7 0 0 1 9.9-6.4"></path></svg>
                                                                Unblock
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Unblock Modal -->
                        <div class="modal fade" id="unblockModal" tabindex="-1" aria-labelledby="unblockModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="unblockModalLabel">Unblock Proxy User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-3">
                                            Unblocking <strong id="unblockUsername"></strong>.<br>
                                            <span class="text-muted small">Current usage: <strong id="unblockUsedGb"></strong> GB. New limit must exceed this.</span>
                                        </p>
                                        <div id="unblockError" class="alert alert-danger d-none"></div>
                                        <div class="mb-3">
                                            <label for="unblockLimitInput" class="form-label fw-semibold">New Bandwidth Limit (GB)</label>
                                            <input type="number" id="unblockLimitInput" class="form-control" step="0.001" min="0.001" placeholder="e.g. 5.000">
                                            <div class="form-text">Must be greater than current usage.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-success" id="unblockSubmitBtn" onclick="submitUnblock()">
                                            <span id="unblockSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                            Unblock &amp; Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

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

                                                <!-- Unit Selector -->
                                                <select id="unitSelector" class="form-select form-select-sm" style="width: auto; min-width: 80px;">
                                                    <option value="gb">GB</option>
                                                    <option value="mb">MB</option>
                                                </select>

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
</div>
@endsection

@push('scripts')
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
                        label: 'Bandwidth',
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
                                    const unit = document.getElementById('unitSelector').value.toUpperCase();
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' ' + unit;
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
                                    const unit = document.getElementById('unitSelector').value.toUpperCase();
                                    return value.toFixed(1) + ' ' + unit;
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
                    // Get selected unit
                    const unit = document.getElementById('unitSelector').value;
                    const values = unit === 'mb' ? data.values_mb : data.values_gb;

                    // Update chart
                    bandwidthChart.data.labels = data.labels || [];
                    bandwidthChart.data.datasets[0].data = values || [];
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
        const unitSelector = document.getElementById('unitSelector');
        const refreshButton = document.getElementById('refreshChart');

        if (timeRangeFilter) {
            timeRangeFilter.addEventListener('change', loadBandwidthData);
        }
        if (proxyUserFilter) {
            proxyUserFilter.addEventListener('change', loadBandwidthData);
        }
        if (unitSelector) {
            unitSelector.addEventListener('change', loadBandwidthData);
        }
        if (refreshButton) {
            refreshButton.addEventListener('click', loadBandwidthData);
        }

        // Load initial data
        if (bandwidthChart) {
            loadBandwidthData();
        }
    </script>
</script>

<script>
    // Unblock Modal
    let unblockTargetId = null;

    function openUnblockModal(id, username) {
        unblockTargetId = id;
        document.getElementById('unblockUsername').textContent = username;
        document.getElementById('unblockUsedGb').textContent = '…';
        document.getElementById('unblockLimitInput').value = '';
        document.getElementById('unblockError').classList.add('d-none');

        const modal = new bootstrap.Modal(document.getElementById('unblockModal'));
        modal.show();

        // Fetch fresh usage stats so the user sees the real current value
        const statusUrl = '{{ route("user.blocked.status", ["id" => "__ID__"]) }}'.replace('__ID__', id);
        fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('unblockUsedGb').textContent = data.used_gb;
                    const limitInput = document.getElementById('unblockLimitInput');
                    limitInput.min = data.used_gb;
                    limitInput.value = Math.ceil(data.used_gb + 1);
                }
            });
    }

    function submitUnblock() {
        if (!unblockTargetId) return;

        const limitInput = document.getElementById('unblockLimitInput');
        const newLimit = parseFloat(limitInput.value);
        const errorDiv = document.getElementById('unblockError');
        const submitBtn = document.getElementById('unblockSubmitBtn');

        errorDiv.classList.add('d-none');

        if (!newLimit || newLimit <= 0) {
            errorDiv.textContent = 'Please enter a valid bandwidth limit greater than 0.';
            errorDiv.classList.remove('d-none');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Unblocking...';

        const url = '{{ route("user.blocked.unblock", ["id" => "__ID__"]) }}'.replace('__ID__', unblockTargetId);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ bandwidth_limit_gb: newLimit }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                errorDiv.textContent = data.error || 'Failed to unblock user.';
                errorDiv.classList.remove('d-none');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Unblock User';
            }
        })
        .catch(() => {
            errorDiv.textContent = 'Network error. Please try again.';
            errorDiv.classList.remove('d-none');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Unblock User';
        });
    }
</script>
@endpush
