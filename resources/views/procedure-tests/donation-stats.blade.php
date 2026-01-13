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
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 mb-6 text-white">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold font-mono">{{ $procedureName }}</h3>
                        <p class="text-green-100 mt-1">Database: {{ $database }}</p>
                        <p class="text-sm text-green-200 mt-2">Returns donation statistics for campaigns including totals and averages.</p>
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
                <form action="{{ route('procedures.donation-stats') }}" method="GET" class="grid md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Campaign (optional)</label>
                        <select name="campaign_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Campaigns</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->Campaign_ID }}" {{ $filters['campaign_id'] == $campaign->Campaign_ID ? 'selected' : '' }}>
                                    {{ $campaign->Title }}
                                </option>
                            @endforeach
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
                        <button type="submit" class="w-full px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            Execute Procedure
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results -->
            @if($result)
                <div class="grid md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-sm text-gray-500 mb-1">Total Donations</div>
                        <div class="text-3xl font-bold text-gray-900">{{ number_format($result->total_donations ?? 0) }}</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-sm text-gray-500 mb-1">Unique Donors</div>
                        <div class="text-3xl font-bold text-indigo-600">{{ number_format($result->unique_donors ?? 0) }}</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-sm text-gray-500 mb-1">Total Completed</div>
                        <div class="text-3xl font-bold text-green-600">RM {{ number_format($result->total_completed_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-sm text-gray-500 mb-1">Average Donation</div>
                        <div class="text-3xl font-bold text-purple-600">RM {{ number_format($result->avg_donation_amount ?? 0, 2) }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-900">Detailed Statistics</h4>
                    </div>
                    <div class="p-6">
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-700">By Status</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                        <span class="text-green-700">Completed</span>
                                        <span class="font-bold text-green-800">{{ $result->completed_count ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                        <span class="text-yellow-700">Pending</span>
                                        <span class="font-bold text-yellow-800">{{ $result->pending_count ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                        <span class="text-red-700">Failed</span>
                                        <span class="font-bold text-red-800">{{ $result->failed_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-700">Amount Breakdown</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-600">Total Pending</span>
                                        <span class="font-bold">RM {{ number_format($result->total_pending_amount ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-600">Total Failed</span>
                                        <span class="font-bold">RM {{ number_format($result->total_failed_amount ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-700">Range</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-600">Max Donation</span>
                                        <span class="font-bold">RM {{ number_format($result->max_donation_amount ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-600">Min Donation</span>
                                        <span class="font-bold">RM {{ number_format($result->min_donation_amount ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
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
