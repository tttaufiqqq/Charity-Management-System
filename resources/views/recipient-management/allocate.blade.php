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
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
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

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                        @php
                            $isHighlighted = session('highlight_recipient') == $recipient->Recipient_ID;
                        @endphp
                        <div class="p-6 hover:bg-gray-50 transition-all {{ $isHighlighted ? 'bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 animate-pulse' : '' }}"
                             id="recipient-{{ $recipient->Recipient_ID }}">
                            @if($isHighlighted)
                                <div class="mb-3 flex items-center gap-2 text-green-700 font-medium text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>✨ Suggestion Accepted - Ready for Allocation</span>
                                </div>
                            @endif
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
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-lg p-12 text-center border-2 border-blue-200">
                <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-900 mb-3">No Recipients Suggested Yet</h3>
                <p class="text-gray-700 mb-6 max-w-2xl mx-auto">
                    Administrators have not suggested any recipients for this campaign yet.
                    You'll be able to allocate funds once admins review and suggest eligible recipients for <strong>{{ $campaign->Title }}</strong>.
                </p>

                <div class="bg-white rounded-lg p-6 max-w-xl mx-auto border border-blue-200">
                    <div class="flex items-start gap-3 text-left">
                        <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">What happens next?</h4>
                            <ul class="text-sm text-gray-600 space-y-1.5">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">1.</span>
                                    <span>Administrators will review eligible recipients in the system</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">2.</span>
                                    <span>They will suggest suitable recipients for your campaign</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">3.</span>
                                    <span>You'll be able to allocate collected funds to suggested recipients</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-gray-500 mt-6">
                    Need help? Contact your system administrator to request recipient suggestions for this campaign.
                </p>
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

            <div class="mb-2">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount to Allocate (RM)</label>
                <input type="number" name="amount" id="amount" required min="1" step="0.01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="0.00">
            </div>
            <p id="amount_error" class="text-sm text-red-600 mb-4 hidden"></p>

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
    let currentAvailableFunds = 0;

    function openAllocateModal(recipientId, recipientName, availableFunds) {
        currentAvailableFunds = availableFunds;
        document.getElementById('modal_recipient_id').value = recipientId;
        document.getElementById('modal_recipient_name').value = recipientName;
        document.getElementById('modal_available_funds').value = 'RM ' + availableFunds.toFixed(2);
        document.getElementById('allocateForm').action = '{{ route("recipients.allocate.store", $campaign->Campaign_ID) }}';
        document.getElementById('allocateModal').classList.remove('hidden');
        document.getElementById('amount').value = '';
        document.getElementById('amount').max = availableFunds;
        document.getElementById('amount_error').classList.add('hidden');
        document.getElementById('amount').focus();
    }

    function closeAllocateModal() {
        document.getElementById('allocateModal').classList.add('hidden');
        document.getElementById('amount_error').classList.add('hidden');
    }

    // Validate amount on input
    document.getElementById('amount').addEventListener('input', function() {
        const amount = parseFloat(this.value) || 0;
        const errorEl = document.getElementById('amount_error');
        const submitBtn = document.querySelector('#allocateForm button[type="submit"]');

        if (amount > currentAvailableFunds) {
            errorEl.textContent = 'Amount exceeds available funds (RM ' + currentAvailableFunds.toFixed(2) + ')';
            errorEl.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else if (amount <= 0) {
            errorEl.textContent = 'Amount must be greater than 0';
            errorEl.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            errorEl.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    });

    // Form submission validation
    document.getElementById('allocateForm').addEventListener('submit', function(e) {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        if (amount > currentAvailableFunds) {
            e.preventDefault();
            alert('Cannot allocate more than available funds (RM ' + currentAvailableFunds.toFixed(2) + ')');
            return false;
        }
        if (amount <= 0) {
            e.preventDefault();
            alert('Amount must be greater than 0');
            return false;
        }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAllocateModal();
        }
    });

    // Auto-scroll to highlighted recipient (from accepted suggestion)
    @if(session('highlight_recipient'))
    document.addEventListener('DOMContentLoaded', function() {
        const highlightedRecipient = document.getElementById('recipient-{{ session("highlight_recipient") }}');
        if (highlightedRecipient) {
            setTimeout(() => {
                highlightedRecipient.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                // Remove pulse animation after 3 seconds
                setTimeout(() => {
                    highlightedRecipient.classList.remove('animate-pulse');
                }, 3000);
            }, 500);
        }
    });
    @endif
</script>

</body>
</html>
