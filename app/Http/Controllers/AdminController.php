<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
