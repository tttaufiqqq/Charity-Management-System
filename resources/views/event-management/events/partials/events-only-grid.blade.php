@php
    $hasEvents = $events->count() > 0;
    $isPending = $isPending ?? false;
@endphp

@if($hasEvents)
    <!-- Grid View -->
    <div x-show="viewMode === 'grid'" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($events as $event)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group">
                <!-- Header -->
                <div class="relative bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="px-3 py-1 text-xs font-bold rounded-full
                            {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $event->Status === 'Ongoing' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $event->Status === 'Completed' ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $event->Status === 'Pending' ? 'bg-orange-100 text-orange-700' : '' }}">
                            {{ $event->Status }}
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-white group-hover:scale-105 transition-transform">
                        {{ $event->Title }}
                    </h3>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2 min-h-[40px]">
                        {{ $event->Description ?? 'No description available' }}
                    </p>

                    <!-- Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $event->Start_Date->format('M d, Y') }}
                        </div>

                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            <span class="line-clamp-1">{{ $event->Location }}</span>
                        </div>

                        @php
                            $volunteerCount = $event->volunteers->count();
                            $capacity = $event->Capacity ?? 0;
                        @endphp
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $volunteerCount }} / {{ $capacity > 0 ? $capacity : '∞' }} volunteers
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('events.show', $event->Event_ID) }}"
                           class="flex-1 text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                            View
                        </a>
                        @if(!$isPending)
                            <a href="{{ route('events.edit', $event->Event_ID) }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('events.manage-volunteers', $event->Event_ID) }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Table View -->
    <div x-show="viewMode === 'table'" class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volunteers</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($events as $event)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $event->Title }}</div>
                                <div class="text-xs text-gray-500 line-clamp-1">{{ $event->Description ?? 'No description' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-bold rounded-full
                                    {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $event->Status === 'Ongoing' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $event->Status === 'Completed' ? 'bg-gray-100 text-gray-700' : '' }}
                                    {{ $event->Status === 'Pending' ? 'bg-orange-100 text-orange-700' : '' }}">
                                    {{ $event->Status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $event->Start_Date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="line-clamp-1">{{ $event->Location }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @php
                                    $volunteerCount = $event->volunteers->count();
                                    $capacity = $event->Capacity ?? 0;
                                @endphp
                                {{ $volunteerCount }} / {{ $capacity > 0 ? $capacity : '∞' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('events.show', $event->Event_ID) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    @if(!$isPending)
                                        <a href="{{ route('events.edit', $event->Event_ID) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <a href="{{ route('events.manage-volunteers', $event->Event_ID) }}" class="text-green-600 hover:text-green-900">Manage</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No events found</h3>
        <p class="text-gray-600 mb-6">Get started by creating your first event!</p>
        <a href="{{ route('events.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create Event
        </a>
    </div>
@endif
