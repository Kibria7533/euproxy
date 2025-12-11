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
    .action-btn-edit {
        background-color: #eff6ff;
        color: #2563eb;
        text-decoration: none;
        display: inline-block;
    }
    .action-btn-edit:hover {
        background-color: #dbeafe;
        color: #2563eb;
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
    .status-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-badge-active {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-badge-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .status-indicator {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1">Proxy Users</h3>
            <p class="text-muted mb-0">Manage Squid proxy user accounts and credentials</p>
        </div>
        <a href="{{ route('squiduser.creator') }}" class="btn btn-success" style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            Create Proxy User
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
                            <th scope="col">{{ __('Username') }}</th>
                            <th scope="col" style="width: 140px;">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Full Name') }}</th>
                            <th scope="col">{{ __('Comment') }}</th>
                            <th scope="col" style="width: 180px; text-align: right; padding-right: 1.5rem;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                @can('create-user')
                                    <td class="fw-semibold text-muted">#{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem; margin-right: 10px;">
                                                {{ strtoupper(substr($user->laravel_user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span>{{ $user->laravel_user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                @endcan
                                <td>
                                    <span class="fw-semibold">{{ $user->user }}</span>
                                </td>
                                <td>
                                    @if($user->enabled == 1)
                                        <span class="status-badge status-badge-active">
                                            <span class="status-indicator" style="background-color: #10b981;"></span>
                                            {{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="status-badge status-badge-inactive">
                                            <span class="status-indicator" style="background-color: #dc2626;"></span>
                                            {{ __('Disabled') }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $user->fullname ?? '-' }}</td>
                                <td>
                                    <span class="text-muted">{{ $user->comment ? Str::limit($user->comment, 40) : '-' }}</span>
                                </td>
                                <td style="text-align: right; padding-right: 1.5rem;">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('squiduser.editor',$user->id) }}" class="action-btn action-btn-edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Edit
                                        </a>
                                        <form method="post" action="{{ route('squiduser.destroy',$user->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-delete" onclick="return confirm('Are you sure you want to delete this proxy user?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('create-user') ? '7' : '5' }}" class="text-center py-5 text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.3; margin-bottom: 1rem;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                    <div>No proxy users found</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white" style="border-top: 1px solid #f1f5f9; padding: 1rem 1.5rem;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
