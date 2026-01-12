<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Schedule - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gradient-to-br from-gray-50 to-gray-100" x-data="{
    filter: 'all',
    hoveredEvent: null,
    showEventModal: false,
    selectedEvent: null
}">
<div class="min-h-screen flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header with Statistics -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        My Schedule
                    </h1>
                    <p class="text-gray-600 mt-1">View and manage your volunteer events</p>
                </div>

                <!-- Quick Stats -->
                <div class="flex gap-3">
                    <div class="bg-white rounded-lg shadow-sm px-4 py-3 border-l-4 border-blue-500">
                        <div class="text-2xl font-bold text-blue-600">{{ $upcomingEvents->count() }}</div>
                        <div class="text-xs text-gray-600">Upcoming</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm px-4 py-3 border-l-4 border-indigo-500">
                        <div class="text-2xl font-bold text-indigo-600">{{ $events->count() }}</div>
                        <div class="text-xs text-gray-600">This Month</div>
                    </div>
                </div>
            </div>

            <!-- Legend & Filters -->
            <div class="mt-6 bg-white rounded-lg shadow-sm p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4 flex-wrap">
                        <span class="text-sm font-medium text-gray-700">Event Status:</span>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span class="text-xs text-gray-600">Upcoming</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-xs text-gray-600">Ongoing</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                            <span class="text-xs text-gray-600">Completed</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">
                            All Events
                        </button>
                        <button @click="filter = 'Upcoming'" :class="filter === 'Upcoming' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">
                            Upcoming
                        </button>
                        <button @click="filter = 'Ongoing'" :class="filter === 'Ongoing' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">
                            Ongoing
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Calendar -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden">
                <!-- Month Navigation with Dropdown Calendar -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
                        </h2>

                        <!-- Dropdown Calendar Selector -->
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Month Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button"
                                        class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all duration-200 flex items-center gap-2 min-w-[140px] justify-between">
                                    <span>{{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F') }}</span>
                                    <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-50 max-h-64 overflow-y-auto">
                                    @foreach(range(1, 12) as $month)
                                        <a href="{{ route('volunteer.schedule', ['month' => $month, 'year' => $currentYear]) }}"
                                           class="block px-4 py-2 text-sm hover:bg-indigo-50 transition-colors {{ $month == $currentMonth ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700' }}">
                                            {{ \Carbon\Carbon::create(null, $month)->format('F') }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Year Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button"
                                        class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all duration-200 flex items-center gap-2 min-w-[100px] justify-between">
                                    <span>{{ $currentYear }}</span>
                                    <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute left-0 mt-2 w-32 bg-white rounded-lg shadow-xl py-2 z-50 max-h-64 overflow-y-auto">
                                    @foreach(range(now()->year - 2, now()->year + 2) as $year)
                                        <a href="{{ route('volunteer.schedule', ['month' => $currentMonth, 'year' => $year]) }}"
                                           class="block px-4 py-2 text-sm hover:bg-indigo-50 transition-colors {{ $year == $currentYear ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700' }}">
                                            {{ $year }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Quick Navigation Buttons -->
                            <div class="flex gap-1">
                                <a href="{{ route('volunteer.schedule', ['month' => $currentMonth == 1 ? 12 : $currentMonth - 1, 'year' => $currentMonth == 1 ? $currentYear - 1 : $currentYear]) }}"
                                   class="p-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all duration-200" title="Previous Month">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('volunteer.schedule') }}"
                                   class="px-3 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-all duration-200 font-medium text-sm">
                                    Today
                                </a>
                                <a href="{{ route('volunteer.schedule', ['month' => $currentMonth == 12 ? 1 : $currentMonth + 1, 'year' => $currentMonth == 12 ? $currentYear + 1 : $currentYear]) }}"
                                   class="p-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all duration-200" title="Next Month">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="p-4">
                    <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                        <!-- Day Headers -->
                        <div class="grid grid-cols-7 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                <div class="py-3 text-center">
                                    <span class="text-xs font-bold text-gray-700 hidden md:inline">{{ $day }}</span>
                                    <span class="text-xs font-bold text-gray-700 md:hidden">{{ substr($day, 0, 3) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Calendar Days -->
                        @foreach($calendar as $week)
                            <div class="grid grid-cols-7 border-b border-gray-200 last:border-b-0">
                                @foreach($week as $day)
                                    <div class="relative min-h-28 p-2 border-r border-gray-100 last:border-r-0 transition-all duration-200 hover:bg-gray-50
                                            {{ !$day['isCurrentMonth'] ? 'bg-gray-50/50' : 'bg-white' }}
                                            {{ $day['isToday'] ? 'bg-indigo-50 ring-2 ring-indigo-300 ring-inset' : '' }}"
                                         x-data="{ showTooltip: false }">

                                        <!-- Date Number -->
                                        <div class="flex items-start justify-between mb-1">
                                            <div class="text-sm font-semibold
                                                    {{ !$day['isCurrentMonth'] ? 'text-gray-300' : 'text-gray-700' }}
                                                    {{ $day['isToday'] ? 'bg-indigo-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs' : '' }}">
                                                {{ $day['date']->format('j') }}
                                            </div>
                                            @if($day['events']->count() > 0)
                                                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-indigo-600 rounded-full">
                                                    {{ $day['events']->count() }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Events -->
                                        <div class="space-y-1">
                                            @foreach($day['events']->take(2) as $event)
                                                <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                                                   @mouseenter="hoveredEvent = {{ $event->Event_ID }}"
                                                   @mouseleave="hoveredEvent = null"
                                                   class="group relative block text-xs px-2 py-1 rounded-md transition-all duration-200 transform hover:scale-105 hover:shadow-md
                                                        {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-800 hover:bg-blue-200' : '' }}
                                                        {{ $event->Status === 'Ongoing' ? 'bg-green-100 text-green-800 hover:bg-green-200' : '' }}
                                                        {{ $event->Status === 'Completed' ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : '' }}">
                                                    <div class="flex items-center gap-1">
                                                        <div class="w-1.5 h-1.5 rounded-full flex-shrink-0
                                                            {{ $event->Status === 'Upcoming' ? 'bg-blue-500' : '' }}
                                                            {{ $event->Status === 'Ongoing' ? 'bg-green-500' : '' }}
                                                            {{ $event->Status === 'Completed' ? 'bg-gray-400' : '' }}">
                                                        </div>
                                                        <span class="truncate font-medium">{{ $event->Title }}</span>
                                                    </div>

                                                    <!-- Hover Tooltip -->
                                                    <div x-show="hoveredEvent === {{ $event->Event_ID }}"
                                                         x-transition
                                                         class="absolute z-50 left-0 top-full mt-1 w-64 p-3 bg-white rounded-lg shadow-xl border border-gray-200 hidden md:block">
                                                        <h4 class="font-bold text-gray-900 mb-2">{{ $event->Title }}</h4>
                                                        <div class="space-y-1 text-xs text-gray-600">
                                                            <div class="flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                {{ $event->Start_Date->format('M d, Y') }} - {{ $event->End_Date->format('M d, Y') }}
                                                            </div>
                                                            <div class="flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                </svg>
                                                                {{ $event->Location }}
                                                            </div>
                                                        </div>
                                                        <div class="mt-2 pt-2 border-t border-gray-200">
                                                            <span class="text-xs font-medium text-indigo-600">Click to view details →</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach

                                            @if($day['events']->count() > 2)
                                                <div class="text-xs text-gray-500 font-medium px-2">
                                                    +{{ $day['events']->count() - 2 }} more
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Upcoming Events Sidebar -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        Upcoming Events
                    </h3>
                    <p class="text-indigo-100 text-sm mt-1">Next 30 days</p>
                </div>

                <div class="p-4 max-h-[600px] overflow-y-auto">
                    @if($upcomingEvents->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingEvents as $event)
                                <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                                   x-show="filter === 'all' || filter === '{{ $event->Status }}'"
                                   class="block p-4 rounded-lg transition-all duration-200 transform hover:scale-102 hover:shadow-lg
                                        {{ $event->Status === 'Upcoming' ? 'bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200' : '' }}
                                        {{ $event->Status === 'Ongoing' ? 'bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200' : '' }}">

                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="font-bold text-gray-900 text-sm flex-1">{{ $event->Title }}</h4>
                                        <span class="ml-2 px-2 py-1 text-xs font-bold rounded-full whitespace-nowrap
                                                {{ $event->Status === 'Upcoming' ? 'bg-blue-500 text-white' : '' }}
                                                {{ $event->Status === 'Ongoing' ? 'bg-green-500 text-white' : '' }}">
                                                {{ $event->Status }}
                                        </span>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-start gap-2 text-xs text-gray-700">
                                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <div class="font-medium">{{ $event->Start_Date->format('D, M d, Y') }}</div>
                                                @if($event->Start_Date->format('Y-m-d') !== $event->End_Date->format('Y-m-d'))
                                                    <div class="text-gray-500">to {{ $event->End_Date->format('D, M d, Y') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 text-xs text-gray-700">
                                            <svg class="w-4 h-4 flex-shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            </svg>
                                            <span class="truncate">{{ $event->Location }}</span>
                                        </div>

                                        <div class="flex items-center gap-2 text-xs font-medium">
                                            <svg class="w-4 h-4 flex-shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            @php
                                                $daysUntil = (int) now()->diffInDays($event->Start_Date, false);
                                            @endphp
                                            <span class="
                                                @if($daysUntil == 0) text-red-600
                                                @elseif($daysUntil <= 3) text-orange-600
                                                @else text-gray-700
                                                @endif">
                                                @if($daysUntil == 0)
                                                    🔥 Today!
                                                @elseif($daysUntil == 1)
                                                    ⚡ Tomorrow
                                                @elseif($daysUntil > 0)
                                                    In {{ $daysUntil }} days
                                                @else
                                                    🟢 Ongoing
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between">
                                        <span class="text-xs text-gray-600">Click for details</span>
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 font-medium mb-2">No upcoming events</p>
                            <p class="text-sm text-gray-500 mb-4">Start making a difference today!</p>
                            <a href="{{ route('volunteer.events.browse') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Browse Events
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Event List for Current Month -->
        @if($events->count() > 0)
            <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        All Events in {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid gap-3">
                        @foreach($events as $event)
                            <a href="{{ route('volunteer.events.show', $event->Event_ID) }}"
                               x-show="filter === 'all' || filter === '{{ $event->Status }}'"
                               class="group flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 rounded-xl transition-all duration-200 hover:shadow-lg
                                    {{ $event->Status === 'Upcoming' ? 'bg-blue-50 hover:bg-blue-100 border-l-4 border-blue-500' : '' }}
                                    {{ $event->Status === 'Ongoing' ? 'bg-green-50 hover:bg-green-100 border-l-4 border-green-500' : '' }}
                                    {{ $event->Status === 'Completed' ? 'bg-gray-50 hover:bg-gray-100 border-l-4 border-gray-400' : '' }}">

                                <div class="flex items-center gap-4 flex-1">
                                    <!-- Date Badge -->
                                    <div class="flex-shrink-0 text-center bg-white rounded-lg shadow-sm p-3 w-16">
                                        <div class="text-2xl font-bold
                                            {{ $event->Status === 'Upcoming' ? 'text-blue-600' : '' }}
                                            {{ $event->Status === 'Ongoing' ? 'text-green-600' : '' }}
                                            {{ $event->Status === 'Completed' ? 'text-gray-500' : '' }}">
                                            {{ $event->Start_Date->format('d') }}
                                        </div>
                                        <div class="text-xs font-medium text-gray-600">{{ $event->Start_Date->format('M') }}</div>
                                    </div>

                                    <!-- Event Info -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-indigo-600 transition-colors">
                                            {{ $event->Title }}
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                </svg>
                                                {{ $event->Location }}
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $event->Start_Date->format('M d') }} - {{ $event->End_Date->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Arrow -->
                                <div class="flex items-center gap-3 mt-3 sm:mt-0">
                                    <span class="px-4 py-2 text-sm font-bold rounded-lg shadow-sm
                                        {{ $event->Status === 'Upcoming' ? 'bg-blue-500 text-white' : '' }}
                                        {{ $event->Status === 'Ongoing' ? 'bg-green-500 text-white' : '' }}
                                        {{ $event->Status === 'Completed' ? 'bg-gray-400 text-white' : '' }}">
                                        {{ $event->Status }}
                                    </span>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="mt-8 bg-white rounded-xl shadow-md p-12 text-center">
                <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Events This Month</h3>
                <p class="text-gray-600 mb-6">You don't have any events scheduled for {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}</p>
                <a href="{{ route('volunteer.events.browse') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Find Events to Join
                </a>
            </div>
        @endif
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
