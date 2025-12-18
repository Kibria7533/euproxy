<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sync bandwidth usage from proxy_requests to squid_users every 5 minutes
        $schedule->command('bandwidth:sync-usage')->everyFiveMinutes();

        // Check for low bandwidth subscriptions and send alerts every 2 hours
        $schedule->command('subscriptions:check-low-bandwidth')->everyTwoHours();

        // Check for expired subscriptions and mark them as expired every hour
        $schedule->command('subscriptions:check-expiry')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
