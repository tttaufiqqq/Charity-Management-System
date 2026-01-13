<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - CharityHub</title>
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

        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-8 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-14 h-14 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="text-white">
                        <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                        <p class="text-purple-100 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ $user->email }}
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-block px-3 py-1 bg-white/20 text-white rounded-full text-sm font-medium">Administrator</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Platform users</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Campaigns</p>
                        <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Campaign::count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">All campaigns</p>
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
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Events</p>
                        <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Event::count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">All events</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Pending Approvals</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ \App\Models\Campaign::where('Status', 'Pending')->count() +
                               \App\Models\Event::where('Status', 'Pending')->count() +
                               \App\Models\Recipient::where('Status', 'Pending')->count() }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Requires action</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Account Information
            </h2>
            <dl class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Full Name</dt>
                    <dd class="text-base font-medium text-gray-900">{{ $user->name }}</dd>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Email Address</dt>
                    <dd class="text-base font-medium text-gray-900">{{ $user->email }}</dd>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Account Created</dt>
                    <dd class="text-base font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                    <dd class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</dd>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Last Updated</dt>
                    <dd class="text-base font-medium text-gray-900">{{ $user->updated_at->format('M d, Y') }}</dd>
                    <dd class="text-xs text-gray-500 mt-1">{{ $user->updated_at->diffForHumans() }}</dd>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Role</dt>
                    <dd class="text-base font-medium text-gray-900">
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                            {{ $user->roles->first()?->name ?? 'No role assigned' }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.manage.users') }}" class="flex items-center gap-3 p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors border border-purple-200 group">
                <div class="bg-purple-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-purple-700">Manage Users</span>
            </a>
            <a href="{{ route('admin.campaigns.pending') }}" class="flex items-center gap-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors border border-green-200 group">
                <div class="bg-green-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-green-700">Pending Campaigns</span>
            </a>
            <a href="{{ route('admin.events.pending') }}" class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200 group">
                <div class="bg-blue-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-blue-700">Pending Events</span>
            </a>
            <a href="{{ route('admin.recipients.pending') }}" class="flex items-center gap-3 p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors border border-yellow-200 group">
                <div class="bg-yellow-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-yellow-700">Pending Recipients</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors border border-indigo-200 group">
                <div class="bg-indigo-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-indigo-700">Dashboard</span>
            </a>
            <a href="{{ route('admin.analytics.dashboard') }}" class="flex items-center gap-3 p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition-colors border border-pink-200 group">
                <div class="bg-pink-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-pink-700">Analytics</span>
            </a>
            <a href="{{ route('admin.recipients.all') }}" class="flex items-center gap-3 p-4 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors border border-teal-200 group">
                <div class="bg-teal-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-teal-700">All Recipients</span>
            </a>
            <a href="{{ route('campaigns.index') }}" class="flex items-center gap-3 p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors border border-emerald-200 group">
                <div class="bg-emerald-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-emerald-700">All Campaigns</span>
            </a>
        </div>
    </main>
</div>
</body>
</html>
