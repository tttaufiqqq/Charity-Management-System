<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Organization;
use App\Services\Api\UserApiService;
use Illuminate\Http\Request;

class OrganizationApiController extends Controller
{
    protected $userService;

    public function __construct(UserApiService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get all organizations
     */
    public function index(Request $request)
    {
        $organizations = Organization::all();

        return response()->json($organizations);
    }

    /**
     * Get single organization
     */
    public function show($id)
    {
        $organization = Organization::findOrFail($id);

        return response()->json($organization);
    }

    /**
     * Create a new organization
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Organizer_ID' => 'required|integer',
            'Phone_No' => 'required|string',
            'Register_No' => 'required|string|unique:organization,Register_No',
            'Address' => 'required|string',
            'State' => 'required|string',
            'City' => 'required|string',
            'Description' => 'nullable|string',
        ]);

        // Validate user exists via API
        if (! $this->userService->exists($validated['Organizer_ID'])) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Validate user has organizer role
        if (! $this->userService->hasRole($validated['Organizer_ID'], 'organizer')) {
            return response()->json(['error' => 'User must have organizer role'], 403);
        }

        $organization = Organization::create($validated);

        return response()->json($organization, 201);
    }

    /**
     * Update organization
     */
    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);

        $validated = $request->validate([
            'Phone_No' => 'sometimes|string',
            'Register_No' => 'sometimes|string|unique:organization,Register_No,'.$id.',Organization_ID',
            'Address' => 'sometimes|string',
            'State' => 'sometimes|string',
            'City' => 'sometimes|string',
            'Description' => 'sometimes|string',
        ]);

        $organization->update($validated);

        return response()->json($organization);
    }

    /**
     * Delete organization
     */
    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();

        return response()->json(['message' => 'Organization deleted successfully']);
    }

    /**
     * Get organization campaigns
     */
    public function getCampaigns($id)
    {
        Organization::findOrFail($id);
        $campaigns = Campaign::where('Organization_ID', $id)->get();

        return response()->json($campaigns);
    }

    /**
     * Get organization events (from Volunteer service)
     */
    public function getEvents($id)
    {
        Organization::findOrFail($id);
        $events = Event::where('Organizer_ID', $id)->get();

        return response()->json($events);
    }
}
