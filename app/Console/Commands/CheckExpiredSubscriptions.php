<?php

namespace App\Console\Commands;

use App\UseCases\ProxySubscription\CheckExpiryAction;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and mark them as expired';

    /**
     * Execute the console command.
     */
    public function handle(CheckExpiryAction $action)
    {
        $this->info('Checking for expired subscriptions...');

        $results = $action->execute();

        $this->info("Checked {$results['checked']} subscriptions");
        $this->info("Marked {$results['expired']} as expired");
        $this->info("Marked {$results['expired_with_balance']} as expired with remaining balance");

        $this->comment('Expiry check completed.');

        return Command::SUCCESS;
    }
}
