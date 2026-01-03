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

        <!-- Existing Suggestions -->
        @if($suggestions->count() > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Current Suggestions</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($suggestions as $suggestion)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $suggestion->recipient->Name }}</div>
                                <div class="text-sm text-gray-500">{{ $suggestion->recipient->Need_Description }}</div>
                                @if($suggestion->Suggestion_Reason)
                                    <div class="mt-2 text-sm text-gray-600">
                                        <strong>Reason:</strong> {{ $suggestion->Suggestion_Reason }}
                                    </div>
                                @endif
                                <div class="mt-1 text-xs text-gray-500">
                                    Suggested by {{ $suggestion->suggestedBy->name }} on {{ \Carbon\Carbon::parse($suggestion->created_at)->format('M d, Y') }}
                                </div>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($suggestion->Status === 'Pending') bg-yellow-100 text-yellow-800
                                    @elseif($suggestion->Status === 'Accepted') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $suggestion->Status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Available Recipients -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Available Recipients</h2>
                <p class="text-sm text-gray-600 mt-1">Select recipients to suggest for this campaign</p>
            </div>

            @if($recipients->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($recipients as $recipient)
                        <div class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.campaigns.suggest-recipients.store', $campaign->Campaign_ID) }}" class="flex gap-4">
                                @csrf
                                <input type="hidden" name="recipient_id" value="{{ $recipient->Recipient_ID }}">

                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $recipient->Name }}</div>
                                    <div class="text-sm text-gray-600 mt-1">{{ $recipient->Address }}</div>
                                    <div class="text-sm text-gray-700 mt-2">
                                        <strong>Need:</strong> {{ $recipient->Need_Description }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">Contact: {{ $recipient->Contact }}</div>

                                    <div class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Suggestion Reason (Optional)</label>
                                        <textarea name="suggestion_reason" rows="2"
                                                  placeholder="Why is this recipient suitable for this campaign?"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Suggest
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $recipients->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No recipients available</h3>
                    <p class="text-gray-600">All approved recipients have already been suggested for this campaign</p>
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
