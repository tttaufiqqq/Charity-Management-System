<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Volunteer Dashboard - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header with Volunteer Tier Badge -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ $volunteer->user->name }}!</h1>
                    <p class="text-gray-600 mt-1">Here's your volunteer activity overview</p>
                </div>
                <!-- Volunteer Tier Badge -->
                <div class="flex items-center gap-3">
                    @php
                        $tierColors = [
                            'Legend' => 'from-yellow-400 to-amber-500 text-white',
                            'Champion' => 'from-purple-500 to-indigo-600 text-white',
                            'Dedicated' => 'from-blue-500 to-cyan-500 text-white',
                            'Active' => 'from-green-500 to-emerald-500 text-white',
                            'Regular' => 'from-gray-400 to-gray-500 text-white',
                            'New' => 'from-gray-200 to-gray-300 text-gray-700',
                        ];
                        $tierIcons = [
                            'Legend' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
                            'Champion' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                            'Dedicated' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                            'Active' => 'M13 10V3L4 14h7v7l9-11h-7z',
                            'Regular' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                            'New' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                        ];
                        $currentTier = $volunteerTier ?? 'New';
                    @endphp
                    <div class="bg-gradient-to-r {{ $tierColors[$currentTier] ?? $tierColors['New'] }} px-4 py-2 rounded-full shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tierIcons[$currentTier] ?? $tierIcons['New'] }}"></path>
                        </svg>
                        <span class="font-bold text-sm">{{ $currentTier }} Volunteer</span>
                    </div>
                </div>
            </div>
            <!-- Tier Progress Info -->
            <div class="mt-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-100">
                <div class="flex items-center gap-2 text-sm text-indigo-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Volunteer Tiers:</strong> New (0-9 hrs) → Regular (10-49 hrs) → Active (50-99 hrs) → Dedicated (100-199 hrs) → Champion (200-499 hrs) → Legend (500+ hrs)</span>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <!-- Total Hours -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Volunteer Hours</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ number_format($totalHours ?? 0, 1) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Hours contributed</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Events -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Events</p>
                        <p class="text-3xl font-bold text-green-600">{{ $totalEvents }}</p>
                        <p class="text-xs text-gray-500 mt-1">Events participated</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed Events -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Completed Events</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $completedEvents }}</p>
                        <p class="text-xs text-gray-500 mt-1">Events finished</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Attendance Rate -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Attendance Rate</p>
                        <p class="text-3xl font-bold {{ ($attendanceRate ?? 0) >= 80 ? 'text-green-600' : (($attendanceRate ?? 0) >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ number_format($attendanceRate ?? 0, 1) }}%</p>
                        <p class="text-xs text-gray-500 mt-1">
                            @if(($attendanceRate ?? 0) >= 80)
                                Excellent!
                            @elseif(($attendanceRate ?? 0) >= 50)
                                Good progress
                            @else
                                Keep attending!
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 {{ ($attendanceRate ?? 0) >= 80 ? 'bg-green-100' : (($attendanceRate ?? 0) >= 50 ? 'bg-yellow-100' : 'bg-red-100') }} rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 {{ ($attendanceRate ?? 0) >= 80 ? 'text-green-600' : (($attendanceRate ?? 0) >= 50 ? 'text-yellow-600' : 'text-red-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->


        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Upcoming Events -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Upcoming Events</h2>
                    <a href="{{ route('volunteer.events.browse') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        View All →
                    </a>
                </div>

                @if($upcomingEvents->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingEvents as $event)
                            <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                               class="block p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-900">{{ $event->Title }}</h3>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $event->Status }}
                                    </span>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $event->Start_Date->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        {{ Str::limit($event->Location, 30) }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-4 text-gray-500">No upcoming events</p>
                        <a href="{{ route('volunteer.events.browse') }}"
                           class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            Browse Events →
                        </a>
                    </div>
                @endif
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
                    <a href="{{ route('volunteer.schedule') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        View Schedule →
                    </a>
                </div>

                @if($recentEvents->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentEvents as $event)
                            <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                               class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-900 text-sm">{{ $event->Title }}</h3>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $event->Status === 'Ongoing' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $event->Status === 'Completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $event->Status }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-xs text-gray-600">
                                    <span>{{ $event->Start_Date->format('M d, Y') }}</span>
                                    @if($event->pivot && $event->pivot->Total_Hours)
                                        <span class="font-medium text-indigo-600">{{ $event->pivot->Total_Hours }} hours</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="mt-4 text-gray-500">No recent activity</p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} CharityHub. Making a difference together.</p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
