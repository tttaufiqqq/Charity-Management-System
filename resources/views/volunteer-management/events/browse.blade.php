<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Events - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100" x-data="{
    searchQuery: '',
    statusFilter: 'all',
    availabilityFilter: 'all',
    sortBy: 'date',
    viewMode: 'grid',

    filterEvents(events) {
        let filtered = events;

        // Search filter
        if (this.searchQuery) {
            filtered = filtered.filter(event => {
                const query = this.searchQuery.toLowerCase();
                return event.title.toLowerCase().includes(query) ||
                       event.location.toLowerCase().includes(query) ||
                       (event.description && event.description.toLowerCase().includes(query));
            });
        }

        // Status filter
        if (this.statusFilter !== 'all') {
            filtered = filtered.filter(event => event.status === this.statusFilter);
        }

        // Availability filter
        if (this.availabilityFilter === 'available') {
            filtered = filtered.filter(event => event.currentVolunteers < event.capacity);
        } else if (this.availabilityFilter === 'full') {
            filtered = filtered.filter(event => event.currentVolunteers >= event.capacity);
        } else if (this.availabilityFilter === 'registered') {
            filtered = filtered.filter(event => event.isRegistered);
        }

        // Sort
        if (this.sortBy === 'date') {
            filtered.sort((a, b) => new Date(a.startDate) - new Date(b.startDate));
        } else if (this.sortBy === 'popular') {
            filtered.sort((a, b) => b.currentVolunteers - a.currentVolunteers);
        } else if (this.sortBy === 'capacity') {
            filtered.sort((a, b) => {
                const aAvailable = a.capacity - a.currentVolunteers;
                const bAvailable = b.capacity - b.currentVolunteers;
                return bAvailable - aAvailable;
            });
        }

        return filtered;
    }
}" x-init="
    // Initialize events data
    window.eventsData = [
        @foreach($events as $event)
        {
            id: {{ $event->Event_ID }},
            title: '{{ addslashes($event->Title) }}',
            description: '{{ addslashes($event->Description ?? '') }}',
            location: '{{ addslashes($event->Location) }}',
            status: '{{ $event->Status }}',
            startDate: '{{ $event->Start_Date->format('Y-m-d') }}',
            endDate: '{{ $event->End_Date->format('Y-m-d') }}',
            currentVolunteers: {{ $event->getTotalVolunteersFilled() }},
            capacity: {{ $event->getTotalVolunteerCapacity() }},
            isRegistered: {{ in_array($event->Event_ID, $registeredEventIds) ? 'true' : 'false' }},
            daysUntil: {{ now()->diffInDays($event->Start_Date, false) }}
        },
        @endforeach
    ];
