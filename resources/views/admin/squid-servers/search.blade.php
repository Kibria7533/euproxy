@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h3 class="fw-bold mb-1">Proxy Servers</h3>
            <p class="text-muted mb-0">Manage Squid proxy server nodes</p>
        </div>
        <a href="{{ route('admin.squid-servers.creator') }}" class="btn btn-success" style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            Add Server
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search / Filter -->
    <div class="card mb-4" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.squid-servers.search') }}" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control" placeholder="Search IP, hostname, location..." value="{{ $search ?? '' }}" style="border-radius: 8px; min-width: 200px; flex: 1;">
                <select name="proxy_type_id" class="form-select" style="border-radius: 8px; min-width: 200px; flex: 1;">
                    <option value="">All Proxy Types</option>
                    @foreach($proxyTypes as $type)
                        <option value="{{ $type->id }}" {{ ($filterTypeId ?? '') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; white-space: nowrap;">Filter</button>
                @if($search || $filterTypeId)
                    <a href="{{ route('admin.squid-servers.search') }}" class="btn btn-secondary" style="border-radius: 8px;">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr style="background-color: #f8fafc;">
                            <th style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">ID</th>
                            <th style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Proxy Type</th>
                            <th style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">IP Address</th>
                            <th style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Hostname</th>
                            <th style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Port</th>
                            <th style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Location</th>
                            <th style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Status</th>
                            <th style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servers as $server)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1rem;">#{{ $server->id }}</td>
                                <td style="padding: 1rem;">
                                    @if($server->proxyType)
                                        <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background-color: #ede9fe; color: #5b21b6;">
                                            {{ $server->proxyType->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    <code style="background-color: #f1f5f9; padding: 4px 8px; border-radius: 4px;">{{ $server->ip }}</code>
                                </td>
                                <td style="padding: 1rem;">
                                    @if($server->hostname)
                                        <span style="color: #475569;">{{ $server->hostname }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">{{ $server->port }}</td>
                                <td style="padding: 1rem;">{{ $server->location ?? '—' }}</td>
                                <td style="padding: 1rem;">
                                    @if($server->is_active)
                                        <span style="padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background-color: #d1fae5; color: #065f46;">Active</span>
                                    @else
                                        <span style="padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background-color: #fee2e2; color: #991b1b;">Inactive</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <a href="{{ route('admin.squid-servers.editor', $server->id) }}" class="btn btn-sm" style="padding: 6px 16px; border-radius: 6px; background-color: #eff6ff; color: #2563eb; text-decoration: none;">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.squid-servers.destroy', $server->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Delete this server?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm" style="padding: 6px 16px; border-radius: 6px; background-color: #fef2f2; color: #dc2626; border: none;">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 3rem; text-align: center;">
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem; opacity: 0.3;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                        <p>No proxy servers found. <a href="{{ route('admin.squid-servers.creator') }}">Add one.</a></p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $servers->links() }}
    </div>
</div>
@endsection
