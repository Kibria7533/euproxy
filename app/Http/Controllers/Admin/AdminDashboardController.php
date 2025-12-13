<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SquidUser;
use App\Models\SquidAllowedIp;
use App\Models\Scopes\SquidUserScope;
use App\Services\BandwidthService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    private $bandwidthService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BandwidthService $bandwidthService)
    {
        $this->middleware('auth');
        $this->bandwidthService = $bandwidthService;
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

        // Top bandwidth users (last 7 days)
        $stats['top_bandwidth_users'] = $this->bandwidthService->getTopUsersByBandwidth(5, 7);

        // All squid users with bandwidth info (top 10)
        $stats['squid_users_bandwidth'] = SquidUser::withoutGlobalScope(SquidUserScope::class)
            ->get()
            ->map(function ($squidUser) {
                return [
                    'username' => $squidUser->user,
                    'total_bandwidth_gb' => $squidUser->total_bandwidth_used,
                    'bandwidth_limit_gb' => $squidUser->bandwidth_limit_gb,
                    'usage_percentage' => $squidUser->bandwidth_usage_percentage,
                    'is_over_limit' => $squidUser->isOverBandwidthLimit(),
                ];
            })
            ->sortByDesc('total_bandwidth_gb')
            ->take(10);

        return view('home', compact('stats'));
    }
}
