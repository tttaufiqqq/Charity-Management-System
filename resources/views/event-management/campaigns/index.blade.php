<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Campaigns - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Campaigns</h1>
                <p class="text-gray-600 mt-1">Manage your fundraising campaigns</p>
            </div>
            <a href="{{ route('campaigns.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                + Create Campaign
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Pending Suggestions Alert -->
        @php
            $totalPendingSuggestions = $campaigns->sum('pending_suggestions_count');
            $campaignsWithSuggestions = $campaigns->where('pending_suggestions_count', '>', 0)->count();
        @endphp
        @if($totalPendingSuggestions > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Recipient Suggestions Awaiting Your Review
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>
                                You have <strong>{{ $totalPendingSuggestions }}</strong> pending recipient suggestion{{ $totalPendingSuggestions != 1 ? 's' : '' }} across <strong>{{ $campaignsWithSuggestions }}</strong> campaign{{ $campaignsWithSuggestions != 1 ? 's' : '' }}.
                                Administrators have recommended recipients they believe would benefit from your campaigns. Review and accept suitable recipients to allocate funds.
                            </p>
                        </div>
                        <div class="mt-3">
                            <p class="text-xs text-yellow-600">
                                💡 <em>Look for the yellow badges in the "Suggestions" column below, or click "Suggestions" in the Actions column.</em>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Campaigns List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($campaigns->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Campaign</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Funding Progress</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Suggestions</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($campaigns as $campaign)
                            @php
                                $progress = $campaign->Goal_Amount > 0 ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <!-- Campaign Info -->
                                <td class="px-6 py-5">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ $campaign->Title }}</h3>
                                            <p class="text-xs text-gray-500 line-clamp-2">{{ $campaign->Description }}</p>
                                            <div class="mt-2 flex items-center gap-3 text-xs text-gray-500">
                                                <span class="inline-flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $campaign->Start_Date->format('M d') }} - {{ $campaign->End_Date->format('M d, Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Funding Progress -->
                                <td class="px-6 py-5">
                                    <div class="min-w-[200px]">
                                        <div class="flex items-baseline justify-between mb-2">
                                            <span class="text-lg font-bold text-indigo-600">RM {{ number_format($campaign->Collected_Amount, 0) }}</span>
                                            <span class="text-xs font-medium text-gray-500">of RM {{ number_format($campaign->Goal_Amount, 0) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ min($progress, 100) }}%"></div>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-semibold text-indigo-600">{{ number_format($progress, 1) }}%</span>
                                            <span class="text-xs text-gray-500">{{ $campaign->Goal_Amount - $campaign->Collected_Amount > 0 ? 'RM ' . number_format($campaign->Goal_Amount - $campaign->Collected_Amount, 0) . ' remaining' : 'Goal reached!' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold
                                        {{ $campaign->Status === 'Active' ? 'bg-green-100 text-green-800 ring-1 ring-green-600/20' : '' }}
                                        {{ $campaign->Status === 'Completed' ? 'bg-blue-100 text-blue-800 ring-1 ring-blue-600/20' : '' }}
                                        {{ $campaign->Status === 'Cancelled' ? 'bg-red-100 text-red-800 ring-1 ring-red-600/20' : '' }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                            {{ $campaign->Status === 'Active' ? 'bg-green-600' : '' }}
                                            {{ $campaign->Status === 'Completed' ? 'bg-blue-600' : '' }}
                                            {{ $campaign->Status === 'Cancelled' ? 'bg-red-600' : '' }}">
                                        </span>
                                        {{ $campaign->Status }}
                                    </span>
                                </td>

                                <!-- Suggestions -->
                                <td class="px-6 py-5 text-center">
                                    @if($campaign->pending_suggestions_count > 0)
                                        <a href="{{ route('campaigns.suggestions', $campaign->Campaign_ID) }}" class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition-all ring-1 ring-yellow-600/20">
                                            <svg class="w-3.5 h-3.5 mr-1.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                            </svg>
                                            <span class="font-bold">{{ $campaign->pending_suggestions_count }}</span> Pending
                                        </a>
                                    @elseif($campaign->recipient_suggestions_count > 0)
                                        <a href="{{ route('campaigns.suggestions', $campaign->Campaign_ID) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $campaign->recipient_suggestions_count }} Reviewed
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">None</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('campaigns.show', $campaign->Campaign_ID) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </a>

                                        <!-- Dropdown Menu -->
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                </svg>
                                            </button>

                                            <div x-show="open"
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    @if($campaign->recipient_suggestions_count > 0)
                                                        <a href="{{ route('campaigns.suggestions', $campaign->Campaign_ID) }}" class="flex items-center px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50 transition-colors">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                            </svg>
                                                            View Suggestions
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('recipients.allocate', $campaign->Campaign_ID) }}" class="flex items-center px-4 py-2 text-sm text-green-700 hover:bg-green-50 transition-colors">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Allocate Funds
                                                    </a>
                                                    <a href="{{ route('campaigns.edit', $campaign->Campaign_ID) }}" class="flex items-center px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 transition-colors">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit Campaign
                                                    </a>
                                                    <hr class="my-1">
                                                    <form action="{{ route('campaigns.destroy', $campaign->Campaign_ID) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors text-left">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                            Delete Campaign
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $campaigns->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No campaigns</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new campaign.</p>
                    <div class="mt-6">
                        <a href="{{ route('campaigns.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            + Create Campaign
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
