<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->Title }} - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">CharityHub</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('volunteer.events.browse') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Back to Events</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
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
                        <div class="flex items-center space-x-3 mb-4">
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
                        <p class="font-semibold text-gray-900">{{ $event->volunteers->count() }} / {{ $event->Capacity ?? '∞' }}</p>
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
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <h3 class="font-semibold text-blue-900 mb-1">You're Registered!</h3>
                                <p class="text-blue-700 text-sm mb-3">
                                    Status: <span class="font-medium">{{ $participation->Status }}</span>
                                    @if($participation->Total_Hours > 0)
                                        | Hours Logged: <span class="font-medium">{{ $participation->Total_Hours }}</span>
                                    @endif
                                </p>
                                @if($event->Status !== 'Completed' && $participation->Status !== 'Attended')
                                    <form action="{{ route('volunteer.events.cancel', $event->Event_ID) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to cancel your registration?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                            Cancel Registration
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    @if(!$isRegistered)
                        @if($isFull)
                            <button disabled class="flex-1 px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed font-medium">
                                Event is Full
                            </button>
                        @elseif($event->Status === 'Upcoming' || $event->Status === 'Ongoing')
                            <form action="{{ route('volunteer.events.register', $event->Event_ID) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                    Register for This Event
                                </button>
                            </form>
                        @else
                            <button disabled class="flex-1 px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed font-medium">
                                Registration Closed
                            </button>
                        @endif
                    @endif

                    <a href="{{ route('volunteer.events.browse') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        Back to Events
                    </a>
                </div>
            </div>
        </div>

        <!-- Other Volunteers -->
        @if($event->volunteers->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Registered Volunteers ({{ $event->volunteers->count() }})</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($event->volunteers->take(6) as $volunteer)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-indigo-600 font-semibold">{{ strtoupper(substr($volunteer->user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $volunteer->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $volunteer->pivot->status }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($event->volunteers->count() > 6)
                    <p class="text-sm text-gray-500 mt-4 text-center">
                        And {{ $event->volunteers->count() - 6 }} more volunteers...
                    </p>
                @endif
            </div>
        @endif
    </main>
</div>
</body>
</html>
