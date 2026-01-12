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
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-12">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-4">
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-white text-blue-600">
                                    {{ $event->Status }}
                                </span>
                            @if($spotsLeft !== null && $spotsLeft <= 5 && $spotsLeft > 0)
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-yellow-400 text-yellow-900">
                                        Only {{ $spotsLeft }} spots left!
                                    </span>
                            @elseif($spotsLeft === 0)
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-red-500 text-white">
                                        Event Full
                                    </span>
                            @endif
                        </div>
                        <h1 class="text-4xl font-bold text-white mb-2">{{ $event->Title }}</h1>
                        <p class="text-blue-100">Organized by {{ $event->organization->user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Event Details -->
            <div class="p-8">
                <!-- Quick Info Grid -->
                <div class="grid md:grid-cols-4 gap-6 mb-8">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Start Date</p>
                        <p class="font-semibold text-gray-900">{{ $event->Start_Date->format('M d, Y') }}</p>
                    </div>

                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">End Date</p>
                        <p class="font-semibold text-gray-900">{{ $event->End_Date->format('M d, Y') }}</p>
                    </div>

                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Volunteers</p>
                        <p class="font-semibold text-gray-900">{{ $volunteerCount }} / {{ $event->Capacity ?? '∞' }}</p>
                    </div>

                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Duration</p>
                        <p class="font-semibold text-gray-900">{{ $event->Start_Date->diffInDays($event->End_Date) + 1 }} days</p>
                    </div>
                </div>

                <!-- Volunteer Capacity Progress -->
                @if($event->Capacity)
                    <div class="mb-8">
                        @php
                            $capacityPercent = ($volunteerCount / $event->Capacity) * 100;
                        @endphp
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Volunteer Registration Progress</span>
                            <span>{{ $volunteerCount }} / {{ $event->Capacity }} registered ({{ number_format($capacityPercent, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="h-4 rounded-full transition-all {{ $capacityPercent >= 100 ? 'bg-red-500' : 'bg-green-500' }}"
                                 style="width: {{ min($capacityPercent, 100) }}%"></div>
                        </div>
                    </div>
                @endif

                <!-- Location -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Location</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 text-gray-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-gray-700 leading-relaxed">{{ $event->Location }}</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">About This Event</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                            {{ $event->Description ?? 'No description provided.' }}
                        </p>
                    </div>
                </div>

                <!-- Event Timeline -->
                <div class="bg-blue-50 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-4">Event Schedule</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-24 text-sm text-gray-600">Start:</div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $event->Start_Date->format('l, F d, Y') }}</p>
                                <p class="text-sm text-gray-600">{{ $event->Start_Date->format('g:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-24 text-sm text-gray-600">End:</div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $event->End_Date->format('l, F d, Y') }}</p>
                                <p class="text-sm text-gray-600">{{ $event->End_Date->format('g:i A') }}</p>
                            </div>
                        </div>
                        @php
                            $daysUntil = (int) now()->diffInDays($event->Start_Date, false);
                        @endphp
                        @if($daysUntil >= 0)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-24 text-sm text-gray-600">Time Until:</div>
                                <div class="flex-1">
                                    <p class="font-semibold text-indigo-600">
                                        @if($daysUntil == 0)
                                            Today!
                                        @elseif($daysUntil == 1)
                                            Tomorrow
                                        @else
                                            {{ $daysUntil }} days
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Organizer Info -->
                <div class="bg-indigo-50 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-3">Event Organizer</h3>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-indigo-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-lg font-bold text-indigo-700">
                                    {{ strtoupper(substr($event->organization->user->name, 0, 1)) }}
                                </span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $event->organization->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $event->organization->City }}, {{ $event->organization->State }}</p>
                            @if($event->organization->Description)
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($event->organization->Description, 100) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg p-6 text-center">
                    <h3 class="text-2xl font-bold text-white mb-2">Interested in Volunteering?</h3>
                    <p class="text-blue-100 mb-4">Sign up as a volunteer to participate in this event!</p>
                    <a href="{{ route('register') }}" class="inline-block px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold">
                        Register as Volunteer
                    </a>
                </div>
            </div>
        </div>

        <!-- Registered Volunteers Preview -->
        @if($volunteerCount > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Registered Volunteers ({{ $volunteerCount }})</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($volunteers->take(6) as $volunteer)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-indigo-600 font-semibold">{{ strtoupper(substr($volunteer->user->name ?? 'V', 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $volunteer->user->name ?? 'Volunteer' }}</p>
                                <p class="text-xs text-gray-500">{{ $volunteer->City ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($volunteerCount > 6)
                    <p class="text-sm text-gray-500 mt-4 text-center">
                        And {{ $volunteerCount - 6 }} more volunteers...
                    </p>
                @endif
            </div>
        @endif
    </main>
</div>
</body>
</html>
