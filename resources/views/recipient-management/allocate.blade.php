<!-- resources/views/recipient-management/allocate.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Allocate Funds - CharityHub</title>
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
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
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
            <a href="{{ route('welcome') }}" class="text-indigo-600 hover:text-indigo-700 font-medium flex items-center mb-4">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Dashboard
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Allocate Funds to Recipients</h1>
                    <p class="text-gray-600 mt-1">Campaign: {{ $campaign->Title }}</p>
                </div>
                <a href="{{ route('recipients.allocations.history', $campaign->Campaign_ID) }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                    View History
                </a>
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

        <!-- Campaign Fund Summary -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-sm text-gray-600 mb-1">Total Collected</p>
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($campaign->Collected_Amount, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-sm text-gray-600 mb-1">Total Allocated</p>
                <p class="text-2xl font-bold text-orange-600">RM {{ number_format($totalAllocated, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-sm text-gray-600 mb-1">Remaining Funds</p>
                <p class="text-2xl font-bold text-green-600">RM {{ number_format($remainingAmount, 2) }}</p>
            </div>
        </div>

        <!-- Recipients List -->
        @if($recipients->count() > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Approved Recipients</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($recipients as $recipient)
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $recipient->Name }}</h3>
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <p class="text-sm text-gray-600">Contact: {{ $recipient->Contact }}</p>
                                            <p class="text-sm text-gray-600">Address: {{ Str::limit($recipient->Address, 50) }}</p>
                                        </div>
                                        <div>
                                            @php
                                                $allocation = $recipient->donationAllocations->first();
                                            @endphp
                                            @if($allocation)
                                                <p class="text-sm font-semibold text-green-600">
                                                    Already Allocated: RM {{ number_format($allocation->Amount_Allocated, 2) }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    Last: {{ \Carbon\Carbon::parse($allocation->Allocated_At)->format('M d, Y') }}
                                                </p>
                                            @else
                                                <p class="text-sm text-gray-500">No allocation yet</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-sm text-gray-700">{{ Str::limit($recipient->Need_Description, 150) }}</p>
                                    </div>
                                </div>
                                <div class="ml-6">
                                    <button onclick="openAllocateModal({{ $recipient->Recipient_ID }}, '{{ $recipient->Name }}', {{ $remainingAmount }})"
                                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors whitespace-nowrap">
                                        Allocate Funds
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $recipients->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No approved recipients</h3>
                <p class="text-gray-600">There are no approved recipients available for fund allocation at this time.</p>
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

<!-- Allocate Funds Modal -->
<div id="allocateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Allocate Funds</h3>
            <button onclick="closeAllocateModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="allocateForm" method="POST" action="">
            @csrf

            <input type="hidden" name="recipient_id" id="modal_recipient_id">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
                <input type="text" id="modal_recipient_name" readonly
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Available Funds</label>
                <input type="text" id="modal_available_funds" readonly
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 font-semibold text-green-600">
            </div>

            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount to Allocate (RM)</label>
                <input type="number" name="amount" id="amount" required min="1" step="0.01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="0.00">
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeAllocateModal()"
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Allocate
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAllocateModal(recipientId, recipientName, availableFunds) {
        document.getElementById('modal_recipient_id').value = recipientId;
        document.getElementById('modal_recipient_name').value = recipientName;
        document.getElementById('modal_available_funds').value = 'RM ' + availableFunds.toFixed(2);
        document.getElementById('allocateForm').action = '{{ route("recipients.allocate.store", $campaign->Campaign_ID) }}';
        document.getElementById('allocateModal').classList.remove('hidden');
        document.getElementById('amount').value = '';
        document.getElementById('amount').focus();
    }

    function closeAllocateModal() {
        document.getElementById('allocateModal').classList.add('hidden');
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAllocateModal();
        }
    });
</script>

</body>
</html>
