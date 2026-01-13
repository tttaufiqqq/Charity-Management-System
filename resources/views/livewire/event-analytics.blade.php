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

    <!-- Volunteer Tier Distribution -->
    @if(!empty($volunteerTierBreakdown))
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Volunteer Tier Distribution</h3>
        <p class="text-sm text-gray-600 mb-4">Volunteers earn tiers based on their total verified volunteer hours.</p>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            @php
                $tierStyles = [
                    'Legend' => ['bg' => 'bg-gradient-to-br from-yellow-400 to-amber-500', 'text' => 'text-amber-900', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'hours' => '500+ hrs'],
                    'Champion' => ['bg' => 'bg-gradient-to-br from-purple-500 to-indigo-600', 'text' => 'text-white', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'hours' => '200-499 hrs'],
                    'Dedicated' => ['bg' => 'bg-gradient-to-br from-blue-500 to-cyan-500', 'text' => 'text-white', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'hours' => '100-199 hrs'],
                    'Active' => ['bg' => 'bg-gradient-to-br from-green-500 to-emerald-500', 'text' => 'text-white', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'hours' => '50-99 hrs'],
                    'Regular' => ['bg' => 'bg-gradient-to-br from-gray-400 to-gray-500', 'text' => 'text-white', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'hours' => '10-49 hrs'],
                    'New' => ['bg' => 'bg-gradient-to-br from-gray-200 to-gray-300', 'text' => 'text-gray-700', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6', 'hours' => '0-9 hrs'],
                ];
                $tierOrder = ['Legend', 'Champion', 'Dedicated', 'Active', 'Regular', 'New'];
            @endphp
            @foreach($tierOrder as $tier)
                @php
                    $count = $volunteerTierBreakdown[$tier] ?? 0;
                    $style = $tierStyles[$tier] ?? $tierStyles['New'];
                @endphp
                <div class="text-center p-4 {{ $style['bg'] }} rounded-lg shadow-md">
                    <div class="flex justify-center mb-2">
                        <svg class="w-8 h-8 {{ $style['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $style['icon'] }}"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold {{ $style['text'] }}">{{ $count }}</div>
                    <div class="text-sm font-medium {{ $style['text'] }} opacity-90">{{ $tier }}</div>
                    <div class="text-xs {{ $style['text'] }} opacity-75 mt-1">{{ $style['hours'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top Volunteers -->
    @if(!empty($topVolunteers) && count($topVolunteers) > 0)
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg shadow-sm p-6 mb-8 border border-indigo-100">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-indigo-900">Top Volunteers by Hours</h3>
        </div>
        <div class="grid md:grid-cols-5 gap-4">
            @foreach($topVolunteers as $index => $volunteer)
                @php
                    $tier = $volunteer->volunteer_tier ?? 'New';
                    $tierBadgeStyles = [
                        'Legend' => 'bg-gradient-to-r from-yellow-400 to-amber-500 text-amber-900',
                        'Champion' => 'bg-gradient-to-r from-purple-500 to-indigo-600 text-white',
                        'Dedicated' => 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white',
                        'Active' => 'bg-gradient-to-r from-green-500 to-emerald-500 text-white',
                        'Regular' => 'bg-gradient-to-r from-gray-400 to-gray-500 text-white',
                        'New' => 'bg-gray-200 text-gray-700',
                    ];
                @endphp
                <div class="bg-white rounded-lg p-4 text-center shadow-sm border {{ $index === 0 ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-gray-200' }}">
                    @if($index === 0)
                        <div class="flex justify-center mb-2">
                            <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    @else
                        <div class="w-8 h-8 mx-auto mb-2 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold">
                            {{ $index + 1 }}
                        </div>
                    @endif
                    <div class="text-2xl font-bold text-gray-900">{{ $volunteer->verified_hours }}</div>
                    <div class="text-sm text-gray-600">hours</div>
                    <div class="mt-2">
                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $tierBadgeStyles[$tier] ?? $tierBadgeStyles['New'] }}">
                            {{ $tier }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">{{ $volunteer->total_events }} events</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Events by Status -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Events by Status</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $statusStyles = [
                    'Upcoming' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'Ongoing' => 'bg-green-100 text-green-800 border-green-200',
                    'Completed' => 'bg-gray-100 text-gray-800 border-gray-200',
                    'Cancelled' => 'bg-red-100 text-red-800 border-red-200',
                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                ];
            @endphp
            @foreach($eventsByStatus as $status => $count)
                <div class="text-center p-4 {{ $statusStyles[$status] ?? 'bg-gray-100' }} rounded-lg border">
                    <div class="text-2xl font-bold">{{ $count }}</div>
                    <div class="text-sm font-medium">{{ $status }}</div>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volunteers</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($topEvents as $index => $event)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($index < 3)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                    {{ $index === 0 ? 'bg-yellow-400 text-yellow-900' : ($index === 1 ? 'bg-gray-300 text-gray-700' : 'bg-orange-400 text-orange-900') }}
                                    font-bold text-sm">
                                    {{ $index + 1 }}
                                </span>
                            @else
                                <span class="text-sm font-medium text-gray-500">#{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $event->Title }}</div>
                            <div class="text-xs text-gray-500">{{ $event->Location }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $event->organization->user->name ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($event->Event_Date)->format('M d, Y') }}
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
