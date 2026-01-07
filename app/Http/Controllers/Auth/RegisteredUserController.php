<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Models\Organization;
use App\Models\PublicProfile;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle role selection and redirect to details form
     */
    public function selectRole(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:donor,public,organizer,volunteer'],
        ]);

        // Store temporary data in session
        session([
            'registration_email' => $request->email,
            'registration_password' => $request->password,
            'registration_role' => $request->role,
        ]);

        return redirect()->route('register.role-details');
    }

    /**
     * Display role-specific details form
     */
    public function showRoleDetails(Request $request): View
    {
        // Check if session data exists
        if (! session()->has('registration_role')) {
            return redirect()->route('register');
        }

        $role = session('registration_role');

        return view('auth.role-details', ['role' => $role]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Get data from session
        $email = session('registration_email');
        $password = session('registration_password');
        $role = session('registration_role');

        // Safety check
        if (! $email || ! $password || ! $role) {
            return back()->withErrors(['error' => 'Session expired. Please start registration again.'])->withInput();
        }

        // Validate role-specific data
        $roleData = $this->validateRoleData($request, $role);

        DB::beginTransaction();

        try {
            // Extract name based on role
            $name = $this->extractNameFromRoleData($role, $roleData);

            // Create user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            // Assign role using Spatie
            $user->assignRole($role);

            // Create role-specific profile
            $this->createRoleProfile($user, $role, $roleData);

            event(new Registered($user));

            Auth::login($user);

            // Clear session data
            session()->forget(['registration_email', 'registration_password', 'registration_role']);

            DB::commit();

            return redirect(route('welcome', absolute: false));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Extract user name from role-specific data
     */
    private function extractNameFromRoleData(string $role, array $data): string
    {
        return match ($role) {
            'donor' => $data['full_name'],
            'public' => $data['full_name'],
            'organizer' => $data['organization_name'] ?? 'Organization User',
            'volunteer' => $data['full_name'] ?? 'Volunteer User',
        };
    }

    /**
     * Validate role-specific data
     */
    private function validateRoleData(Request $request, string $role): array
    {
        $rules = match ($role) {
            'donor' => [
                'full_name' => ['required', 'string', 'max:255'],
                'phone_num' => ['required', 'string', 'max:20'],
            ],
            'public' => [
                'full_name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
                'email' => ['required', 'email', 'max:255'],
                'position' => ['nullable', 'string', 'max:255'],
            ],
            'organizer' => [
                'organization_name' => ['required', 'string', 'max:255'],
                'phone_no' => ['required', 'string', 'max:20'],
                'register_no' => ['required', 'string', 'max:50'],
                'address' => ['required', 'string'],
                'state' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'description' => ['nullable', 'string'],
            ],
            'volunteer' => [
                'full_name' => ['required', 'string', 'max:255'],
                'availability' => ['required', 'string'],
                'address' => ['required', 'string'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['required', 'string', 'max:100'],
                'gender' => ['required', 'string', 'in:Male,Female,Other'],
                'phone_num' => ['required', 'string', 'max:20'],
                'description' => ['nullable', 'string'],
            ],
        };

        return $request->validate($rules);
    }

    /**
     * Create role-specific profile
     */
    private function createRoleProfile(User $user, string $role, array $data): void
    {
        match ($role) {
            'donor' => Donor::create([
                'User_ID' => $user->id,
                'Full_Name' => $data['full_name'],
                'Phone_Num' => $data['phone_num'],
                'Total_Donated' => 0,
            ]),
            'public' => PublicProfile::create([
                'User_ID' => $user->id,
                'Full_Name' => $data['full_name'],
                'Phone' => $data['phone'],
                'Email' => $data['email'],
                'Position' => $data['position'] ?? null,
            ]),
            'organizer' => Organization::create([
                'Organizer_ID' => $user->id,
                'Phone_No' => $data['phone_no'],
                'Register_No' => $data['register_no'],
                'Address' => $data['address'],
                'State' => $data['state'],
                'City' => $data['city'],
                'Description' => $data['description'] ?? null,
            ]),
            'volunteer' => Volunteer::create([
                'User_ID' => $user->id,
                'Availability' => $data['availability'],
                'Address' => $data['address'],
                'City' => $data['city'],
                'State' => $data['state'],
                'Gender' => $data['gender'],
                'Phone_Num' => $data['phone_num'],
                'Description' => $data['description'] ?? null,
            ]),
        };
    }
}
