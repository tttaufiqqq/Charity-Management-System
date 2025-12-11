<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Campaigns - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Browse Campaigns</h1>
            <p class="text-gray-600 mt-1">Discover active fundraising campaigns making a difference</p>
        </div>

        <!-- Campaigns Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($campaigns as $campaign)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Progress Header -->
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                        <div class="flex justify-between items-start text-white mb-2">
                            <h3 class="text-lg font-bold">{{ $campaign->Title }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-white text-green-600">
                                    {{ $campaign->Status }}
                                </span>
                        </div>
                        @php
                            $progress = $campaign->Goal_Amount > 0
                                ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100
                                : 0;
                        @endphp
                        <div class="w-full bg-white bg-opacity-30 rounded-full h-2">
                            <div class="bg-white h-2 rounded-full transition-all" style="width: {{ min($progress, 100) }}%"></div>
                        </div>
                        <div class="flex justify-between text-sm text-white mt-2">
                            <span>{{ number_format($progress, 1) }}% Funded</span>
                            <span class="font-semibold">RM {{ number_format($campaign->Collected_Amount, 0) }}</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ $campaign->Description ?? 'No description available' }}
                        </p>

                        <!-- Campaign Stats -->
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between text-gray-700">
                                <span>Goal:</span>
                                <span class="font-semibold">RM {{ number_format($campaign->Goal_Amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Raised:</span>
                                <span class="font-semibold text-green-600">RM {{ number_format($campaign->Collected_Amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Ends:</span>
                                <span>{{ $campaign->End_Date->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Donors:</span>
                                <span>{{ $campaign->donations->unique('Donor_ID')->count() }}</span>
                            </div>
                        </div>

                        <a href="{{ route('public.campaigns.show', $campaign->Campaign_ID) }}"
                           class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            View Campaign
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No campaigns available</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later for new fundraising campaigns.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($campaigns->hasPages())
            <div class="mt-8">
                {{ $campaigns->links() }}
            </div>
        @endif
    </main>
</div>
</body>
</html>
