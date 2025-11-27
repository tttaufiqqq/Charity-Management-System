<!-- resources/views/donation-management/donate.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Make a Donation - CharityHub</title>
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
                    <a href="{{ route('campaigns.browse') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Browse Campaigns</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-4xl w-full">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Campaign Information -->
                <div class="bg-white rounded-lg shadow-xl p-8">
                    <div class="mb-6">
                        <a href="{{ route('campaigns.show.donate', $campaign->Campaign_ID) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center mb-4">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Campaign
                        </a>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Campaign Details</h2>
                    </div>

                    <!-- Campaign Image Placeholder -->
                    <div class="h-48 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>

                    <!-- Campaign Info -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $campaign->Title }}</h3>
                            <p class="text-sm text-indigo-600 font-medium">{{ $campaign->organization->Organization_Name }}</p>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-gray-600 text-sm line-clamp-3">{{ $campaign->Description }}</p>
                        </div>

                        <!-- Progress -->
                        @php
                            $progress = $campaign->Goal_Amount > 0 ? min(($campaign->Collected_Amount / $campaign->Goal_Amount) * 100, 100) : 0;
                        @endphp
                        <div class="border-t border-gray-200 pt-4">
                            <div class="mb-2">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Raised: RM {{ number_format($campaign->Total_Collected, 2) }}</span>
                                    <span class="text-gray-600">{{ number_format($progress, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Goal: RM {{ number_format($campaign->Goal_Amount, 2) }}</p>
                            </div>
                        </div>

                        <!-- Campaign Details -->
                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Start: {{ \Carbon\Carbon::parse($campaign->Start_Date)->format('M d, Y') }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                End: {{ \Carbon\Carbon::parse($campaign->End_Date)->format('M d, Y') }}
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $campaign->Status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Donation Form -->
                <div class="bg-white rounded-lg shadow-xl p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Make a Donation</h2>
                        <p class="text-gray-600">Your generosity makes a difference</p>
                    </div>

                    <!-- Error Messages -->
                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-600">{{ session('error') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('campaigns.donate.process', $campaign->Campaign_ID) }}">
                        @csrf

                        <!-- Donor Info Display -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Donating as:</p>
                            <p class="font-semibold text-gray-900">{{ $donor->Full_Name }}</p>
                            <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                        </div>

                        <!-- Quick Amount Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quick Select Amount</label>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" onclick="setAmount(50)" class="quick-amount px-4 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:border-indigo-600 hover:text-indigo-600 transition-colors">
                                    RM 50
                                </button>
                                <button type="button" onclick="setAmount(100)" class="quick-amount px-4 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:border-indigo-600 hover:text-indigo-600 transition-colors">
                                    RM 100
                                </button>
                                <button type="button" onclick="setAmount(200)" class="quick-amount px-4 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:border-indigo-600 hover:text-indigo-600 transition-colors">
                                    RM 200
                                </button>
                                <button type="button" onclick="setAmount(500)" class="quick-amount px-4 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:border-indigo-600 hover:text-indigo-600 transition-colors">
                                    RM 500
                                </button>
                                <button type="button" onclick="setAmount(1000)" class="quick-amount px-4 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:border-indigo-600 hover:text-indigo-600 transition-colors">
                                    RM 1,000
                                </button>
                                <button type="button" onclick="clearAmount()" class="quick-amount px-4 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:border-indigo-600 hover:text-indigo-600 transition-colors">
                                    Custom
                                </button>
                            </div>
                        </div>

                        <!-- Custom Amount Input -->
                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Donation Amount (RM)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">RM</span>
                                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" required min="1" step="0.01"
                                       class="w-full pl-12 pr-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                       placeholder="0.00">
                            </div>
                            @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select id="payment_method" name="payment_method" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="">Select payment method...</option>
                                <option value="Credit Card" {{ old('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="Debit Card" {{ old('payment_method') == 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                                <option value="Online Banking" {{ old('payment_method') == 'Online Banking' ? 'selected' : '' }}>Online Banking</option>
                                <option value="E-Wallet" {{ old('payment_method') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                            </select>
                            @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Demo Mode Notice -->
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-yellow-800">
                                        <strong>Demo Mode:</strong> This is a simulated payment system for demonstration purposes.
                                        No real payment will be processed. Your donation will be recorded for testing.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Information Box -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-blue-800">
                                        <strong>Receipt Generation:</strong> You will receive a receipt after completing this donation.
                                        The receipt can be downloaded from your donation history.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tax Deduction Notice -->
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-green-800">
                                        <strong>Tax Deductible:</strong> Your donation may be eligible for tax deduction.
                                        Download your receipt after completion.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-3">
                            <a href="{{ url()->previous() }}"
                               class="flex-1 text-center bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                                Complete Donation
                            </button>
                        </div>
                    </form>

                    <!-- Security Notice -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Secured by CharityHub
                        </div>
                    </div>
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

<script>
    function setAmount(amount) {
        document.getElementById('amount').value = amount;

        // Update button styles
        document.querySelectorAll('.quick-amount').forEach(btn => {
            btn.classList.remove('border-indigo-600', 'text-indigo-600', 'bg-indigo-50');
            btn.classList.add('border-gray-300', 'text-gray-700');
        });

        event.target.classList.remove('border-gray-300', 'text-gray-700');
        event.target.classList.add('border-indigo-600', 'text-indigo-600', 'bg-indigo-50');
    }

    function clearAmount() {
        document.getElementById('amount').value = '';
        document.getElementById('amount').focus();

        // Reset all button styles
        document.querySelectorAll('.quick-amount').forEach(btn => {
            btn.classList.remove('border-indigo-600', 'text-indigo-600', 'bg-indigo-50');
            btn.classList.add('border-gray-300', 'text-gray-700');
        });
    }

    // Format amount input with thousand separators (optional)
    document.getElementById('amount').addEventListener('blur', function(e) {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });
</script>

</body>
</html>
