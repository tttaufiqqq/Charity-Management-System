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
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-4xl w-full">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Campaign Information -->
                <div class="bg-white rounded-lg shadow-xl p-8">
                    <div class="mb-6">
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
                            <p class="text-sm text-indigo-600 font-medium">{{ $campaign->organization->user->name ?? 'N/A' }}</p>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-gray-600 text-sm line-clamp-3">{{ $campaign->Description }}</p>
                        </div>

                        <!-- Progress -->
                        @php
                            $progress = $campaign->Goal_Amount > 0 ? min(($campaign->Collected_Amount / $campaign->Goal_Amount) * 100, 100) : 0;
                            $remainingAmount = max($campaign->Goal_Amount - $campaign->Collected_Amount, 0);
                        @endphp
                        <div class="border-t border-gray-200 pt-4">
                            <div class="mb-2">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Raised: RM {{ number_format($campaign->Collected_Amount, 2) }}</span>
                                    <span class="text-gray-600">{{ number_format($progress, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="flex justify-between text-sm mt-1">
                                    <span class="text-gray-500">Goal: RM {{ number_format($campaign->Goal_Amount, 2) }}</span>
                                    @if($remainingAmount > 0)
                                        <span class="text-indigo-600 font-medium">Remaining: RM {{ number_format($remainingAmount, 2) }}</span>
                                    @else
                                        <span class="text-green-600 font-medium">Goal Reached!</span>
                                    @endif
                                </div>
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

                    @php
                        $remainingAmount = max($campaign->Goal_Amount - $campaign->Collected_Amount, 0);
                    @endphp

                    <!-- Warning if campaign is fully funded -->
                    @if($remainingAmount <= 0)
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-green-800 font-medium">
                                        This campaign has reached its funding goal! Thank you for your interest in supporting this cause.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif($remainingAmount < $campaign->Goal_Amount * 0.1)
                        <!-- Warning if less than 10% remaining -->
                        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-amber-800">
                                        <strong>Almost there!</strong> This campaign only needs RM {{ number_format($remainingAmount, 2) }} more to reach its goal.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('campaigns.donate.process', $campaign->Campaign_ID) }}"
                          x-data="{ showConfirmModal: false, donationAmount: 0 }"
                          @submit.prevent="if (!showConfirmModal) { donationAmount = document.getElementById('amount').value; showConfirmModal = true; } else { $el.submit(); }">
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
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                Donation Amount (RM)
                                @if($remainingAmount > 0)
                                    <span class="text-xs text-gray-500">(Max: RM {{ number_format($remainingAmount, 2) }})</span>
                                @endif
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">RM</span>
                                <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
                                       required min="1" max="{{ $remainingAmount > 0 ? $remainingAmount : 0 }}" step="0.01"
                                       class="w-full pl-12 pr-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors {{ $remainingAmount <= 0 ? 'bg-gray-100' : '' }}"
                                       placeholder="0.00"
                                       {{ $remainingAmount <= 0 ? 'disabled' : '' }}>
                            </div>
                            @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>

                            <!-- ToyyibPay Gateway Badge -->
                            <div class="mb-4 p-3 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center">
                                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-semibold text-indigo-900">Powered by ToyyibPay</p>
                                            <p class="text-xs text-indigo-600">Secure Malaysian Payment Gateway</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-green-600">SSL Secured</span>
                                    </div>
                                </div>
                            </div>

                            <!-- FPX Online Banking (Single Option) -->
                            <input type="hidden" name="payment_method" value="FPX Online Banking">
                            <div class="relative flex items-center p-4 border-2 border-indigo-600 bg-indigo-50 rounded-lg">
                                <div class="flex-shrink-0 w-12 h-12 bg-white rounded-lg shadow-sm flex items-center justify-center">
                                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-semibold text-gray-900">FPX Online Banking</p>
                                    <p class="text-xs text-gray-600">Pay directly from your bank account</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Payment Security Notice -->
                            <div class="mt-3 flex items-start text-xs text-gray-600">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>All transactions are encrypted and processed securely through ToyyibPay's PCI-DSS compliant FPX gateway. Your bank credentials are never stored on our servers.</span>
                            </div>

                            @error('payment_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                {{ $remainingAmount <= 0 ? 'Back' : 'Cancel' }}
                            </a>
                            <button type="submit"
                                    {{ $remainingAmount <= 0 ? 'disabled' : '' }}
                                    class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl {{ $remainingAmount <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                Complete Donation
                            </button>
                        </div>

                        <!-- Payment Confirmation Modal -->
                        <div x-show="showConfirmModal"
                             x-cloak
                             class="fixed inset-0 z-50 overflow-y-auto"
                             style="display: none;">
                            <!-- Backdrop -->
                            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                                 @click="showConfirmModal = false"></div>

                            <!-- Modal -->
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="relative bg-white rounded-lg shadow-2xl max-w-md w-full p-6 transform transition-all"
                                     @click.away="showConfirmModal = false">
                                    <!-- Modal Header -->
                                    <div class="mb-6">
                                        <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full mx-auto mb-4">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900 text-center">Confirm Your Donation</h3>
                                        <p class="text-sm text-gray-600 text-center mt-2">Please review your donation details before proceeding to payment</p>
                                    </div>

                                    <!-- Donation Summary -->
                                    <div class="space-y-4 mb-6">
                                        <!-- Campaign -->
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-xs text-gray-600 mb-1">Campaign</p>
                                            <p class="font-semibold text-gray-900">{{ $campaign->Title }}</p>
                                            <p class="text-xs text-indigo-600 mt-1">{{ $campaign->organization->user->name ?? 'N/A' }}</p>
                                        </div>

                                        <!-- Amount -->
                                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg p-4">
                                            <p class="text-xs text-gray-600 mb-1">Donation Amount</p>
                                            <p class="text-3xl font-bold text-indigo-900">
                                                RM <span x-text="parseFloat(donationAmount).toFixed(2)">0.00</span>
                                            </p>
                                        </div>

                                        <!-- Payment Method -->
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-xs text-gray-600 mb-1">Payment Method</p>
                                            <p class="font-semibold text-gray-900">FPX Online Banking</p>
                                            <div class="flex items-center mt-2 text-xs text-green-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Secured by ToyyibPay</span>
                                            </div>
                                        </div>

                                        <!-- Donor Info -->
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-xs text-gray-600 mb-1">Donor</p>
                                            <p class="font-semibold text-gray-900">{{ $donor->Full_Name }}</p>
                                            <p class="text-xs text-gray-600">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>

                                    <!-- Important Notice -->
                                    <div class="mb-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div class="flex-1">
                                                <p class="text-xs text-blue-800">
                                                    You will be redirected to ToyyibPay's secure payment page to complete this transaction. Please do not close your browser during the payment process.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Actions -->
                                    <div class="flex gap-3">
                                        <button type="button"
                                                @click="showConfirmModal = false"
                                                class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                                class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition-colors shadow-lg hover:shadow-xl">
                                            Proceed to Payment
                                        </button>
                                    </div>
                                </div>
                            </div>
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
