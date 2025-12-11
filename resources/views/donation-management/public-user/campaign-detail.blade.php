<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->Title }} - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Campaign Header -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-12">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex-1">
                        <h1 class="text-4xl font-bold text-white mb-2">{{ $campaign->Title }}</h1>
                        <p class="text-green-100">By {{ $campaign->organization->user->name }}</p>
                    </div>
                    <span class="px-4 py-2 text-sm font-semibold rounded-full bg-white text-green-600">
                            {{ $campaign->Status }}
                        </span>
                </div>

                <!-- Progress Section -->
                <div class="bg-white bg-opacity-20 rounded-lg p-6">
                    <div class="flex justify-between text-white mb-2">
                        <span class="text-sm">Progress</span>
                        <span class="text-2xl font-bold">{{ number_format($progress, 1) }}%</span>
                    </div>
                    <div class="w-full bg-white bg-opacity-30 rounded-full h-4 mb-3">
                        <div class="bg-white h-4 rounded-full transition-all" style="width: {{ min($progress, 100) }}%"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-white">
                        <div>
                            <p class="text-sm text-green-100">Raised</p>
                            <p class="text-3xl font-bold">RM {{ number_format($campaign->Collected_Amount, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-green-100">Goal</p>
                            <p class="text-3xl font-bold">RM {{ number_format($campaign->Goal_Amount, 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Details -->
            <div class="p-8">
                <!-- Quick Stats -->
                <div class="grid md:grid-cols-4 gap-6 mb-8">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-2xl font-bold text-gray-900">{{ $campaign->donations->unique('Donor_ID')->count() }}</p>
                        <p class="text-sm text-gray-600">Donors</p>
                    </div>

                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-2xl font-bold text-gray-900">{{ $campaign->donations->count() }}</p>
                        <p class="text-sm text-gray-600">Donations</p>
                    </div>

                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-2xl font-bold text-gray-900">{{ $campaign->Start_Date->format('M d') }}</p>
                        <p class="text-sm text-gray-600">Start Date</p>
                    </div>

                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <svg class="w-8 h-8 mx-auto mb-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-2xl font-bold text-gray-900">{{ now()->diffInDays($campaign->End_Date) }}</p>
                        <p class="text-sm text-gray-600">Days Left</p>
                    </div>
                </div>

                <!-- Campaign Description -->
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">About This Campaign</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                            {{ $campaign->Description ?? 'No description provided.' }}
                        </p>
                    </div>
                </div>

                <!-- Campaign Timeline -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-3">Campaign Timeline</h3>
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <p class="text-gray-600">Start Date</p>
                            <p class="font-semibold text-gray-900">{{ $campaign->Start_Date->format('F d, Y') }}</p>
                        </div>
                        <div class="flex-1 mx-4">
                            <div class="h-2 bg-green-200 rounded-full">
                                @php
                                    $totalDays = $campaign->Start_Date->diffInDays($campaign->End_Date);
                                    $daysPassed = $campaign->Start_Date->diffInDays(now());
                                    $timeProgress = $totalDays > 0 ? min(($daysPassed / $totalDays) * 100, 100) : 0;
                                @endphp
                                <div class="h-2 bg-green-600 rounded-full" style="width: {{ $timeProgress }}%"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-600">End Date</p>
                            <p class="font-semibold text-gray-900">{{ $campaign->End_Date->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Organizer Info -->
                <div class="bg-indigo-50 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-3">Campaign Organizer</h3>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-indigo-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-lg font-bold text-indigo-700">
                                    {{ strtoupper(substr($campaign->organization->user->name, 0, 1)) }}
                                </span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $campaign->organization->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $campaign->organization->City }}, {{ $campaign->organization->State }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Donations</h2>
            @if($recentDonations->count() > 0)
                <div class="space-y-4">
                    @foreach($recentDonations as $donation)
                        <div class="flex justify-between items-center py-3 border-b border-gray-200 last:border-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-green-600 font-semibold text-sm">
                                            {{ strtoupper(substr($donation->donor->Full_Name, 0, 1)) }}
                                        </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $donation->donor->Full_Name }}</p>
                                    <p class="text-sm text-gray-500">{{ $donation->Donation_Date->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-green-600">RM {{ number_format($donation->Amount, 2) }}</p>
                                <p class="text-sm text-gray-500">{{ $donation->Payment_Method }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No donations yet. Be the first to donate!</p>
            @endif
        </div>
    </main>
</div>
</body>
</html>
