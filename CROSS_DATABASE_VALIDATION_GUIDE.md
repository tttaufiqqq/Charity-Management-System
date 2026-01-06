# 🛡️ Cross-Database Validation Guide

**Application-Layer Validation for Distributed Database References**

## Overview

Since **database-level foreign key constraints cannot span multiple databases**, we use **application-layer validation** to ensure referential integrity across our heterogeneous distributed database architecture.

The `ValidatesCrossDatabaseReferences` trait provides reusable validation methods for cross-database references.

---

## 📦 Trait Location

```
app/Traits/ValidatesCrossDatabaseReferences.php
```

---

## 🎯 When to Use This Trait

Use this trait in:
- **Controllers** - Validate before saving data
- **Form Requests** - Add custom validation rules
- **Service Classes** - Business logic validation
- **Jobs** - Background task validation

**Use whenever creating/updating records that reference data in different databases!**

---

## 📚 Available Validation Methods

### **User Validation (Izzhilmy Database)**

```php
// Validate User exists
$user = $this->validateUserExists($userId);

// Prevent duplicate profiles
$this->validateUserDoesNotHaveProfile($userId, 'donor');
$this->validateUserDoesNotHaveProfile($userId, 'volunteer');
$this->validateUserDoesNotHaveProfile($userId, 'organization');
$this->validateUserDoesNotHaveProfile($userId, 'publicProfile');
```

### **Campaign Validation (Izzati Database)**

```php
// Validate Campaign exists
$campaign = $this->validateCampaignExists($campaignId);

// Validate Campaign is active (for donations)
$campaign = $this->validateCampaignIsActive($campaignId);

// Validate sufficient funds for allocation
$campaign = $this->validateCampaignHasSufficientFunds($campaignId, $amount);

// Validate donation amount
$campaign = $this->validateDonationAmount($campaignId, $amount);
```

### **Event Validation (Izzati Database)**

```php
// Validate Event exists
$event = $this->validateEventExists($eventId);

// Validate Event is accepting registrations
$event = $this->validateEventIsUpcoming($eventId);

// Validate Event has capacity
$event = $this->validateEventHasCapacity($eventId);
```

### **Recipient Validation (Adam Database)**

```php
// Validate Recipient exists
$recipient = $this->validateRecipientExists($recipientId);

// Validate Recipient is approved (for fund allocation)
$recipient = $this->validateRecipientIsApproved($recipientId);
```

### **Batch Validation**

```php
// Validate multiple references at once
$validated = $this->validateCrossDatabaseReferences([
    'user' => ['id' => $userId, 'field' => 'User_ID'],
    'campaignActive' => ['id' => $campaignId, 'field' => 'Campaign_ID'],
    'recipientApproved' => ['id' => $recipientId, 'field' => 'Recipient_ID'],
]);

// Access validated models
$user = $validated['user'];
$campaign = $validated['campaignActive'];
$recipient = $validated['recipientApproved'];
```

---

## 💡 Usage Examples

### **Example 1: Creating a Donation (Cross-DB: Campaign in Izzati)**

```php
// app/Http/Controllers/DonationManagementController.php

use App\Traits\ValidatesCrossDatabaseReferences;

class DonationManagementController extends Controller
{
    use ValidatesCrossDatabaseReferences;

    public function store(Request $request)
    {
        $request->validate([
            'Campaign_ID' => 'required|integer',
            'Amount' => 'required|numeric|min:1',
            'Payment_Method' => 'required|string',
        ]);

        // ⚠️ CRITICAL: Validate Campaign exists (cross-database)
        $campaign = $this->validateCampaignIsActive($request->Campaign_ID);

        // Optional: Validate donation amount
        $this->validateDonationAmount($request->Campaign_ID, $request->Amount);

        // Get authenticated donor
        $donor = auth()->user()->donor;

        // Create donation in Hannah database
        $donation = Donation::on('hannah')->create([
            'Donor_ID' => $donor->Donor_ID,
            'Campaign_ID' => $campaign->Campaign_ID,
            'Amount' => $request->Amount,
            'Donation_Date' => now(),
            'Payment_Method' => $request->Payment_Method,
            'Receipt_No' => $this->generateReceiptNumber(),
        ]);

        // Update campaign collected amount (in Izzati database)
        $campaign->increment('Collected_Amount', $request->Amount);

        return redirect()->route('my-donations')
            ->with('success', 'Donation successful! Receipt: '.$donation->Receipt_No);
    }
}
```

