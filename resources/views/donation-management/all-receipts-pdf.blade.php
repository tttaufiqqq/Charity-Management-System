<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Donation Receipts - {{ $donor->Full_Name ?? 'Donor' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4F46E5;
            font-size: 32px;
            margin: 0;
        }
        .summary {
            background-color: #F3F4F6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .summary table {
            width: 100%;
        }
        .summary td {
            padding: 8px 0;
        }
        .donations-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .donations-table th {
            background-color: #4F46E5;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .donations-table td {
            padding: 12px;
            border-bottom: 1px solid #E5E7EB;
        }
        .donations-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        .total-row {
            background-color: #EEF2FF !important;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>CharityHub</h1>
        <p>Donation Summary Report</p>
    </div>

    <!-- Donor Information -->
    <div class="summary">
        <h2 style="margin-top: 0;">Donor Information</h2>
        <table>
            <tr>
                <td style="width: 200px; font-weight: bold;">Name:</td>
                <td>{{ $donor->Full_Name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Email:</td>
                <td>{{ $donor->user->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Donated:</td>
                <td><strong>RM {{ number_format($donor->Total_Donated ?? 0, 2) }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Donations:</td>
                <td>{{ $donations->count() }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Report Date:</td>
                <td>{{ now()->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Donations List -->
    <h2>Donation History</h2>
    <table class="donations-table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Receipt No.</th>
            <th>Campaign</th>
            <th>Organization</th>
            <th style="text-align: right;">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($donations as $donation)
            <tr>
                <td>{{ \Carbon\Carbon::parse($donation->Donation_Date)->format('M d, Y') }}</td>
                <td>{{ $donation->Receipt_No }}</td>
                <td>{{ $donation->campaign->Title ?? 'N/A' }}</td>
                <td>{{ $donation->campaign->organization->user->name ?? 'N/A' }}</td>
                <td style="text-align: right;">RM {{ number_format($donation->Amount, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="4" style="text-align: right;">TOTAL:</td>
            <td style="text-align: right;">RM {{ number_format($donations->sum('Amount'), 2) }}</td>
        </tr>
        </tbody>
    </table>

    <!-- Thank You -->
    <div style="background-color: #D1FAE5; border-left: 4px solid #10B981; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0; color: #065F46;">
            <strong>Thank you for your continued support!</strong><br>
            Your generosity is making a lasting impact on our community.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>CharityHub</strong></p>
        <p>This is an official donation summary report</p>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</div>
</body>
</html>
