<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\CustomerFtaMedia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractExpiryMailForClient extends Mailable
{
    use Queueable, SerializesModels;

    public $contract;
    public $days;
    public $customer;

    /**
     * Create a new message instance.
     */
    public function __construct(CustomerFtaMedia $contract, int $days,Customer $customer)
    {
        $this->contract = $contract;
        $this->days = $days;
        $this->customer = $customer;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Contract Expiring in {$this->days} Days")
            ->view('emails.contract_expiryForClient')
            ->with([
                'contractName' => $this->contract->document_name,
                'expiryDate' => $this->contract->expire_date,
                'days' => $this->days,
                'customer' => $this->customer,
            ]);
    }
}
