<?php

namespace App\Jobs;

use App\Mail\ContractExpiryMail;
use App\Models\CustomerFtaMedia;
use App\Models\User; // Assuming customers have user records
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\ContractReminder;

class NotifyExpiringContracts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $daysBeforeExpiry = [2, 7, 30, 60, 90];

        $superVisors = User::role('supervisor')->get();

        foreach ($daysBeforeExpiry as $days) {
            $date = Carbon::now()->addDays($days)->toDateString();

            $expiringContracts = CustomerFtaMedia::whereDate('expire_date', $date)->get();

            foreach ($expiringContracts as $contract) {

                $customer = $contract->customer;

                if ($customer) {
                    foreach ($superVisors as $superVisor) {
                        Mail::to($superVisor->email)->send(new ContractExpiryMail($contract, $days, $customer));
                        ContractReminder::create([
                            'customer_id' => $customer->id,
                            'contract_id' => $contract->id,
                            'notified_by' => $superVisor->id,
                            'days_before_expiry' => $days,
                            'expire_date' => $contract->expire_date,
                        ]);

                        //  Mail::to($customer->email)->send(new ContractExpiryMailForClient($contract, $days));
                    }
                }
            }
        }
    }
}
