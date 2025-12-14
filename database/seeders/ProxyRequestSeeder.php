<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProxyRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates 6 months of proxy request logs with realistic usage patterns
     */
    public function run(): void
    {
        $this->command->info('Generating 6 months of proxy request data...');
        $this->command->info('This may take a few minutes...');

        $usernames = ['user001', 'user002', 'user003', 'user004', 'user005',
                      'user007', 'user008', 'user009', 'user010'];

        // Different activity levels per user
        $userActivityLevels = [
            'user001' => 120,  // Medium activity
            'user002' => 250,  // High activity (near quota)
            'user003' => 200,  // High activity (exceeded)
            'user004' => 30,   // Low activity
            'user005' => 400,  // Very high (unlimited)
            'user007' => 10,   // Minimal (new user)
            'user008' => 230,  // High activity
            'user009' => 100,  // Medium activity
            'user010' => 300,  // Business user
        ];

        $clientIps = [
            '192.168.1.101', '192.168.1.102', '192.168.1.103',
            '10.0.0.50', '10.0.0.51', '10.0.0.52',
            '172.16.0.100', '172.16.0.101',
        ];

        $methods = ['GET', 'POST', 'CONNECT', 'HEAD'];
        $methodWeights = [0.70, 0.15, 0.10, 0.05]; // 70% GET, 15% POST, etc.

        $domains = [
            'example.com', 'api.example.com', 'cdn.example.com',
            'images.example.org', 'videos.example.net',
            'static.example.io', 'download.example.com',
            'streaming.example.tv', 'files.example.biz',
            'media.example.co'
        ];

        $paths = [
            '/api/v1/data', '/images/photo.jpg', '/videos/video.mp4',
            '/files/document.pdf', '/static/style.css', '/download/app.zip',
            '/stream/live', '/content/page.html', '/assets/script.js',
            '/media/audio.mp3', '/docs/manual.pdf', '/software/installer.exe'
        ];

        $statusCodes = [200, 200, 200, 200, 200, 304, 301, 302, 404, 403, 502]; // Weighted towards 200

        // Common file sizes in bytes (varied distribution)
        $fileSizes = [
            512, 1024, 2048, 5120, 10240, // Small files (KB range)
            51200, 102400, 256000, 512000, 1048576, // Medium (100KB - 1MB)
            2097152, 5242880, 10485760, 20971520, // Large (2-20 MB)
            52428800, 104857600, 209715200, 524288000 // Very large (50-500 MB)
        ];

        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        $batchSize = 500;
        $requests = [];
        $totalInserted = 0;

        foreach ($usernames as $username) {
            $requestsPerDay = $userActivityLevels[$username] ?? 50;
            $totalRequests = $requestsPerDay * 180; // 6 months ≈ 180 days

            $this->command->info("  Generating {$totalRequests} requests for {$username}...");

            for ($i = 0; $i < $totalRequests; $i++) {
                // Random timestamp within 6 months
                $randomDays = rand(0, 179);
                $randomHours = rand(0, 23);
                $randomMinutes = rand(0, 59);
                $randomSeconds = rand(0, 59);

                $timestamp = $startDate->copy()
                    ->addDays($randomDays)
                    ->addHours($randomHours)
                    ->addMinutes($randomMinutes)
                    ->addSeconds($randomSeconds);

                // Weighted method selection
                $method = $this->weightedRandom($methods, $methodWeights);

                // Random URL
                $domain = $domains[array_rand($domains)];
                $path = $paths[array_rand($paths)];
                $url = "http://{$domain}{$path}?id=" . rand(1000, 9999);

                // Random status code
                $status = $statusCodes[array_rand($statusCodes)];

                // File size varies by method and status
                if ($status !== 200) {
                    $bytes = rand(200, 2000); // Error responses are small
                } elseif ($method === 'HEAD') {
                    $bytes = rand(100, 500);
                } elseif ($method === 'CONNECT') {
                    $bytes = rand(10000, 100000000); // HTTPS tunnels can be large
                } else {
                    // Weighted towards smaller files, but occasional large ones
                    $sizeRoll = rand(1, 100);
                    if ($sizeRoll <= 50) {
                        $bytes = $fileSizes[rand(0, 4)]; // 50% small
                    } elseif ($sizeRoll <= 80) {
                        $bytes = $fileSizes[rand(5, 9)]; // 30% medium
                    } elseif ($sizeRoll <= 95) {
                        $bytes = $fileSizes[rand(10, 13)]; // 15% large
                    } else {
                        $bytes = $fileSizes[rand(14, 17)]; // 5% very large
                    }
                }

                $requests[] = [
                    'ts' => $timestamp->timestamp + ($timestamp->micro / 1000000),
                    'client_ip' => $clientIps[array_rand($clientIps)],
                    'username' => $username,
                    'method' => $method,
                    'url' => $url,
                    'status' => $status,
                    'bytes' => $bytes,
                    'created_at' => $timestamp,
                ];

                // Insert in batches
                if (count($requests) >= $batchSize) {
                    DB::table('proxy_requests')->insert($requests);
                    $totalInserted += count($requests);
                    $requests = [];

                    if ($totalInserted % 5000 === 0) {
                        $this->command->info("    Inserted {$totalInserted} requests...");
                    }
                }
            }
        }

        // Insert remaining requests
        if (!empty($requests)) {
            DB::table('proxy_requests')->insert($requests);
            $totalInserted += count($requests);
        }

        $this->command->info("✓ Successfully inserted {$totalInserted} proxy requests spanning 6 months");
        $this->command->info('  Data includes:');
        $this->command->info('    - Varied HTTP methods (GET, POST, CONNECT, HEAD)');
        $this->command->info('    - Realistic file sizes (512 bytes to 500 MB)');
        $this->command->info('    - Multiple status codes (200, 304, 404, etc.)');
        $this->command->info('    - Different activity patterns per user');
    }

    /**
     * Select a random value based on weights
     */
    private function weightedRandom(array $values, array $weights): mixed
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight * 100) / 100;

        $sum = 0;
        foreach ($weights as $i => $weight) {
            $sum += $weight;
            if ($random <= $sum) {
                return $values[$i];
            }
        }

        return $values[0];
    }
}
