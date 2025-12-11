<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SquidUser;
use App\Models\SquidAllowedIp;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_squid_users' => SquidUser::count(),
            'enabled_squid_users' => SquidUser::where('enabled', true)->count(),
            'total_allowed_ips' => SquidAllowedIp::count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_squid_users' => SquidUser::latest()->take(5)->get(),
        ];

        return view('home', compact('stats'));
    }
}
