<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600">CharityHub</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('volunteer.events.browse') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Browse Events</a>
                    <a href="{{ route('volunteer.schedule') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Schedule</a>
                    <a href="{{ route('volunteer.profile') }}" class="text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-8 mb-6">
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center">
                            <span class="text-4xl font-bold text-indigo-600">
                                {{ strtoupper(substr($volunteer->user->name, 0, 1)) }}
                            </span>
                    </div>
                    <div class="text-white">
                        <h1 class="text-3xl font-bold mb-2">{{ $volunteer->user->name }}</h1>
                        <p class="text-indigo-100 mb-1">{{ $volunteer->user->email }}</p>
                        <p class="text-indigo-100">{{ $volunteer->City }}, {{ $volunteer->State }}</p>
                    </div>
                </div>
                <a href="{{ route('volunteer.profile.edit') }}"
                   class="px-6 py-2 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors font-medium">
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Hours</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalHours }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Events</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalEvents }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $completedEvents }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Upcoming</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $upcomingEvents }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Personal Information</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gender</dt>
                        <dd class="text-sm text-gray-900">{{ $volunteer->Gender }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                        <dd class="text-sm text-gray-900">{{ $volunteer->Phone_Num }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="text-sm text-gray-900">{{ $volunteer->Address }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">City / State</dt>
                        <dd class="text-sm text-gray-900">{{ $volunteer->City }}, {{ $volunteer->State }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Availability</dt>
                        <dd class="text-sm text-gray-900">{{ $volunteer->Availability }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">About Me</h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                    {{ $volunteer->Description ?? 'No description provided.' }}
                </p>
            </div>
        </div>

        <!-- Skills -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Skills</h2>
                <a href="{{ route('volunteer.skills.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    + Add Skill
                </a>
            </div>


        @if($skills->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($skills as $skill)
                        <span class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                                {{ $skill->Skill_Name }}
                            @if($skill->pivot->Skill_Level)
                                <span class="text-indigo-500 ml-1">({{ $skill->pivot->Skill_Level }})</span>
                            @endif
                            </span>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No skills added yet</p>
            @endif
        </div>
    </main>
</div>
</body>
</html>
