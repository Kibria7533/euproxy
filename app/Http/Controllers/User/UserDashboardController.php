<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SquidUser;
use App\Models\SquidAllowedIp;
use App\Services\BandwidthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    private $bandwidthService;

    /**
     * Create a new controller instance.
     */
    public function __construct(BandwidthService $bandwidthService)
    {
        $this->middleware('auth');
        $this->bandwidthService = $bandwidthService;
    }

    /**
     * Show the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Get user-specific statistics
        // Note: SquidUserScope automatically filters by user_id for non-admin users
        // But we explicitly filter here to ensure data isolation
        $userSquidUsers = SquidUser::where('user_id', $user->id)->get();

        $stats = [
            'total_proxy_users' => $userSquidUsers->count(),
            'enabled_proxy_users' => $userSquidUsers->where('enabled', true)->count(),
            'total_allowed_ips' => SquidAllowedIp::where('user_id', $user->id)->count(),
            'recent_proxy_users' => $userSquidUsers->sortByDesc('created_at')->take(5),
            'recent_allowed_ips' => SquidAllowedIp::where('user_id', $user->id)->latest()->take(5)->get(),
        ];

        // Calculate bandwidth data for each SquidUser (only user's own) with individual 7-day data
        $squidUsersWithBandwidth = $userSquidUsers->map(function ($squidUser) {
            $last7Days = $this->bandwidthService->getLast7DaysBandwidth($squidUser->user);
            return [
                'username' => $squidUser->user,
                'total_bandwidth_gb' => $squidUser->total_bandwidth_used,
                'bandwidth_limit_gb' => $squidUser->bandwidth_limit_gb,
                'usage_percentage' => $squidUser->bandwidth_usage_percentage,
                'is_over_limit' => $squidUser->isOverBandwidthLimit(),
                'last_7_days' => $last7Days,
            ];
        });

        // Get 7-day aggregated data (only user's proxy users)
        $usernames = $userSquidUsers->pluck('user')->toArray();
        $last7DaysData = $this->bandwidthService->getLast7DaysBandwidthForMultipleUsers($usernames);

        $stats['bandwidth_data'] = $squidUsersWithBandwidth;
        $stats['last_7_days_bandwidth'] = $last7DaysData;

        return view('user.dashboard', compact('user', 'stats'));
    }
}
