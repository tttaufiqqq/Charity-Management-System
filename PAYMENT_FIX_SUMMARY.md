# Payment Status Fix - Summary

## Problem Solved

**Issue:** Donations were being inserted into the database with `Payment_Status = 'Pending'` even before payment completion, resulting in many pending records that never get resolved.

**Solution:** Changed the flow to **only insert donations AFTER** getting the ToyyibPay payment result. Database now contains **ONLY** `Completed` or `Failed` statuses - **NO Pending**.

---

## How It Works Now

### New Payment Flow

```
1. User fills donation form
   ↓
2. Donation data stored in SESSION (not database)
   ↓
3. Create ToyyibPay bill and redirect to payment page
   ↓
4. User completes/cancels payment at ToyyibPay simulator
   ↓
5. ToyyibPay returns to app with status_id
   ↓
6. ✅ status_id = 1 (Success)  → Insert to DB as 'Completed'
   ❌ status_id = 2|3 (Failed) → Insert to DB as 'Failed'
   ↓
7. Clear session data
```

**Key Change:** Donation record is **ONLY created** when we know the final payment status!

---

## What Changed

### 1. **DonationManagementController.php**

#### `processDonation()` Method
**Before:**
```php
// Created donation in database immediately
$donation = Donation::create([
    'Payment_Status' => 'Pending', // ❌ Creates pending record
]);

// Redirect to ToyyibPay
```

**After:**
```php
// Store in session only
session([
    'pending_donation' => [
        'donor_id' => $donor->Donor_ID,
        'amount' => $request->amount,
        // ... other fields
    ]
]);

// Redirect to ToyyibPay
// ✅ No database record yet!
```

#### `paymentReturn()` Method
**Before:**
```php
// Found existing pending donation
$donation = Donation::findOrFail($donationId);

// Updated status
$donation->update(['Payment_Status' => 'Completed']);
```

**After:**
```php
// Get data from session
$donationData = session('pending_donation');

if ($statusId == '1') {
    // ✅ CREATE donation as Completed
    Donation::create([
        'Payment_Status' => 'Completed',
        // ... other fields from session
    ]);
}
```

### 2. **Routes (web.php)**

**Before:**
```php
Route::get('/donation/payment/return/{donationId}', ...);
```

**After:**
```php
Route::get('/donation/payment/return', ...); // No ID needed
```

### 3. **Migration (create_donation.php)**

**Before:**
```php
$table->string('Payment_Status', 20)->default('Pending');
```

**After:**
```php
$table->string('Payment_Status', 20); // No default, only accepts Completed/Failed
```

---

## Clean Up Old Pending Records

Run this command to delete all existing pending donations:

```bash
php artisan donations:clean-pending
```

**What it does:**
- Finds all donations with `Payment_Status = 'Pending'`
- Asks for confirmation
- Deletes all pending records
- Leaves only `Completed` and `Failed` donations

**Sample output:**
```
Cleaning up pending donations...
Found 11 pending donation(s).

 Do you want to delete these records? (yes/no) [yes]:
 > yes

Successfully deleted 11 pending donation(s).
Database now only contains Completed and Failed donations.
```

---

## Testing the Fix

### 1. Test Successful Payment

```bash
# Start server
php artisan serve
```

1. Visit: `http://127.0.0.1:8000/campaigns/7/donate`
2. Enter amount (e.g., 100) and submit
3. At ToyyibPay simulator: `https://dev.toyyibpay.com/5lspw9g8?mode=paymentBankSimulator`
4. Click **SUCCESS** button
5. **Verify:**
   ```sql
   SELECT Payment_Status FROM donation
   ORDER BY created_at DESC LIMIT 1;
   ```
   **Expected:** `Completed` ✅

### 2. Test Failed Payment

1. Same steps as above
2. At ToyyibPay simulator, click **FAILED** button
3. **Verify:**
   ```sql
   SELECT Payment_Status FROM donation
   ORDER BY created_at DESC LIMIT 1;
   ```
   **Expected:** `Failed` ✅

