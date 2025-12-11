<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SquidUser;
use App\Models\SquidAllowedIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Get user-specific statistics
        // Note: SquidUserScope automatically filters by user_id for non-admin users
        $stats = [
            'total_proxy_users' => SquidUser::count(),
            'enabled_proxy_users' => SquidUser::where('enabled', true)->count(),
            'total_allowed_ips' => SquidAllowedIp::count(),
            'recent_proxy_users' => SquidUser::latest()->take(5)->get(),
            'recent_allowed_ips' => SquidAllowedIp::latest()->take(5)->get(),
        ];

        return view('user.dashboard', compact('user', 'stats'));
    }
}
