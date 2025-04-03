<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify Laravel email configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        try {
            // ✅ Send email using Laravel's built-in Mail function
            Mail::raw("This is a test email from Laravel from live. If you received this, your email configuration is working correctly.", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from Laravel');
            });

            // ✅ Output success message
            $this->info("✅ Test email successfully sent to: $email");
        } catch (\Exception $e) {
            // ✅ Handle any errors
            $this->error("❌ Error sending email: " . $e->getMessage());
        }

        return 0;
    }
}
