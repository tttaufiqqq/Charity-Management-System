<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Events - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Browse Events</h1>
            <p class="text-gray-600 mt-1">Find volunteer opportunities that match your interests</p>
        </div>

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

        <!-- Events Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Status Badge -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <div class="flex justify-between items-start">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-white text-indigo-600">
                                    {{ $event->Status }}
                                </span>
                            @if(in_array($event->Event_ID, $registeredEventIds))
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-500 text-white">
                                        ✓ Registered
                                    </span>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-white mt-3">{{ $event->Title }}</h3>
                    </div>

                    <div class="p-6">
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ $event->Description ?? 'No description available' }}
                        </p>

                        <!-- Event Details -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-start text-sm text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $event->Location }}</span>
                            </div>

                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $event->Start_Date->format('M d, Y') }}</span>
                            </div>

                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                @php
                                    $currentVolunteers = $event->getTotalVolunteersFilled();
                                    $capacity = $event->getTotalVolunteerCapacity();
                                @endphp
                                <span>{{ $currentVolunteers }} / {{ $capacity }} volunteers</span>
                            </div>

                            <!-- Capacity Progress -->
                            @if($capacity > 0)
                                @php
                                    $capacityPercent = ($currentVolunteers / $capacity) * 100;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all {{ $capacityPercent >= 100 ? 'bg-red-500' : 'bg-green-500' }}"
                                         style="width: {{ min($capacityPercent, 100) }}%"></div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                           class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No events available</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later for new volunteer opportunities.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
            <div class="mt-8">
                {{ $events->links() }}
            </div>
        @endif
    </main>
</div>
</body>
</html>
