<?php

namespace App\Console\Commands;

use App\Jobs\PaymentReminder;
use App\Models\Customer;
use Illuminate\Console\Command;

class UpcomingPaymentTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:upcoming-payment-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test upcoming payment notifications to supervisors';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (Customer::all() as $customer) {
            PaymentReminder::dispatch($customer);
        }
        $this->info('✅ PaymentReminder job dispatched successfully!');
    }
}
