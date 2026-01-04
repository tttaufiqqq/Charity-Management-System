<!-- resources/views/recipient-management/admin/suggest-recipients.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suggest Recipients - {{ $campaign->Title }} - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Campaign Info -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $campaign->Title }}</h1>
            <p class="text-gray-600 mb-4">{{ $campaign->Description }}</p>
            <div class="flex items-center gap-4 text-sm text-gray-600">
                <span><strong>Organizer:</strong> {{ $campaign->organization->user->name ?? 'N/A' }}</span>
                <span><strong>Goal:</strong> RM {{ number_format($campaign->Goal_Amount, 2) }}</span>
                <span><strong>Collected:</strong> RM {{ number_format($campaign->Collected_Amount, 2) }}</span>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-600">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Existing Suggestions Section -->
        @if($suggestions->count() > 0)
            <div class="mb-8">
                <!-- Section Header -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-8 w-1 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900">Current Suggestions</h2>
                        <p class="text-sm text-gray-600 mt-1">Recipients already suggested for this campaign ({{ $suggestions->count() }})</p>
                    </div>
                </div>

                <!-- Suggestions List -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-md border-2 border-blue-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-100 to-indigo-100 border-b-2 border-blue-200">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-500 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-blue-900">Already Suggested Recipients</h3>
                                <p class="text-xs text-blue-700 mt-0.5">These recipients have been recommended to the campaign organizer</p>
                            </div>
                        </div>
                    </div>
                    <div class="divide-y divide-blue-200 bg-white">
                        @foreach($suggestions as $suggestion)
                            <div class="px-6 py-5 hover:bg-blue-50/50 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $suggestion->recipient->Name }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    Suggested by {{ $suggestion->suggestedBy->name }} on {{ \Carbon\Carbon::parse($suggestion->created_at)->format('M d, Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-700 mb-2 pl-13">
                                            <strong class="text-gray-900">Need:</strong> {{ $suggestion->recipient->Need_Description }}
                                        </div>
                                        @if($suggestion->Suggestion_Reason)
                                            <div class="mt-2 p-3 bg-blue-50 border-l-4 border-blue-400 rounded-r pl-13">
                                                <p class="text-sm text-blue-900">
                                                    <strong class="font-semibold">Suggestion Reason:</strong> {{ $suggestion->Suggestion_Reason }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold shadow-sm
                                            @if($suggestion->Status === 'Pending') bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-300
                                            @elseif($suggestion->Status === 'Accepted') bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-300
                                            @else bg-gradient-to-r from-red-100 to-rose-100 text-red-800 border border-red-300
                                            @endif">
                                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                @if($suggestion->Status === 'Pending')
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                @elseif($suggestion->Status === 'Accepted')
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                @else
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                @endif
                                            </svg>
                                            {{ $suggestion->Status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Available Recipients Section -->
        <div class="mb-8">
            <!-- Section Header -->
            <div class="flex items-center gap-3 mb-4">
                <div class="h-8 w-1 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900">Available Recipients</h2>
                    <p class="text-sm text-gray-600 mt-1">New recipients you can suggest for this campaign ({{ $recipients->total() }})</p>
                </div>
            </div>

            @if($recipients->count() > 0)
                <!-- Recipients List -->
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl shadow-md border-2 border-emerald-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-emerald-100 to-green-100 border-b-2 border-emerald-200">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-500 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-emerald-900">New Recipients to Suggest</h3>
                                <p class="text-xs text-emerald-700 mt-0.5">Select and add recipients to recommend for this campaign</p>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-emerald-200 bg-white">
                        @foreach($recipients as $recipient)
                            <div class="px-6 py-5 hover:bg-emerald-50/50 transition-colors">
                                <form method="POST" action="{{ route('admin.campaigns.suggest-recipients.store', $campaign->Campaign_ID) }}" class="flex gap-6">
                                    @csrf
                                    <input type="hidden" name="recipient_id" value="{{ $recipient->Recipient_ID }}">

                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-green-200 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900">{{ $recipient->Name }}</div>
                                                <div class="text-xs text-gray-600 mt-0.5 flex items-center gap-3">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        {{ $recipient->Address }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                        </svg>
                                                        {{ $recipient->Contact }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pl-13 mb-3">
                                            <div class="p-3 bg-emerald-50 border-l-4 border-emerald-400 rounded-r">
                                                <p class="text-sm text-gray-900">
                                                    <strong class="font-semibold text-emerald-900">Need:</strong> {{ $recipient->Need_Description }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="pl-13">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Suggestion Reason (Optional)</label>
                                            <textarea name="suggestion_reason" rows="2"
                                                      placeholder="Explain why this recipient is suitable for this campaign..."
                                                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm placeholder-gray-400"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex items-start pt-2">
                                        <button type="submit"
                                                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-lg hover:from-emerald-700 hover:to-green-700 transition-all text-sm font-bold shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Suggest Recipient
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-emerald-50 border-t-2 border-emerald-200">
                    {{ $recipients->links() }}
                </div>
            @else
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl shadow-md border-2 border-emerald-200 overflow-hidden">
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-emerald-100 to-green-200 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">All Recipients Suggested</h3>
                        <p class="text-gray-600 max-w-md mx-auto">All approved recipients have already been suggested for this campaign. Check the "Current Suggestions" section above to see the status of your suggestions.</p>
                    </div>
                </div>
            @endif
        </div>
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
