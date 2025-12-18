@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <div>
            <h3 class="fw-bold mb-1">{{ $proxyType->name }}</h3>
            @if($proxyType->description)
                <p class="text-muted mb-0">{{ $proxyType->description }}</p>
            @endif
        </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" style="border-bottom: 2px solid #e2e8f0;">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'buy' ? 'active' : '' }}"
               href="{{ route('user.proxies.buy', $proxyType->slug) }}"
               style="padding: 12px 24px; font-weight: 500; color: {{ $activeTab === 'buy' ? '#3b82f6' : '#64748b' }}; border: none; border-bottom: 3px solid {{ $activeTab === 'buy' ? '#3b82f6' : 'transparent' }}; transition: all 0.2s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                Buy Plans
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'configuration' ? 'active' : '' }}"
               href="{{ route('user.proxies.configuration', $proxyType->slug) }}"
               style="padding: 12px 24px; font-weight: 500; color: {{ $activeTab === 'configuration' ? '#3b82f6' : '#64748b' }}; border: none; border-bottom: 3px solid {{ $activeTab === 'configuration' ? '#3b82f6' : 'transparent' }}; transition: all 0.2s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                Using Configuration
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'subscriptions' ? 'active' : '' }}"
               href="{{ route('user.proxies.subscriptions', $proxyType->slug) }}"
               style="padding: 12px 24px; font-weight: 500; color: {{ $activeTab === 'subscriptions' ? '#3b82f6' : '#64748b' }}; border: none; border-bottom: 3px solid {{ $activeTab === 'subscriptions' ? '#3b82f6' : 'transparent' }}; transition: all 0.2s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                My Subscriptions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'documentation' ? 'active' : '' }}"
               href="{{ route('user.proxies.documentation', $proxyType->slug) }}"
               style="padding: 12px 24px; font-weight: 500; color: {{ $activeTab === 'documentation' ? '#3b82f6' : '#64748b' }}; border: none; border-bottom: 3px solid {{ $activeTab === 'documentation' ? '#3b82f6' : 'transparent' }}; transition: all 0.2s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                Documentation
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'buy')
            @include('user.proxies.tabs.buy-plans')
        @elseif($activeTab === 'configuration')
            @include('user.proxies.tabs.configuration')
        @elseif($activeTab === 'subscriptions')
            @include('user.proxies.tabs.subscriptions')
        @elseif($activeTab === 'documentation')
            @include('user.proxies.tabs.documentation')
        @endif
    </div>
</div>

<style>
.nav-link:hover {
    color: #3b82f6 !important;
    background-color: transparent !important;
}
</style>
@endsection
