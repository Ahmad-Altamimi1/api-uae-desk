<!DOCTYPE html>
<html>
<head>
    <title>Account Created on the Federal Tax Authority Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .email-header {
            background-color: #0056b3;
            color: #fff;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .email-body {
            padding: 20px;
        }
        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            color: #fff !important;
            background-color: #0056b3;
            border-radius: 4px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Welcome to {{ config('app.name') }}</h2>
        </div>
        <div class="email-body">
            <p>Dear {{ $customerName }},</p>
            <p>Thank you for choosing us! We appreciate your trust and look forward to serving you.</p>

            {{-- <p><strong>Details of your account:</strong></p>
            <ul>
                <li><strong>TRN (Tax Registration Number):</strong> {{ $trn }}</li>
                <li><strong>Portal Email:</strong> {{ $portalEmail }}</li>
                <li><strong>Portal Password:</strong> {{ $portalPassword }}</li>
            </ul>

            <p>To access your account and manage your tax-related matters, please click the button below:</p>
            <p style="text-align: center;">
                <a href="{{ $loginUrl }}" class="cta-button" style="color: #fff">Login to FTA Portal</a>
            </p> --}}

            <p>If you have any questions or require assistance, feel free to contact us:</p>
            <ul>
                <li><strong>Address:</strong> {{ $companyAddress }}</li>
                <li><strong>Phone:</strong> {{ $companyPhone }}</li>
                <li><strong>Email:</strong> {{ $companyEmail }}</li>
            </ul>

            <p>Thank you for choosing the {{ config('app.name') }}</p>
            <p>Best regards,<br>The {{ config('app.name') }}Team</p>
        
        </div>
    </div>
</body>
</html>
