<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaxIdAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $taxId;
    public $companyEmail;
    public $companyPhone;
    public $companyAddress;
    public $attachmentPath;
    public $pdfFilePath;

    public function __construct($customer, $taxId, $companyEmail, $companyPhone, $companyAddress, $attachmentPath = null, $pdfFilePath = null)
    {
        $this->customer = $customer;
        $this->taxId = $taxId;
        $this->companyEmail = $companyEmail;
        $this->companyPhone = $companyPhone;
        $this->companyAddress = $companyAddress;
        $this->attachmentPath = $attachmentPath;
        $this->pdfFilePath = $pdfFilePath;

    }

    public function build()
    {
        $email = $this->subject('ThankYou for choosing us')
            ->view('emails.tax_id_added')
            ->with([
                'customerName' => $this->customer->first_name . ' ' . $this->customer->last_name,
                'trn' => $this->taxId,
                'portalEmail' => $this->customer->portal_email,
                'portalPassword' => $this->customer->portal_password,
                'loginUrl' => 'https://eservices.tax.gov.ae/',
                'companyEmail' => $this->companyEmail,
                'companyPhone' => $this->companyPhone,
                'companyAddress' => $this->companyAddress,
            ]);

        // Attach the file if it exists
        if (isset($this->attachmentPath) && file_exists($this->attachmentPath)) {
            $email->attach($this->attachmentPath, [
                'as' => basename($this->attachmentPath), // Optional: Rename the file in the email
                'mime' => mime_content_type($this->attachmentPath), // Get the MIME type dynamically
            ]);
        }

        if (isset($this->pdfFilePath) && file_exists($this->pdfFilePath)) {
            $email->attach($this->pdfFilePath, [
                'as' => basename($this->pdfFilePath), // Optional: Rename the file in the email
                'mime' => 'application/pdf',
            ]);
        }
        return $email;
    }

}
