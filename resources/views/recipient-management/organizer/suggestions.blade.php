<!-- resources/views/recipient-management/organizer/suggestions.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recipient Suggestions - {{ $campaign->Title }} - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col" x-data="suggestionManager()">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @php
            $totalAllocated = \App\Models\DonationAllocation::where('Campaign_ID', $campaign->Campaign_ID)->sum('Amount_Allocated');
            $availableFunds = $campaign->Collected_Amount - $totalAllocated;
        @endphp

        <!-- Campaign Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Recipient Suggestions for {{ $campaign->Title }}</h1>
                    <p class="text-gray-600 mb-4">{{ $campaign->Description }}</p>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <span><strong>Goal:</strong> RM {{ number_format($campaign->Goal_Amount, 2) }}</span>
                        <span><strong>Collected:</strong> RM {{ number_format($campaign->Collected_Amount, 2) }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($campaign->Status === 'Active') bg-green-100 text-green-800
                            @elseif($campaign->Status === 'Pending') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ $campaign->Status }}
                        </span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                    <p class="text-xs text-gray-600 mb-1">Available Funds</p>
                    <p class="text-2xl font-bold text-green-600">RM {{ number_format($availableFunds, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-lg">
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium">Admin Suggestions</p>
                    <p class="mt-1">Administrators have suggested these recipients. You can accept and allocate funds in one action, or just accept for later allocation.</p>
                </div>
            </div>
        </div>

        <!-- Suggestions List -->
        @if($suggestions->count() > 0)
            <div class="space-y-4">
                @foreach($suggestions as $suggestion)
                    @php
                        $existingAllocation = \App\Models\DonationAllocation::where('Campaign_ID', $campaign->Campaign_ID)
                            ->where('Recipient_ID', $suggestion->Recipient_ID)
                            ->first();
                    @endphp
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all hover:shadow-xl">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $suggestion->recipient->Name }}</h3>
                                    <div class="mt-1 flex items-center gap-3 text-sm text-gray-600">
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            {{ $suggestion->recipient->Contact }}
                                        </span>
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $suggestion->recipient->Address }}
                                        </span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium flex-shrink-0
                                    @if($suggestion->Status === 'Pending') bg-yellow-100 text-yellow-800 ring-1 ring-yellow-600/20
                                    @elseif($suggestion->Status === 'Accepted') bg-green-100 text-green-800 ring-1 ring-green-600/20
                                    @else bg-red-100 text-red-800 ring-1 ring-red-600/20
                                    @endif">
                                    {{ $suggestion->Status }}
                                </span>
                            </div>

                            <!-- Need Description -->
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 mb-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Need Description:
                                </h4>
                                <p class="text-sm text-gray-600">{{ $suggestion->recipient->Need_Description }}</p>
                            </div>

                            <!-- Admin's Reason -->
                            @if($suggestion->Suggestion_Reason)
                                <div class="mb-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-100">
                                    <h4 class="text-sm font-medium text-indigo-900 mb-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                        Admin's Suggestion Reason:
                                    </h4>
                                    <p class="text-sm text-indigo-800">{{ $suggestion->Suggestion_Reason }}</p>
                                </div>
                            @endif

                            <!-- Existing Allocation Info -->
                            @if($existingAllocation)
                                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-sm text-green-800 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <strong>Already Allocated:</strong>&nbsp;RM {{ number_format($existingAllocation->Amount_Allocated, 2) }} on {{ \Carbon\Carbon::parse($existingAllocation->Allocated_At)->format('M d, Y') }}
                                    </p>
                                </div>
                            @endif

                            <!-- Footer with Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="text-xs text-gray-500 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Suggested by {{ $suggestion->suggestedBy->name }} • {{ \Carbon\Carbon::parse($suggestion->created_at)->format('M d, Y h:i A') }}
                                </div>

                                <!-- Action Buttons -->
                                @if($suggestion->Status === 'Pending')
                                    <div class="flex gap-3">
                                        <!-- Reject -->
                                        <form method="POST" action="{{ route('suggestions.reject', $suggestion->id) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Are you sure you want to reject this suggestion?')"
                                                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all text-sm font-medium border border-gray-300">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Reject
                                            </button>
                                        </form>

                                        <!-- Accept & Allocate (Opens Modal) -->
                                        <button @click="openModal({
                                            id: {{ $suggestion->id }},
                                            recipient_id: {{ $suggestion->Recipient_ID }},
                                            recipient_name: '{{ addslashes($suggestion->recipient->Name) }}',
                                            recipient_need: '{{ addslashes($suggestion->recipient->Need_Description) }}',
                                            suggestion_reason: '{{ $suggestion->Suggestion_Reason ? addslashes($suggestion->Suggestion_Reason) : '' }}',
                                            existing_allocation: {{ $existingAllocation ? $existingAllocation->Amount_Allocated : 0 }}
                                        })"
                                                type="button"
                                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all text-sm font-medium shadow-lg shadow-green-500/30">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Accept & Allocate
                                        </button>
                                    </div>
                                @elseif($suggestion->Status === 'Accepted')
                                    <a href="{{ route('recipients.allocate', $campaign->Campaign_ID) }}"
                                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Go to Allocation Page
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $suggestions->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No suggestions yet</h3>
                <p class="text-gray-600">Administrators haven't suggested any recipients for this campaign yet</p>
            </div>
        @endif
    </main>

    <!-- Allocation Modal -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="showModal = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">

            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-6 rounded-t-2xl">
                <div class="flex items-center justify-between text-white">
                    <h3 class="text-xl font-bold flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Accept & Allocate Funds
                    </h3>
                    <button @click="showModal = false" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <form :action="'/suggestions/' + selectedSuggestion.id + '/accept-and-allocate'" method="POST" class="p-6">
                @csrf

                <!-- Recipient Summary -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-5 mb-6 border border-indigo-200">
                    <h4 class="font-semibold text-gray-900 flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span x-text="selectedSuggestion.recipient_name"></span>
                    </h4>
                    <p class="text-sm text-gray-600 mb-3" x-text="selectedSuggestion.recipient_need"></p>
                    <div class="text-xs bg-white rounded-lg p-3 border border-indigo-200" x-show="selectedSuggestion.suggestion_reason">
                        <strong class="text-indigo-900">Admin's Reason:</strong>
                        <p class="text-gray-700 mt-1 italic" x-text="selectedSuggestion.suggestion_reason"></p>
                    </div>
                </div>

                <!-- Available Funds Display -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 mb-6 border border-green-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">Available Campaign Funds:</span>
                        </div>
                        <span class="text-2xl font-bold text-green-600">RM {{ number_format($availableFunds, 2) }}</span>
                    </div>
                    <div x-show="selectedSuggestion.existing_allocation > 0" class="mt-3 pt-3 border-t border-green-200">
                        <p class="text-xs text-gray-600">
                            This recipient already has: <strong class="text-green-700">RM <span x-text="selectedSuggestion.existing_allocation.toLocaleString()"></span></strong>
                        </p>
                    </div>
                </div>

                <!-- Allocation Amount Input -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Allocation Amount (RM) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-medium">RM</span>
                        </div>
                        <input type="number"
                               name="amount"
                               x-model="allocationAmount"
                               step="0.01"
                               min="0.01"
                               :max="{{ $availableFunds }}"
                               required
                               class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-lg font-semibold"
                               placeholder="0.00">
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="mt-3 flex gap-2 flex-wrap">
                        <button type="button" @click="allocationAmount = Math.min(1000, {{ $availableFunds }}).toFixed(2)"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors border border-gray-300">
                            RM 1,000
                        </button>
                        <button type="button" @click="allocationAmount = Math.min(5000, {{ $availableFunds }}).toFixed(2)"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors border border-gray-300">
                            RM 5,000
                        </button>
                        <button type="button" @click="allocationAmount = Math.min(10000, {{ $availableFunds }}).toFixed(2)"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors border border-gray-300">
                            RM 10,000
                        </button>
                        <button type="button" @click="allocationAmount = ({{ $availableFunds }}).toFixed(2)"
                                class="px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 hover:from-indigo-200 hover:to-purple-200 rounded-lg text-sm font-medium transition-colors border border-indigo-300">
                            All Available
                        </button>
                    </div>

                    <!-- Validation Message -->
                    <p x-show="allocationAmount > {{ $availableFunds }}" class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Amount exceeds available funds!
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <!-- Accept & Allocate Now -->
                    <button type="submit"
                            :disabled="!allocationAmount || allocationAmount <= 0 || allocationAmount > {{ $availableFunds }}"
                            class="w-full px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all text-sm font-semibold shadow-lg shadow-green-500/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Accept & Allocate RM <span x-text="parseFloat(allocationAmount || 0).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </button>

                    <!-- Accept Only (Without Allocation) -->
                    <button type="button"
                            @click="acceptOnly(selectedSuggestion.id)"
                            class="w-full px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium border-2 border-gray-300 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Accept Only (Allocate Later)
                    </button>

                    <!-- Cancel -->
                    <button type="button"
                            @click="showModal = false"
                            class="w-full text-sm text-gray-500 hover:text-gray-700 py-2 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} CharityHub. Making a difference together.</p>
            </div>
        </div>
    </footer>
</div>

<!-- Alpine.js Component Script -->
<script>
function suggestionManager() {
    return {
        showModal: false,
        selectedSuggestion: {
            id: null,
            recipient_id: null,
            recipient_name: '',
            recipient_need: '',
            suggestion_reason: '',
            existing_allocation: 0
        },
        allocationAmount: '',

        openModal(suggestion) {
            this.selectedSuggestion = suggestion;
            this.allocationAmount = '';
            this.showModal = true;
        },

        acceptOnly(suggestionId) {
            if (confirm('Accept this suggestion without allocating funds now?')) {
                // Create a form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/suggestions/' + suggestionId + '/accept';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                document.body.appendChild(form);
                form.submit();
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
</body>
</html>
