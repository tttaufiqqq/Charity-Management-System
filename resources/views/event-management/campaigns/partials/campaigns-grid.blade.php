@php
    $hasCampaigns = $campaigns->count() > 0;
    $isPending = $isPending ?? false;
@endphp

@if($hasCampaigns)
    <!-- Grid View -->
    <div x-show="viewMode === 'grid'" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($campaigns as $campaign)
            @php
                $progress = $campaign->Goal_Amount > 0 ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100 : 0;
                $hasPendingSuggestions = ($campaign->pending_suggestions_count ?? 0) > 0;
            @endphp
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group">
                <!-- Header -->
                <div class="relative bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="px-3 py-1 text-xs font-bold rounded-full
                            {{ $campaign->Status === 'Active' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $campaign->Status === 'Pending' ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $campaign->Status === 'Completed' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ $campaign->Status }}
                        </span>
                        @if($hasPendingSuggestions)
                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-500 text-white">
                                {{ $campaign->pending_suggestions_count }} suggestion{{ $campaign->pending_suggestions_count != 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-white group-hover:scale-105 transition-transform">
                        {{ $campaign->Title }}
                    </h3>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2 min-h-[40px]">
                        {{ $campaign->Description ?? 'No description available' }}
                    </p>

                    <!-- Details -->
                    <div class="space-y-3 mb-4">
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600">Raised</span>
                                <span class="font-bold text-green-600">RM {{ number_format($campaign->Collected_Amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-gray-600">Goal</span>
                                <span class="font-bold text-gray-900">RM {{ number_format($campaign->Goal_Amount, 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all
                                    {{ $progress >= 100 ? 'bg-green-500' : 'bg-green-400' }}"
                                     style="width: {{ min($progress, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($progress, 1) }}% funded</p>
                        </div>

                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $campaign->Start_Date->format('M d, Y') }} - {{ $campaign->End_Date->format('M d, Y') }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('campaigns.show', $campaign->Campaign_ID) }}"
                           class="flex-1 text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            View
                        </a>
                        @if(!$isPending)
                            <a href="{{ route('campaigns.edit', $campaign->Campaign_ID) }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Suggestions</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($campaigns as $campaign)
                        @php
                            $progress = $campaign->Goal_Amount > 0 ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100 : 0;
                            $hasPendingSuggestions = ($campaign->pending_suggestions_count ?? 0) > 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $campaign->Title }}</div>
                                <div class="text-xs text-gray-500 line-clamp-1">{{ $campaign->Description ?? 'No description' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-bold rounded-full
                                    {{ $campaign->Status === 'Active' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $campaign->Status === 'Pending' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $campaign->Status === 'Completed' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ $campaign->Status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-600 mb-1">
                                    RM {{ number_format($campaign->Collected_Amount, 2) }} / RM {{ number_format($campaign->Goal_Amount, 2) }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full bg-green-500" style="width: {{ min($progress, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ number_format($progress, 0) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $campaign->Start_Date->format('M d') }} - {{ $campaign->End_Date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($hasPendingSuggestions)
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700">
                                        {{ $campaign->pending_suggestions_count }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('campaigns.show', $campaign->Campaign_ID) }}" class="text-green-600 hover:text-green-900">View</a>
                                    @if(!$isPending)
                                        <a href="{{ route('campaigns.edit', $campaign->Campaign_ID) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No campaigns found</h3>
        <p class="text-gray-600 mb-6">Get started by creating your first campaign!</p>
        <a href="{{ route('campaigns.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create Campaign
        </a>
    </div>
@endif
