<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Route to role-specific profile views
        if ($user->hasRole('volunteer')) {
            $volunteer = $user->volunteer;

            if (! $volunteer) {
                return view('profile.edit', ['user' => $user]);
            }

            // Calculate statistics (cross-database safe - no JOINs)
            // Get event IDs from event_participation (sashvini database)
            $eventIds = $volunteer->eventParticipations()->pluck('Event_ID');

            // Query events directly (izzati database)
            $totalEvents = \App\Models\Event::whereIn('Event_ID', $eventIds)->count();
            $completedEvents = \App\Models\Event::whereIn('Event_ID', $eventIds)->where('Status', 'Completed')->count();
            $upcomingEvents = \App\Models\Event::whereIn('Event_ID', $eventIds)->whereIn('Status', ['Upcoming', 'Ongoing'])->count();
            $totalHours = $volunteer->eventParticipations()->sum('Total_Hours');
            $skills = $volunteer->skills;

            return view('profile.volunteer', compact('volunteer', 'totalEvents', 'completedEvents', 'upcomingEvents', 'totalHours', 'skills'));
        }

        if ($user->hasRole('organizer')) {
            $organization = $user->organization;

            if (! $organization) {
                return view('profile.edit', ['user' => $user]);
            }

            // Calculate statistics
            $totalCampaigns = $organization->campaigns()->count();
            $activeCampaigns = $organization->campaigns()->where('Status', 'Active')->count();
            $totalRaised = $organization->campaigns()->sum('Collected_Amount');
            $totalEvents = $organization->events()->count();
            $upcomingEvents = $organization->events()->whereIn('Status', ['Upcoming', 'Ongoing'])->count();

            return view('profile.organizer', compact('organization', 'totalCampaigns', 'activeCampaigns', 'totalRaised', 'totalEvents', 'upcomingEvents'));
        }

        if ($user->hasRole('donor')) {
            $donor = $user->donor;

            if (! $donor) {
                return view('profile.edit', ['user' => $user]);
            }

            // Calculate statistics
            $totalDonations = $donor->donations()->count();
            $totalAmount = $donor->donations()->sum('Amount');
            $campaignsSupported = $donor->donations()->distinct('Campaign_ID')->count('Campaign_ID');
            $recentDonations = $donor->donations()->with('campaign')->latest()->take(5)->get();

            return view('profile.donor', compact('donor', 'totalDonations', 'totalAmount', 'campaignsSupported', 'recentDonations'));
        }

        if ($user->hasRole('public')) {
            $publicProfile = $user->publicProfile;

            if (! $publicProfile) {
                return view('profile.edit', ['user' => $user]);
            }

            // Check if user has applied as recipient
            $recipient = $publicProfile->recipient;
            $isRecipient = (bool) $recipient;
            $recipientStatus = $recipient ? $recipient->Status : null;

            return view('profile.public', compact('publicProfile', 'isRecipient', 'recipientStatus', 'recipient'));
        }

        // Admin profile
        if ($user->hasRole('admin')) {
            return view('profile.admin', ['user' => $user]);
        }

        // Default fallback for users without specific roles
        return view('profile.edit', ['user' => $user]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show organizer profile edit form
     */
    public function editOrganizer(Request $request)
    {
        $organization = $request->user()->organization;

        if (! $organization) {
            return redirect()->route('profile.edit')->with('error', 'Organization profile not found. (Database: Izzati)');
        }

        return view('profile.edit-organizer', compact('organization'));
    }

    /**
     * Update organizer profile
     */
    public function updateOrganizer(Request $request)
    {
        $organization = $request->user()->organization;

        if (! $organization) {
            return redirect()->route('profile.edit')->with('error', 'Organization profile not found. (Database: Izzati)');
        }

        $validated = $request->validate([
            'organization_name' => ['nullable', 'string', 'max:255'],
            'phone_num' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $organization->update([
            'Organization_Name' => $validated['organization_name'],
            'Phone_Num' => $validated['phone_num'],
            'Address' => $validated['address'],
            'City' => $validated['city'],
            'State' => $validated['state'],
            'Description' => $validated['description'],
        ]);

        return redirect()->route('profile.edit')->with('success', 'Organization profile updated successfully! (Database: Izzati)');
    }

    /**
     * Show donor profile edit form
     */
    public function editDonor(Request $request)
    {
        $donor = $request->user()->donor;

        if (! $donor) {
            return redirect()->route('profile.edit')->with('error', 'Donor profile not found. (Database: Hannah)');
        }

        return view('profile.edit-donor', compact('donor'));
    }

    /**
     * Update donor profile
     */
    public function updateDonor(Request $request)
    {
        $donor = $request->user()->donor;

        if (! $donor) {
            return redirect()->route('profile.edit')->with('error', 'Donor profile not found. (Database: Hannah)');
        }

        // Donor table might not have editable fields, but we keep this for future use
        // Currently, donor info comes from user table which is updated via profile.update

        return redirect()->route('profile.edit')->with('success', 'Donor profile updated successfully! (Database: Hannah)');
    }

    /**
     * Show public profile edit form
     */
    public function editPublic(Request $request)
    {
        $publicProfile = $request->user()->publicProfile;

        if (! $publicProfile) {
            return redirect()->route('profile.edit')->with('error', 'Public profile not found. (Database: Adam)');
        }

        return view('profile.edit-public', compact('publicProfile'));
    }

    /**
     * Update public profile
     */
    public function updatePublic(Request $request)
    {
        $publicProfile = $request->user()->publicProfile;

        if (! $publicProfile) {
            return redirect()->route('profile.edit')->with('error', 'Public profile not found. (Database: Adam)');
        }

        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone_num' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
        ]);

        $publicProfile->update([
            'Full_Name' => $validated['full_name'],
            'Phone_Num' => $validated['phone_num'],
            'Gender' => $validated['gender'],
            'Address' => $validated['address'],
            'City' => $validated['city'],
            'State' => $validated['state'],
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully! (Database: Adam)');
    }
}
