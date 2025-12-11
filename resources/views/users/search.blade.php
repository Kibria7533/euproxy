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
    }
    .action-btn-edit:hover {
        background-color: #dbeafe;
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
</style>

<div class="container-fluid">
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1">Users Management</h3>
            <p class="text-muted mb-0">Manage all system users and administrators</p>
        </div>
        <a href="{{ route('user.creator') }}" class="btn btn-primary" style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y1="11"></line></svg>
            Create User
        </a>
    </div>

    <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table modern-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 80px;">{{ __('Id') }}</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Email') }}</th>
                            <th scope="col" style="width: 120px;">{{ __('Role') }}</th>
                            <th scope="col" style="width: 180px; text-align: right; padding-right: 1.5rem;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="fw-semibold text-muted">#{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem; margin-right: 12px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="fw-semibold">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->is_administrator == 1)
                                        <span class="badge" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 6px 12px; border-radius: 6px;">{{ __('Admin') }}</span>
                                    @else
                                        <span class="badge" style="background-color: #f1f5f9; color: #64748b; padding: 6px 12px; border-radius: 6px;">{{ __('User') }}</span>
                                    @endif
                                </td>
                                <td style="text-align: right; padding-right: 1.5rem;">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('user.editor',$user->id) }}" class="action-btn action-btn-edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Edit
                                        </a>
                                        <form method="post" action="{{ route('user.destroy',$user->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.3; margin-bottom: 1rem;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                    <div>No users found</div>
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
