<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationAllocation;
use App\Models\Donor;
use App\Services\Api\CampaignApiService;
use App\Services\Api\RecipientApiService;
use App\Services\Api\UserApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationApiController extends Controller
{
    protected $campaignService;

    protected $recipientService;

    protected $userService;

    public function __construct(
        CampaignApiService $campaignService,
        RecipientApiService $recipientService,
        UserApiService $userService
    ) {
        $this->campaignService = $campaignService;
        $this->recipientService = $recipientService;
        $this->userService = $userService;
    }

    /**
     * Get all donations
     */
    public function index(Request $request)
    {
        $query = Donation::query();

        if ($request->has('donor_id')) {
            $query->where('Donor_ID', $request->donor_id);
        }

        if ($request->has('campaign_id')) {
            $query->where('Campaign_ID', $request->campaign_id);
        }

        if ($request->has('payment_status')) {
            $query->where('Payment_Status', $request->payment_status);
        }

        $donations = $query->with('donor')->get();

        return response()->json($donations);
    }

    /**
     * Get single donation
     */
    public function show($id)
    {
        $donation = Donation::with('donor')->findOrFail($id);

        return response()->json($donation);
    }

    /**
     * Create a new donation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Donor_ID' => 'required|integer',
            'Campaign_ID' => 'required|integer',
            'Amount' => 'required|numeric|min:1',
            'Payment_Method' => 'required|string',
        ]);

        // Validate campaign exists and is active via API
        if (! $this->campaignService->isActive($validated['Campaign_ID'])) {
            return response()->json(['error' => 'Campaign not found or not active'], 404);
        }

        DB::connection('hannah')->beginTransaction();

        try {
            // Create donation
            $donation = Donation::create([
                'Donor_ID' => $validated['Donor_ID'],
                'Campaign_ID' => $validated['Campaign_ID'],
                'Amount' => $validated['Amount'],
                'Payment_Method' => $validated['Payment_Method'],
                'Donation_Date' => now(),
                'Receipt_No' => 'REC-'.time().'-'.rand(1000, 9999),
                'Payment_Status' => 'Pending',
            ]);

            // Update donor total
            $donor = Donor::findOrFail($validated['Donor_ID']);
            $donor->increment('Total_Donated', $validated['Amount']);

            DB::connection('hannah')->commit();

            // Notify campaign service to update collected amount (async)
            try {
                $this->campaignService->updateCollectedAmount(
                    $validated['Campaign_ID'],
                    $validated['Amount']
                );
            } catch (\Exception $e) {
                \Log::error('Failed to update campaign collected amount', [
                    'donation_id' => $donation->Donation_ID,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json($donation, 201);
        } catch (\Exception $e) {
            DB::connection('hannah')->rollBack();
            throw $e;
        }
    }

    /**
     * Update donation payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $donation = Donation::findOrFail($id);

        $validated = $request->validate([
            'Payment_Status' => 'required|in:Pending,Completed,Failed',
            'Transaction_ID' => 'nullable|string',
            'Bill_Code' => 'nullable|string',
        ]);

        $donation->update($validated);

        return response()->json($donation);
    }

    /**
     * Create donation allocation
     */
    public function createAllocation(Request $request)
    {
        $validated = $request->validate([
            'Campaign_ID' => 'required|integer',
            'Recipient_ID' => 'required|integer',
            'Amount_Allocated' => 'required|numeric|min:0',
        ]);

        // Validate campaign exists via API
        if (! $this->campaignService->exists($validated['Campaign_ID'])) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        // Validate recipient is approved via API
        if (! $this->recipientService->isApproved($validated['Recipient_ID'])) {
            return response()->json(['error' => 'Recipient not approved'], 400);
        }

        // Check available funds via API
        $availableFunds = $this->campaignService->getAvailableFunds($validated['Campaign_ID']);

        if ($validated['Amount_Allocated'] > $availableFunds) {
            return response()->json(['error' => 'Insufficient funds available'], 400);
        }

        $allocation = DonationAllocation::create([
            'Campaign_ID' => $validated['Campaign_ID'],
            'Recipient_ID' => $validated['Recipient_ID'],
            'Amount_Allocated' => $validated['Amount_Allocated'],
            'Allocated_At' => now(),
        ]);

        return response()->json($allocation, 201);
    }

    /**
     * Get allocations for a campaign
     */
    public function getAllocationsByCampaign($campaignId)
    {
        $allocations = DonationAllocation::where('Campaign_ID', $campaignId)->get();

        return response()->json($allocations);
    }

    /**
     * Get allocations for a recipient
     */
    public function getAllocationsByRecipient($recipientId)
    {
        $allocations = DonationAllocation::where('Recipient_ID', $recipientId)->get();

        return response()->json($allocations);
    }
}
