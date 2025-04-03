<?php

namespace App\Jobs;

use App\Models\UpcomingPaymentReminder;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReminderMail;

class PaymentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Customer $customer;

    /**
     * Create a new job instance.
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $currentDate = Carbon::now();
        $superVisors = User::role('supervisor')->get();

        foreach ($this->customer->entries as $entry) {
            if (!$entry->date) {
                continue;
            }

            $entryDate = Carbon::parse($entry->date);
            $daysDifference = $entryDate->diffInDays($currentDate);
            $reminderType = "1 day";
            if ($daysDifference == 30) {
                $reminderType = '1 month';
            } elseif ($daysDifference == 10 && $daysDifference < 30) {
                $reminderType = '10 days';
            } elseif ($daysDifference == 7) {
                $reminderType = '7 days';
            }

            if ($reminderType) {
                foreach ($superVisors as $superVisor) {
                    // Avoid duplicate reminders
                    $alreadySent = UpcomingPaymentReminder::where('customer_id', $this->customer->id)
                        ->where('upcoming-payments_id', $entry->id)
                        ->where('notified_by', $superVisor->id)
                        ->where('reminder_type', $reminderType)
                        ->exists();

                    if (!$alreadySent) {
                        // Mail::to($superVisor->email)->send(new PaymentReminderMail($this->customer, $reminderType));

                        UpcomingPaymentReminder::create([
                            'customer_id' => $this->customer->id,
                            'upcoming-payments_id' => $entry->id,
                            'notified_by' => $superVisor->id,
                            'reminder_type' => $reminderType,
                            'upcoming-payments_date' => $entryDate,
                        ]);
                    }
                }
            }
        }
    }
}
