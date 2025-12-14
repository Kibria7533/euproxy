<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SquidUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 10 squid proxy users with varied bandwidth usage scenarios
     */
    public function run(): void
    {
        // First, create a regular user to own these squid users
        $owner = User::firstOrCreate(
            ['email' => 'proxyowner@example.com'],
            [
                'name' => 'Proxy Owner',
                'password' => Hash::make('password123'),
                'is_administrator' => 0,
            ]
        );

        $squidUsers = [
            [
                'user' => 'user001',
                'password' => Hash::make('password001'),
                'enabled' => 1,
                'fullname' => 'John Doe',
                'comment' => 'Regular user with 50GB quota',
                'bandwidth_limit_gb' => 50.00,
                'quota_bytes' => 50 * 1024 * 1024 * 1024, // 50 GB
                'used_bytes' => 25 * 1024 * 1024 * 1024, // Used 25 GB (50% used)
                'is_blocked' => 0,
                'user_id' => $owner->id,
            ],
            [
                'user' => 'user002',
                'password' => Hash::make('password002'),
                'enabled' => 1,
                'fullname' => 'Jane Smith',
                'comment' => 'Power user with 100GB quota',
                'bandwidth_limit_gb' => 100.00,
                'quota_bytes' => 100 * 1024 * 1024 * 1024,
                'used_bytes' => 95 * 1024 * 1024 * 1024, // 95% used (near limit)
                'is_blocked' => 0,
                'user_id' => $owner->id,
            ],
            [
                'user' => 'user003',
                'password' => Hash::make('password003'),
                'enabled' => 1,
                'fullname' => 'Bob Johnson',
                'comment' => 'Exceeded quota - auto blocked',
                'bandwidth_limit_gb' => 30.00,
                'quota_bytes' => 30 * 1024 * 1024 * 1024,
                'used_bytes' => 32 * 1024 * 1024 * 1024, // Over quota
                'is_blocked' => 1, // Auto-blocked
                'user_id' => $owner->id,
            ],
            [
                'user' => 'user004',
                'password' => Hash::make('password004'),
                'enabled' => 1,
                'fullname' => 'Alice Williams',
                'comment' => 'Light user with small quota',
                'bandwidth_limit_gb' => 10.00,
                'quota_bytes' => 10 * 1024 * 1024 * 1024,
                'used_bytes' => 2 * 1024 * 1024 * 1024, // 20% used
                'is_blocked' => 0,
                'user_id' => $owner->id,
            ],
            [
                'user' => 'user005',
                'password' => Hash::make('password005'),
                'enabled' => 1,
                'fullname' => 'Charlie Brown',
                'comment' => 'Unlimited bandwidth user',
                'bandwidth_limit_gb' => null,
                'quota_bytes' => 0, // 0 = unlimited
                'used_bytes' => 150 * 1024 * 1024 * 1024, // Used 150 GB
                'is_blocked' => 0,
                'user_id' => $owner->id,
            ],
            [
                'user' => 'user006',
                'password' => Hash::make('password006'),
                'enabled' => 0, // Disabled user
                'fullname' => 'David Miller',
                'comment' => 'Disabled account',
                'bandwidth_limit_gb' => 20.00,
                'quota_bytes' => 20 * 1024 * 1024 * 1024,
                'used_bytes' => 5 * 1024 * 1024 * 1024,
                'is_blocked' => 0,
                'user_id' => $owner->id,
            ],
            [
                'user' => 'user007',
                'password' => Hash::make('password007'),
                'enabled' => 1,
                'fullname' => 'Emma Davis',
                'comment' => 'New user with minimal usage',
                'bandwidth_limit_gb' => 50.00,
                'quota_bytes' => 50 * 1024 * 1024 * 1024,
                'used_bytes' => 500 * 1024 * 1024, // 500 MB
                'is_blocked' => 0,
                'user_id' => $owner->id,
            ],
            [
                'user' => 'user008',
                'password' => Hash::make('password008'),
                'enabled' => 1,
                'fullname' => 'Frank Wilson',
                'comment' => 'Heavy user approaching limit',
                'bandwidth_limit_gb' => 75.00,
                'quota_bytes' => 75 * 1024 * 1024 * 1024,
                'used_bytes' => 73 * 1024 * 1024 * 1024, // 97% used
                'is_blocked' => 0,
                'user_id' => $owner->id,
            ],
            [
                'user' => 'user009',
                'password' => Hash::make('password009'),
                'enabled' => 1,
                'fullname' => 'Grace Taylor',
                'comment' => 'Medium quota, moderate usage',
                'bandwidth_limit_gb' => 40.00,
                'quota_bytes' => 40 * 1024 * 1024 * 1024,
                'used_bytes' => 18 * 1024 * 1024 * 1024, // 45% used
                'is_blocked' => 0,
                'user_id' => $owner->id,
            ],
            [
                'user' => 'user010',
                'password' => Hash::make('password010'),
                'enabled' => 1,
                'fullname' => 'Henry Martinez',
                'comment' => 'Business user with large quota',
                'bandwidth_limit_gb' => 200.00,
                'quota_bytes' => 200 * 1024 * 1024 * 1024,
                'used_bytes' => 120 * 1024 * 1024 * 1024, // 60% used
                'is_blocked' => 0,
                'user_id' => $owner->id,
            ],
        ];

        foreach ($squidUsers as $userData) {
            DB::table('squid_users')->updateOrInsert(
                ['user' => $userData['user']],
                array_merge($userData, [
                    'created_at' => now()->subMonths(6),
                    'updated_at' => now(),
                    'last_seen_at' => now()->subHours(rand(1, 72)),
                    'reset_at' => null,
                ])
            );
        }

        $this->command->info('✓ Created 10 squid proxy users with varied usage patterns');
        $this->command->info('  - 8 active users (enabled)');
        $this->command->info('  - 1 disabled user');
        $this->command->info('  - 1 auto-blocked user (quota exceeded)');
        $this->command->info('  - 1 unlimited bandwidth user');
        $this->command->info('  - Owner: proxyowner@example.com (password: password123)');
    }
}
