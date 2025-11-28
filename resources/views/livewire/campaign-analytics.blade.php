<!-- ============================================================================ -->
<!-- File: resources/views/livewire/campaign-analytics.blade.php -->
<!-- ============================================================================ -->
<div>
    <!-- Summary Cards -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Goal Amount</h3>
            <p class="text-3xl font-bold text-gray-900">RM {{ number_format($totalGoal, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Collected</h3>
            <p class="text-3xl font-bold text-green-600">RM {{ number_format($totalCollected, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Average Progress</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ number_format($averageProgress, 1) }}%</p>
        </div>
    </div>

    <!-- Campaigns by Status -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaigns by Status</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($campaignsByStatus as $status => $count)
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                    <div class="text-sm text-gray-600">{{ $status }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Campaigns -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Campaigns by Amount Raised</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organization</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Goal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raised</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($topCampaigns as $index => $campaign)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $campaign->Title }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $campaign->organization->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            RM {{ number_format($campaign->Goal_Amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                            RM {{ number_format($campaign->Collected_Amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $progress = $campaign->Goal_Amount > 0 ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100 : 0;
                            @endphp
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                                </div>
                                <span class="text-sm text-gray-900">{{ number_format($progress, 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $campaign->Status === 'Active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $campaign->Status === 'Completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $campaign->Status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ $campaign->Status }}
                                </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
