@extends('layouts.app')

@section('content')
<style>
    .modern-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem;
    }
    .modern-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s;
    }
    .modern-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .modern-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    .action-btn {
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .action-btn-delete {
        background-color: #fef2f2;
        color: #dc2626;
    }
    .action-btn-delete:hover {
        background-color: #fee2e2;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .ip-badge {
        padding: 8px 16px;
        border-radius: 8px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        font-weight: 600;
        font-family: 'Courier New', monospace;
        display: inline-block;
    }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1">Allowed IP Addresses</h3>
            <p class="text-muted mb-0">Manage whitelisted IP addresses for proxy access</p>
        </div>
        <a href="{{ route('ip.creator') }}" class="btn btn-warning text-white" style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
            Add IP Address
        </a>
    </div>

    <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table modern-table mb-0">
                    <thead>
                        <tr>
                            @can('create-user')
                                <th scope="col" style="width: 80px;">{{ __('Id') }}</th>
                                <th scope="col">{{ __('Owner') }}</th>
                            @endcan
                            <th scope="col">{{ __('IP Address') }}</th>
                            <th scope="col" style="width: 150px; text-align: right; padding-right: 1.5rem;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ips as $ip)
                            <tr>
                                @can('create-user')
                                    <td class="fw-semibold text-muted">#{{ $ip->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem; margin-right: 10px;">
                                                {{ strtoupper(substr($ip->laravel_user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span>{{ $ip->laravel_user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                @endcan
                                <td>
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 10px; color: #f59e0b;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                        <code class="fw-bold" style="background-color: #fef3c7; padding: 6px 12px; border-radius: 6px; color: #92400e;">{{ $ip->ip }}</code>
                                    </div>
                                </td>
                                <td style="text-align: right; padding-right: 1.5rem;">
                                    <form method="post" action="{{ route('ip.destroy',$ip->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-delete" onclick="return confirm('Are you sure you want to remove this IP address?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('create-user') ? '4' : '2' }}" class="text-center py-5 text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.3; margin-bottom: 1rem;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                    <div>No IP addresses whitelisted</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($ips->hasPages())
            <div class="card-footer bg-white" style="border-top: 1px solid #f1f5f9; padding: 1rem 1.5rem;">
                {{ $ips->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
