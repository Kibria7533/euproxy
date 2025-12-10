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
    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');
        
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
    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\User\UserDashboardController::class, 'index'])->name('user.dashboard');
    });
});

// Legacy routes for backwards compatibility
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::post('/logout', function () {
    return redirect()->route('admin.logout');
})->name('logout');

Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->name('home');


