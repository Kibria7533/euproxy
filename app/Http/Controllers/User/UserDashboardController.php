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

    /**
     * Get bandwidth data via AJAX
     */
    public function getBandwidthData(Request $request)
    {
        try {
            $user = Auth::user();

            // Get user's squid usernames
            $userSquidUsers = SquidUser::where('user_id', $user->id)->get();
            $allUsernames = $userSquidUsers->pluck('user')->toArray();

            // Get filter parameters
            $range = $request->input('range', '7days');

            // Get usernames array (sent as usernames[] from JavaScript)
            $selectedUsernames = $request->input('usernames', []);

            // If empty, use all usernames
            if (empty($selectedUsernames) || !is_array($selectedUsernames)) {
                $selectedUsernames = $allUsernames;
            }

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Ensure selected usernames belong to this user
            $selectedUsernames = array_intersect($selectedUsernames, $allUsernames);

            if (empty($selectedUsernames)) {
                $selectedUsernames = $allUsernames;
            }

            // Get bandwidth data
            $bandwidthData = $this->bandwidthService->getBandwidthData(
                $selectedUsernames,
                $range,
                $startDate,
                $endDate
            );

            \Log::info('Bandwidth data request', [
                'range' => $range,
                'usernames' => $selectedUsernames,
                'data_count' => count($bandwidthData),
            ]);

            return response()->json([
                'success' => true,
                'data' => $bandwidthData,
                'labels' => array_column($bandwidthData, 'label'),
                'values_gb' => array_column($bandwidthData, 'gb'),
                'values_mb' => array_column($bandwidthData, 'mb'),
                'debug' => [
                    'range' => $range,
                    'usernames_count' => count($selectedUsernames),
                    'data_points' => count($bandwidthData),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Bandwidth data fetch error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch bandwidth data',
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }
}
