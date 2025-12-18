# ToyyibPay FPX Payment Gateway Integration Guide

## Overview
This application has been configured to accept donations via **FPX Online Banking** through **ToyyibPay** payment gateway.

## What Has Been Done

### 1. ✅ Views Updated to Tabular Display
- `/public/events` - Now displays in clean table format
- `/campaigns` - Now displays in clean table format with progress bars

### 2. ✅ Donation Form Updated
- Payment method locked to "FPX Online Banking" only
- Form validation updated to only accept FPX payments
- UI shows "Secured payment via ToyyibPay FPX Gateway"

### 3. ✅ ToyyibPay Integration Files Created
- `app/Services/ToyyibPayService.php` - Payment gateway service
- Configuration added to `config/services.php`

## Setup Instructions

### Step 1: Get ToyyibPay Credentials

1. **Register for ToyyibPay Account**
   - Sandbox (Testing): https://dev.toyyibpay.com
   - Production: https://toyyibpay.com

2. **Create a Category**
   - Login to your ToyyibPay dashboard
   - Go to "Package/Category"
   - Create a new category (e.g., "Charity Donations")
   - Copy the **Category Code**

3. **Get Your Secret Key**
   - Go to "Setting" or "Profile"
   - Copy your **Secret Key** (userSecretKey)

### Step 2: Add Credentials to .env File

Open your `.env` file and add these lines at the bottom:

```env
# ToyyibPay Payment Gateway Configuration
TOYYIBPAY_SECRET_KEY=your-secret-key-here
TOYYIBPAY_CATEGORY_CODE=your-category-code-here
TOYYIBPAY_SANDBOX=true
```

**Example:**
```env
TOYYIBPAY_SECRET_KEY=abc123xyz-789def-456ghi
TOYYIBPAY_CATEGORY_CODE=abc123
TOYYIBPAY_SANDBOX=true
```

**Important Notes:**
- Keep `TOYYIBPAY_SANDBOX=true` for testing
- Change to `TOYYIBPAY_SANDBOX=false` when going to production
- **NEVER commit your .env file to Git!**

### Step 3: Update DonationManagementController

The payment processing needs to be integrated with ToyyibPay. Here's where to add the code:

**File:** `app/Http/Controllers/DonationManagementController.php`

**Location:** In the `processDonation` method (around line 123)

**Replace the existing donation processing** with:

```php
public function processDonation(Request $request, $campaignId)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
        'payment_method' => 'required|in:FPX Online Banking',
    ]);

    $campaign = Campaign::findOrFail($campaignId);
    $donor = Auth::user()->donor;

    if (!$donor) {
        return redirect()->route('dashboard')->with('error', 'Donor profile not found.');
    }

    if ($campaign->Status !== 'Active') {
        return redirect()->back()->with('error', 'This campaign is not accepting donations.')->withInput();
    }

    // Check if donation would exceed campaign goal
    $remainingAmount = $campaign->Goal_Amount - $campaign->Collected_Amount;
    if ($remainingAmount <= 0) {
        return redirect()->back()
            ->with('error', 'This campaign has already reached its funding goal. Thank you for your interest!')
            ->withInput();
    }

    if ($request->amount > $remainingAmount) {
        return redirect()->back()
            ->with('error', sprintf(
                'Your donation of RM %s exceeds the remaining amount needed (RM %s). Please adjust your donation amount.',
                number_format($request->amount, 2),
                number_format($remainingAmount, 2)
            ))
            ->withInput();
    }

    DB::beginTransaction();
    try {
        // Create donation record with pending status
        $donation = Donation::create([
            'Donor_ID' => $donor->Donor_ID,
            'Campaign_ID' => $campaign->Campaign_ID,
            'Amount' => $request->amount,
            'Donation_Date' => now(),
            'Payment_Method' => 'FPX Online Banking',
            'Receipt_No' => 'RCP-' . strtoupper(uniqid()),
            'Payment_Status' => 'Pending', // Add this column to donations table
        ]);

        // Initialize ToyyibPay service
        $toyyibpay = new \App\Services\ToyyibPayService();

        // Create bill
        $billData = [
            'billName' => 'Donation to ' . $campaign->Title,
            'billDescription' => 'Charity donation via CharityHub',
            'billAmount' => $request->amount,
            'billReturnUrl' => route('donation.payment.return', $donation->Donation_ID),
            'billCallbackUrl' => route('donation.payment.callback'),
            'billExternalReferenceNo' => 'DON-' . $donation->Donation_ID,
            'billTo' => $donor->Full_Name,
            'billEmail' => Auth::user()->email,
            'billPhone' => $donor->Contact ?? '',
        ];

        $result = $toyyibpay->createBill($billData);

        if ($result['success']) {
            // Store bill code in donation record
            $donation->update([
                'Bill_Code' => $result['billCode'], // Add this column to donations table
            ]);

            DB::commit();

            // Redirect to ToyyibPay payment page
            return redirect()->away($result['paymentUrl']);
        } else {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Payment gateway error: ' . $result['message'])
                ->withInput();
        }

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Donation failed: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Donation failed: ' . $e->getMessage())
            ->withInput();
    }
}
```

