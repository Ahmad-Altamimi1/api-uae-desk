<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentLinkEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $paymentLink;

    public function __construct($paymentLink)
    {
        $this->paymentLink = $paymentLink;
    }

    public function build()
    {
        return $this->subject('Your Payment Link')
                    ->view('emails.payment-link')
                    ->with(['paymentLink' => $this->paymentLink]);
    }
}
