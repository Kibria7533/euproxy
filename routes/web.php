<?php

use App\Http\Controllers\Gui\SquidAllowedIpController;
use App\Http\Controllers\Gui\SquidUserController;
use App\Http\Controllers\Gui\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('user.login');
});

// Admin routes (existing backend login/dashboard)
Route::prefix('admin')->group(function () {
    // Admin auth routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [\App\Http\Controllers\Auth\AdminLoginController::class, 'login'])->name('admin.login.post');
    });
    
    Route::post('/logout', [\App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('admin.logout');

    // Admin authenticated routes (existing dashboard)
    Route::middleware(['auth:web', 'admin'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');
        
        Route::prefix('account')->group(function () {
            Route::post('create', [UserController::class, 'create'])->name('user.create');
            Route::post('modify/{id}', [UserController::class, 'modify'])->name('user.modify');
            Route::post('destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');
            Route::get('search', [UserController::class, 'search'])->name('user.search');
            Route::get('creator', [UserController::class, 'creator'])->name('user.creator');
            Route::get('editor/{id}', [UserController::class, 'editor'])->name('user.editor');
        });
        
        Route::prefix('squidallowedip')->group(function () {
            Route::get('search/to_specified_user/{user_id}', [SquidAllowedIpController::class, 'search'])->name('ip.search');
            Route::get('creator', [SquidAllowedIpController::class, 'creator'])->name('ip.creator');
            Route::get('editor/{id}', [SquidAllowedIpController::class, 'editor'])->name('ip.editor');
            Route::post('create/to_specified_user/{user_id}', [SquidAllowedIpController::class, 'create'])->name('ip.create');
            Route::post('destroy/{id}', [SquidAllowedIpController::class, 'destroy'])->name('ip.destroy');
        });
        
        Route::prefix('squiduser')->group(function () {
            Route::get('search/to_specified_user/{user_id}', [SquidUserController::class, 'search'])->name('squiduser.search');
            Route::get('creator', [SquidUserController::class, 'creator'])->name('squiduser.creator');
            Route::get('editor/{id}', [SquidUserController::class, 'editor'])->name('squiduser.editor');
            Route::post('create/to_specified_user/{user_id}', [SquidUserController::class, 'create'])->name('squiduser.create');
            Route::post('modify/{id}', [SquidUserController::class, 'modify'])->name('squiduser.modify');
            Route::post('destroy/{id}', [SquidUserController::class, 'destroy'])->name('squiduser.destroy');
        });

        // Proxy Types Management
        Route::prefix('proxy-types')->group(function () {
            Route::get('search', [\App\Http\Controllers\Admin\ProxyTypeController::class, 'search'])->name('admin.proxy-types.search');
            Route::get('creator', [\App\Http\Controllers\Admin\ProxyTypeController::class, 'creator'])->name('admin.proxy-types.creator');
            Route::get('editor/{id}', [\App\Http\Controllers\Admin\ProxyTypeController::class, 'editor'])->name('admin.proxy-types.editor');
            Route::post('create', [\App\Http\Controllers\Admin\ProxyTypeController::class, 'create'])->name('admin.proxy-types.create');
            Route::post('modify/{id}', [\App\Http\Controllers\Admin\ProxyTypeController::class, 'modify'])->name('admin.proxy-types.modify');
            Route::post('destroy/{id}', [\App\Http\Controllers\Admin\ProxyTypeController::class, 'destroy'])->name('admin.proxy-types.destroy');
        });

        // Proxy Plans Management
        Route::prefix('proxy-plans')->group(function () {
            Route::get('search', [\App\Http\Controllers\Admin\ProxyPlanController::class, 'search'])->name('admin.proxy-plans.search');
            Route::get('creator', [\App\Http\Controllers\Admin\ProxyPlanController::class, 'creator'])->name('admin.proxy-plans.creator');
            Route::get('editor/{id}', [\App\Http\Controllers\Admin\ProxyPlanController::class, 'editor'])->name('admin.proxy-plans.editor');
            Route::post('create', [\App\Http\Controllers\Admin\ProxyPlanController::class, 'create'])->name('admin.proxy-plans.create');
            Route::post('modify/{id}', [\App\Http\Controllers\Admin\ProxyPlanController::class, 'modify'])->name('admin.proxy-plans.modify');
            Route::post('destroy/{id}', [\App\Http\Controllers\Admin\ProxyPlanController::class, 'destroy'])->name('admin.proxy-plans.destroy');
        });
    });
});

