<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recipient Details - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-600">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Recipient Detail Card -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Header with Status -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $recipient->Name }}</h1>
                        <p class="text-indigo-100">Registered on {{ \Carbon\Carbon::parse($recipient->created_at)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                            @if($recipient->Status === 'Approved') bg-green-100 text-green-800
                            @elseif($recipient->Status === 'Pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $recipient->Status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recipient Information -->
            <div class="px-8 py-8">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <!-- Contact Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Contact Number</label>
                                <p class="text-gray-900 font-medium">{{ $recipient->Contact }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Address</label>
                                <p class="text-gray-900">{{ $recipient->Address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Details -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Registration Details</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Registered By</label>
                                <p class="text-gray-900 font-medium">{{ $recipient->publicUser->user->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $recipient->publicUser->user->email ?? '' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Registration Date</label>
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($recipient->created_at)->format('F d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Need Description -->
                <div class="border-t border-gray-200 pt-8 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Description of Need</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <p class="text-gray-700 whitespace-pre-line">{{ $recipient->Need_Description }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if(auth()->check() && auth()->user()->publicUser && auth()->user()->publicUser->Public_ID === $recipient->Public_ID)
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex gap-3">
                            @if($recipient->Status === 'Pending')
                                <a href="{{ route('public.recipients.edit', $recipient->Recipient_ID) }}"
                                   class="flex-1 text-center bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Registration
                                </a>
                            @else
                                <div class="flex-1 bg-gray-100 text-gray-500 px-6 py-3 rounded-lg font-medium text-center">
                                    @if($recipient->Status === 'Approved')
                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Registration Approved
                                    @else
                                        Registration {{ $recipient->Status }}
                                    @endif
                                </div>
                            @endif

                            @if($recipient->Status === 'Pending')
                                <button onclick="confirmDelete()"
                                        class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Cancel Registration
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Status Information Box -->
                @if($recipient->Status === 'Pending')
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-yellow-800">
                                    <strong>Pending Review:</strong> This registration is awaiting approval from our team.
                                    You will be notified once it has been reviewed.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($recipient->Status === 'Approved')
                    <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-green-800">
                                    <strong>Approved:</strong> This recipient has been approved and may receive donations through campaigns.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($recipient->Status === 'Rejected')
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-red-800">
                                    <strong>Rejected:</strong> This registration was not approved. Please contact support for more information.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
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

<!-- Delete Confirmation Modal -->
@if(auth()->check() && auth()->user()->publicUser && auth()->user()->publicUser->Public_ID === $recipient->Public_ID && $recipient->Status === 'Pending')
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Cancel Registration</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <p class="text-gray-600 mb-6">Are you sure you want to cancel this recipient registration? This action cannot be undone.</p>

            <form method="POST" action="{{ route('public.recipients.destroy', $recipient->Recipient_ID) }}">
                @csrf
                @method('DELETE')

                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                        Keep Registration
                    </button>
                    <button type="submit"
                            class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors">
                        Yes, Cancel It
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
@endif

</body>
</html>
