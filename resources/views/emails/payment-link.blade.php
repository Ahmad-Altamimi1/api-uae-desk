<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment Link</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #eeeeee;
        }
        .email-header h2 {
            color: #333;
            margin: 0;
        }
        .email-body {
            padding: 20px;
            text-align: left;
            color: #555;
            font-size: 16px;
            line-height: 1.6;
        }
        .payment-link {
            display: block;
            width: 100%;
            max-width: 280px;
            margin: 20px auto;
            padding: 12px;
            text-align: center;
            background-color: #007BFF;
            color: #ffffff !important;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
        }
        .payment-link:hover {
            background-color: #0056b3;
            color: #ffffff !important;
        }
        .email-footer {
            text-align: center;
            padding-top: 10px;
            border-top: 2px solid #eeeeee;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h2>Secure Payment Request</h2>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Dear Customer,</p>
            <p>We appreciate your trust in <strong>{{ config('app.name') }}</strong>. Please complete your payment by clicking the secure link below:</p>

            <!-- Call to Action (CTA) Button -->
            <a href="{{ $paymentLink }}" class="payment-link" target="_blank">Make Payment</a>

            <p>If you have any questions, feel free to contact our support team.</p>

            <p>Best Regards,</p>
            <p><strong>{{ config('app.name') }}</strong></p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
