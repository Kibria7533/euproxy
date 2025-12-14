<?php

namespace App\Console\Commands;

use App\Models\SquidUser;
use Illuminate\Console\Command;

class SyncBandwidthQuota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bandwidth:sync-quota';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync bandwidth_limit_gb to quota_bytes for all SquidUsers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to sync bandwidth_limit_gb to quota_bytes...');

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

        $synced = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // If bandwidth_limit_gb is set but quota_bytes is 0 or doesn't match
            if (!is_null($user->bandwidth_limit_gb)) {
                $expectedQuotaBytes = (int)($user->bandwidth_limit_gb * 1073741824);

                if ($user->quota_bytes != $expectedQuotaBytes) {
                    $user->quota_bytes = $expectedQuotaBytes;
                    $user->save();
                    $synced++;
                } else {
                    $skipped++;
                }
            } else {
                // If bandwidth_limit_gb is null, set quota_bytes to 0
                if ($user->quota_bytes != 0) {
                    $user->quota_bytes = 0;
                    $user->save();
                    $synced++;
                } else {
                    $skipped++;
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Sync completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Synced', $synced],
                ['Skipped (already synced)', $skipped],
                ['Total', $totalUsers],
            ]
        );

        return 0;
    }
}
