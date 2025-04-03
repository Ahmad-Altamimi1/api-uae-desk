<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\NotifyExpiringContracts;

class NotifyExpiringContractsTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * You can run it using: php artisan notify:contracts-test
     */
    protected $signature = 'notify:contracts-test';

    /**
     * The console command description.
     */
    protected $description = 'Dispatch the NotifyExpiringContracts job manually for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dispatch(new NotifyExpiringContracts());
        $this->info('✅ NotifyExpiringContracts job dispatched successfully!');
    }
}
