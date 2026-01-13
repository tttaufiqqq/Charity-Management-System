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
            <div class="bg-gradient-to-r from-pink-500 to-rose-600 rounded-xl shadow-lg p-6 mb-6 text-white">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold font-mono">{{ $procedureName }}</h3>
                        <p class="text-pink-100 mt-1">Database: {{ $database }}</p>
                        <p class="text-sm text-pink-200 mt-2">Returns recipient application summary with status and review information.</p>
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
                <form action="{{ route('procedures.recipient-summary') }}" method="GET" class="grid md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient (optional)</label>
                        <select name="recipient_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Recipients</option>
                            @foreach($recipients as $recipient)
                                <option value="{{ $recipient->Recipient_ID }}" {{ $filters['recipient_id'] == $recipient->Recipient_ID ? 'selected' : '' }}>
                                    {{ $recipient->Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="Pending" {{ $filters['status'] === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ $filters['status'] === 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ $filters['status'] === 'Rejected' ? 'selected' : '' }}>Rejected</option>
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
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors font-medium text-sm">
                            Execute
                        </button>
                        <button type="submit" name="show_all" value="1" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium text-sm">
                            All
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recipient ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Allocated</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Allocation Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applied At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved At</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($results as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $row->Recipient_ID }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->recipient_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($row->status === 'Approved') bg-green-100 text-green-800
                                                @elseif($row->status === 'Pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $row->status ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-lg font-bold text-green-600">RM {{ number_format($row->total_amount_allocated ?? 0, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->allocation_count ?? 0 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            @if($row->applied_at ?? null)
                                                {{ \Carbon\Carbon::parse($row->applied_at)->format('M d, Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            @if($row->approved_at ?? null)
                                                {{ \Carbon\Carbon::parse($row->approved_at)->format('M d, Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
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
                    <p class="text-yellow-600 text-sm mt-1">Apply filters and click "Execute" or "All" to run the query.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
