<!-- resources/views/donation-management/browse-campaigns.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Browse Campaigns - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Browse Active Campaigns</h1>
            <p class="text-gray-600 mt-1">Discover causes that need your support</p>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('campaigns.browse') }}" class="space-y-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search Campaigns</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or description..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="ending_soon" {{ request('sort') == 'ending_soon' ? 'selected' : '' }}>Ending Soon</option>
                            <option value="most_funded" {{ request('sort') == 'most_funded' ? 'selected' : '' }}>Most Funded</option>
                            <option value="goal_amount" {{ request('sort') == 'goal_amount' ? 'selected' : '' }}>Highest Goal</option>
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Campaigns Grid -->
        @if($campaigns->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($campaigns as $campaign)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <!-- Campaign Image Placeholder -->
                        <div class="h-48 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>

                        <div class="p-6">
                            <!-- Organization -->
                            <p class="text-sm text-indigo-600 font-medium mb-2">{{ $campaign->organization->Organization_Name }}</p>

                            <!-- Title -->
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $campaign->Title }}</h3>

                            <!-- Description -->
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $campaign->Description }}</p>

                            <!-- Progress Bar -->
                            @php
                                $progress = $campaign->Goal_Amount > 0 ? min(($campaign->Collected_Amount / $campaign->Goal_Amount) * 100, 100) : 0;
                            @endphp
                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Raised: RM {{ number_format($campaign->Collected_Amount, 2) }}</span>
                                    <span class="text-gray-600">{{ number_format($progress, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Goal: RM {{ number_format($campaign->Goal_Amount, 2) }}</p>
                            </div>

                            <!-- End Date -->
                            <p class="text-sm text-gray-500 mb-4">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Ends: {{ \Carbon\Carbon::parse($campaign->End_Date)->format('M d, Y') }}
                            </p>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('campaigns.show.donate', $campaign->Campaign_ID) }}"
                                   class="flex-1 text-center bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                                    View Details
                                </a>
                                <a href="{{ route('campaigns.donate', $campaign->Campaign_ID) }}"
                                   class="flex-1 text-center bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                                    Donate Now
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $campaigns->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No campaigns found</h3>
                <p class="text-gray-600">Try adjusting your search or filters</p>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} CharityHub. Making a difference together.</p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
