<!DOCTYPE html>
<html>
<head>
    <title>Contract Expiry Alert</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #007bff;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
            font-size: 16px;
            color: #333;
        }
        .button {
            display: block;
            width: 200px;
            background: #28a745;
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            margin: 20px auto;
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            padding: 10px;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">Contract Expiry Notification</div>
        <div class="content">
            <p>Customer {{ $customer->name }},</p>
            <p>His contract <strong>{{ $contractName }}</strong> is expiring in <strong>{{ $days }}</strong> days.</p>
            <p><strong>Expiry Date:</strong> {{ $expiryDate }}</p>
          
            <p>For any assistance, please contact our support team.</p>
        </div>
        <div class="footer">
            <p>Thank you for choosing the {{ config('app.name') }}</p>
            <p>Best regards,<br>The {{ config('app.name') }}Team</p>
        </div>
    </div>
</body>
</html>