### Step 4: Add Payment Callback Routes

Add these routes to `routes/web.php`:

```php
// ToyyibPay Payment Routes
Route::get('/donation/payment/return/{donationId}', [DonationManagementController::class, 'paymentReturn'])->name('donation.payment.return');
Route::post('/donation/payment/callback', [DonationManagementController::class, 'paymentCallback'])->name('donation.payment.callback');
```

### Step 5: Add Callback Methods to Controller

Add these methods to `DonationManagementController.php`:

```php
/**
 * Handle payment return from ToyyibPay
 */
public function paymentReturn($donationId)
{
    $donation = Donation::with(['campaign', 'donor'])->findOrFail($donationId);

    // Verify this donation belongs to current user
    if ($donation->donor->User_ID !== Auth::id()) {
        abort(403);
    }

    // Check payment status
    if ($donation->Payment_Status === 'Completed') {
        return redirect()->route('donation.success', $donation->Donation_ID)
            ->with('success', 'Thank you for your donation!');
    }

    return view('donation-management.payment-pending', compact('donation'));
}

/**
 * Handle payment callback from ToyyibPay
 */
public function paymentCallback(Request $request)
{
    \Log::info('ToyyibPay Callback', $request->all());

    $toyyibpay = new \App\Services\ToyyibPayService();

    // Verify payment
    if ($toyyibpay->verifyPayment($request->all())) {
        // Find donation by external reference
        $refNo = $request->input('refno');
        $donationId = str_replace('DON-', '', $refNo);

        $donation = Donation::find($donationId);

        if ($donation && $donation->Payment_Status !== 'Completed') {
            DB::beginTransaction();
            try {
                // Update donation status
                $donation->update([
                    'Payment_Status' => 'Completed',
                    'Transaction_ID' => $request->input('transaction_id'),
                ]);

                // Update campaign total
                $donation->campaign->increment('Collected_Amount', $donation->Amount);

                // Update donor total
                $donation->donor->increment('Total_Donated', $donation->Amount);

                DB::commit();

                \Log::info('Payment completed for donation: ' . $donationId);
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Payment callback failed: ' . $e->getMessage());
            }
        }
    }

    return response('OK', 200);
}
```

### Step 6: Add Database Columns

Run this migration to add required columns to the donations table:

```bash
php artisan make:migration add_payment_fields_to_donation_table
```

Then add this in the migration file:

```php
public function up()
{
    Schema::table('donation', function (Blueprint $table) {
        $table->string('Payment_Status')->default('Pending')->after('Payment_Method');
        $table->string('Bill_Code')->nullable()->after('Payment_Status');
        $table->string('Transaction_ID')->nullable()->after('Bill_Code');
    });
}

public function down()
{
    Schema::table('donation', function (Blueprint $table) {
        $table->dropColumn(['Payment_Status', 'Bill_Code', 'Transaction_ID']);
    });
}
```

Run the migration:
```bash
php artisan migrate
```

## Testing

### Sandbox Testing
1. Make sure `TOYYIBPAY_SANDBOX=true` in `.env`
2. Try making a donation
3. You'll be redirected to ToyyibPay sandbox
4. Use test bank credentials provided by ToyyibPay

### Test Banks (Sandbox)
ToyyibPay sandbox provides test bank logins. Check their documentation for current test credentials.

## Going to Production

When ready for production:
1. Change `TOYYIBPAY_SANDBOX=false` in `.env`
2. Replace sandbox credentials with production credentials
3. Clear config cache: `php artisan config:clear`

## Support

- ToyyibPay Documentation: https://toyyibpay.com/apireference/
- ToyyibPay Support: support@toyyibpay.com

## Security Notes

- ✅ Payment method is locked to FPX only
- ✅ All transactions go through ToyyibPay secure gateway
- ✅ No credit card data is stored on your server
- ✅ Callback verification ensures payment authenticity
- ✅ Over-donation prevention is in place
