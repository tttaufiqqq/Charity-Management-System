<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - CharityHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <p class="text-gray-600 mt-1">Manage all platform users and their roles</p>
        </div>

        <!-- Filters and Stats -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Public Users - First/Top -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Public Users</div>
                <div class="text-2xl font-bold text-teal-600">{{ $roleStats['public'] ?? 0 }}</div>
            </div>

            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Total Users</div>
                <div class="text-2xl font-bold text-indigo-600">{{ $users->total() }}</div>
            </div>

            <!-- Role Stats -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Admins</div>
                <div class="text-2xl font-bold text-purple-600">{{ $roleStats['admin'] ?? 0 }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Organizers</div>
                <div class="text-2xl font-bold text-blue-600">{{ $roleStats['organizer'] ?? 0 }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Donors</div>
                <div class="text-2xl font-bold text-green-600">{{ $roleStats['donor'] ?? 0 }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Volunteers</div>
                <div class="text-2xl font-bold text-orange-600">{{ $roleStats['volunteer'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4">
                <form method="GET" action="{{ route('admin.manage.users') }}" class="flex flex-wrap gap-4">
                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search"
                               value="{{ request('search') }}"
                               placeholder="Name or email..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Role Filter -->
                    <div class="w-full sm:w-48">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" id="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="organizer" {{ request('role') === 'organizer' ? 'selected' : '' }}>Organizer</option>
                            <option value="donor" {{ request('role') === 'donor' ? 'selected' : '' }}>Donor</option>
                            <option value="volunteer" {{ request('role') === 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                            <option value="public" {{ request('role') === 'public' ? 'selected' : '' }}>Public</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('admin.manage.users') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Joined
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <span class="text-indigo-600 font-semibold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->hasRole('admin'))
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Admin</span>
                                    @elseif($user->hasRole('organizer'))
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Organizer</span>
                                    @elseif($user->hasRole('donor'))
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Donor</span>
                                    @elseif($user->hasRole('volunteer'))
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Volunteer</span>
                                    @elseif($user->hasRole('public'))
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Public</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No Role</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $user->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.users.view', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <p class="text-lg font-medium">No users found</p>
                                        <p class="text-sm">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
