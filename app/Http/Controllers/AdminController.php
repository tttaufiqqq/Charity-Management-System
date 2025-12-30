<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function manageUsers(Request $request)
    {
        $query = User::with('roles');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Get users with pagination
        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate role statistics
        $roleStats = [
            'admin' => User::role('admin')->count(),
            'organizer' => User::role('organizer')->count(),
            'donor' => User::role('donor')->count(),
            'volunteer' => User::role('volunteer')->count(),
            'public' => User::role('public')->count(),
        ];

        return view('admin.manage-users', compact('users', 'roleStats'));
    }

    public function viewUser(User $user)
    {
        $user->load(['roles', 'donor', 'organization', 'volunteer', 'publicProfile']);

        return view('admin.view-user', compact('user'));
    }

    public function editUser(User $user)
    {
        $user->load('roles');
        $roles = Role::all();

        return view('admin.edit-user', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Update user details
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Sync role
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.manage.users')
            ->with('success', 'User updated successfully');
    }
}