">
<div class="min-h-screen">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header with Stats -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Browse Events
                    </h1>
                    <p class="text-gray-600 mt-1">Find volunteer opportunities that match your interests</p>
                </div>

                <!-- Quick Stats -->
                <div class="flex gap-3 flex-wrap">
                    <div class="bg-white rounded-lg shadow-sm px-4 py-3 border-l-4 border-indigo-500">
                        <div class="text-2xl font-bold text-indigo-600">{{ $events->total() }}</div>
                        <div class="text-xs text-gray-600">Total Events</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm px-4 py-3 border-l-4 border-green-500">
                        <div class="text-2xl font-bold text-green-600">{{ count($registeredEventIds) }}</div>
                        <div class="text-xs text-gray-600">Registered</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm px-4 py-3 border-l-4 border-blue-500">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ $events->filter(fn($e) => $e->getTotalVolunteersFilled() < $e->getTotalVolunteerCapacity())->count() }}
                        </div>
                        <div class="text-xs text-gray-600">Available</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-lg mb-6 flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg mb-6 flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="flex-1">{{ session('error') }}</span>
                <button @click="show = false" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Search & Filter
                </h2>
            </div>

            <div class="p-6">
                <!-- Search Bar -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Events</label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery"
                               placeholder="Search by title, location, or description..."
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button x-show="searchQuery" @click="searchQuery = ''"
                                class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="statusFilter"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all">All Statuses</option>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Ongoing">Ongoing</option>
                        </select>
                    </div>

                    <!-- Availability Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Availability</label>
                        <select x-model="availabilityFilter"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all">All Events</option>
                            <option value="available">Available Slots</option>
                            <option value="full">Full Events</option>
                            <option value="registered">My Registrations</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select x-model="sortBy"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="date">Start Date</option>
                            <option value="popular">Most Popular</option>
                            <option value="capacity">Available Slots</option>
                        </select>
                    </div>
                </div>

                <!-- View Mode Toggle -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <span x-text="filterEvents(window.eventsData).length"></span> events found
                    </div>
                    <div class="flex gap-2">
                        <button @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            Grid
                        </button>
                        <button @click="viewMode = 'list'"
                                :class="viewMode === 'list' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            List
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Grid/List -->
        <div :class="viewMode === 'grid' ? 'grid md:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-4'">
            @foreach($events as $event)
                @php
                    $currentVolunteers = $event->getTotalVolunteersFilled();
                    $capacity = $event->getTotalVolunteerCapacity();
                    $capacityPercent = $capacity > 0 ? ($currentVolunteers / $capacity) * 100 : 0;
                    $isRegistered = in_array($event->Event_ID, $registeredEventIds);
                    $isFull = $currentVolunteers >= $capacity;
                    $daysUntil = now()->diffInDays($event->Start_Date, false);
                @endphp

                <div x-show="filterEvents(window.eventsData).find(e => e.id === {{ $event->Event_ID }})"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     :class="viewMode === 'grid' ? '' : 'flex'"
                     class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group">

                    <!-- Grid View -->
                    <template x-if="viewMode === 'grid'">
                        <div>
                            <!-- Header with Status -->
                            <div class="relative bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                                <div class="flex justify-between items-start gap-2 mb-3">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-white
                                        {{ $event->Status === 'Upcoming' ? 'text-blue-600' : '' }}
                                        {{ $event->Status === 'Ongoing' ? 'text-green-600' : '' }}">
                                        {{ $event->Status }}
                                    </span>
                                    @if($isRegistered)
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-500 text-white flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Registered
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold text-white group-hover:scale-105 transition-transform">
                                    {{ $event->Title }}
                                </h3>

                                <!-- Urgency Indicator -->
                                @if($daysUntil >= 0 && $daysUntil <= 7 && !$isRegistered)
                                    <div class="absolute top-2 right-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold rounded-full
                                            {{ $daysUntil == 0 ? 'bg-red-500 text-white animate-pulse' : '' }}
                                            {{ $daysUntil > 0 && $daysUntil <= 3 ? 'bg-orange-500 text-white' : '' }}
                                            {{ $daysUntil > 3 && $daysUntil <= 7 ? 'bg-yellow-500 text-white' : '' }}">
                                            @if($daysUntil == 0)
                                                🔥 Today
                                            @elseif($daysUntil == 1)
                                                ⚡ Tomorrow
                                            @else
                                                ⏰ {{ $daysUntil }}d
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-6">
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3 min-h-[60px]">
                                    {{ $event->Description ?? 'Join us for this amazing volunteer opportunity!' }}
                                </p>

                                <!-- Event Details -->
                                <div class="space-y-3 mb-4">
                                    <div class="flex items-start text-sm text-gray-700">
                                        <svg class="w-5 h-5 mr-2 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="line-clamp-1">{{ $event->Location }}</span>
                                    </div>

                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ $event->Start_Date->format('M d, Y') }}</span>
                                        @if($event->Start_Date->format('Y-m-d') !== $event->End_Date->format('Y-m-d'))
                                            <span class="text-gray-400 mx-1">→</span>
                                            <span>{{ $event->End_Date->format('M d, Y') }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center text-gray-700">
                                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span>{{ $currentVolunteers }} / {{ $capacity }}</span>
                                        </div>
                                        @if($isFull)
                                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">
                                                FULL
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">
                                                {{ $capacity - $currentVolunteers }} left
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Capacity Progress -->
                                    @if($capacity > 0)
                                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all duration-500
                                                {{ $capacityPercent >= 100 ? 'bg-red-500' : '' }}
                                                {{ $capacityPercent >= 80 && $capacityPercent < 100 ? 'bg-orange-500' : '' }}
                                                {{ $capacityPercent < 80 ? 'bg-green-500' : '' }}"
                                                 style="width: {{ min($capacityPercent, 100) }}%"></div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                                   class="group/btn relative block w-full text-center px-4 py-3 rounded-lg font-medium overflow-hidden transition-all duration-300
                                        {{ $isRegistered ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white' }}">
                                    <span class="relative z-10 flex items-center justify-center gap-2">
                                        @if($isRegistered)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View My Registration
                                        @else
                                            <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                            View Details & Register
                                        @endif
                                    </span>
                                </a>
                            </div>
                        </div>
                    </template>

                    <!-- List View -->
                    <template x-if="viewMode === 'list'">
                        <div class="flex flex-col sm:flex-row w-full">
                            <!-- Left: Event Info -->
                            <div class="flex-1 p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-3 py-1 text-xs font-bold rounded-full
                                                {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $event->Status === 'Ongoing' ? 'bg-green-100 text-green-700' : '' }}">
                                                {{ $event->Status }}
                                            </span>
                                            @if($isRegistered)
                                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-500 text-white flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Registered
                                                </span>
                                            @endif
                                            @if($daysUntil >= 0 && $daysUntil <= 3 && !$isRegistered)
                                                <span class="px-2 py-1 text-xs font-bold rounded-full
                                                    {{ $daysUntil == 0 ? 'bg-red-500 text-white' : 'bg-orange-500 text-white' }}">
                                                    @if($daysUntil == 0) 🔥 Today @else ⚡ {{ $daysUntil }}d @endif
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                                            {{ $event->Title }}
                                        </h3>
                                        <p class="text-gray-600 text-sm line-clamp-2 mb-3">
                                            {{ $event->Description ?? 'Join us for this volunteer opportunity!' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="flex items-center text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ $event->Start_Date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        <span class="truncate">{{ $event->Location }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Stats & Action -->
                            <div class="sm:w-64 bg-gray-50 p-6 flex flex-col justify-between border-t sm:border-t-0 sm:border-l border-gray-200">
                                <div class="space-y-3 mb-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Volunteers</span>
                                        <span class="text-sm font-bold text-gray-900">{{ $currentVolunteers }} / {{ $capacity }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all
                                            {{ $capacityPercent >= 100 ? 'bg-red-500' : '' }}
                                            {{ $capacityPercent >= 80 && $capacityPercent < 100 ? 'bg-orange-500' : '' }}
                                            {{ $capacityPercent < 80 ? 'bg-green-500' : '' }}"
                                             style="width: {{ min($capacityPercent, 100) }}%"></div>
                                    </div>
                                    @if($isFull)
                                        <span class="block text-center px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">
                                            EVENT FULL
                                        </span>
                                    @else
                                        <span class="block text-center px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">
                                            {{ $capacity - $currentVolunteers }} SLOTS LEFT
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                                   class="block text-center px-4 py-2.5 rounded-lg font-medium transition-all
                                        {{ $isRegistered ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }}">
                                    {{ $isRegistered ? 'View Registration' : 'View Details' }}
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            @endforeach
        </div>

        <!-- No Results -->
        <div x-show="filterEvents(window.eventsData).length === 0"
             x-transition
             class="text-center py-16 bg-white rounded-xl shadow-md">
            <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No events found</h3>
            <p class="text-gray-600 mb-6">Try adjusting your search or filters</p>
            <button @click="searchQuery = ''; statusFilter = 'all'; availabilityFilter = 'all'"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset Filters
            </button>
        </div>

        <!-- Empty State (No Events at All) -->
        @if($events->count() === 0)
            <div class="text-center py-16 bg-white rounded-xl shadow-md">
                <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No events available</h3>
                <p class="text-gray-600">Check back later for new volunteer opportunities.</p>
            </div>
        @endif

        <!-- Pagination -->
        @if($events->hasPages())
            <div class="mt-8">
                {{ $events->links() }}
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} CharityHub. Making a difference together.</p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
