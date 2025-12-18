@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h3 class="fw-bold mb-1">Proxy Plans</h3>
            <p class="text-muted mb-0">Manage pricing plans and bandwidth packages</p>
        </div>
        <a href="{{ route('admin.proxy-plans.creator') }}" class="btn btn-success" style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            Create Plan
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

    <div class="card mb-3" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.proxy-plans.search') }}" class="row g-3">
                <div class="col-md-5">
                    <input type="text"
                           class="form-control"
                           name="search"
                           placeholder="Search by plan name..."
                           value="{{ $search }}"
                           style="border-radius: 8px; padding: 10px 14px;">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="proxy_type_id" style="border-radius: 8px; padding: 10px 14px;">
                        <option value="">All Proxy Types</option>
                        @foreach($proxyTypes as $type)
                            <option value="{{ $type->id }}" {{ $selectedType == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 8px; padding: 10px 14px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background-color: #f8fafc;">
                            <th scope="col" style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">ID</th>
                            <th scope="col" style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Plan Name</th>
                            <th scope="col" style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Proxy Type</th>
                            <th scope="col" style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Bandwidth</th>
                            <th scope="col" style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Pricing</th>
                            <th scope="col" style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem;">Status</th>
                            <th scope="col" style="padding: 1rem; color: #475569; font-weight: 600; font-size: 0.875rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1rem;">#{{ $plan->id }}</td>
                                <td style="padding: 1rem;">
                                    <div class="fw-semibold">{{ $plan->name }}</div>
                                    @if($plan->is_popular)
                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; background-color: #fef3c7; color: #92400e; margin-top: 4px;">
                                            POPULAR
                                        </span>
                                    @endif
                                    @if($plan->is_free_trial)
                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; background-color: #d1fae5; color: #065f46; margin-top: 4px;">
                                            FREE TRIAL
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="badge bg-info">{{ $plan->proxyType->name }}</span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div class="fw-semibold">{{ number_format($plan->bandwidth_gb, 2) }} GB</div>
                                    <div class="text-muted small">${{ number_format($plan->price_per_gb, 2) }}/GB</div>
                                </td>
                                <td style="padding: 1rem;">
                                    @if($plan->discount_percentage > 0)
                                        <div>
                                            <span class="text-muted" style="text-decoration: line-through;">${{ number_format($plan->base_price, 2) }}</span>
                                            <span class="fw-bold text-success ms-1">${{ number_format($plan->final_price, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-danger">-{{ $plan->discount_percentage }}%</span>
                                        </div>
                                    @else
                                        <div class="fw-bold">${{ number_format($plan->base_price, 2) }}</div>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if($plan->is_active)
                                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background-color: #d1fae5; color: #065f46;">
                                            Active
                                        </span>
                                    @else
                                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background-color: #fee2e2; color: #991b1b;">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <a href="{{ route('admin.proxy-plans.editor', $plan->id) }}" class="btn btn-sm" style="padding: 6px 16px; border-radius: 6px; background-color: #eff6ff; color: #2563eb; text-decoration: none;">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.proxy-plans.destroy', $plan->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm" style="padding: 6px 16px; border-radius: 6px; background-color: #fef2f2; color: #dc2626; border: none;">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 3rem; text-align: center;">
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem; opacity: 0.3;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                        <p>No proxy plans found</p>
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
        {{ $plans->links() }}
    </div>
</div>
@endsection
