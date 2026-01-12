<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Campaigns - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Browse Active Campaigns</h1>
            <p class="text-gray-600 mt-1">Discover active fundraising campaigns making a difference</p>
        </div>

        <!-- Table View -->
        @if($campaigns->count() > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Campaign
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Organizer
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Progress
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Goal Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    End Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Donors
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($campaigns as $campaign)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- Campaign -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $campaign->Title }}</div>
                                        <div class="text-sm text-gray-500 line-clamp-2">{{ $campaign->Description }}</div>
                                    </td>

                                    <!-- Organizer -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-indigo-600 font-medium">{{ $campaign->organization->user->name ?? 'N/A' }}</div>
                                    </td>

                                    <!-- Progress -->
                                    <td class="px-6 py-4">
                                        @php
                                            $progress = $campaign->Goal_Amount > 0 ? min(($campaign->Collected_Amount / $campaign->Goal_Amount) * 100, 100) : 0;
                                        @endphp
                                        <div class="flex items-center">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs text-gray-600">RM {{ number_format($campaign->Collected_Amount, 2) }}</span>
                                                    <span class="text-xs font-semibold text-gray-700">{{ number_format($progress, 1) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Goal Amount -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">RM {{ number_format($campaign->Goal_Amount, 2) }}</div>
                                    </td>

                                    <!-- End Date -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $campaign->End_Date->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">
                                            @php
                                                $endDate = $campaign->End_Date->startOfDay();
                                                $today = now()->startOfDay();
                                                $daysLeft = (int) $today->diffInDays($endDate, false);
                                                $isExpired = $daysLeft < 0;
                                                $daysLeft = abs($daysLeft);
                                            @endphp
                                            @if($isExpired)
                                                <span class="text-red-600">{{ $daysLeft }} days ago</span>
                                            @else
                                                {{ $daysLeft }} days left
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Donors -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ $campaign->donations->unique('Donor_ID')->count() }}</span>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @php
                                            $goalReached = $campaign->Collected_Amount >= $campaign->Goal_Amount;
                                        @endphp
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('public.campaigns.show', $campaign->Campaign_ID) }}"
                                               class="text-green-600 hover:text-green-900 font-medium"
                                               title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @if($goalReached)
                                                <button disabled
                                                        class="inline-flex items-center px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed opacity-60 text-xs"
                                                        title="Goal Reached">
                                                    Goal Reached
                                                </button>
                                            @else
                                                <a href="{{ route('public.campaigns.show', $campaign->Campaign_ID) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                                    Learn More
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($campaigns->hasPages())
                <div class="mt-8">
                    {{ $campaigns->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No campaigns available</h3>
                <p class="text-gray-600">Check back later for new fundraising campaigns.</p>
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
