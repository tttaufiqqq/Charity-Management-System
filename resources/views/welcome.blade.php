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
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
                            <p class="text-gray-600 mt-1">Thank you for your generous support</p>
                        </div>
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-green-50 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Total Donated</p>
                            <p class="text-2xl font-bold text-gray-900">RM {{ number_format(auth()->user()->donor->Total_Donated ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Active Campaigns</p>
                            <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->donor->donations()->count() ?? 0 }}</p>
                        </div>
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Impact Score</p>
                            <p class="text-2xl font-bold text-gray-900">A+</p>
                        </div>
                    </div>

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
                        </div>
                    </div>
                </div>

                @elseif(auth()->user()->hasRole('volunteer'))
                    <!-- Volunteer Dashboard Preview -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
                                <p class="text-gray-600 mt-1">Ready to make a difference today?</p>
                            </div>
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>

                        @php
                            $volunteer = auth()->user()->volunteer;
                            $totalHours = $volunteer ? \App\Models\EventParticipation::where('Volunteer_ID', $volunteer->Volunteer_ID)->sum('Total_Hours') : 0;
                            $totalEvents = $volunteer ? $volunteer->events()->count() : 0;
                            $upcomingEvents = $volunteer ? $volunteer->events()->whereIn('event.Status', ['Upcoming', 'Ongoing'])->count() : 0;
                        @endphp

                        <div class="grid md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Total Hours</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalHours }} hrs</p>
                            </div>
                            <div class="bg-green-50 p-6 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Events Attended</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalEvents }}</p>
                            </div>
                            <div class="bg-purple-50 p-6 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Upcoming Events</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $upcomingEvents }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold text-gray-900">Quick Actions</h3>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('volunteer.events.browse') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                    Find Events
                                </a>
                                <a href="{{ route('volunteer.schedule') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                    My Schedule
                                </a>
                                <a href="{{ route('volunteer.profile') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                    My Profile
                                </a>
                            </div>
                        </div>
                    </div>

            @elseif(auth()->user()->hasRole('organizer'))
                <!-- Organizer Dashboard Preview -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
                            <p class="text-gray-600 mt-1">Manage your campaigns and events</p>
                        </div>
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Active Campaigns</p>
                            <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->organization->campaigns()->where('Status', 'Active')->count() ?? 0 }}</p>
                        </div>
                        <div class="bg-green-50 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Total Raised</p>
                            <p class="text-2xl font-bold text-gray-900">RM {{ number_format(auth()->user()->organization->campaigns()->sum('Collected_Amount') ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Upcoming Events</p>
                            <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->organization->events()->where('Status', 'Upcoming')->count() ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-gray-900">Quick Actions</h3>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('campaigns.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                Create Campaign
                            </a>
                            <a href="{{ route('campaigns.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                View All Campaigns
                            </a>
                            <a href="{{ route('events.create') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                Create Events
                            </a>
                            <a href="{{ route('dashboard') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                View Analytics
                            </a>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->hasRole('public'))
                <!-- Public User Dashboard Preview -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h2>
                            <p class="text-gray-600 mt-1">Explore campaigns and events</p>
                        </div>
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Explore CharityHub</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <a href="{{ route('public.campaigns.browse') }}" class="block p-6 bg-gradient-to-r from-green-50 to-green-100 rounded-lg hover:shadow-md transition-shadow">
                                    <h4 class="font-semibold text-gray-900 mb-2">Browse Campaigns</h4>
                                    <p class="text-sm text-gray-600">Discover active campaigns and causes</p>
                                </a>
                                <a href="{{ route('public.events.browse') }}" class="block p-6 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg hover:shadow-md transition-shadow">
                                    <h4 class="font-semibold text-gray-900 mb-2">Find Events</h4>
                                    <p class="text-sm text-gray-600">See upcoming charity events</p>
                                </a>
                            </div>
                        </div>

                        <div class="bg-indigo-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-2">Suggest recipients to receive donation.</h4>
                            <p class="text-sm text-gray-600 mb-4">Help is find those who are in need of donations by suggesting them below.</p>
                            <div class="flex gap-3">
                                <a href="{{ route('public.recipients.create') }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                    Register recipients
                                </a>
                            </div>
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
