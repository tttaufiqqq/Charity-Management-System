<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">CharityHub Admin</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.campaigns.pending') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Campaigns</a>
                    <a href="{{ route('admin.events.pending') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Events</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistics -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Pending Campaigns</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $pendingCampaigns }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.campaigns.pending') }}" class="mt-4 block text-sm text-indigo-600 hover:text-indigo-800">
                    Review →
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Pending Events</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $pendingEvents }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.events.pending') }}" class="mt-4 block text-sm text-indigo-600 hover:text-indigo-800">
                    Review →
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Pending Recipients</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $pendingRecipients }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Pending Campaigns -->
        @if($recentCampaigns->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Pending Campaigns</h2>
                    <a href="{{ route('admin.campaigns.pending') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All →</a>
                </div>
                <div class="space-y-3">
                    @foreach($recentCampaigns as $campaign)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $campaign->Title }}</h3>
                                <p class="text-sm text-gray-600">By {{ $campaign->organization->user->name }}</p>
                                <p class="text-sm text-gray-500">Goal: RM {{ number_format($campaign->Goal_Amount, 2) }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="{{ route('admin.campaigns.approve', $campaign->Campaign_ID) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.campaigns.reject', $campaign->Campaign_ID) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recent Pending Events -->
        @if($recentEvents->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Pending Events</h2>
                    <a href="{{ route('admin.events.pending') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All →</a>
                </div>
                <div class="space-y-3">
                    @foreach($recentEvents as $event)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $event->Title }}</h3>
                                <p class="text-sm text-gray-600">By {{ $event->organization->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $event->Start_Date->format('M d, Y') }} - {{ $event->Location }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="{{ route('admin.events.approve', $event->Event_ID) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.events.reject', $event->Event_ID) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($recentCampaigns->count() == 0 && $recentEvents->count() == 0)
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">All caught up!</h3>
                <p class="mt-1 text-sm text-gray-500">No pending items require your attention.</p>
            </div>
        @endif
    </main>
</div>
</body>
</html>
