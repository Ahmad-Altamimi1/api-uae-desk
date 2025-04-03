<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class TestPHPMailer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:phpmailer {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email using PHPMailer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $mail = new PHPMailer(true);

        try {
            // ✅ SMTP Server Settings
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'your_mail_host');  // SMTP Server
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME', 'your_username');  // SMTP Username
            $mail->Password   = env('MAIL_PASSWORD', 'your_password');  // SMTP Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // TLS Encryption
            $mail->Port       = env('MAIL_PORT', 587);  // SMTP Port

            // ✅ Debugging Options (Optional)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            // ✅ Set Email Sender & Recipient
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'your@email.com'), 'Laravel PHPMailer');
            $mail->addAddress($email, 'Test User');  // Add recipient
            $mail->addCC('cc@example.com');  // Optional CC
            $mail->addBCC(env('MAIL_FROM_ADDRESS')); // BCC to sender

            // ✅ Email Content
            $mail->isHTML(true);
            $mail->Subject = 'Test Email via PHPMailer';
            $mail->Body    = '<h1>Hello!</h1><p>This is a test email sent via PHPMailer in Laravel.</p>';
            $mail->AltBody = 'This is a test email sent via PHPMailer in Laravel.';

            // ✅ Send the Email
            $mail->send();
            $this->info("✅ Email successfully sent to: $email");
        } catch (Exception $e) {
            $this->error("❌ Error sending email: " . $mail->ErrorInfo);
        }
    }
}
