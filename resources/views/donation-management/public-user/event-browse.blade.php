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
            <p class="text-gray-600 mt-1">Discover upcoming charity and volunteer events</p>
        </div>

        <!-- Table View -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($events->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event Details
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Organizer
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Volunteers
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($events as $event)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- Event Details -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $event->Title }}</div>
                                        <div class="text-sm text-gray-500 line-clamp-1">{{ $event->Description ?? 'No description' }}</div>
                                    </td>

                                    <!-- Organizer -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-indigo-600 font-medium">{{ $event->organization->user->name }}</div>
                                    </td>

                                    <!-- Location -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="line-clamp-1">{{ $event->Location }}</span>
                                        </div>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $event->Start_Date->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $event->Start_Date->format('h:i A') }}</div>
                                    </td>

                                    <!-- Volunteers -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $currentVolunteers = $volunteerCounts[$event->Event_ID] ?? 0;
                                            $capacity = $event->Capacity ?? 0;
                                            $capacityPercent = $capacity > 0 ? ($currentVolunteers / $capacity) * 100 : 0;
                                        @endphp
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm font-medium text-gray-700">{{ $currentVolunteers }} / {{ $event->Capacity ?? '∞' }}</span>
                                            </div>
                                            @if($event->Capacity)
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full transition-all {{ $capacityPercent >= 100 ? 'bg-red-500' : 'bg-green-500' }}"
                                                         style="width: {{ min($capacityPercent, 100) }}%"></div>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    @if($capacityPercent >= 100)
                                                        Full
                                                    @else
                                                        {{ $event->Capacity - $currentVolunteers }} spots
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $event->Status === 'Ongoing' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $event->Status === 'Completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ $event->Status }}
                                        </span>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('public.events.show', $event->Event_ID) }}"
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No events available</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later for upcoming events.</p>
                </div>
            @endif
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
