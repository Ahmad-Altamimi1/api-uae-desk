<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            /* color: #333; */
            background-color: #f5f5f5;
            line-height: 1.2;
            font-size: 10px;
        }

        .container {
            width: 100%;
            max-width: 793px;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #fff;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
        }

        .header-left,
        .header-right {
            flex: 1;
        }

        .header-left h4,
        .header-right h2 {
            margin: 0 0 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .header-left p,
        .header-right p {
            margin: 2px 0;
            font-size: 9px;
        }

        .header-center {
            text-align: center;
            flex: 1;
            font-size: 9px;
        }

        .header-center img {
            max-width: 50px;
            margin-bottom: 3px;
        }

        /* Table Section */
        h3 {
            font-size: 11px;
            border-bottom: 1px solid #007bff;
            margin-bottom: 8px;
            padding-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            font-size: 9px;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Prevents table row breaking */
        tr {
            page-break-inside: avoid;
        }

        /* Signed By Section */
        .signed-by {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 3px;
            font-size: 9px;
            font-style: italic;
        }

        /* Footer Section */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8px;
            color: #555;
            margin-top: 8px;
        }

        .serial-number {
            text-align: center;
            font-size: 8px;
            color: #555;
            font-style: italic;
            margin-top: 5px;
        }

        .print-btn-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .print-btn {
            background-color: #00713b;
            border: none;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        /* Print Settings */
        @media print {
            .print-btn-container {
                display: none;
            }

            body {
                margin: 0;
                padding: 0;
                background-color: #fff;
            }

            .container {
                box-shadow: none;
                border: none;
                max-width: 793px;
            }

            table {
                page-break-after: auto;
            }

            tr {
                page-break-inside: avoid;
            }

            .footer {
                position: relative;
                bottom: 0;
                left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>



    <!-- Receipt Section -->
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h4>Customer Information</h4>
                <p><strong>Name:</strong> {{ $customer->first_name }} {{ $customer->last_name }}</p>
                <p><strong>Business:</strong> {{ $customer->business_name }}</p>
                <p><strong>Phone:</strong> {{ $customer->phone_number }}</p>
                <p><strong>Email:</strong> {{ $customer->email }}</p>
                <p><strong>TRN:</strong> {{ $customer->tax_id }}</p>

            </div>
            <div class="header-center">
                <img src="{{ asset('assets/admin/img/logo-def.png') }}" alt="Company Logo">
                <p>{{ $companyAddress }}</p>
                <p>Phone: {{ $companyPhone }}</p>
                <p>Email: {{ $companyEmail }}</p>
            </div>
            <div class="header-right">
                <h2 style="color: #e74c3c;">Receipt</h2>
                <p><strong>Receipt Number:</strong> {{ $receiptNumber }}</p>
                <p><strong>Receipt Date:</strong> {{ $receiptDate }}</p>
                <p><strong>Generated By:</strong> {{ $created->name }}</p>
                <p><strong>Payment By:</strong> {{ $payment_method }}</p>
            </div>
        </div>

        <!-- Payment Details -->
        <h3>Payment Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount (AED)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($services as $serviceItem)
                    <tr>
                        <td> {{ $loop->iteration }}. {{ $serviceItem['name'] }}</td>
                        <td>AED {{ number_format($serviceItem['price'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Payment Summary -->
        <h3>Total Paid</h3>
        <table>
            <tr>
                <th>Base Amount</th>
                <td>AED {{ number_format($customer->price, 2) }}</td>
            </tr>
            <tr>
                <th>VAT ({{ $customer->vat_value }}%)</th>
                <td>AED {{ number_format($vatAmount, 2) }}</td>
            </tr>
            <tr>
                <th>Total Paid</th>
                <td>AED {{ number_format($customer->price + $vatAmount, 2) }}</td>
            </tr>
        </table>

        <!-- Signed By -->
        <div class="signed-by">
            <div class="signature-box">Customer Signature</div>
            <div class="signature-box">Authorized Signature</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>&copy; {{ date('Y') }} HOLISTIC LEGACY ACCOUNTING S.P.S L.L.C All Rights Reserved.</div>
        </div>

        <!-- Serial Number -->
        <div class="serial-number">
            Serial Number: {{ $customer->serial_number }}
        </div>
        <div class="print-btn-container ">
            <button class="btn print-btn" onclick="window.print()">Print Receipt</button>
            <a href="{{ route('print-invoice', $customer->id) }}" target="_blank" class="btn print-btn"> View
                Invoice</a>
        </div>
    </div>

</body>

</html>
