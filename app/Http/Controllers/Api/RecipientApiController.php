<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicProfile;
use App\Models\Recipient;
use App\Services\Api\UserApiService;
use Illuminate\Http\Request;

class RecipientApiController extends Controller
{
    protected $userService;

    public function __construct(UserApiService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get all recipients
     */
    public function index(Request $request)
    {
        $query = Recipient::query();

        if ($request->has('status')) {
            $query->where('Status', $request->status);
        }

        $recipients = $query->with('publicProfile')->get();

        return response()->json($recipients);
    }

    /**
     * Get single recipient
     */
    public function show($id)
    {
        $recipient = Recipient::with('publicProfile')->findOrFail($id);

        return response()->json($recipient);
    }

    /**
     * Create a new recipient application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Public_ID' => 'required|integer',
            'Name' => 'required|string|max:255',
            'Address' => 'required|string',
            'Contact' => 'required|string',
            'Need_Description' => 'required|string',
        ]);

        // Validate public profile exists
        $publicProfile = PublicProfile::find($validated['Public_ID']);
        if (! $publicProfile) {
            return response()->json(['error' => 'Public profile not found'], 404);
        }

        $recipient = Recipient::create(array_merge($validated, [
            'Status' => 'Pending', // Requires admin approval
            'Approved_At' => null,
        ]));

        return response()->json($recipient, 201);
    }

    /**
     * Update recipient
     */
    public function update(Request $request, $id)
    {
        $recipient = Recipient::findOrFail($id);

        $validated = $request->validate([
            'Name' => 'sometimes|string|max:255',
            'Address' => 'sometimes|string',
            'Contact' => 'sometimes|string',
            'Need_Description' => 'sometimes|string',
        ]);

        $recipient->update($validated);

        return response()->json($recipient);
    }

    /**
     * Approve recipient
     */
    public function approve($id)
    {
        $recipient = Recipient::findOrFail($id);

        $recipient->update([
            'Status' => 'Approved',
            'Approved_At' => now(),
        ]);

        return response()->json($recipient);
    }

    /**
     * Reject recipient
     */
    public function reject(Request $request, $id)
    {
        $recipient = Recipient::findOrFail($id);

        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $recipient->update(['Status' => 'Rejected']);

        return response()->json($recipient);
    }

    /**
     * Get recipient allocations (calls Donation service)
     */
    public function getAllocations($id)
    {
        // This would call the Donation service API
        // For now, return empty array as placeholder
        return response()->json([]);
    }
}
