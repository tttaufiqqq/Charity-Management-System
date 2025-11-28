<!-- ============================================================================ -->
<!-- File: resources/views/livewire/event-analytics.blade.php -->
<!-- ============================================================================ -->
<div>
    <!-- Summary Cards -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Event Participations</h3>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalEventParticipations) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Volunteer Hours</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($totalVolunteerHours, 1) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Avg Volunteers Per Event</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ number_format($averageVolunteersPerEvent, 1) }}</p>
        </div>
    </div>

    <!-- Events by Status -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Events by Status</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($eventsByStatus as $status => $count)
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                    <div class="text-sm text-gray-600">{{ $status }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Events -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Events by Volunteer Count</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organization</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volunteers</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($topEvents as $index => $event)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $event->Title }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $event->organization->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($event->Event_Date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $event->Location }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600">
                            {{ number_format($event->volunteers_count) }} volunteers
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $event->Status === 'Ongoing' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $event->Status === 'Completed' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $event->Status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $event->Status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
