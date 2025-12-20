<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Profile - CharityHub</title>
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

        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-cyan-600 to-blue-600 rounded-lg shadow-lg p-8 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-4xl font-bold text-cyan-600">
                            {{ strtoupper(substr($publicProfile->user->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="text-white">
                        <h1 class="text-3xl font-bold mb-2">{{ $publicProfile->user->name }}</h1>
                        <p class="text-cyan-100 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ $publicProfile->user->email }}
                        </p>
                        @if($publicProfile->City || $publicProfile->State)
                            <p class="text-cyan-100 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $publicProfile->City }}{{ $publicProfile->State ? ', ' . $publicProfile->State : '' }}
                            </p>
                        @endif
                        <span class="inline-block mt-2 px-3 py-1 bg-white/20 text-white rounded-full text-sm font-medium">Community Member</span>
                    </div>
                </div>
                <a href="{{ route('profile.public.edit') }}"
                   class="px-6 py-3 bg-white text-cyan-600 rounded-lg hover:bg-cyan-50 transition-colors font-medium shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- Recipient Application Status -->
        @if($isRecipient)
            <div class="mb-6">
                @if($recipientStatus === 'Pending')
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6">
                        <div class="flex items-start gap-4">
                            <div class="bg-yellow-500 rounded-full p-3">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-yellow-900 mb-2">Recipient Application Pending</h3>
                                <p class="text-yellow-800 mb-3">Your application to become a recipient is currently under review by our administrators.</p>
                                <div class="bg-white rounded-lg p-4">
                                    <dl class="grid sm:grid-cols-2 gap-4">
                                        @if($recipient->Recipient_Name)
                                            <div>
                                                <dt class="text-xs font-semibold text-gray-500 uppercase">Name</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $recipient->Recipient_Name }}</dd>
                                            </div>
                                        @endif
                                        @if($recipient->Category)
                                            <div>
                                                <dt class="text-xs font-semibold text-gray-500 uppercase">Category</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $recipient->Category }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($recipientStatus === 'Approved')
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6">
                        <div class="flex items-start gap-4">
                            <div class="bg-green-500 rounded-full p-3">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-green-900 mb-2">Approved Recipient</h3>
                                <p class="text-green-800 mb-3">Your application has been approved! You are now eligible to receive donations from campaigns.</p>
                                <div class="bg-white rounded-lg p-4">
                                    <dl class="grid sm:grid-cols-2 gap-4">
                                        @if($recipient->Recipient_Name)
                                            <div>
                                                <dt class="text-xs font-semibold text-gray-500 uppercase">Name</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $recipient->Recipient_Name }}</dd>
                                            </div>
                                        @endif
                                        @if($recipient->Category)
                                            <div>
                                                <dt class="text-xs font-semibold text-gray-500 uppercase">Category</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $recipient->Category }}</dd>
                                            </div>
                                        @endif
                                        @if($recipient->Needs_Description)
                                            <div class="sm:col-span-2">
                                                <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Needs</dt>
                                                <dd class="text-sm text-gray-700">{{ $recipient->Needs_Description }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="bg-blue-500 rounded-full p-3">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-blue-900 mb-2">Register a Recipient</h3>
                        <p class="text-blue-800 mb-4">If you know someone who need assistance, you can regsiter them to become a recipient and receive donations from our campaigns.</p>
                        <a href="{{ route('public.recipients.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Register Them Now
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Personal Information
            </h2>
            <dl class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @if($publicProfile->Full_Name)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Full Name</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $publicProfile->Full_Name }}</dd>
                    </div>
                @endif
                @if($publicProfile->Phone_Num)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Phone Number</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $publicProfile->Phone_Num }}</dd>
                    </div>
                @endif
                @if($publicProfile->Gender)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Gender</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $publicProfile->Gender }}</dd>
                    </div>
                @endif
                @if($publicProfile->Address)
                    <div class="bg-gray-50 p-4 rounded-lg sm:col-span-2 lg:col-span-1">
                        <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Address</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $publicProfile->Address }}</dd>
                    </div>
                @endif
                @if($publicProfile->City)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">City</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $publicProfile->City }}</dd>
                    </div>
                @endif
                @if($publicProfile->State)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">State</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $publicProfile->State }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ route('campaigns.browse') }}" class="flex items-center gap-3 p-4 bg-cyan-50 rounded-lg hover:bg-cyan-100 transition-colors border border-cyan-200 group">
                <div class="bg-cyan-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-cyan-700">Browse Campaigns</span>
            </a>
            <a href="{{ route('public.events.browse') }}" class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200 group">
                <div class="bg-blue-600 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 group-hover:text-blue-700">Browse Events</span>
            </a>
            @if(!$isRecipient)
                <a href="{{ route('public.recipients.create') }}" class="flex items-center gap-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors border border-green-200 group">
                    <div class="bg-green-600 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900 group-hover:text-green-700">Register Recipient</span>
                </a>
            @endif
        </div>
    </main>
</div>
</body>
</html>
