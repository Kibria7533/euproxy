<?php

namespace App\Providers;

use App\Models\ProxyType;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Use Bootstrap 5 for pagination
        Paginator::useBootstrapFive();

        // Share active proxy types with all views (for navigation)
        View::composer('*', function ($view) {
            $availableProxyTypes = ProxyType::active()
                ->orderBy('sort_order')
                ->get();

            $view->with('availableProxyTypes', $availableProxyTypes);
        });
    }
}
