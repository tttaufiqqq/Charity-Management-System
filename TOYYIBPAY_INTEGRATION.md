# ToyyibPay Payment Integration Guide

Complete guide for the ToyyibPay payment gateway integration in Charity-Izz.

## Overview

The donation payment flow is integrated with ToyyibPay (https://toyyibpay.com), Malaysia's leading payment gateway. The system handles payments with **only SUCCESS or FAILED statuses** - no pending status is shown to users.

## Payment Flow

### 1. User Initiates Donation
- User visits `/campaigns/{id}/donate` and fills donation form
- Selects amount and payment method (FPX Online Banking)
- Submits form

### 2. Payment Record Creation
```php
// Create donation with Pending status (internal only)
Donation::create([
    'Donor_ID' => $donor->Donor_ID,
    'Campaign_ID' => $campaign->Campaign_ID,
    'Amount' => $request->amount,
    'Payment_Method' => 'FPX Online Banking',
    'Payment_Status' => 'Pending', // Will be updated after payment
    'Bill_Code' => null, // Set after ToyyibPay bill creation
]);
```

### 3. ToyyibPay Bill Creation
```php
$toyyibpay->createBill([
    'billName' => 'Donation to {Campaign Title}',
    'billAmount' => $amount,
    'billReturnUrl' => route('donation.payment.return', $donationId),
    'billCallbackUrl' => route('donation.payment.callback'),
    // ... other fields
]);
```

### 4. Redirect to ToyyibPay
- User is redirected to ToyyibPay payment page
- URL format: `https://dev.toyyibpay.com/{billCode}` (sandbox) or `https://toyyibpay.com/{billCode}` (production)
- User sees bank selection and completes FPX payment

### 5. Payment Completion
User completes payment at ToyyibPay simulator: `https://dev.toyyibpay.com/5lspw9g8?mode=paymentBankSimulator`

ToyyibPay redirects back to: `/donation/payment/return/{donationId}?status_id={1|2|3}`

**Status Codes:**
- `status_id=1` → Payment SUCCESS
- `status_id=2` → Payment PENDING/CANCELLED
- `status_id=3` → Payment FAILED

### 6. Status Update (paymentReturn method)

```php
if ($statusId == '1') {
    // SUCCESS - Update to Completed
    $donation->update(['Payment_Status' => 'Completed']);
    $campaign->increment('Collected_Amount', $donation->Amount);
    $donor->increment('Total_Donated', $donation->Amount);

    // Redirect to success page
    return redirect()->route('donation.success', $donationId);

} elseif (in_array($statusId, ['2', '3'])) {
    // FAILED - Update to Failed
    $donation->update(['Payment_Status' => 'Failed']);

    // Show failure page
    return view('donation-management.payment-failed', compact('donation'));
}
```

### 7. User Sees Result
- **Success:** Redirected to success page with donation details and receipt option
- **Failed:** Shown failure page with retry option
- **No Pending Page:** Users never see a "pending" status

## Database Schema

### Donation Table Columns

```php
Schema::table('donation', function (Blueprint $table) {
    $table->id('Donation_ID');
    $table->foreignId('Donor_ID');
    $table->foreignId('Campaign_ID');
    $table->decimal('Amount', 10, 2);
    $table->date('Donation_Date');
    $table->string('Payment_Method', 50); // 'FPX Online Banking'
    $table->string('Receipt_No', 100)->unique();

    // ToyyibPay Integration Fields
    $table->string('Payment_Status', 20)->default('Pending'); // Pending → Completed/Failed
    $table->string('Bill_Code', 100)->nullable(); // ToyyibPay bill code
    $table->string('Transaction_ID', 100)->nullable(); // ToyyibPay transaction ID

    $table->timestamps();
});
```

## Configuration

### 1. ToyyibPay Account Setup

1. Register at https://dev.toyyibpay.com (sandbox) or https://toyyibpay.com (production)
2. Create a category for your charity platform
3. Get your:
   - **Secret Key** (userSecretKey)
   - **Category Code** (categoryCode)

### 2. Environment Configuration

Add to `.env`:

```env
# ToyyibPay Payment Gateway
TOYYIBPAY_SECRET_KEY=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
TOYYIBPAY_CATEGORY_CODE=xxxxx
TOYYIBPAY_SANDBOX=true  # Set to false for production
```

### 3. SSL Certificate (Sandbox Only)

For sandbox/development, download the CA certificate:

```bash
# Download cacert.pem to storage directory
curl https://curl.se/ca/cacert.pem -o storage/cacert.pem
```

This is required for HTTPS verification when calling ToyyibPay sandbox API.

## Key Features

### ✅ No Pending Status for Users
- Donations are created with `Pending` status internally
- Users **NEVER** see pending status
- After payment, status is immediately updated to either `Completed` or `Failed`
- Only completed donations appear in donor's history

### ✅ Automatic Campaign Updates
When payment succeeds:
```php
$campaign->increment('Collected_Amount', $donation->Amount);
$donor->increment('Total_Donated', $donation->Amount);
```

### ✅ Unique Receipt Generation
Each donation gets a unique receipt:
```php
'Receipt_No' => 'RCP-' . strtoupper(uniqid())
```

### ✅ Donation History Filtering
Only completed donations shown:
```php
$donor->donations()
    ->where('Payment_Status', 'Completed')
    ->get();
```

## API Endpoints

### Return URL
```
GET /donation/payment/return/{donationId}
```
Parameters from ToyyibPay:
- `status_id`: Payment status (1=success, 2=pending, 3=failed)
- `transaction_id`: ToyyibPay transaction ID
- `order_id`: Bill code

### Callback URL
```
POST /donation/payment/callback
```
Server-to-server notification from ToyyibPay. Verifies and updates payment status.

## Testing

### 1. Sandbox Test Flow

1. Start development server:
   ```bash
   php artisan serve
   ```

2. Navigate to campaign donation page:
   ```
   http://127.0.0.1:8000/campaigns/7/donate
   ```

3. Fill donation form and submit

4. At ToyyibPay sandbox, you'll be redirected to:
   ```
   https://dev.toyyibpay.com/5lspw9g8?mode=paymentBankSimulator
   ```

5. **Test Success:**
   - Select any bank
   - Click "Proceed to Payment"
   - Click "SUCCESS" button
   - Verify redirect to success page
   - Check donation status = 'Completed'

6. **Test Failure:**
   - Select any bank
   - Click "Proceed to Payment"
   - Click "FAILED" button
   - Verify redirect to failed page
   - Check donation status = 'Failed'

### 2. Verify Database Updates

After successful payment:
```sql
SELECT Payment_Status, Bill_Code, Transaction_ID, Amount
FROM donation
WHERE Donation_ID = {your_donation_id};
-- Should show: Payment_Status = 'Completed'
```

Check campaign updated:
```sql
SELECT Collected_Amount
FROM campaign
WHERE Campaign_ID = {campaign_id};
-- Should be incremented by donation amount
```

Check donor total updated:
```sql
SELECT Total_Donated
FROM donor
WHERE Donor_ID = {donor_id};
-- Should be incremented by donation amount
```

## Troubleshooting

### Issue: SSL Certificate Error
**Error:** `cURL error 60: SSL certificate problem`

**Solution:**
```bash
# Download CA certificate
curl https://curl.se/ca/cacert.pem -o storage/cacert.pem

# Ensure path in ToyyibPayService.php:
if (config('services.toyyibpay.sandbox')) {
    $http = $http->withOptions([
        'verify' => storage_path('cacert.pem'),
    ]);
}
```

### Issue: Payment Status Not Updating
**Problem:** Donation stays as 'Pending'

**Solution:**
1. Check logs: `storage/logs/laravel.log`
2. Verify return URL is accessible from ToyyibPay
3. Ensure `status_id` parameter is being received
4. Check database transaction isn't rolling back

### Issue: Invalid Bill Creation
**Error:** "Failed to create payment bill"

**Solution:**
1. Verify ToyyibPay credentials in `.env`
2. Check `billAmount` is in cents (multiplied by 100)
3. Ensure `billName` is ≤ 30 characters
4. Verify category code is active in ToyyibPay dashboard

## Code Structure

```
app/
├── Http/Controllers/
│   └── DonationManagementController.php
│       ├── showDonationForm()       # Show donation form
│       ├── processDonation()        # Create donation & redirect to ToyyibPay
│       ├── paymentReturn()          # Handle return from ToyyibPay (SUCCESS/FAILED)
│       ├── paymentCallback()        # Handle server callback
│       └── myDonations()            # Show completed donations only
│
└── Services/
    └── ToyyibPayService.php
        ├── createBill()             # Create payment bill
        ├── getBillTransactions()    # Query payment status
        └── verifyPayment()          # Verify callback authenticity

resources/views/donation-management/
├── donate.blade.php                 # Donation form
├── success.blade.php                # Payment success page ✅
├── payment-failed.blade.php         # Payment failed page ✅
└── payment-pending.blade.php        # NOT USED ❌

routes/web.php
├── GET  /campaigns/{id}/donate      # Show donation form
├── POST /campaigns/{id}/donate      # Process donation
├── GET  /donation/payment/return/{id}    # Return from ToyyibPay
└── POST /donation/payment/callback       # ToyyibPay callback
```

## Security Considerations

1. **CSRF Protection:** All POST routes protected by Laravel's CSRF middleware
2. **User Verification:** Payment return validates donation belongs to current user
3. **Callback Verification:** `verifyPayment()` method checks status_id
4. **Database Transactions:** All updates wrapped in DB transactions
5. **Amount Validation:** Prevents donations exceeding campaign goal

## Production Checklist

Before going live:

- [ ] Set `TOYYIBPAY_SANDBOX=false` in `.env`
- [ ] Update to production credentials from https://toyyibpay.com
- [ ] Remove SSL certificate override in `ToyyibPayService.php`
- [ ] Test all payment flows in production environment
- [ ] Enable Laravel error logging
- [ ] Set up monitoring for failed payments
- [ ] Verify callback URL is publicly accessible
- [ ] Test receipt PDF generation
- [ ] Verify campaign goals update correctly

## Support

- **ToyyibPay Documentation:** https://toyyibpay.com/apireference/
- **ToyyibPay Support:** support@toyyibpay.com
- **Sandbox Dashboard:** https://dev.toyyibpay.com
- **Production Dashboard:** https://toyyibpay.com

---

**Last Updated:** December 2025
**Integration Version:** 1.0
**ToyyibPay API Version:** 2.0
