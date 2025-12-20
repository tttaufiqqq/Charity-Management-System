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
                                    Organization
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
                                        <div class="text-sm text-gray-500 line-clamp-1">{{ $campaign->Description }}</div>
                                    </td>

                                    <!-- Organization -->
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
                                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
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
                                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($campaign->End_Date)->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">
                                            @php
                                                $endDate   = \Carbon\Carbon::parse($campaign->End_Date)->startOfDay();
                                                $today     = now()->startOfDay();

                                                $daysLeft  = $today->diffInDays($endDate, false);
                                                $isExpired = $daysLeft < 0;
                                                $daysLeft  = abs($daysLeft);
                                            @endphp
                                        @if($isExpired)
                                                <span class="text-red-600">{{ $daysLeft }} days ago</span>
                                            @else
                                                {{ $daysLeft }} days left
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @php
                                            $goalReached = $campaign->Collected_Amount >= $campaign->Goal_Amount;
                                        @endphp
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('campaigns.show.donate', $campaign->Campaign_ID) }}"
                                               class="text-indigo-600 hover:text-indigo-900 font-medium"
                                               title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @if($goalReached)
                                                <button disabled
                                                        class="inline-flex items-center px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed opacity-60"
                                                        title="Goal Reached">
                                                    Goal Reached
                                                </button>
                                            @else
                                                <a href="{{ route('campaigns.donate', $campaign->Campaign_ID) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                                    Donate Now
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

<!-- Payment Status Modal -->
@if(session('payment_success'))
    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden animate-fade-in">
            <!-- Success Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-3">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">Payment Successful!</h2>
                <p class="text-green-100">Thank you for your generosity</p>
            </div>

            <!-- Success Content -->
            <div class="p-6">
                <div class="text-center mb-4">
                    <p class="text-sm text-gray-600 mb-1">Donation Amount</p>
                    <p class="text-4xl font-bold text-gray-900">RM {{ number_format(session('payment_success')['amount'], 2) }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Receipt No:</span>
                            <span class="font-mono font-semibold text-gray-900">{{ session('payment_success')['receipt_no'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Campaign:</span>
                            <span class="font-semibold text-gray-900">{{ Str::limit(session('payment_success')['campaign_title'], 30) }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('donation.receipt', session('payment_success')['donation_id']) }}"
                       class="w-full block text-center bg-indigo-600 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                        Download Receipt
                    </a>
                    <button onclick="closeModal()"
                            class="w-full bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                        Continue Browsing
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if(session('payment_failed'))
    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden animate-fade-in">
            <!-- Failed Header -->
            <div class="bg-gradient-to-r from-red-500 to-orange-600 px-6 py-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-3">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">Payment Failed</h2>
                <p class="text-red-100">Transaction could not be completed</p>
            </div>

            <!-- Failed Content -->
            <div class="p-6">
                <div class="text-center mb-4">
                    <p class="text-sm text-gray-600 mb-1">Attempted Amount</p>
                    <p class="text-4xl font-bold text-gray-900">RM {{ number_format(session('payment_failed')['amount'], 2) }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Receipt No:</span>
                            <span class="font-mono font-semibold text-gray-900">{{ session('payment_failed')['receipt_no'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Campaign:</span>
                            <span class="font-semibold text-gray-900">{{ Str::limit(session('payment_failed')['campaign_title'], 30) }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-red-800">
                        Payment could not be completed. This may be due to insufficient funds, cancelled transaction, or connection issues.
                    </p>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('campaigns.donate', session('payment_failed')['campaign_id']) }}"
                       class="w-full block text-center bg-indigo-600 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                        Try Again
                    </a>
                    <button onclick="closeModal()"
                            class="w-full bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                        Continue Browsing
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
</style>

<script>
    function closeModal() {
        const modal = document.getElementById('paymentModal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.remove();
            }, 200);
        }
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('paymentModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }
    });
</script>

</body>
</html>
