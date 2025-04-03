<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $timeframe;

    /**
     * Create a new message instance.
     *
     * @param Customer $customer
     * @param string $timeframe
     */
    public function __construct(Customer $customer, $timeframe)
    {
        $this->customer = $customer;
        $this->timeframe = $timeframe;
    }

    /**
     * Build the message.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->subject('Payment Reminder')
                    ->view('emails.payment_reminder') 
                    ->with([
                        'customerName' => $this->customer->first_name . ' ' . $this->customer->last_name,
                        'timeframe' => $this->timeframe,
                    ]);
    }
}
