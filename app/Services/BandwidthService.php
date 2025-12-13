<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BandwidthService
{
    /**
     * Get last 7 days bandwidth usage for a specific username
     *
     * @param string $username
     * @return array
     */
    public function getLast7DaysBandwidth(string $username): array
    {
        $results = DB::table('proxy_requests')
            ->select(DB::raw('DATE(created_at) as date, SUM(bytes) as total_bytes'))
            ->where('username', $username)
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Fill in missing days with 0
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $found = $results->firstWhere('date', $date);
            $data[] = [
                'date' => $date,
                'label' => now()->subDays($i)->format('M d'),
                'bytes' => $found ? $found->total_bytes : 0,
                'gb' => $found ? round($found->total_bytes / 1073741824, 2) : 0,
            ];
        }

        return $data;
    }

    /**
     * Get last 7 days bandwidth usage for multiple usernames (aggregated)
     *
     * @param array $usernames
     * @return array
     */
    public function getLast7DaysBandwidthForMultipleUsers(array $usernames): array
    {
        if (empty($usernames)) {
            // Return empty data for 7 days
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $data[] = [
                    'date' => now()->subDays($i)->format('Y-m-d'),
                    'label' => now()->subDays($i)->format('M d'),
                    'bytes' => 0,
                    'gb' => 0,
                ];
            }
            return $data;
        }

        $results = DB::table('proxy_requests')
            ->select(DB::raw('DATE(created_at) as date, SUM(bytes) as total_bytes'))
            ->whereIn('username', $usernames)
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Fill in missing days with 0
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $found = $results->firstWhere('date', $date);
            $data[] = [
                'date' => $date,
                'label' => now()->subDays($i)->format('M d'),
                'bytes' => $found ? $found->total_bytes : 0,
                'gb' => $found ? round($found->total_bytes / 1073741824, 2) : 0,
            ];
        }

        return $data;
    }

    /**
     * Get top users by bandwidth usage
     *
     * @param int $limit Number of users to return
     * @param int $days Number of days to look back
     * @return Collection
     */
    public function getTopUsersByBandwidth(int $limit = 5, int $days = 7): Collection
    {
        return DB::table('proxy_requests')
            ->select('username', DB::raw('SUM(bytes) as total_bytes'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('username')
            ->orderBy('total_bytes', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                $item->total_gb = round($item->total_bytes / 1073741824, 2);
                return $item;
            });
    }

    /**
     * Get total bandwidth used by a specific username (all time)
     *
     * @param string $username
     * @return float Bandwidth in GB
     */
    public function getTotalBandwidthUsed(string $username): float
    {
        $bytes = DB::table('proxy_requests')
            ->where('username', $username)
            ->sum('bytes');

        return round($bytes / 1073741824, 2);
    }

    /**
     * Get bandwidth data with flexible time range and granularity
     *
     * @param array $usernames Array of usernames to filter
     * @param string $range Time range: 'hour', 'today', '7days', '30days', 'custom'
     * @param string|null $startDate Custom start date (Y-m-d H:i:s)
     * @param string|null $endDate Custom end date (Y-m-d H:i:s)
     * @return array
     */
    public function getBandwidthData(array $usernames, string $range = '7days', ?string $startDate = null, ?string $endDate = null): array
    {
        // Determine time range and granularity
        switch ($range) {
            case 'hour':
                $start = now()->subHour();
                $end = now();
                $granularity = '5minute';
                // Group by 5-minute intervals by rounding down minutes to nearest 5
                $groupBy = "CONCAT(DATE_FORMAT(created_at, '%Y-%m-%d %H:'), LPAD(FLOOR(MINUTE(created_at) / 5) * 5, 2, '0'), ':00')";
                $labelFormat = 'H:i';
                $intervals = 12; // 12 intervals of 5 minutes each
                break;

            case 'today':
                $start = now()->startOfDay();
                $end = now();
                $granularity = 'hour';
                $groupBy = "DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')";
                $labelFormat = 'H:00';
                $intervals = 24; // 24 hours
                break;

            case '7days':
                $start = now()->subDays(6)->startOfDay();
                $end = now();
                $granularity = 'day';
                $groupBy = "DATE(created_at)";
                $labelFormat = 'M d';
                $intervals = 7;
                break;

            case '30days':
                $start = now()->subDays(29)->startOfDay();
                $end = now();
                $granularity = 'day';
                $groupBy = "DATE(created_at)";
                $labelFormat = 'M d';
                $intervals = 30;
                break;

            case 'custom':
                if (!$startDate || !$endDate) {
                    throw new \InvalidArgumentException('Start and end dates required for custom range');
                }
                $start = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);
                $diffDays = $start->diffInDays($end);

                if ($diffDays <= 1) {
                    $granularity = 'hour';
                    $groupBy = "DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')";
                    $labelFormat = 'M d H:00';
                    $intervals = $start->diffInHours($end);
                } else {
                    $granularity = 'day';
                    $groupBy = "DATE(created_at)";
                    $labelFormat = 'M d';
                    $intervals = $diffDays + 1;
                }
                break;

            default:
                throw new \InvalidArgumentException('Invalid range specified');
        }

        // Query database
        $query = DB::table('proxy_requests')
            ->select(DB::raw("{$groupBy} as period, SUM(bytes) as total_bytes"))
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->groupBy(DB::raw($groupBy))
            ->orderBy('period', 'asc');

        if (!empty($usernames)) {
            $query->whereIn('username', $usernames);
        }

        $results = $query->get();

        // Fill in missing periods with 0
        $data = [];
        $current = clone $start;

        for ($i = 0; $i < $intervals; $i++) {
            if ($granularity === '5minute') {
                // Round down to nearest 5 minutes
                $minute = floor($current->minute / 5) * 5;
                $current->minute = $minute;
                $current->second = 0;
                $periodKey = $current->format('Y-m-d H:i:00');
                $label = $current->format($labelFormat);
                $current->addMinutes(5);
            } elseif ($granularity === 'minute') {
                $periodKey = $current->format('Y-m-d H:i:00');
                $label = $current->format($labelFormat);
                $current->addMinute();
            } elseif ($granularity === 'hour') {
                $periodKey = $current->format('Y-m-d H:00:00');
                $label = $current->format($labelFormat);
                $current->addHour();
            } else { // day
                $periodKey = $current->format('Y-m-d');
                $label = $current->format($labelFormat);
                $current->addDay();
            }

            $found = $results->firstWhere('period', $periodKey);
            $bytes = $found ? $found->total_bytes : 0;

            $data[] = [
                'period' => $periodKey,
                'label' => $label,
                'bytes' => $bytes,
                'gb' => round($bytes / 1073741824, 2),
                'mb' => round($bytes / 1048576, 2),
            ];
        }

        return $data;
    }

    /**
     * Get bandwidth data for multiple users separately (for comparison)
     *
     * @param array $usernames
     * @param string $range
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array Array of datasets, one per username
     */
    public function getBandwidthDataPerUser(array $usernames, string $range = '7days', ?string $startDate = null, ?string $endDate = null): array
    {
        if (empty($usernames)) {
            return [];
        }

        $datasets = [];

        foreach ($usernames as $username) {
            $datasets[$username] = $this->getBandwidthData([$username], $range, $startDate, $endDate);
        }

        return $datasets;
    }
}
