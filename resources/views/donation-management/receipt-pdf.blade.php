<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Donation Receipt - {{ $donation->Receipt_No }}</title>
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
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .receipt-info {
            background-color: #F3F4F6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .receipt-info table {
            width: 100%;
        }
        .receipt-info td {
            padding: 8px 0;
        }
        .receipt-info .label {
            font-weight: bold;
            color: #4B5563;
            width: 200px;
        }
        .donation-details {
            margin-bottom: 30px;
        }
        .donation-details h2 {
            color: #1F2937;
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .amount-box {
            background-color: #EEF2FF;
            border: 2px solid #4F46E5;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .amount-box .label {
            color: #4B5563;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .amount-box .amount {
            color: #4F46E5;
            font-size: 36px;
            font-weight: bold;
        }
        .campaign-info {
            background-color: #F9FAFB;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 12px;
        }
        .thank-you {
            background-color: #D1FAE5;
            border-left: 4px solid #10B981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .thank-you p {
            margin: 0;
            color: #065F46;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>CharityHub</h1>
        <p>Official Donation Receipt</p>
        <p>Making a difference together</p>
    </div>

    <!-- Receipt Information -->
    <div class="receipt-info">
        <table>
            <tr>
                <td class="label">Receipt Number:</td>
                <td><strong>{{ $donation->Receipt_No }}</strong></td>
            </tr>
            <tr>
                <td class="label">Donation Date:</td>
                <td>{{ \Carbon\Carbon::parse($donation->Donation_Date)->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="label">Issued To:</td>
                <td>{{ $donation->donor->Full_Name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Email:</td>
                <td>{{ $donation->donor->user->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Phone:</td>
                <td>{{ $donation->donor->Phone_Num ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Amount Box -->
    <div class="amount-box">
        <div class="label">DONATION AMOUNT</div>
        <div class="amount">RM {{ number_format($donation->Amount, 2) }}</div>
    </div>

    <!-- Campaign Information -->
    <div class="donation-details">
        <h2>Donation Details</h2>
        <div class="campaign-info">
            <table style="width: 100%;">
                <tr>
                    <td class="label">Campaign:</td>
                    <td>{{ $donation->campaign->Title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Organization:</td>
                    <td>{{ $donation->campaign->organization->user->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Method:</td>
                    <td>{{ $donation->Payment_Method }}</td>
                </tr>
                <tr>
                    <td class="label">Transaction Date:</td>
                    <td>{{ \Carbon\Carbon::parse($donation->created_at)->format('F d, Y H:i:s') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Thank You Message -->
    <div class="thank-you">
        <p><strong>Thank you for your generous donation!</strong></p>
        <p>Your contribution is making a real difference in the lives of those we serve.</p>
    </div>

    <!-- Tax Information -->
    <div style="background-color: #FEF3C7; padding: 15px; border-radius: 4px; margin: 20px 0;">
        <p style="margin: 0; color: #92400E; font-size: 12px;">
            <strong>Tax Information:</strong> This receipt may be used for tax deduction purposes.
            Please consult with your tax advisor regarding the deductibility of this donation.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>CharityHub</strong></p>
        <p>For inquiries, please contact us at support@charityhub.com</p>
        <p>This is an official donation receipt generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</div>
</body>
</html>