### 3. Verify No Pending Records

```sql
SELECT Payment_Status, COUNT(*) as count
FROM donation
GROUP BY Payment_Status;
```

**Expected Result:**
```
Payment_Status | count
---------------|------
Completed      | 90
Failed         | 2
```

**Should NOT see:** `Pending` ❌

---

## Database Schema

```sql
CREATE TABLE donation (
    Donation_ID BIGINT PRIMARY KEY,
    Donor_ID BIGINT NOT NULL,
    Campaign_ID BIGINT NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    Donation_Date DATE NOT NULL,
    Payment_Method VARCHAR(50) NOT NULL,
    Receipt_No VARCHAR(100) UNIQUE NOT NULL,

    -- Payment Status: ONLY 'Completed' or 'Failed' (NO Pending)
    Payment_Status VARCHAR(20) NOT NULL, -- ✅ Completed or Failed only
    Bill_Code VARCHAR(100),
    Transaction_ID VARCHAR(100),

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## Benefits of This Approach

### ✅ Cleaner Database
- No orphaned pending records
- Easy to query successful donations
- Clear audit trail

### ✅ Better User Experience
- Users only see Success or Failed pages
- No confusing "pending" status
- Clear outcome after payment

### ✅ Accurate Reporting
- `Total_Donated` only counts completed payments
- Campaign `Collected_Amount` is always accurate
- Donation history shows only real donations

### ✅ Session-Based Temporary Storage
- No database pollution
- Sessions auto-expire
- Clean and scalable

---

## Rollback (If Needed)

If you need to revert to the old behavior:

1. Restore the old `processDonation()` method that creates donation immediately
2. Restore the old `paymentReturn()` that updates existing donation
3. Revert route to include `{donationId}` parameter
4. Run: `php artisan migrate:fresh --seed`

**Note:** This is NOT recommended. The new approach is cleaner and prevents data pollution.

---

## Files Modified

| File | Change Summary |
|------|---------------|
| `app/Http/Controllers/DonationManagementController.php` | Store donation in session, create only after payment |
| `routes/web.php` | Removed `{donationId}` parameter from return route |
| `database/migrations/2025_11_25_173320_create_donation.php` | Removed 'Pending' default value |
| `app/Console/Commands/CleanPendingDonations.php` | NEW - Command to delete pending records |

---

## Important Notes

1. **Session Configuration:**
   - Ensure your session driver is set to `database` or `redis`
   - Session lifetime should be reasonable (default: 120 minutes is fine)
   - Sessions auto-clear on payment completion

2. **For New Setups:**
   - Run `php artisan migrate:fresh --seed` to start fresh
   - Database will only have Completed/Failed donations from the start

3. **For Existing Setups:**
   - Run `php artisan donations:clean-pending` to clean old records
   - Continue using the app normally

4. **ToyyibPay Sandbox:**
   - Make sure `TOYYIBPAY_SANDBOX=true` in `.env`
   - Download SSL certificate: `curl https://curl.se/ca/cacert.pem -o storage/cacert.pem`
   - Test URL: `https://dev.toyyibpay.com/5lspw9g8?mode=paymentBankSimulator`

---

## Quick Reference

### Clean Pending Donations
```bash
php artisan donations:clean-pending
```

### Check Database Status
```sql
-- Count by status
SELECT Payment_Status, COUNT(*) FROM donation GROUP BY Payment_Status;

-- Should ONLY show Completed and Failed
```

### Test Payment Flow
```
1. Go to: http://127.0.0.1:8000/campaigns/7/donate
2. Submit donation
3. At ToyyibPay: Click SUCCESS or FAILED
4. Verify database has correct status
```

---

## Support

If you encounter issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify session is working: `php artisan tinker`, then `session()->all()`
3. Check ToyyibPay credentials in `.env`
4. Ensure database has the Payment_Status column

---

**Date:** December 21, 2025
**Status:** ✅ **FIXED - No More Pending Status in Database**
