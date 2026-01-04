<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->Title }} - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="{ selectedRole: null }">
<div class="min-h-screen">
    @include('navbar')

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-12">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-4 flex-wrap gap-2">
                            <span class="px-4 py-2 text-sm font-semibold rounded-full bg-white text-indigo-600">
                                {{ $event->Status }}
                            </span>
                            @if($isRegistered)
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-500 text-white">
                                    ✓ You're Registered
                                </span>
                            @endif
                            @if($isFull)
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-red-500 text-white">
                                    Event Full
                                </span>
                            @endif
                        </div>
                        <h1 class="text-4xl font-bold text-white mb-2">{{ $event->Title }}</h1>
                        <p class="text-indigo-100">Organized by {{ $event->organization->user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Event Details -->
            <div class="p-8">
                <!-- Quick Info Grid -->
                <div class="grid md:grid-cols-4 gap-6 mb-8">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Start Date</p>
                        <p class="font-semibold text-gray-900">{{ $event->Start_Date->format('M d, Y') }}</p>
                    </div>

                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">End Date</p>
                        <p class="font-semibold text-gray-900">{{ $event->End_Date->format('M d, Y') }}</p>
                    </div>

                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Volunteers</p>
                        <p class="font-semibold text-gray-900">{{ $totalFilled }} / {{ $totalCapacity }}</p>
                    </div>

                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Location</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ Str::limit($event->Location, 20) }}</p>
                    </div>
                </div>

                <!-- Location -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Location</h2>
                    <div class="flex items-start">
                        <svg class="w-6 h-6 mr-3 text-gray-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p class="text-gray-700">{{ $event->Location }}</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">About This Event</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                        {{ $event->Description ?? 'No description provided.' }}
                    </p>
                </div>

                <!-- Registration Status -->
                @if($isRegistered)
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 ml-4">
                                <h3 class="font-bold text-blue-900 text-lg mb-2">You're Registered!</h3>

                                @if($assignedRole)
                                    <div class="bg-white rounded-lg p-4 mb-3">
                                        <p class="text-sm text-gray-600 mb-1">Your Assigned Role:</p>
                                        <p class="font-bold text-gray-900 text-lg">{{ $assignedRole->Role_Name }}</p>
                                        @if($assignedRole->Role_Description)
                                            <p class="text-sm text-gray-600 mt-2">{{ $assignedRole->Role_Description }}</p>
                                        @endif
                                    </div>
                                @endif

                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="px-3 py-1 rounded-full font-medium
                                        {{ $participation->Status === 'Attended' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $participation->Status === 'Registered' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $participation->Status === 'Cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        Status: {{ $participation->Status }}
                                    </span>
                                    @if($participation->Total_Hours > 0)
                                        <span class="text-gray-700 font-medium">Hours: {{ $participation->Total_Hours }}</span>
                                    @endif
                                </div>

                                @if($event->Status !== 'Completed' && $participation->Status !== 'Attended')
                                    <form action="{{ route('volunteer.events.cancel', $event->Event_ID) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to cancel your registration?');" class="mt-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium underline">
                                            Cancel Registration
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Volunteer Roles Overview -->
                @if($event->roles->count() > 0)
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Volunteer Roles</h2>

                        <!-- Overall Capacity -->
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-5 mb-6 border border-indigo-200">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-sm font-medium text-gray-700">Overall Event Capacity</span>
                                <span class="text-lg font-bold text-indigo-600">{{ $totalFilled }}/{{ $totalCapacity }}</span>
                            </div>
                            <x-role-progress-bar :filled="$totalFilled" :total="$totalCapacity" :showPercentage="false" />
                        </div>

                        @if(!$isRegistered && ($event->Status === 'Upcoming' || $event->Status === 'Ongoing'))
                            <!-- Role Selection Cards (for registration) -->
                            <form action="{{ route('volunteer.events.register', $event->Event_ID) }}" method="POST">
                                @csrf
                                <input type="hidden" name="role_id" x-model="selectedRole">

                                <div class="mb-6">
                                    <label class="block text-lg font-semibold text-gray-900 mb-4">Choose Your Role *</label>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        @foreach($event->roles as $role)
                                            @php
                                                $isRoleFull = $role->isFull();
                                            @endphp
                                            <div @click="!{{ $isRoleFull ? 'true' : 'false' }} && (selectedRole = {{ $role->Role_ID }})"
                                                 class="cursor-pointer">
                                                <x-role-card
                                                    :role="$role"
                                                    variant="selectable"
                                                    :disabled="$isRoleFull"
                                                    :selected="false"
                                                    x-bind:class="{ 'ring-2 ring-indigo-500': selectedRole === {{ $role->Role_ID }} }" />
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('role_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex space-x-4">
                                    <button type="submit"
                                            x-bind:disabled="!selectedRole || {{ $isFull ? 'true' : 'false' }}"
                                            x-bind:class="selectedRole && !{{ $isFull ? 'true' : 'false' }} ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                                            class="flex-1 px-6 py-3 text-white rounded-lg transition-colors font-medium">
                                        <span x-show="!selectedRole">Select a Role to Register</span>
                                        <span x-show="selectedRole">Register for This Event</span>
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- Role Display Cards (view only) -->
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach($event->roles as $role)
                                    <x-role-card :role="$role" variant="view" />
                                @endforeach
                            </div>

                            @if(!$isRegistered)
                                <div class="mt-6 text-center py-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                    <p class="text-gray-600 font-medium">Registration is currently closed for this event.</p>
                                    <a href="{{ route('volunteer.events.browse') }}"
                                       class="inline-block mt-4 px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-white transition-colors">
                                        Browse Other Events
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                @else
                    <!-- No roles defined -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                        <svg class="w-12 h-12 mx-auto text-yellow-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-gray-700 mb-4">This event doesn't have specific volunteer roles defined yet.</p>
                        <a href="{{ route('volunteer.events.browse') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Browse Other Events
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
</body>
</html>
