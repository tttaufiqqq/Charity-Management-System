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

    <!-- Funding Status Breakdown -->
    @if(!empty($fundingStatusBreakdown))
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaign Funding Status</h3>
        <p class="text-sm text-gray-600 mb-4">Campaigns are categorized by their progress toward the funding goal.</p>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            @php
                $fundingStyles = [
                    'Goal Reached' => ['bg' => 'bg-gradient-to-br from-green-500 to-emerald-600', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'Almost There' => ['bg' => 'bg-gradient-to-br from-blue-500 to-cyan-500', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                    'Halfway' => ['bg' => 'bg-gradient-to-br from-yellow-400 to-amber-500', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                    'Making Progress' => ['bg' => 'bg-gradient-to-br from-orange-400 to-orange-500', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                    'Just Started' => ['bg' => 'bg-gradient-to-br from-gray-400 to-gray-500', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
                    'Ended' => ['bg' => 'bg-gradient-to-br from-red-400 to-red-500', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'Not Started' => ['bg' => 'bg-gradient-to-br from-purple-400 to-purple-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp
            @foreach($fundingStatusBreakdown as $status => $count)
                @php $style = $fundingStyles[$status] ?? ['bg' => 'bg-gray-400', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z']; @endphp
                <div class="text-center p-4 {{ $style['bg'] }} rounded-lg shadow-md text-white">
                    <div class="flex justify-center mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $style['icon'] }}"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold">{{ $count }}</div>
                    <div class="text-xs font-medium opacity-90">{{ $status }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Campaigns by Status -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaigns by Status</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $statusStyles = [
                    'Active' => 'bg-green-100 text-green-800 border-green-200',
                    'Completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                ];
            @endphp
            @foreach($campaignsByStatus as $status => $count)
                <div class="text-center p-4 {{ $statusStyles[$status] ?? 'bg-gray-100' }} rounded-lg border">
                    <div class="text-2xl font-bold">{{ $count }}</div>
                    <div class="text-sm font-medium">{{ $status }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Campaigns Needing Attention -->
    @if(!empty($campaignsNeedingAttention) && count($campaignsNeedingAttention) > 0)
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg shadow-sm p-6 mb-8 border border-amber-200">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-amber-900">Campaigns Needing Attention</h3>
        </div>
        <p class="text-sm text-amber-700 mb-4">These active campaigns have low progress and are ending soon (within 7 days).</p>
        <div class="space-y-3">
            @foreach($campaignsNeedingAttention as $campaign)
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-amber-200">
                    <div>
                        <div class="font-medium text-gray-900">{{ $campaign->campaign_title }}</div>
                        <div class="text-sm text-gray-500">{{ $campaign->days_remaining }} days remaining</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-amber-700">{{ $campaign->progress_percentage }}% funded</div>
                        <div class="text-xs text-gray-500">RM {{ number_format($campaign->remaining_amount, 2) }} needed</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top Campaigns -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Campaigns by Amount Raised</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Goal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raised</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funding Status</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($topCampaigns as $index => $campaign)
                    @php
                        $fundingStatus = $campaign->funding_status ?? 'Just Started';
                        $fundingBadgeStyles = [
                            'Goal Reached' => 'bg-green-100 text-green-800',
                            'Almost There' => 'bg-blue-100 text-blue-800',
                            'Halfway' => 'bg-yellow-100 text-yellow-800',
                            'Making Progress' => 'bg-orange-100 text-orange-800',
                            'Just Started' => 'bg-gray-100 text-gray-800',
                            'Ended' => 'bg-red-100 text-red-800',
                            'Not Started' => 'bg-purple-100 text-purple-800',
                        ];
                    @endphp
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
                            <div class="text-sm font-medium text-gray-900">{{ $campaign->campaign_title }}</div>
                            <div class="text-xs text-gray-500">{{ $campaign->days_remaining }} days left</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            RM {{ number_format($campaign->Goal_Amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                            RM {{ number_format($campaign->Collected_Amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($campaign->progress_percentage, 100) }}%"></div>
                                </div>
                                <span class="text-sm text-gray-900">{{ number_format($campaign->progress_percentage, 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $fundingBadgeStyles[$fundingStatus] ?? $fundingBadgeStyles['Just Started'] }}">
                                {{ $fundingStatus }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
