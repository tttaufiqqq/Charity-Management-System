<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Events - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <p class="text-gray-600 mt-1">Discover upcoming charity and volunteer events</p>
        </div>

        <!-- Events Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Event Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                        <div class="flex justify-between items-start">
                            <h3 class="text-xl font-bold text-white">{{ $event->Title }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-white text-blue-600">
                                    {{ $event->Status }}
                                </span>
                        </div>
                        <p class="text-blue-100 text-sm mt-1">{{ $event->organization->user->name }}</p>
                    </div>

                    <div class="p-6">
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ $event->Description ?? 'No description available' }}
                        </p>

                        <!-- Event Details -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $event->Start_Date->format('M d, Y') }}</span>
                            </div>

                            <div class="flex items-start text-sm text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="line-clamp-2">{{ $event->Location }}</span>
                            </div>

                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                @php
                                    $currentVolunteers = $event->volunteers->count();
                                    $capacity = $event->Capacity ?? '∞';
                                @endphp
                                <span>{{ $currentVolunteers }} / {{ $capacity }} volunteers</span>
                            </div>

                            @if($event->Capacity)
                                @php
                                    $capacityPercent = ($currentVolunteers / $event->Capacity) * 100;
                                @endphp
                                <div class="pt-2">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all {{ $capacityPercent >= 100 ? 'bg-red-500' : 'bg-green-500' }}"
                                             style="width: {{ min($capacityPercent, 100) }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        @if($capacityPercent >= 100)
                                            Event is full
                                        @else
                                            {{ $event->Capacity - $currentVolunteers }} spots left
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('public.events.show', $event->Event_ID) }}"
                           class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
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
                    <p class="mt-1 text-sm text-gray-500">Check back later for upcoming events.</p>
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
