<?php

namespace App\Providers;

use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\StripePaymentGateway;
use App\Services\Payment\PayPalPaymentGateway;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register payment gateways as singletons
        $this->app->singleton(StripePaymentGateway::class, function ($app) {
            return new StripePaymentGateway();
        });

        $this->app->singleton(PayPalPaymentGateway::class, function ($app) {
            return new PayPalPaymentGateway();
        });

        // Bind the default gateway based on config
        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            $defaultGateway = config('payment.default_gateway', 'stripe');

            return match ($defaultGateway) {
                'stripe' => $app->make(StripePaymentGateway::class),
                'paypal' => $app->make(PayPalPaymentGateway::class),
                default => $app->make(StripePaymentGateway::class),
            };
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
            PayPalPaymentGateway::class,
        ];
    }
}
