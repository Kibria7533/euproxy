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
}
