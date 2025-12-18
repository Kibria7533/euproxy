<?php

namespace App\Console\Commands;

use App\UseCases\ProxySubscription\CheckLowBandwidthAction;
use Illuminate\Console\Command;

class CheckLowBandwidth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-low-bandwidth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for subscriptions with low bandwidth and send alerts';

    /**
     * Execute the console command.
     */
    public function handle(CheckLowBandwidthAction $action)
    {
        $this->info('Checking for low bandwidth subscriptions...');

        $results = $action->execute();

        $this->info("Checked {$results['checked']} subscriptions");
        $this->info("Sent {$results['alerted_90']} alerts for 90% threshold");
        $this->info("Sent {$results['alerted_95']} alerts for 95% threshold");

        $this->comment('Low bandwidth check completed.');

        return Command::SUCCESS;
    }
}
