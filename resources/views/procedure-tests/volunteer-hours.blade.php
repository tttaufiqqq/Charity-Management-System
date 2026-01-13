<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Test: {{ $procedureName }}
            </h2>
            <a href="{{ route('procedures.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; Back to Procedures
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Procedure Info -->
            <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-xl shadow-lg p-6 mb-6 text-white">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold font-mono">{{ $procedureName }}</h3>
                        <p class="text-orange-100 mt-1">Database: {{ $database }}</p>
                        <p class="text-sm text-orange-200 mt-2">Returns volunteer participation hours and event statistics.</p>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Filter Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h4 class="font-semibold text-gray-900 mb-4">Filter Options</h4>
                <form action="{{ route('procedures.volunteer-hours') }}" method="GET" class="grid md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Volunteer (optional)</label>
                        <select name="volunteer_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Volunteers</option>
                            @foreach($volunteers as $volunteer)
                                <option value="{{ $volunteer->Volunteer_ID }}" {{ $filters['volunteer_id'] == $volunteer->Volunteer_ID ? 'selected' : '' }}>
                                    {{ $volunteer->user->name ?? 'Unknown' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="Registered" {{ $filters['status'] === 'Registered' ? 'selected' : '' }}>Registered</option>
                            <option value="Attended" {{ $filters['status'] === 'Attended' ? 'selected' : '' }}>Attended</option>
                            <option value="Cancelled" {{ $filters['status'] === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $filters['start_date'] }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $filters['end_date'] }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium">
                            Execute Procedure
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results -->
            @if(!empty($results))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-900">Results ({{ count($results) }} rows)</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volunteer ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Events</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attended Hours</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attended</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cancelled</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Hours</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roles</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($results as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $row->Volunteer_ID }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->total_events_participated ?? 0 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-lg font-bold text-orange-600">{{ $row->total_attended_hours ?? 0 }}h</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">{{ $row->registered_events ?? 0 }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">{{ $row->attended_events ?? 0 }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">{{ $row->cancelled_events ?? 0 }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->avg_hours_per_event ?? 0, 1) }}h</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->unique_roles_taken ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                    <svg class="w-12 h-12 text-yellow-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-yellow-800 font-medium">No results yet.</p>
                    <p class="text-yellow-600 text-sm mt-1">Apply filters and click "Execute Procedure" to run the query.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
