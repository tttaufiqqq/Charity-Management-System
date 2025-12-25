<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->Title }} - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="{ showVolunteerModal: false, selectedVolunteer: null }">
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
                                        <div @click="selectedVolunteer = {{ json_encode([
                                                'id' => $volunteer->Volunteer_ID,
                                                'name' => $volunteer->user->name,
                                                'email' => $volunteer->user->email,
                                                'phone' => $volunteer->Phone_Num,
                                                'gender' => $volunteer->Gender,
                                                'availability' => $volunteer->Availability,
                                                'address' => $volunteer->Address,
                                                'city' => $volunteer->City,
                                                'state' => $volunteer->State,
                                                'description' => $volunteer->Description,
                                                'status' => $volunteer->pivot->Status,
                                                'hours' => $volunteer->pivot->Total_Hours,
                                                'skills' => $volunteer->skills->map(fn($skill) => [
                                                    'name' => $skill->Skill_Name,
                                                    'level' => $skill->pivot->Skill_Level
                                                ])
                                            ]) }}; showVolunteerModal = true"
                                             class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors cursor-pointer">
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
                                            <div class="text-indigo-600 hover:text-indigo-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
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
                                    <div @click="selectedVolunteer = {{ json_encode([
                                            'id' => $volunteer->Volunteer_ID,
                                            'name' => $volunteer->user->name,
                                            'email' => $volunteer->user->email,
                                            'phone' => $volunteer->Phone_Num,
                                            'gender' => $volunteer->Gender,
                                            'availability' => $volunteer->Availability,
                                            'address' => $volunteer->Address,
                                            'city' => $volunteer->City,
                                            'state' => $volunteer->State,
                                            'description' => $volunteer->Description,
                                            'status' => $volunteer->pivot->Status,
                                            'hours' => $volunteer->pivot->Total_Hours,
                                            'skills' => $volunteer->skills->map(fn($skill) => [
                                                'name' => $skill->Skill_Name,
                                                'level' => $skill->pivot->Skill_Level
                                            ])
                                        ]) }}; showVolunteerModal = true"
                                         class="flex justify-between items-center py-3 px-4 bg-yellow-50 rounded-lg border border-yellow-200 hover:border-yellow-400 hover:bg-yellow-100 transition-colors cursor-pointer">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $volunteer->user->name }}</p>
                                            <p class="text-sm text-gray-600">Status: {{ $volunteer->pivot->Status }}</p>
                                        </div>
                                        <div class="text-yellow-600 hover:text-yellow-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
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

    <!-- Volunteer Profile Modal -->
    <div x-show="showVolunteerModal"
         x-cloak
         @click.away="showVolunteerModal = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <!-- Modal -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
                 @click.stop>
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white" x-text="selectedVolunteer?.name"></h3>
                        <button @click="showVolunteerModal = false" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-6">
                    <template x-if="selectedVolunteer">
                        <div class="space-y-6">
                            <!-- Event Status -->
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Event Status</p>
                                    <span class="text-sm px-3 py-1 rounded-full font-medium inline-block"
                                          :class="{
                                              'bg-green-100 text-green-800': selectedVolunteer.status === 'Attended',
                                              'bg-blue-100 text-blue-800': selectedVolunteer.status === 'Registered',
                                              'bg-red-100 text-red-800': selectedVolunteer.status === 'No-Show',
                                              'bg-gray-100 text-gray-800': selectedVolunteer.status === 'Cancelled'
                                          }"
                                          x-text="selectedVolunteer.status"></span>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600 mb-1">Hours Logged</p>
                                    <p class="text-2xl font-bold text-indigo-600" x-text="selectedVolunteer.hours"></p>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Contact Information
                                </h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1">Email</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedVolunteer.email || 'N/A'"></p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1">Phone</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedVolunteer.phone || 'N/A'"></p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1">Gender</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedVolunteer.gender || 'N/A'"></p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1">Availability</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedVolunteer.availability || 'N/A'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Address
                                </h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-900" x-text="selectedVolunteer.address || 'N/A'"></p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span x-text="selectedVolunteer.city || 'N/A'"></span>,
                                        <span x-text="selectedVolunteer.state || 'N/A'"></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Skills -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    Skills
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    <template x-if="selectedVolunteer.skills && selectedVolunteer.skills.length > 0">
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="skill in selectedVolunteer.skills" :key="skill.name">
                                                <span class="px-3 py-2 bg-indigo-100 text-indigo-800 rounded-lg text-sm font-medium">
                                                    <span x-text="skill.name"></span>
                                                    <span class="text-indigo-600 ml-1">•</span>
                                                    <span class="text-xs" x-text="skill.level"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!selectedVolunteer.skills || selectedVolunteer.skills.length === 0">
                                        <p class="text-sm text-gray-500 italic">No skills listed</p>
                                    </template>
                                </div>
                            </div>

                            <!-- Description -->
                            <template x-if="selectedVolunteer.description">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                        </svg>
                                        About
                                    </h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap" x-text="selectedVolunteer.description"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button @click="showVolunteerModal = false"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
