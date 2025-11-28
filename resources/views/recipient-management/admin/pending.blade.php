<!-- resources/views/recipient-management/admin/pending.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pending Recipients - Admin - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">CharityHub</a>
                    <span class="ml-3 px-2 py-1 bg-red-100 text-red-600 text-xs font-semibold rounded">ADMIN</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                    <a href="{{ route('admin.recipients.all') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">All Recipients</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pending Recipient Approvals</h1>
            <p class="text-gray-600 mt-1">Review and approve recipient registrations</p>
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

        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('admin.recipients.pending') }}" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, contact, or address..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.recipients.pending') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Recipients List -->
        @if($recipients->count() > 0)
            <div class="space-y-6">
                @foreach($recipients as $recipient)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-xl font-semibold text-gray-900">{{ $recipient->Name }}</h3>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">Pending</span>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        Registered by: <strong>{{ $recipient->publicProfile->user->name }}</strong>
                                        ({{ $recipient->publicProfile->user->email ?? 'N/A' }})
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Submitted: {{ \Carbon\Carbon::parse($recipient->created_at)->format('M d, Y h:i A') }}
                                        ({{ \Carbon\Carbon::parse($recipient->created_at)->diffForHumans() }})
                                    </p>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6 mb-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Contact Information</h4>
                                    <p class="text-sm text-gray-600"><strong>Phone:</strong> {{ $recipient->Contact }}</p>
                                    <p class="text-sm text-gray-600"><strong>Address:</strong> {{ $recipient->Address }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Description of Need</h4>
                                    <p class="text-sm text-gray-600">{{ Str::limit($recipient->Need_Description, 150) }}</p>
                                </div>
                            </div>

                            <div class="flex gap-3 pt-4 border-t border-gray-200">
                                <a href="{{ route('admin.recipients.show', $recipient->Recipient_ID) }}"
                                   class="flex-1 text-center bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                                    View Full Details
                                </a>
                                <form method="POST" action="{{ route('admin.recipients.approve', $recipient->Recipient_ID) }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 transition-colors">
                                        ✓ Approve
                                    </button>
                                </form>
                                <button onclick="openRejectModal({{ $recipient->Recipient_ID }}, '{{ $recipient->Name }}')"
                                        class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors">
                                    ✗ Reject
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $recipients->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No pending recipients</h3>
                <p class="text-gray-600">All recipient registrations have been reviewed</p>
            </div>
        @endif
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

<!-- Reject Confirmation Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Reject Recipient</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <p class="text-gray-600 mb-6">Are you sure you want to reject <strong id="reject_recipient_name"></strong>? This action can be reversed later.</p>

        <form id="rejectForm" method="POST" action="">
            @csrf

            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(recipientId, recipientName) {
        document.getElementById('reject_recipient_name').textContent = recipientName;
        document.getElementById('rejectForm').action = `/admin/recipients/${recipientId}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeRejectModal();
        }
    });
</script>

</body>
</html>
