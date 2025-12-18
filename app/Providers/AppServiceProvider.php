<?php

namespace App\Providers;

use App\Models\ProxyType;
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

        // Share active proxy types with all views
        View::composer('*', function ($view) {
            $proxyTypes = ProxyType::active()
                ->orderBy('sort_order')
                ->get();

            $view->with('proxyTypes', $proxyTypes);
        });
    }
}
