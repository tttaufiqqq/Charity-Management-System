<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->Title }} - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Campaign Header -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $campaign->Title }}</h1>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                            {{ $campaign->Status === 'Active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $campaign->Status === 'Completed' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $campaign->Status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $campaign->Status }}
                        </span>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('campaigns.edit', $campaign->Campaign_ID) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Edit
                    </a>
                    <form action="{{ route('campaigns.destroy', $campaign->Campaign_ID) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <!-- Progress Bar -->
            @php
                $progress = $campaign->Goal_Amount > 0 ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100 : 0;
            @endphp
            <div class="mb-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Collected: RM {{ number_format($campaign->Collected_Amount, 2) }}</span>
                    <span>Goal: RM {{ number_format($campaign->Goal_Amount, 2) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-indigo-600 h-4 rounded-full transition-all" style="width: {{ min($progress, 100) }}%"></div>
                </div>
                <div class="text-center mt-2 text-2xl font-bold text-indigo-600">
                    {{ number_format($progress, 1) }}% Funded
                </div>
            </div>

            <!-- Campaign Details -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Start Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $campaign->Start_Date->format('F d, Y') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">End Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $campaign->End_Date->format('F d, Y') }}</p>
                </div>
            </div>

            <!-- Description -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Description</h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $campaign->Description ?? 'No description provided.' }}</p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Donations</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $campaign->donations->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Donors</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $campaign->donations->unique('Donor_ID')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Avg Donation</p>
                        <p class="text-2xl font-bold text-gray-900">
                            RM {{ $campaign->donations->count() > 0 ? number_format($campaign->Collected_Amount / $campaign->donations->count(), 2) : '0.00' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Donations</h2>
            @if($campaign->donations->count() > 0)
                <div class="space-y-4">
                    @foreach($campaign->donations->take(5) as $donation)
                        <div class="flex justify-between items-center py-3 border-b border-gray-200 last:border-0">
                            <div>
                                <p class="font-medium text-gray-900">{{ $donation->donor->Full_Name }}</p>
                                <p class="text-sm text-gray-500">{{ $donation->Donation_Date->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-green-600">RM {{ number_format($donation->Amount, 2) }}</p>
                                <p class="text-sm text-gray-500">{{ $donation->Payment_Method }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No donations yet</p>
            @endif
        </div>
    </main>
</div>
</body>
</html>
