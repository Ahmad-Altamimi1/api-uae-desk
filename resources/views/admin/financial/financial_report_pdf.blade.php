<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; border: 1px solid #000; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .report-header { margin-bottom: 10px; }
        .summary-table { width: 50%; margin-top: 10px; }
    </style>
</head>
<body>
    <!-- ✅ Report Title & Date -->
    <div class="report-header">
        <h2>Financial Report</h2>
        <p><strong>Report Date:</strong> 
            @if(request('start_date') && request('end_date'))
                {{ request('start_date') }} to {{ request('end_date') }}
            @else
                {{ now()->format('Y-m-d') }}
            @endif
        </p>
    </div>

    <!-- ✅ Summary Table -->
    <table class="summary-table">
        <tr>
            <th>Total Transactions</th>
            <td class="text-right">{{ count($payments) }}</td>
        </tr>
        <tr>
            <th>Total Amount (AED)</th>
            <td class="text-right">
                {{ number_format($payments->sum('price'), 2) }}
            </td>
        </tr>
    </table>

    <!-- ✅ Transactions Table -->
    <table>
        <thead>
            <tr>
                <th>Application ID</th>
                <th>Reference Number</th>
                <th>Branch</th>
                <th>Payment Mode</th>
                <th class="text-right">Amount (AED)</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->customer_code }}</td>
                    <td>{{ $payment->invoice_number }}</td>
                    <td>{{ $payment->branch ? $payment->branch->branch_name : '-' }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td class="text-right">{{ number_format($payment->price, 2) }}</td>
                    <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
