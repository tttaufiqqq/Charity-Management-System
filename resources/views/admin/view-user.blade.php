<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - CharityHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- User Detail Card -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 font-bold text-2xl">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-1">{{ $user->name }}</h1>
                            <p class="text-indigo-100">{{ $user->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-white text-indigo-600 rounded-md hover:bg-indigo-50 transition-colors">
                        Edit User
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-8">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <!-- User Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">User Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Name</label>
                                <p class="text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Email</label>
                                <p class="text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Role</label>
                                <div class="mt-1">
                                    @if($user->hasRole('admin'))
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">Admin</span>
                                    @elseif($user->hasRole('organizer'))
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Organizer</span>
                                    @elseif($user->hasRole('donor'))
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Donor</span>
                                    @elseif($user->hasRole('volunteer'))
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-orange-100 text-orange-800">Volunteer</span>
                                    @elseif($user->hasRole('public'))
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">Public</span>
                                    @else
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">No Role</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Account Details</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Joined</label>
                                <p class="text-gray-900">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                                <p class="text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Last Updated</label>
                                <p class="text-gray-900">{{ $user->updated_at->format('M d, Y h:i A') }}</p>
                                <p class="text-sm text-gray-500">{{ $user->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role-Specific Information -->
                @if($user->hasRole('organizer') && $user->organization)
                    <div class="border-t pt-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Organization Information</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Organization Name</label>
                                <p class="text-gray-900">{{ $user->organization->Name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Contact Number</label>
                                <p class="text-gray-900">{{ $user->organization->Contact_Number ?? 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Address</label>
                                <p class="text-gray-900">{{ $user->organization->Address ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->hasRole('volunteer') && $user->volunteer)
                    <div class="border-t pt-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Volunteer Information</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Skills</label>
                                @if($user->volunteer->skills && $user->volunteer->skills->count() > 0)
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($user->volunteer->skills as $skill)
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded">
                                                {{ $skill->Skill_Name }} ({{ $skill->pivot->Skill_Level }})
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-900">No skills added</p>
                                @endif
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Availability</label>
                                <p class="text-gray-900">{{ $user->volunteer->Availability ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->hasRole('public') && $user->publicProfile)
                    <div class="border-t pt-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Public Profile Information</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Contact Number</label>
                                <p class="text-gray-900">{{ $user->publicProfile->Contact_Number ?? 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Address</label>
                                <p class="text-gray-900">{{ $user->publicProfile->Address ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
</body>
</html>
