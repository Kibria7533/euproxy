<?php

namespace App\Providers;

use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\StripePaymentGateway;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Stripe payment gateway as singleton
        $this->app->singleton(StripePaymentGateway::class, function ($app) {
            return new StripePaymentGateway();
        });

        // Bind the payment gateway interface to Stripe
        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            return $app->make(StripePaymentGateway::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            PaymentGatewayInterface::class,
            StripePaymentGateway::class,
        ];
    }
}
