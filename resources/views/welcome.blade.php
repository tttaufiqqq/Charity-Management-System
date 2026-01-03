<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - Charity Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @guest
            <!-- Guest View -->
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Welcome to CharityHub</h2>
                <p class="text-xl text-gray-600 mb-8">Making a difference, one contribution at a time</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">

                <!-- Donor Card -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Become a Donor</h3>
                    <p class="text-gray-600 mb-4">Support meaningful causes and make a direct impact on communities in need.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Track your donations</li>
                        <li>✓ View impact reports</li>
                        <li>✓ Tax receipts available</li>
                    </ul>
                </div>

                <!-- Volunteer Card -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Join as Volunteer</h3>
                    <p class="text-gray-600 mb-4">Give your time and skills to help organizations achieve their missions.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Find local opportunities</li>
                        <li>✓ Track volunteer hours</li>
                        <li>✓ Build your skills</li>
                    </ul>
                </div>

                <!-- Organizer Card -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Register Organization</h3>
                    <p class="text-gray-600 mb-4">Create campaigns, recruit volunteers, and manage your charitable initiatives.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Create campaigns</li>
                        <li>✓ Manage events</li>
                        <li>✓ Connect with volunteers</li>
                    </ul>
                </div>

                <!-- Public Card -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Public Access</h3>
                    <p class="text-gray-600 mb-4">Browse campaigns, discover opportunities, and stay informed about initiatives.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Browse campaigns</li>
                        <li>✓ View events</li>
                        <li>✓ Stay informed</li>
                    </ul>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('register') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-colors">
                    Get Started Today
                </a>
            </div>
        @endguest

        @auth
            <!-- Authenticated Views by Role -->
            @if(auth()->user()->hasRole('donor'))
                <!-- Donor Dashboard Preview -->
                @php
                    $donor = auth()->user()->donor;
                    $totalDonated = $donor ? $donor->donations()->sum('Amount') : 0;
                    $donationCount = $donor ? $donor->donations()->count() : 0;
                    $uniqueCampaigns = $donor ? $donor->donations()->distinct('Campaign_ID')->count('Campaign_ID') : 0;
                    $lastDonation = $donor ? $donor->donations()->latest('Donation_Date')->first() : null;
                    $recentDonations = $donor ? $donor->donations()->with('campaign')->latest('Donation_Date')->take(3)->get() : collect();
                    // Get active campaigns - query directly from database to ensure fresh data
                    $activeCampaigns = \DB::table('campaign')
                        ->where('Status', 'Active')
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                @endphp

                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
                            <p class="text-gray-600 mt-1">Thank you for your generous support to {{ $uniqueCampaigns }} campaign{{ $uniqueCampaigns != 1 ? 's' : '' }}</p>
                        </div>
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Total Donated</p>
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">RM {{ number_format($totalDonated, 2) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Lifetime contributions</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Donations Made</p>
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $donationCount }}</p>
                            <p class="text-xs text-gray-500 mt-1">Total transactions</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Campaigns Supported</p>
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $uniqueCampaigns }}</p>
                            <p class="text-xs text-gray-500 mt-1">Different campaigns</p>
                        </div>
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Last Donation</p>
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-lg font-bold text-gray-900">{{ $lastDonation ? $lastDonation->Donation_Date->diffForHumans() : 'N/A' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $lastDonation ? 'RM ' . number_format($lastDonation->Amount, 2) : 'No donations yet' }}</p>
                        </div>
                    </div>

                    @if($recentDonations->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Recent Donations</h3>
                            <div class="space-y-3">
                                @foreach($recentDonations as $donation)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $donation->campaign->Title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $donation->Donation_Date->format('M d, Y') }} • {{ $donation->Payment_Method }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-green-600">RM {{ number_format($donation->Amount, 2) }}</p>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $donation->Payment_Status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $donation->Payment_Status }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($activeCampaigns->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Featured Campaigns</h3>
                            <div class="grid md:grid-cols-3 gap-4">
                                @foreach($activeCampaigns as $campaign)
                                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-lg hover:shadow-md transition-shadow">
                                        <h4 class="font-semibold text-gray-900 mb-2">{{ Str::limit($campaign->Title, 40) }}</h4>
                                        <div class="mb-3">
                                            @php
                                                $percentage = $campaign->Goal_Amount > 0 ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100 : 0;
                                            @endphp
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>Raised</span>
                                                <span>{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-600 mt-1">
                                                <span>RM {{ number_format($campaign->Collected_Amount, 0) }}</span>
                                                <span>RM {{ number_format($campaign->Goal_Amount, 0) }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('campaigns.donate', $campaign->Campaign_ID) }}" class="block w-full text-center bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                            Donate Now
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-gray-900">Quick Actions</h3>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('campaigns.browse') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                Browse Campaigns
                            </a>
                            <a href="{{ route('donations.my') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                View My Donations
                            </a>
                            <a href="{{ route('donations.receipts.all') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                Download Receipts
                            </a>
                            <a href="{{ route('public.events.browse') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                Browse Events
                            </a>
                        </div>
                    </div>
                </div>

                    @elseif(auth()->user()->hasRole('admin'))
                        <!-- Admin Dashboard Preview -->
                        @php
                            $pendingRecipients = \App\Models\Recipient::where('Status', 'Pending')->count();
                            $approvedRecipients = \App\Models\Recipient::where('Status', 'Approved')->count();
                            $pendingCampaigns = \App\Models\Campaign::where('Status', 'Pending')->count();
                            $activeCampaigns = \App\Models\Campaign::where('Status', 'Active')->count();
                            $pendingEvents = \App\Models\Event::where('Status', 'Pending')->count();
                            $totalOrganizations = \App\Models\Organization::count();
                            $totalUsers = \App\Models\User::count();
                            $totalDonations = \App\Models\Donation::where('Payment_Status', 'Completed')->sum('Amount');
                            $monthlyDonations = \App\Models\Donation::where('Payment_Status', 'Completed')
                                ->whereMonth('Donation_Date', now()->month)
                                ->whereYear('Donation_Date', now()->year)
                                ->sum('Amount');
                            $totalVolunteers = \App\Models\User::role('volunteer')->count();
                            $totalDonors = \App\Models\User::role('donor')->count();
                            $recentPendingItems = collect()
                                ->merge(\App\Models\Recipient::where('Status', 'Pending')->latest()->take(2)->get()->map(fn($r) => ['type' => 'Recipient', 'name' => $r->Name, 'date' => $r->created_at]))
                                ->merge(\App\Models\Campaign::where('Status', 'Pending')->latest()->take(2)->get()->map(fn($c) => ['type' => 'Campaign', 'name' => $c->Title, 'date' => $c->created_at]))
                                ->merge(\App\Models\Event::where('Status', 'Pending')->latest()->take(2)->get()->map(fn($e) => ['type' => 'Event', 'name' => $e->Title, 'date' => $e->created_at]))
                                ->sortByDesc('date')
                                ->take(5);
                        @endphp

                        <div class="bg-white rounded-lg shadow-lg p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
                                    <p class="text-gray-600 mt-1">System Overview • {{ $pendingRecipients + $pendingCampaigns + $pendingEvents }} items need your attention</p>
                                </div>
                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-4 gap-6 mb-8">
                                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm text-gray-600">Pending Approvals</p>
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900">{{ $pendingRecipients + $pendingCampaigns + $pendingEvents }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $pendingRecipients }} recipients • {{ $pendingCampaigns }} campaigns • {{ $pendingEvents }} events</p>
                                </div>
                                <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm text-gray-600">Platform Users</p>
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $totalDonors }} donors • {{ $totalVolunteers }} volunteers</p>
                                </div>
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm text-gray-600">Active Campaigns</p>
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900">{{ $activeCampaigns }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $totalOrganizations }} organizations registered</p>
                                </div>
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm text-gray-600">Total Donations</p>
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900">RM {{ number_format($totalDonations, 0) }}</p>
                                    <p class="text-xs text-gray-500 mt-1">RM {{ number_format($monthlyDonations, 0) }} this month</p>
                                </div>
                            </div>

                            @if($recentPendingItems->count() > 0)
                                <div class="mb-8">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Recent Pending Items</h3>
                                    <div class="space-y-2">
                                        @foreach($recentPendingItems as $item)
                                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">
                                                        {{ $item['type'] }}
                                                    </span>
                                                    <span class="font-medium text-gray-900">{{ $item['name'] }}</span>
                                                </div>
                                                <span class="text-sm text-gray-600">{{ $item['date']->diffForHumans() }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-4">
                                <h3 class="text-xl font-semibold text-gray-900">Quick Actions</h3>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('admin.analytics.dashboard') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                        Analytics Dashboard
                                    </a>
                                    @if($pendingRecipients > 0)
                                        <a href="{{ route('admin.recipients.pending') }}" class="relative bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                            Review Recipients
                                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">{{ $pendingRecipients }}</span>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.recipients.pending') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                            Review Recipients
                                        </a>
                                    @endif
                                    @if($pendingCampaigns > 0)
                                        <a href="{{ route('admin.campaigns.pending') }}" class="relative bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                            Pending Campaigns
                                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">{{ $pendingCampaigns }}</span>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.campaigns.pending') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                            Pending Campaigns
                                        </a>
                                    @endif
                                    @if($pendingEvents > 0)
                                        <a href="{{ route('admin.events.pending') }}" class="relative bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                            Pending Events
                                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">{{ $pendingEvents }}</span>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.events.pending') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                            Pending Events
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.manage.users') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                        Manage Users
                                    </a>
                                </div>
                            </div>
                        </div>

                @elseif(auth()->user()->hasRole('volunteer'))
                    <!-- Volunteer Dashboard Preview -->
                    @php
                        $volunteer = auth()->user()->volunteer;
                        $totalHours = $volunteer ? \App\Models\EventParticipation::where('Volunteer_ID', $volunteer->Volunteer_ID)->sum('Total_Hours') : 0;
                        $totalEvents = $volunteer ? $volunteer->events()->count() : 0;
                        $completedEvents = $volunteer ? $volunteer->events()->where('event.Status', 'Completed')->count() : 0;
                        $upcomingEventsCount = $volunteer ? $volunteer->events()->whereIn('event.Status', ['Upcoming', 'Ongoing'])->count() : 0;
                        $upcomingEventsList = $volunteer ? $volunteer->events()->whereIn('event.Status', ['Upcoming', 'Ongoing'])->orderBy('Start_Date')->take(3)->get() : collect();
                        $skillsCount = $volunteer ? $volunteer->skills()->count() : 0;
                        $availableEvents = \App\Models\Event::whereIn('Status', ['Upcoming', 'Ongoing'])->count();
                    @endphp

                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
                                <p class="text-gray-600 mt-1">{{ $upcomingEventsCount }} upcoming event{{ $upcomingEventsCount != 1 ? 's' : '' }} • {{ number_format($totalHours, 1) }} hours contributed</p>
                            </div>
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-4 gap-6 mb-8">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm text-gray-600">Total Hours</p>
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalHours, 1) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Hours volunteered</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm text-gray-600">Events Joined</p>
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalEvents }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $completedEvents }} completed</p>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm text-gray-600">Upcoming</p>
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $upcomingEventsCount }}</p>
                                <p class="text-xs text-gray-500 mt-1">Events registered</p>
                            </div>
                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm text-gray-600">My Skills</p>
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $skillsCount }}</p>
                                <p class="text-xs text-gray-500 mt-1">Skills registered</p>
                            </div>
                        </div>

                        @if($upcomingEventsList->count() > 0)
                            <div class="mb-8">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">Your Upcoming Events</h3>
                                <div class="space-y-3">
                                    @foreach($upcomingEventsList as $event)
                                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg hover:shadow-md transition-shadow">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">{{ $event->Title }}</h4>
                                                <p class="text-sm text-gray-600">{{ $event->Start_Date->format('M d, Y') }} • {{ $event->Location }}</p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $event->Status === 'Upcoming' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $event->Status }}
                                                </span>
                                                <a href="{{ route('volunteer.events.show', $event->Event_ID) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                                    View →
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($availableEvents > 0)
                            <div class="mb-8 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 mb-1">{{ $availableEvents }} Events Available</h4>
                                        <p class="text-sm text-gray-600">Discover new opportunities to make a difference in your community</p>
                                    </div>
                                    <a href="{{ route('volunteer.events.browse') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                        Browse Events
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold text-gray-900">Quick Actions</h3>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('volunteer.dashboard') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                    Full Dashboard
                                </a>
                                <a href="{{ route('volunteer.events.browse') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                    Find Events
                                </a>
                                <a href="{{ route('volunteer.schedule') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                    My Schedule
                                </a>
                                <a href="{{ route('volunteer.skills.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                    Manage Skills
                                </a>
                                <a href="{{ route('profile.edit') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                    My Profile
                                </a>
                            </div>
                        </div>
                    </div>

            @elseif(auth()->user()->hasRole('organizer'))
                <!-- Organizer Dashboard Preview -->
                @php
                    $organization = auth()->user()->organization;
                    $activeCampaignsCount = $organization->campaigns()->where('Status', 'Active')->count();
                    $pendingCampaignsCount = $organization->campaigns()->where('Status', 'Pending')->count();
                    $totalCampaigns = $organization->campaigns()->count();
                    $totalRaised = $organization->campaigns()->sum('Collected_Amount');
                    $upcomingEvents = $organization->events()->where('Status', 'Upcoming')->count();
                    $totalEvents = $organization->events()->count();

                    // Get campaign and event IDs for this organizer
                    $campaignIds = $organization->campaigns()->pluck('Campaign_ID')->toArray();
                    $eventIds = $organization->events()->pluck('Event_ID')->toArray();

                    // Count total volunteers across all organizer's events
                    $totalVolunteers = \App\Models\EventParticipation::whereIn('Event_ID', $eventIds)->distinct('Volunteer_ID')->count('Volunteer_ID');

                    // Count total donations across all organizer's campaigns
                    $totalDonations = \App\Models\Donation::whereIn('Campaign_ID', $campaignIds)->where('Payment_Status', 'Completed')->count();

                    // Count pending recipient suggestions for this organizer's campaigns
                    $pendingSuggestions = \App\Models\CampaignRecipientSuggestion::whereIn('Campaign_ID', $campaignIds)
                        ->where('Status', 'Pending')
                        ->count();

                    // Count total suggestions (all statuses)
                    $totalSuggestions = \App\Models\CampaignRecipientSuggestion::whereIn('Campaign_ID', $campaignIds)->count();

                    // Get active campaigns and recent events for display
                    $activeCampaignsList = $organization->campaigns()->where('Status', 'Active')->orderBy('created_at', 'desc')->take(3)->get();
                    $recentEvents = $organization->events()->whereIn('Status', ['Upcoming', 'Ongoing'])->orderBy('Start_Date')->take(3)->get();
                @endphp

                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
                            <p class="text-gray-600 mt-1">
                                {{ $activeCampaignsCount }} active campaign{{ $activeCampaignsCount != 1 ? 's' : '' }} •
                                {{ $upcomingEvents }} upcoming event{{ $upcomingEvents != 1 ? 's' : '' }}
                                @if($pendingSuggestions > 0)
                                    • <span class="text-yellow-600 font-medium">{{ $pendingSuggestions }} pending suggestion{{ $pendingSuggestions != 1 ? 's' : '' }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Total Raised</p>
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">RM {{ number_format($totalRaised, 2) }}</p>
                            <p class="text-xs text-gray-500 mt-1">From {{ $totalDonations }} donation{{ $totalDonations != 1 ? 's' : '' }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Campaigns</p>
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $activeCampaignsCount }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $totalCampaigns }} total • {{ $pendingCampaignsCount }} pending</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Events</p>
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $upcomingEvents }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $totalEvents }} total events</p>
                        </div>
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Volunteers</p>
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalVolunteers }}</p>
                            <p class="text-xs text-gray-500 mt-1">Total participants</p>
                        </div>
                    </div>

                    @if($pendingSuggestions > 0)
                        <div class="mb-8 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-semibold text-yellow-900">Recipient Suggestions Pending Review</h4>
                                        <p class="text-sm text-yellow-800 mt-1">
                                            You have <strong>{{ $pendingSuggestions }}</strong> pending recipient suggestion{{ $pendingSuggestions != 1 ? 's' : '' }} from administrators across your campaigns.
                                            Review and accept suitable recipients for fund allocation.
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('campaigns.index') }}" class="ml-4 flex-shrink-0 inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm font-medium whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Review Suggestions
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($activeCampaignsList->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Active Campaigns</h3>
                            <div class="space-y-3">
                                @foreach($activeCampaignsList as $campaign)
                                    @php
                                        $campaignPercentage = $campaign->Goal_Amount > 0 ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100 : 0;
                                        $campaignDonations = \App\Models\Donation::where('Campaign_ID', $campaign->Campaign_ID)->where('Payment_Status', 'Completed')->count();
                                        $campaignSuggestions = \App\Models\CampaignRecipientSuggestion::where('Campaign_ID', $campaign->Campaign_ID)->where('Status', 'Pending')->count();
                                    @endphp
                                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-4 rounded-lg hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-semibold text-gray-900">{{ $campaign->Title }}</h4>
                                                    @if($campaignSuggestions > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            {{ $campaignSuggestions }} suggestion{{ $campaignSuggestions != 1 ? 's' : '' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600">{{ $campaignDonations }} donation{{ $campaignDonations != 1 ? 's' : '' }} • Created {{ $campaign->created_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="ml-4 flex gap-2">
                                                @if($campaignSuggestions > 0)
                                                    <a href="{{ route('campaigns.suggestions', $campaign->Campaign_ID) }}" class="inline-flex items-center bg-yellow-600 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-yellow-700 transition-colors whitespace-nowrap">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                                        </svg>
                                                        View
                                                    </a>
                                                @endif
                                                <a href="{{ route('recipients.allocate', $campaign->Campaign_ID) }}" class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors whitespace-nowrap">
                                                    Allocate Funds
                                                </a>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>RM {{ number_format($campaign->Collected_Amount, 2) }} raised</span>
                                                <span>{{ number_format($campaignPercentage, 1) }}% of RM {{ number_format($campaign->Goal_Amount, 2) }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ min($campaignPercentage, 100) }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($recentEvents->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Events</h3>
                            <div class="grid md:grid-cols-3 gap-4">
                                @foreach($recentEvents as $event)
                                    @php
                                        $eventVolunteers = \App\Models\EventParticipation::where('Event_ID', $event->Event_ID)->count();
                                    @endphp
                                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-4 rounded-lg hover:shadow-md transition-shadow">
                                        <h4 class="font-semibold text-gray-900 mb-2">{{ Str::limit($event->Title, 40) }}</h4>
                                        <div class="space-y-1 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $event->Start_Date->format('M d, Y') }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                {{ $eventVolunteers }} volunteer{{ $eventVolunteers != 1 ? 's' : '' }}
                                            </div>
                                        </div>
                                        <a href="{{ route('events.show', $event->Event_ID) }}" class="mt-3 block text-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                            View Details →
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-gray-900">Quick Actions</h3>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('campaigns.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                Create Campaign
                            </a>
                            <a href="{{ route('events.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                Create Event
                            </a>
                            <a href="{{ route('campaigns.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                View All Campaigns
                            </a>
                            @if($totalSuggestions > 0)
                                <a href="{{ route('campaigns.index') }}" class="{{ $pendingSuggestions > 0 ? 'relative bg-yellow-600 text-white hover:bg-yellow-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} px-6 py-2 rounded-lg transition-colors inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    Recipient Suggestions
                                    @if($pendingSuggestions > 0)
                                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">{{ $pendingSuggestions }}</span>
                                    @endif
                                </a>
                            @endif
                            <a href="{{ route('events.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                View All Events
                            </a>
                            <a href="{{ route('organizer.allocations.all') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                View Allocations
                            </a>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->hasRole('public'))
                <!-- Public User Dashboard Preview -->
                @php
                    $activeCampaignsCount = \App\Models\Campaign::where('Status', 'Active')->count();
                    $upcomingEventsCount = \App\Models\Event::whereIn('Status', ['Upcoming', 'Ongoing'])->count();
                    $totalOrganizations = \App\Models\Organization::count();
                    $featuredCampaigns = \App\Models\Campaign::where('Status', 'Active')->orderBy('created_at', 'desc')->take(3)->get();
                    $upcomingEvents = \App\Models\Event::whereIn('Status', ['Upcoming', 'Ongoing'])->orderBy('Start_Date')->take(3)->get();
                @endphp

                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h2>
                            <p class="text-gray-600 mt-1">Discover {{ $activeCampaignsCount }} active campaigns and {{ $upcomingEventsCount }} upcoming events</p>
                        </div>
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Platform Stats -->
                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Active Campaigns</p>
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $activeCampaignsCount }}</p>
                            <p class="text-xs text-gray-500 mt-1">Fundraising campaigns</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Upcoming Events</p>
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $upcomingEventsCount }}</p>
                            <p class="text-xs text-gray-500 mt-1">Volunteer opportunities</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Organizations</p>
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalOrganizations }}</p>
                            <p class="text-xs text-gray-500 mt-1">Making a difference</p>
                        </div>
                    </div>

                    @if($featuredCampaigns->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Featured Campaigns</h3>
                            <div class="grid md:grid-cols-3 gap-4">
                                @foreach($featuredCampaigns as $campaign)
                                    @php
                                        $percentage = $campaign->Goal_Amount > 0 ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100 : 0;
                                    @endphp
                                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-lg hover:shadow-md transition-shadow">
                                        <h4 class="font-semibold text-gray-900 mb-2">{{ Str::limit($campaign->Title, 40) }}</h4>
                                        <div class="mb-3">
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>Progress</span>
                                                <span>{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-600 mt-1">
                                                <span>RM {{ number_format($campaign->Collected_Amount, 0) }}</span>
                                                <span>RM {{ number_format($campaign->Goal_Amount, 0) }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('public.campaigns.show', $campaign->Campaign_ID) }}" class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                            View Campaign
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($upcomingEvents->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Events</h3>
                            <div class="space-y-3">
                                @foreach($upcomingEvents as $event)
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg hover:shadow-md transition-shadow">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $event->Title }}</h4>
                                            <div class="flex items-center gap-4 mt-1">
                                                <p class="text-sm text-gray-600 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $event->Start_Date->format('M d, Y') }}
                                                </p>
                                                <p class="text-sm text-gray-600 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    </svg>
                                                    {{ Str::limit($event->Location, 30) }}
                                                </p>
                                            </div>
                                        </div>
                                        <a href="{{ route('public.events.show', $event->Event_ID) }}" class="ml-4 text-blue-600 hover:text-blue-800 font-medium text-sm whitespace-nowrap">
                                            View Details →
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Call to Action -->
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-lg border border-indigo-200">
                            <h4 class="font-semibold text-gray-900 mb-2">Help Those in Need</h4>
                            <p class="text-sm text-gray-600 mb-4">Suggest recipients who are in need of donations and support from our community</p>
                            <a href="{{ route('public.recipients.create') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                Register Recipients
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-gray-900 mb-2">View All Recipients</h4>
                            <p class="text-sm text-gray-600 mb-4">See all approved recipients and learn about their stories and needs</p>
                            <a href="{{ route('public.recipients.index') }}" class="inline-flex items-center text-green-600 hover:text-green-700 font-medium text-sm">
                                Browse Recipients
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-gray-900">Explore CharityHub</h3>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('public.campaigns.browse') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                Browse All Campaigns
                            </a>
                            <a href="{{ route('public.events.browse') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                View All Events
                            </a>
                            <a href="{{ route('public.recipients.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                View Recipients
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-gray-600">
                <p>&copy; {{ date('Y') }} CharityHub. Making a difference together.</p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
