<!-- ================================= -->
<!-- resources/views/recipient-management/admin/show.blade.php -->
<!-- ================================= -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recipient Details - Admin - CharityHub</title>
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
                    <a href="{{ route('admin.recipients.pending') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Pending</a>
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
    <main class="flex-grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.recipients.all') }}" class="text-indigo-600 hover:text-indigo-700 font-medium flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to All Recipients
            </a>
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

        <!-- Recipient Detail Card -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $recipient->Name }}</h1>
                        <p class="text-indigo-100">Registered: {{ \Carbon\Carbon::parse($recipient->created_at)->format('M d, Y') }}</p>
                    </div>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        @if($recipient->Status === 'Approved') bg-green-100 text-green-800
                        @elseif($recipient->Status === 'Pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $recipient->Status }}
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-8">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <!-- Recipient Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recipient Information</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Contact Number</label>
                                <p class="text-gray-900">{{ $recipient->Contact }}</p>
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
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Registered By</label>
                                <p class="text-gray-900">{{ $recipient->publicProfile->user->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $recipient->publicProfile->user->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Submission Date</label>
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($recipient->created_at)->format('F d, Y h:i A') }}</p>
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($recipient->created_at)->diffForHumans() }}</p>
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

                <!-- Allocations (if any) -->
                @if($recipient->donationAllocations->count() > 0)
                    <div class="border-t border-gray-200 pt-8 mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Received Allocations</h2>
                        <div class="bg-blue-50 rounded-lg p-6">
                            <p class="text-sm text-gray-600 mb-4">Total Received: <strong class="text-lg text-green-600">RM {{ number_format($recipient->allocations->sum('Amount_Allocated'), 2) }}</strong></p>
                            <div class="space-y-2">
                                @foreach($recipient->donationAllocations as $allocation)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-700">{{ $allocation->campaign->Title }}</span>
                                        <span class="font-semibold text-gray-900">RM {{ number_format($allocation->Amount_Allocated, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Admin Actions -->
                <div class="border-t border-gray-200 pt-6">
                    @if($recipient->Status === 'Pending')
                        <div class="flex gap-3">
                            <form method="POST" action="{{ route('admin.recipients.approve', $recipient->Recipient_ID) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                                    ✓ Approve Recipient
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.recipients.reject', $recipient->Recipient_ID) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                    ✗ Reject Recipient
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-3">Change Status:</p>
                            <form method="POST" action="{{ route('admin.recipients.status', $recipient->Recipient_ID) }}" class="flex gap-3">
                                @csrf
                                @method('PUT')
                                <select name="status" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="Pending" {{ $recipient->Status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Approved" {{ $recipient->Status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="Rejected" {{ $recipient->Status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                                    Update Status
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Delete Button -->
                    @if($recipient->donationAllocations->count() === 0)
                        <div class="mt-4">
                            <button onclick="confirmDelete()" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Delete Recipient
                            </button>
                        </div>
                    @endif
                </div>
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
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Delete Recipient</h3>
            <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <p class="text-gray-600 mb-6">Are you sure you want to permanently delete this recipient? This action cannot be undone.</p>

        <form method="POST" action="{{ route('admin.recipients.delete', $recipient->Recipient_ID) }}">
            @csrf
            @method('DELETE')

            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors">
                    Delete
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

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>

</body>
</html>
