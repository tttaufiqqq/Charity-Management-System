<!-- resources/views/organizer/campaigns/index.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Campaigns - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">CharityHub</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Campaigns</h1>
                    <p class="text-gray-600 mt-1">Manage your campaigns and allocate funds</p>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create New Campaign
                </a>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="?status=all" class="border-b-2 {{ request('status', 'all') == 'all' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 font-medium text-sm">
                    All Campaigns
                </a>
                <a href="?status=Active" class="border-b-2 {{ request('status') == 'Active' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 font-medium text-sm">
                    Active
                </a>
                <a href="?status=Completed" class="border-b-2 {{ request('status') == 'Completed' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 font-medium text-sm">
                    Completed
                </a>
            </nav>
        </div>

        <!-- Campaigns Grid -->
        @if($campaigns->count() > 0)
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($campaigns as $campaign)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <!-- Campaign Header -->
                        <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>

                        <div class="p-6">
                            <!-- Status Badge -->
                            <div class="mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($campaign->Status === 'Active') bg-green-100 text-green-800
                                    @elseif($campaign->Status === 'Completed') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $campaign->Status }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $campaign->Title }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $campaign->Description }}</p>

                            <!-- Progress Bar -->
                            @php
                                $progress = $campaign->Goal_Amount > 0 ? min(($campaign->Total_Collected / $campaign->Goal_Amount) * 100, 100) : 0;
                                $totalAllocated = $campaign->allocations()->sum('Amount_Allocated') ?? 0;
                                $remainingFunds = $campaign->Total_Collected - $totalAllocated;
                            @endphp
                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="text-gray-600">{{ number_format($progress, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-3 gap-3 mb-4 pb-4 border-b border-gray-200">
                                <div>
                                    <p class="text-xs text-gray-500">Collected</p>
                                    <p class="text-sm font-semibold text-gray-900">RM {{ number_format($campaign->Total_Collected, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Allocated</p>
                                    <p class="text-sm font-semibold text-orange-600">RM {{ number_format($totalAllocated, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Available</p>
                                    <p class="text-sm font-semibold text-green-600">RM {{ number_format($remainingFunds, 0) }}</p>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="text-xs text-gray-500 mb-4">
                                <p>Start: {{ \Carbon\Carbon::parse($campaign->Start_Date)->format('M d, Y') }}</p>
                                <p>End: {{ \Carbon\Carbon::parse($campaign->End_Date)->format('M d, Y') }}</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('recipients.allocate', $campaign->Campaign_ID) }}"
                                   class="flex-1 text-center bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors text-sm">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Allocate Funds
                                </a>
                                <a href="{{ route('recipients.allocations.history', $campaign->Campaign_ID) }}"
                                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors text-sm">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No campaigns yet</h3>
                <p class="text-gray-600 mb-6">Create your first campaign to start raising funds</p>
                <a href="{{ route('dashboard') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                    Create Campaign
                </a>
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

<!-- ================================= -->
<!-- Add this controller method -->
<!-- ================================= -->

<?php
// In OrganizerController.php or CampaignController.php

public function myCampaigns(Request $request)
{
    $query = Auth::user()->organization->campaigns()->with('allocations');

    // Filter by status
    if ($request->has('status') && $request->status !== 'all') {
        $query->where('Status', $request->status);
    }

    $campaigns = $query->orderBy('created_at', 'desc')->paginate(6);

    return view('organizer.campaigns.index', compact('campaigns'));
}

