<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder</title>
</head>
<body>
    <p>This is a reminder that  payment is due in the next {{ $timeframe }} for {{ $customerName }}.</p>
    <p>Thank you for choosing the {{ config('app.name') }}</p>
    <p>Best regards,<br>The {{ config('app.name') }}Team</p>
</body>
</html>
