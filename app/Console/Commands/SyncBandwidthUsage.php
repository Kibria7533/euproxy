<?php

namespace App\Console\Commands;

use App\Models\SquidUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncBandwidthUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bandwidth:sync-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync used_bytes from proxy_requests to squid_users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to sync bandwidth usage from proxy_requests...');

        // Get all SquidUsers
        $users = SquidUser::withoutGlobalScopes()->get();
        $totalUsers = $users->count();

        if ($totalUsers === 0) {
            $this->warn('No SquidUsers found in database.');
            return 0;
        }

        $this->info("Found {$totalUsers} SquidUser(s) to process.");

        $progressBar = $this->output->createProgressBar($totalUsers);
        $progressBar->start();

        $updated = 0;
        $unchanged = 0;

        foreach ($users as $user) {
            // Calculate actual bandwidth used from proxy_requests
            $actualUsedBytes = DB::table('proxy_requests')
                ->where('username', $user->user)
                ->sum('bytes');

            // Update used_bytes if different
            if ($user->used_bytes != $actualUsedBytes) {
                $user->used_bytes = $actualUsedBytes;
                $user->save();
                $updated++;
            } else {
                $unchanged++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Sync completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Updated', $updated],
                ['Unchanged', $unchanged],
                ['Total', $totalUsers],
            ]
        );

        return 0;
    }
}
