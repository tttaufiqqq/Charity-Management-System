<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule - CharityHub</title>
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
                    <a href="{{ route('volunteer.schedule') }}" class="text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">My Schedule</a>
                    <a href="{{ route('volunteer.events.my-events') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">My Events</a>
                    <a href="{{ route('volunteer.profile') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Schedule</h1>
            <p class="text-gray-600 mt-1">View your upcoming volunteer events</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Calendar -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                <!-- Month Navigation -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
                    </h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('volunteer.schedule', ['month' => $currentMonth == 1 ? 12 : $currentMonth - 1, 'year' => $currentMonth == 1 ? $currentYear - 1 : $currentYear]) }}"
                           class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                            ← Prev
                        </a>
                        <a href="{{ route('volunteer.schedule') }}"
                           class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition-colors">
                            Today
                        </a>
                        <a href="{{ route('volunteer.schedule', ['month' => $currentMonth == 12 ? 1 : $currentMonth + 1, 'year' => $currentMonth == 12 ? $currentYear + 1 : $currentYear]) }}"
                           class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                            Next →
                        </a>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <!-- Day Headers -->
                    <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="py-2 text-center text-sm font-semibold text-gray-700">{{ $day }}</div>
                        @endforeach
                    </div>

                    <!-- Calendar Days -->
                    @foreach($calendar as $week)
                        <div class="grid grid-cols-7 border-b border-gray-200 last:border-b-0">
                            @foreach($week as $day)
                                <div class="min-h-24 p-2 border-r border-gray-200 last:border-r-0
                                        {{ !$day['isCurrentMonth'] ? 'bg-gray-50' : '' }}
                                        {{ $day['isToday'] ? 'bg-blue-50' : '' }}">
                                    <div class="text-sm font-medium mb-1
                                            {{ !$day['isCurrentMonth'] ? 'text-gray-400' : 'text-gray-900' }}
                                            {{ $day['isToday'] ? 'text-blue-600 font-bold' : '' }}">
                                        {{ $day['date']->format('j') }}
                                    </div>

                                    @foreach($day['events'] as $event)
                                        <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                                           class="block text-xs bg-indigo-100 text-indigo-700 px-1 py-0.5 rounded mb-1 hover:bg-indigo-200 transition-colors truncate">
                                            {{ $event->Title }}
                                        </a>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Upcoming Events Sidebar -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Events</h3>

                @if($upcomingEvents->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingEvents as $event)
                            <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                               class="block p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-900 text-sm">{{ $event->Title }}</h4>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $event->Status === 'Ongoing' ? 'bg-green-100 text-green-800' : '' }}">
                                            {{ $event->Status }}
                                        </span>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex items-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $event->Start_Date->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ Str::limit($event->Location, 25) }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        @php
                                            $daysUntil = now()->diffInDays($event->Start_Date, false);
                                        @endphp
                                        @if($daysUntil == 0)
                                            Today
                                        @elseif($daysUntil == 1)
                                            Tomorrow
                                        @elseif($daysUntil > 0)
                                            In {{ $daysUntil }} days
                                        @else
                                            Ongoing
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No upcoming events</p>
                        <a href="{{ route('volunteer.events.browse') }}"
                           class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-800">
                            Browse Events →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Event List for Current Month -->
        @if($events->count() > 0)
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Events in {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
                </h3>
                <div class="space-y-3">
                    @foreach($events as $event)
                        <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                           class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-indigo-600">{{ $event->Start_Date->format('d') }}</div>
                                    <div class="text-xs text-gray-500">{{ $event->Start_Date->format('M') }}</div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $event->Title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $event->Location }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $event->Status === 'Ongoing' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $event->Status === 'Completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $event->Status }}
                                    </span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </main>
</div>
</body>
</html>
