<?php

namespace App\Console;

use App\Jobs\PaymentReminder;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\NotifyExpiringContractsTest::class,
        \App\Console\Commands\UpcomingPaymentTestNotification::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Scheduling the job to run daily for all customers
        $schedule->call(function () {
            $superVisors = User::all();

            foreach ($superVisors as $superVisor) {
                if ($superVisor . getRoleNames() == "supervisor") {
                    PaymentReminder::dispatch($superVisor);
                }
            }
        })->daily();
        $schedule->job(new \App\Jobs\NotifyExpiringContracts())->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