---

### **Example 2: Creating Volunteer Profile (Cross-DB: User in Izzhilmy)**

```php
// app/Http/Controllers/VolunteerController.php

use App\Traits\ValidatesCrossDatabaseReferences;

class VolunteerController extends Controller
{
    use ValidatesCrossDatabaseReferences;

    public function store(Request $request)
    {
        $request->validate([
            'Availability' => 'required|string',
            'Address' => 'required|string',
            'City' => 'required|string',
            'State' => 'required|string',
            'Gender' => 'required|in:Male,Female,Other',
            'Phone_Num' => 'required|string|max:20',
        ]);

        $userId = auth()->id();

        // ⚠️ CRITICAL: Validate User exists (cross-database)
        $user = $this->validateUserExists($userId);

        // ⚠️ CRITICAL: Prevent duplicate volunteer profiles
        $this->validateUserDoesNotHaveProfile($userId, 'volunteer');

        // Create volunteer profile in Sashvini database
        $volunteer = Volunteer::on('sashvini')->create([
            'User_ID' => $userId,
            'Availability' => $request->Availability,
            'Address' => $request->Address,
            'City' => $request->City,
            'State' => $request->State,
            'Gender' => $request->Gender,
            'Phone_Num' => $request->Phone_Num,
            'Description' => $request->Description,
        ]);

        return redirect()->route('volunteer.dashboard')
            ->with('success', 'Volunteer profile created successfully!');
    }
}
```

---

### **Example 3: Event Registration (Cross-DB: Event in Izzati, Volunteer in Sashvini)**

```php
// app/Http/Controllers/VolunteerController.php

use App\Traits\ValidatesCrossDatabaseReferences;
use App\Models\EventParticipation;

class VolunteerController extends Controller
{
    use ValidatesCrossDatabaseReferences;

    public function registerForEvent(Request $request, $eventId)
    {
        // ⚠️ CRITICAL: Validate Event exists and is upcoming (cross-database)
        $event = $this->validateEventIsUpcoming($eventId);

        // ⚠️ CRITICAL: Validate Event has capacity
        $this->validateEventHasCapacity($eventId);

        $volunteer = auth()->user()->volunteer;

        // Check if already registered
        $existingRegistration = EventParticipation::on('sashvini')
            ->where('Volunteer_ID', $volunteer->Volunteer_ID)
            ->where('Event_ID', $eventId)
            ->first();

        if ($existingRegistration) {
            return back()->withErrors(['event' => 'You are already registered for this event.']);
        }

        // Create event participation in Sashvini database
        EventParticipation::on('sashvini')->create([
            'Volunteer_ID' => $volunteer->Volunteer_ID,
            'Event_ID' => $eventId,
            'Status' => 'Registered',
            'Total_Hours' => 0,
        ]);

        return back()->with('success', 'Successfully registered for event!');
    }
}
```

---

### **Example 4: Fund Allocation (Cross-DB: Campaign in Izzati, Recipient in Adam)**

```php
// app/Http/Controllers/RecipientManagementController.php

use App\Traits\ValidatesCrossDatabaseReferences;
use App\Models\DonationAllocation;

class RecipientManagementController extends Controller
{
    use ValidatesCrossDatabaseReferences;

    public function allocateFunds(Request $request)
    {
        $request->validate([
            'Campaign_ID' => 'required|integer',
            'Recipient_ID' => 'required|integer',
            'Amount_Allocated' => 'required|numeric|min:1',
        ]);

        // ⚠️ CRITICAL: Validate both Campaign and Recipient exist (cross-database)
        $validated = $this->validateCrossDatabaseReferences([
            'campaign' => ['id' => $request->Campaign_ID, 'field' => 'Campaign_ID'],
            'recipientApproved' => ['id' => $request->Recipient_ID, 'field' => 'Recipient_ID'],
        ]);

        $campaign = $validated['campaign'];
        $recipient = $validated['recipientApproved'];

        // ⚠️ CRITICAL: Validate sufficient funds
        $this->validateCampaignHasSufficientFunds(
            $campaign->Campaign_ID,
            $request->Amount_Allocated
        );

        // Create allocation in Hannah database
        DonationAllocation::on('hannah')->create([
            'Campaign_ID' => $campaign->Campaign_ID,
            'Recipient_ID' => $recipient->Recipient_ID,
            'Amount_Allocated' => $request->Amount_Allocated,
            'Allocated_At' => now(),
        ]);

        return redirect()->route('campaigns.allocate', $campaign->Campaign_ID)
            ->with('success', 'Funds allocated successfully!');
    }
}
```

