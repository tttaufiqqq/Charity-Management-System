<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Profile - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success') || session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') ?? session('status') }}
            </div>
        @endif

        <!-- Profile Header with Donor Tier -->
        @php
            $tierColors = [
                'Platinum' => ['bg' => 'from-gray-700 to-gray-900', 'badge' => 'bg-gradient-to-r from-gray-300 to-gray-100 text-gray-800', 'icon' => 'text-gray-300'],
                'Gold' => ['bg' => 'from-yellow-500 to-amber-600', 'badge' => 'bg-gradient-to-r from-yellow-300 to-amber-200 text-amber-900', 'icon' => 'text-yellow-300'],
                'Silver' => ['bg' => 'from-gray-400 to-gray-500', 'badge' => 'bg-gradient-to-r from-gray-200 to-gray-100 text-gray-700', 'icon' => 'text-gray-200'],
                'Bronze' => ['bg' => 'from-orange-600 to-amber-700', 'badge' => 'bg-gradient-to-r from-orange-300 to-amber-200 text-orange-900', 'icon' => 'text-orange-300'],
                'Supporter' => ['bg' => 'from-rose-600 to-pink-600', 'badge' => 'bg-white/20 text-white', 'icon' => 'text-rose-200'],
            ];
            $tierDescriptions = [
                'Platinum' => 'Elite donor - RM10,000+ donated',
                'Gold' => 'Major donor - RM5,000+ donated',
                'Silver' => 'Generous donor - RM1,000+ donated',
                'Bronze' => 'Regular donor - RM100+ donated',
                'Supporter' => 'New donor - Every contribution counts!',
            ];
            $currentTier = $donorTier ?? 'Supporter';
            $tierStyle = $tierColors[$currentTier] ?? $tierColors['Supporter'];
        @endphp
        <div class="bg-gradient-to-r {{ $tierStyle['bg'] }} rounded-lg shadow-lg p-8 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg relative">
                        <svg class="w-14 h-14 {{ $currentTier === 'Platinum' ? 'text-gray-700' : ($currentTier === 'Gold' ? 'text-yellow-500' : ($currentTier === 'Silver' ? 'text-gray-400' : ($currentTier === 'Bronze' ? 'text-orange-600' : 'text-rose-600'))) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        @if($currentTier === 'Platinum' || $currentTier === 'Gold')
                        <div class="absolute -top-1 -right-1 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-yellow-800" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="text-white">
                        <h1 class="text-3xl font-bold mb-2">{{ $donor->user->name }}</h1>
                        <p class="text-white/80 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ $donor->user->email }}
                        </p>
                        <div class="flex items-center gap-3 mt-3">
                            <span class="inline-flex items-center gap-2 px-4 py-2 {{ $tierStyle['badge'] }} rounded-full text-sm font-bold shadow-lg">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                {{ $currentTier }} Donor
                            </span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('profile.donor.edit') }}"
                   class="px-6 py-3 bg-white text-gray-800 rounded-lg hover:bg-gray-50 transition-colors font-medium shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                </a>
            </div>
            <!-- Tier Description -->
            <div class="mt-4 p-3 bg-white/10 rounded-lg text-white/90 text-sm">
                <p class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $tierDescriptions[$currentTier] ?? 'Thank you for your generosity!' }}
                    @if($currentTier !== 'Platinum')
                        • <strong>Next tier:</strong>
                        @if($currentTier === 'Supporter') Bronze at RM100
                        @elseif($currentTier === 'Bronze') Silver at RM1,000
                        @elseif($currentTier === 'Silver') Gold at RM5,000
                        @elseif($currentTier === 'Gold') Platinum at RM10,000
                        @endif
                    @endif
                </p>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-rose-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Donations</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalDonations }}</p>
                        <p class="text-xs text-gray-500 mt-1">Donations made</p>
                    </div>
                    <div class="bg-rose-100 rounded-full p-4">
                        <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Amount</p>
                        <p class="text-3xl font-bold text-gray-900">RM {{ number_format($totalAmount, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Contributed</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Campaigns Supported</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $campaignsSupported }}</p>
                        <p class="text-xs text-gray-500 mt-1">Different campaigns</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Recent Donations
                </h2>
                <a href="{{ route('donations.my') }}" class="text-sm text-rose-600 hover:text-rose-800 font-medium">View All</a>
            </div>

            @if($recentDonations->count() > 0)
                <div class="space-y-4">
                    @foreach($recentDonations as $donation)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $donation->campaign->Title }}</h3>
                                <p class="text-sm text-gray-600">{{ $donation->Donation_Date->format('M d, Y') }} • {{ $donation->Payment_Method }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-green-600">RM {{ number_format($donation->Amount, 2) }}</p>
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Success</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <p class="text-gray-600 mb-3">No donations yet</p>
                    <a href="{{ route('campaigns.browse') }}" class="text-rose-600 hover:text-rose-800 font-medium">Browse campaigns to donate</a>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ route('campaigns.browse') }}" class="flex items-center gap-3 p-4 bg-rose-50 rounded-lg hover:bg-rose-100 transition-colors border border-rose-200 group">
                <div class="bg-rose-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-rose-700">Browse Campaigns</span>
            </a>
            <a href="{{ route('donations.my') }}" class="flex items-center gap-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors border border-green-200 group">
                <div class="bg-green-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-green-700">My Donations</span>
            </a>
            <a href="{{ route('public.events.browse') }}" class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200 group">
                <div class="bg-blue-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-blue-700">Browse Events</span>
            </a>
        </div>
    </main>
</div>
</body>
</html>
