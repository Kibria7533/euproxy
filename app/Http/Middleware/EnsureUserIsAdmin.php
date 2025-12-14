<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
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
            return redirect()->route('admin.login');
        }

        // Check if user is an administrator
        if (!Auth::user()->is_administrator) {
            // User is not an admin, redirect to user dashboard with error
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access. You do not have administrator privileges.');
        }

        return $next($request);
    }
}
