<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->Title }} - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->Title }}</h1>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        {{ $event->Status === 'Upcoming' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $event->Status === 'Ongoing' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $event->Status === 'Completed' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $event->Status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ $event->Status }}
                    </span>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('events.manage-volunteers', $event->Event_ID) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Manage Volunteers
                    </a>
                    <a href="{{ route('events.edit', $event->Event_ID) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Edit
                    </a>
                    <form action="{{ route('events.destroy', $event->Event_ID) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <!-- Event Details -->
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Location</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $event->Location }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Capacity</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $event->Capacity ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Start Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $event->Start_Date->format('F d, Y') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">End Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $event->End_Date->format('F d, Y') }}</p>
                </div>
            </div>

            <!-- Description -->
            @if($event->Description)
                <div class="mt-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Description</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $event->Description }}</p>
                </div>
            @endif
        </div>

        <!-- Overall Capacity Banner -->
        @if($event->roles->count() > 0)
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-6 mb-6 border border-indigo-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Volunteer Capacity Overview</h2>
                        <p class="text-sm text-gray-600 mt-1">Total registered volunteers across all roles</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-indigo-600">{{ $totalFilled }}/{{ $totalCapacity }}</div>
                        <div class="text-sm text-gray-600">volunteers</div>
                    </div>
                </div>

                <!-- Overall Progress Bar -->
                <div class="mb-2">
                    <x-role-progress-bar :filled="$totalFilled" :total="$totalCapacity" />
                </div>
            </div>

            <!-- Roles Grid -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Volunteer Roles</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($event->roles as $role)
                        <x-role-card :role="$role" variant="view" />
                    @endforeach
                </div>
            </div>

            <!-- Volunteers by Role -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Registered Volunteers</h2>

                @if($volunteersByRole->count() > 0)
                    @foreach($event->roles as $role)
                        @php
                            $roleVolunteers = $volunteersByRole->get($role->Role_ID, collect());
                        @endphp

                        <div class="mb-8 last:mb-0">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $role->Role_Name }}</h3>
                                </div>
                                <x-role-capacity-badge :filled="$role->Volunteers_Filled" :total="$role->Volunteers_Needed" />
                            </div>

                            @if($roleVolunteers->count() > 0)
                                <div class="space-y-3 pl-8">
                                    @foreach($roleVolunteers as $volunteer)
                                        <div class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 transition-colors">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $volunteer->user->name }}</p>
                                                <div class="flex items-center space-x-4 mt-1">
                                                    <span class="text-sm px-2 py-1 rounded-full
                                                        {{ $volunteer->pivot->Status === 'Attended' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $volunteer->pivot->Status === 'Registered' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $volunteer->pivot->Status === 'No-Show' ? 'bg-red-100 text-red-800' : '' }}
                                                        {{ $volunteer->pivot->Status === 'Cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                        {{ $volunteer->pivot->Status }}
                                                    </span>
                                                    <span class="text-sm text-gray-600">{{ $volunteer->pivot->Total_Hours }} hours</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm pl-8">No volunteers assigned to this role yet</p>
                            @endif
                        </div>
                    @endforeach

                    <!-- Unassigned Volunteers (if any) -->
                    @php
                        $unassignedVolunteers = $volunteersByRole->get(null, collect());
                    @endphp
                    @if($unassignedVolunteers->count() > 0)
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-700">Unassigned Volunteers</h3>
                            </div>
                            <div class="space-y-3 pl-8">
                                @foreach($unassignedVolunteers as $volunteer)
                                    <div class="flex justify-between items-center py-3 px-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $volunteer->user->name }}</p>
                                            <p class="text-sm text-gray-600">Status: {{ $volunteer->pivot->Status }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-gray-500 text-center py-8">No volunteers registered yet</p>
                @endif
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 mx-auto text-yellow-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Volunteer Roles Defined</h3>
                <p class="text-gray-600 mb-4">This event doesn't have any volunteer roles defined yet. Edit the event to add roles and organize volunteers.</p>
                <a href="{{ route('events.edit', $event->Event_ID) }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Edit Event
                </a>
            </div>
        @endif
    </main>
</div>
</body>
</html>