// User routes (new user login/register/dashboard)
Route::prefix('user')->group(function () {
    // User auth routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Auth\UserLoginController::class, 'showLoginForm'])->name('user.login');
        Route::post('/login', [\App\Http\Controllers\Auth\UserLoginController::class, 'login'])->name('user.login.post');
        Route::get('/register', [\App\Http\Controllers\Auth\UserRegisterController::class, 'showRegistrationForm'])->name('user.register');
        Route::post('/register', [\App\Http\Controllers\Auth\UserRegisterController::class, 'register'])->name('user.register.post');
    });
    
    Route::post('/logout', [\App\Http\Controllers\Auth\UserLoginController::class, 'logout'])->name('user.logout');

    // User authenticated routes (new dashboard - to be created)
    Route::middleware(['auth:web', 'user'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\User\UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::get('/bandwidth-data', [\App\Http\Controllers\User\UserDashboardController::class, 'getBandwidthData'])->name('user.bandwidth.data');

        // User's proxy management routes (uses same controllers as admin, but authorization via policies)
        Route::prefix('squiduser')->group(function () {
            Route::get('search', [SquidUserController::class, 'search'])->name('user.squiduser.search');
            Route::get('creator', [SquidUserController::class, 'creator'])->name('user.squiduser.creator');
            Route::get('editor/{id}', [SquidUserController::class, 'editor'])->name('user.squiduser.editor');
            Route::post('create', [SquidUserController::class, 'create'])->name('user.squiduser.create');
            Route::post('modify/{id}', [SquidUserController::class, 'modify'])->name('user.squiduser.modify');
            Route::post('destroy/{id}', [SquidUserController::class, 'destroy'])->name('user.squiduser.destroy');
        });

        Route::prefix('ip')->group(function () {
            Route::get('search', [SquidAllowedIpController::class, 'search'])->name('user.ip.search');
            Route::get('creator', [SquidAllowedIpController::class, 'creator'])->name('user.ip.creator');
            Route::get('editor/{id}', [SquidAllowedIpController::class, 'editor'])->name('user.ip.editor');
            Route::post('create', [SquidAllowedIpController::class, 'create'])->name('user.ip.create');
            Route::post('destroy/{id}', [SquidAllowedIpController::class, 'destroy'])->name('user.ip.destroy');
        });

        // Proxy Services - Browse and purchase plans
        Route::prefix('proxies')->group(function () {
            Route::get('/{slug}', [\App\Http\Controllers\User\UserProxyController::class, 'show'])->name('user.proxies.show');
            Route::get('/{slug}/buy', [\App\Http\Controllers\User\UserProxyController::class, 'buyPlans'])->name('user.proxies.buy');
            Route::get('/{slug}/configuration', [\App\Http\Controllers\User\UserProxyController::class, 'configuration'])->name('user.proxies.configuration');
            Route::get('/{slug}/subscriptions', [\App\Http\Controllers\User\UserProxyController::class, 'subscriptions'])->name('user.proxies.subscriptions');
            Route::get('/{slug}/documentation', [\App\Http\Controllers\User\UserProxyController::class, 'documentation'])->name('user.proxies.documentation');
        });

        // Checkout & Payment
        Route::prefix('checkout')->group(function () {
            Route::get('/review/{planId}', [\App\Http\Controllers\User\UserCheckoutController::class, 'review'])->name('user.checkout.review');
            Route::post('/process', [\App\Http\Controllers\User\UserCheckoutController::class, 'process'])->name('user.checkout.process');
            Route::get('/success', [\App\Http\Controllers\User\UserCheckoutController::class, 'success'])->name('user.checkout.success');
            Route::get('/cancel/{order?}', [\App\Http\Controllers\User\UserCheckoutController::class, 'cancel'])->name('user.checkout.cancel');
        });
    });
});

// Legacy routes for backwards compatibility
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::post('/logout', [\App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('logout');

Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->name('home');

// Payment Gateway Webhooks (CSRF excluded in middleware)
Route::prefix('webhooks')->group(function () {
    Route::post('/stripe', [\App\Http\Controllers\Webhooks\StripeWebhookController::class, 'handle'])->name('webhooks.stripe');
    Route::post('/paypal', [\App\Http\Controllers\Webhooks\PayPalWebhookController::class, 'handle'])->name('webhooks.paypal');
});

