<?php

namespace Database\Seeders;

use App\Models\ProxyOrder;
use App\Models\ProxyPlan;
use App\Models\ProxySubscription;
use App\Models\ProxyType;
use App\Models\SquidServer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProxyOwnerSeeder extends Seeder
{
    private int $invoiceSeq = 1;

    public function run(): void
    {
        // ── Proxy Types ──────────────────────────────────────────────────────
        $rotating   = ProxyType::where('slug', 'rotating-residential')->firstOrFail();
        $datacenter = ProxyType::where('slug', 'datacenter')->firstOrFail();
        $static     = ProxyType::where('slug', 'static-residential')->firstOrFail();

        // ── Proxy Plans ───────────────────────────────────────────────────────
        $rot5gb  = ProxyPlan::where('proxy_type_id', $rotating->id)->where('bandwidth_gb', 5)->firstOrFail();
        $rot20gb = ProxyPlan::where('proxy_type_id', $rotating->id)->where('bandwidth_gb', 20)->firstOrFail();
        $dc5gb   = ProxyPlan::where('proxy_type_id', $datacenter->id)->where('bandwidth_gb', 5)->firstOrFail();
        $dc50gb  = ProxyPlan::where('proxy_type_id', $datacenter->id)->where('bandwidth_gb', 50)->firstOrFail();
        $st5gb   = ProxyPlan::where('proxy_type_id', $static->id)->where('bandwidth_gb', 5)->firstOrFail();

        // ── Proxy Servers (one per type per location) ─────────────────────────
        $this->seedServers($rotating, $datacenter, $static);

        // ── Owner 1: Alice — Rotating Residential 5 GB (3 sub-accounts) ──────
        $alice = $this->createOwner('alice@example.com', 'Alice Owner');
        $aliceSub = $this->createSubscription($alice, $rot5gb, $rotating, 5.000, 4.500);
        // 2 proxy users — within the 3-slot limit, total assigned 4.5 GB ≤ 5 GB
        $this->createSquidUser($alice, $rotating, [
            'user'               => 'alice_proxy1',
            'password_plain'     => 'AliceP1@pass',
            'fullname'           => 'Alice Sub-1',
            'bandwidth_limit_gb' => 2.000,
            'used_bytes'         => $this->gb(0.800),   // 40 % used
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($alice, $rotating, [
            'user'               => 'alice_proxy2',
            'password_plain'     => 'AliceP2@pass',
            'fullname'           => 'Alice Sub-2',
            'bandwidth_limit_gb' => 2.500,
            'used_bytes'         => $this->gb(0.250),   // 10 % used
            'is_blocked'         => 0,
        ]);

        // ── Owner 2: Bob — Rotating 20 GB (5 slots) + Datacenter 5 GB (3 slots)
        $bob = $this->createOwner('bob@example.com', 'Bob Owner');
        $this->createSubscription($bob, $rot20gb, $rotating, 20.000, 19.000);
        $this->createSubscription($bob, $dc5gb,   $datacenter, 5.000, 2.500);

        // 4 Rotating proxy users (within 5-slot limit), total 19 GB assigned
        $this->createSquidUser($bob, $rotating, [
            'user'               => 'bob_rot1',
            'password_plain'     => 'BobRot1@pass',
            'fullname'           => 'Bob Rotating-1',
            'bandwidth_limit_gb' => 5.000,
            'used_bytes'         => $this->gb(3.000),   // 60 %
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($bob, $rotating, [
            'user'               => 'bob_rot2',
            'password_plain'     => 'BobRot2@pass',
            'fullname'           => 'Bob Rotating-2',
            'bandwidth_limit_gb' => 5.000,
            'used_bytes'         => $this->gb(1.500),   // 30 %
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($bob, $rotating, [
            'user'               => 'bob_rot3',
            'password_plain'     => 'BobRot3@pass',
            'fullname'           => 'Bob Rotating-3',
            'bandwidth_limit_gb' => 5.000,
            'used_bytes'         => $this->gb(4.000),   // 80 % — near limit
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($bob, $rotating, [
            'user'               => 'bob_rot4',
            'password_plain'     => 'BobRot4@pass',
            'fullname'           => 'Bob Rotating-4',
            'bandwidth_limit_gb' => 4.000,
            'used_bytes'         => $this->gb(0.200),   // 5 % — new user
            'is_blocked'         => 0,
        ]);
        // 2 Datacenter proxy users (within 3-slot limit), total 4.5 GB assigned
        $this->createSquidUser($bob, $datacenter, [
            'user'               => 'bob_dc1',
            'password_plain'     => 'BobDC1@pass',
            'fullname'           => 'Bob Datacenter-1',
            'bandwidth_limit_gb' => 2.000,
            'used_bytes'         => $this->gb(1.000),   // 50 %
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($bob, $datacenter, [
            'user'               => 'bob_dc2',
            'password_plain'     => 'BobDC2@pass',
            'fullname'           => 'Bob Datacenter-2',
            'bandwidth_limit_gb' => 2.500,
            'used_bytes'         => $this->gb(2.250),   // 90 % — near limit
            'is_blocked'         => 0,
        ]);

        // ── Owner 3: Carol — Datacenter 50 GB (10 slots) + Static 5 GB (3 slots)
        $carol = $this->createOwner('carol@example.com', 'Carol Owner');
        $this->createSubscription($carol, $dc50gb, $datacenter, 50.000, 37.000);
        $this->createSubscription($carol, $st5gb,  $static,     5.000,  4.200);

        // 5 Datacenter proxy users (within 10-slot limit), total 48 GB assigned
        $this->createSquidUser($carol, $datacenter, [
            'user'               => 'carol_dc1',
            'password_plain'     => 'CarolDC1@pass',
            'fullname'           => 'Carol DC-1',
            'bandwidth_limit_gb' => 10.000,
            'used_bytes'         => $this->gb(2.000),   // 20 %
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($carol, $datacenter, [
            'user'               => 'carol_dc2',
            'password_plain'     => 'CarolDC2@pass',
            'fullname'           => 'Carol DC-2',
            'bandwidth_limit_gb' => 10.000,
            'used_bytes'         => $this->gb(7.500),   // 75 %
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($carol, $datacenter, [
            'user'               => 'carol_dc3',
            'password_plain'     => 'CarolDC3@pass',
            'fullname'           => 'Carol DC-3',
            'bandwidth_limit_gb' => 12.000,
            'used_bytes'         => $this->gb(10.800),  // 90 % — near limit
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($carol, $datacenter, [
            'user'               => 'carol_dc4',
            'password_plain'     => 'CarolDC4@pass',
            'fullname'           => 'Carol DC-4',
            'bandwidth_limit_gb' => 8.000,
            'used_bytes'         => $this->gb(3.600),   // 45 %
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($carol, $datacenter, [
            'user'               => 'carol_dc5',
            'password_plain'     => 'CarolDC5@pass',
            'fullname'           => 'Carol DC-5 (blocked)',
            'bandwidth_limit_gb' => 8.000,
            'used_bytes'         => $this->gb(8.100),   // exceeded → blocked
            'is_blocked'         => 1,
        ]);
        // 2 Static proxy users (within 3-slot limit), total 4.2 GB assigned
        $this->createSquidUser($carol, $static, [
            'user'               => 'carol_st1',
            'password_plain'     => 'CarolST1@pass',
            'fullname'           => 'Carol Static-1',
            'bandwidth_limit_gb' => 2.200,
            'used_bytes'         => $this->gb(0.500),   // 23 %
            'is_blocked'         => 0,
        ]);
        $this->createSquidUser($carol, $static, [
            'user'               => 'carol_st2',
            'password_plain'     => 'CarolST2@pass',
            'fullname'           => 'Carol Static-2',
            'bandwidth_limit_gb' => 2.000,
            'used_bytes'         => $this->gb(1.800),   // 90 % — near limit
            'is_blocked'         => 0,
        ]);

        $this->command->info('');
        $this->command->info('Proxy owners seeded:');
        $this->command->info('  alice@example.com  / password123  → Rotating 5GB  (2/3 users)');
        $this->command->info('  bob@example.com    / password123  → Rotating 20GB (4/5) + Datacenter 5GB (2/3)');
        $this->command->info('  carol@example.com  / password123  → Datacenter 50GB (5/10) + Static 5GB (2/3)');
        $this->command->info('');
        $this->command->info('Proxy users: alice_proxy1/2, bob_rot1-4, bob_dc1-2, carol_dc1-5, carol_st1-2');
        $this->command->info('All passwords = [username]@pass  e.g. AliceP1@pass');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function createOwner(string $email, string $name): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name'             => $name,
                'password'         => Hash::make('password123'),
                'is_administrator' => 0,
            ]
        );
    }

    private function createSubscription(
        User $owner,
        ProxyPlan $plan,
        ProxyType $type,
        float $totalGb,
        float $usedGb
    ): ProxySubscription {
        $invoice = 'INV-' . date('Ymd') . '-' . str_pad($this->invoiceSeq++, 4, '0', STR_PAD_LEFT);

        $order = ProxyOrder::create([
            'invoice_number'         => $invoice,
            'user_id'                => $owner->id,
            'proxy_plan_id'          => $plan->id,
            'proxy_type_id'          => $type->id,
            'bandwidth_gb'           => $totalGb,
            'amount_paid'            => $plan->base_price,
            'payment_status'         => 'completed',
            'payment_method'         => 'manual',
            'payment_transaction_id' => 'SEED-' . strtoupper(substr(md5($invoice), 0, 8)),
        ]);

        $usedBytes      = (int) ($usedGb * 1073741824);
        $remainingBytes = max(0, (int) (($totalGb - $usedGb) * 1073741824));

        return ProxySubscription::create([
            'user_id'                   => $owner->id,
            'proxy_order_id'            => $order->id,
            'proxy_type_id'             => $type->id,
            'bandwidth_total_gb'        => $totalGb,
            'bandwidth_remaining_bytes' => $remainingBytes,
            'bandwidth_used_gb'         => $usedGb,
            'status'                    => 'active',
            'started_at'                => now()->subMonths(1),
            'expires_at'                => now()->addMonths(11),
        ]);
    }

    private function createSquidUser(User $owner, ProxyType $type, array $data): void
    {
        $gb = (int) ($data['bandwidth_limit_gb'] * 1073741824);

        DB::table('squid_users')->updateOrInsert(
            ['user' => $data['user']],
            [
                'user_id'            => $owner->id,
                'proxy_type_id'      => $type->id,
                'user'               => $data['user'],
                'password'           => md5($data['password_plain']),
                'encrypted_password' => Crypt::encryptString($data['password_plain']),
                'fullname'           => $data['fullname'],
                'comment'            => $data['comment'] ?? null,
                'enabled'            => 1,
                'bandwidth_limit_gb' => $data['bandwidth_limit_gb'],
                'quota_bytes'        => $gb,
                'used_bytes'         => $data['used_bytes'],
                'is_blocked'         => $data['is_blocked'],
                'created_at'         => now()->subMonths(1),
                'updated_at'         => now(),
                'last_seen_at'       => now()->subHours(rand(1, 48)),
            ]
        );
    }

    private function seedServers(ProxyType $rotating, ProxyType $datacenter, ProxyType $static): void
    {
        $servers = [
            ['proxy_type_id' => $rotating->id,   'ip' => '192.168.88.13', 'port' => 3128, 'location' => 'Frankfurt, DE',  'hostname' => 'rot-de.euproxy.com'],
            ['proxy_type_id' => $rotating->id,   'ip' => '10.0.0.5',      'port' => 3128, 'location' => 'Amsterdam, NL',  'hostname' => 'rot-nl.euproxy.com'],
            ['proxy_type_id' => $datacenter->id, 'ip' => '10.0.0.20',     'port' => 3128, 'location' => 'Frankfurt, DE',  'hostname' => 'dc-de.euproxy.com'],
            ['proxy_type_id' => $datacenter->id, 'ip' => '10.0.0.21',     'port' => 3128, 'location' => 'London, UK',     'hostname' => 'dc-uk.euproxy.com'],
            ['proxy_type_id' => $static->id,     'ip' => '10.0.0.30',     'port' => 3128, 'location' => 'Frankfurt, DE',  'hostname' => 'st-de.euproxy.com'],
        ];

        foreach ($servers as $s) {
            SquidServer::updateOrCreate(
                ['ip' => $s['ip'], 'proxy_type_id' => $s['proxy_type_id']],
                array_merge($s, ['is_active' => true])
            );
        }

        $this->command->info('Proxy servers seeded: 2 Rotating, 2 Datacenter, 1 Static');
    }

    private function gb(float $gb): int
    {
        return (int) ($gb * 1073741824);
    }
}