---

### **Example 5: Using in Form Requests**

```php
// app/Http/Requests/StoreDonationRequest.php

use App\Traits\ValidatesCrossDatabaseReferences;
use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    use ValidatesCrossDatabaseReferences;

    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('donor');
    }

    public function rules(): array
    {
        return [
            'Campaign_ID' => 'required|integer',
            'Amount' => 'required|numeric|min:1',
            'Payment_Method' => 'required|string|in:Online Banking,Credit/Debit Card,E-Wallet,Other',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                // Validate Campaign exists and is active (cross-database)
                $this->validateCampaignIsActive($this->Campaign_ID);

                // Validate donation amount
                $this->validateDonationAmount($this->Campaign_ID, $this->Amount);

            } catch (\Illuminate\Validation\ValidationException $e) {
                $validator->errors()->merge($e->errors());
            }
        });
    }
}
```

---

## ⚠️ Important Notes

### **1. Always Validate Before Creating Records**

```php
// ❌ WRONG - No validation
$donation = Donation::create([
    'Campaign_ID' => $request->Campaign_ID, // Campaign might not exist!
    ...
]);

// ✅ CORRECT - Validate first
$campaign = $this->validateCampaignIsActive($request->Campaign_ID);
$donation = Donation::create([
    'Campaign_ID' => $campaign->Campaign_ID,
    ...
]);
```

### **2. Validation Throws Exceptions**

All validation methods throw `ValidationException` if validation fails. This automatically:
- Returns error messages to the user
- Prevents invalid data from being saved
- Works with Laravel's standard error handling

### **3. Use the Returned Models**

Validation methods return the validated model instance - use it!

```php
// Validate AND get the model
$campaign = $this->validateCampaignExists($campaignId);

// Now you have the campaign object for further use
$remainingFunds = $campaign->Goal_Amount - $campaign->Collected_Amount;
```

### **4. Batch Validation for Multiple References**

When creating records with multiple cross-database references, use batch validation:

```php
// Instead of:
$user = $this->validateUserExists($userId);
$campaign = $this->validateCampaignExists($campaignId);
$recipient = $this->validateRecipientExists($recipientId);

// Use:
$validated = $this->validateCrossDatabaseReferences([
    'user' => ['id' => $userId],
    'campaign' => ['id' => $campaignId],
    'recipient' => ['id' => $recipientId],
]);
```

---

## 🧪 Testing Cross-Database Validation

```php
// tests/Feature/DonationValidationTest.php

use App\Models\Campaign;
use App\Models\Donor;
use Tests\TestCase;

class DonationValidationTest extends TestCase
{
    /** @test */
    public function it_validates_campaign_exists_before_donation()
    {
        $donor = Donor::factory()->create();
        $this->actingAs($donor->user);

        $response = $this->post(route('donations.store'), [
            'Campaign_ID' => 99999, // Non-existent campaign
            'Amount' => 100.00,
            'Payment_Method' => 'Online Banking',
        ]);

        $response->assertSessionHasErrors('Campaign_ID');
    }

    /** @test */
    public function it_validates_campaign_is_active()
    {
        $campaign = Campaign::factory()->create(['Status' => 'Completed']);
        $donor = Donor::factory()->create();
        $this->actingAs($donor->user);

        $response = $this->post(route('donations.store'), [
            'Campaign_ID' => $campaign->Campaign_ID,
            'Amount' => 100.00,
            'Payment_Method' => 'Online Banking',
        ]);

        $response->assertSessionHasErrors('Campaign_ID');
    }
}
```

---

## 📝 Validation Checklist

Before pushing to production, ensure:

- [ ] All **Donation** controllers validate `Campaign_ID`
- [ ] All **DonationAllocation** controllers validate `Campaign_ID` and `Recipient_ID`
- [ ] All **EventParticipation** controllers validate `Event_ID`
- [ ] All **Volunteer** creation validates `User_ID`
- [ ] All **Donor** creation validates `User_ID`
- [ ] All **Organization** creation validates `Organizer_ID` (User_ID)
- [ ] All **PublicProfile** creation validates `User_ID`
- [ ] All **CampaignRecipientSuggestion** creation validates `Recipient_ID` and `Suggested_By`

---

**Generated for**: Charity-Izz Heterogeneous Distributed Database
**Date**: 2026-01-07
