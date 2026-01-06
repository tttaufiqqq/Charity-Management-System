@props(['stats', 'itemType' => 'campaign'])

<div class="mt-6 border-t border-gray-200 pt-6">
    <h4 class="text-sm font-semibold text-gray-900 flex items-center mb-4">
        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Organizer Track Record
    </h4>

    <!-- Statistics Summary -->
    <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="bg-gray-50 rounded-lg p-3 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide">Total</div>
        </div>
        <div class="bg-green-50 rounded-lg p-3 text-center">
            <div class="text-2xl font-bold text-green-700">{{ $stats['approved'] }}</div>
            <div class="text-xs text-green-600 uppercase tracking-wide">Approved</div>
        </div>
        <div class="bg-red-50 rounded-lg p-3 text-center">
            <div class="text-2xl font-bold text-red-700">{{ $stats['rejected'] }}</div>
            <div class="text-xs text-red-600 uppercase tracking-wide">Rejected</div>
        </div>
    </div>

    <!-- Recent Items -->
    @if($stats['recent']->isNotEmpty())
        <div class="mt-4">
            <h5 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Recent {{ ucfirst($itemType) }}s</h5>
            <div class="space-y-2">
                @foreach($stats['recent'] as $item)
                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $item->Title }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $item->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            @if($item->Status === 'Active' || $item->Status === 'Completed' || $item->Status === 'Upcoming' || $item->Status === 'Ongoing')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $item->Status }}
                                </span>
                            @elseif($item->Status === 'Rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $item->Status }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-sm text-gray-500 italic">No previous {{ $itemType }}s found.</p>
    @endif
</div>
