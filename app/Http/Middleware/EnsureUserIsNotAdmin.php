<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('user.login');
        }

        // Check if user is an administrator
        if (Auth::user()->is_administrator === 1) {
            // User is an admin, redirect to admin dashboard
            return redirect()->route('admin.dashboard')
                ->with('info', 'You are logged in as an administrator. Please use the admin dashboard.');
        }

        return $next($request);
    }
}
