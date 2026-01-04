<!-- resources/views/donation-management/payment-failed.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Failed - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-red-50 to-orange-100 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-3xl w-full">
            <!-- Failed Status Card -->
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Failed Header -->
                <div class="bg-gradient-to-r from-red-500 to-orange-600 px-8 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4">
                        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Payment Failed</h1>
                    <p class="text-red-100 text-lg">Your payment could not be processed</p>
                </div>

                <!-- Donation Details -->
                <div class="px-8 py-8">
                    <!-- Amount Display -->
                    <div class="text-center mb-8 pb-8 border-b border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">Attempted Amount</p>
                        <p class="text-5xl font-bold text-gray-900">RM {{ number_format($donation->Amount, 2) }}</p>
                    </div>

                    <!-- Transaction Information -->
                    <div class="space-y-4 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction Details</h3>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Receipt Number</p>
                                    <p class="font-semibold text-gray-900 font-mono">{{ $donation->Receipt_No }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Transaction Date</p>
                                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($donation->Donation_Date)->format('M d, Y h:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Payment Method</p>
                                    <p class="font-semibold text-gray-900">{{ $donation->Payment_Method }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Payment Status</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $donation->Payment_Status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaign</h3>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-900 text-lg mb-2">{{ $donation->campaign->Title }}</h4>
                            <p class="text-sm text-orange-600 font-medium mb-3">{{ $donation->campaign->organization->user->name ?? 'N/A' }}</p>
                            <p class="text-gray-700 text-sm">{{ Str::limit($donation->campaign->Description, 150) }}</p>

                            <div class="mt-4 pt-4 border-t border-orange-200">
                                <a href="{{ route('campaigns.show', $donation->campaign->Campaign_ID) }}"
                                   class="text-orange-600 hover:text-orange-700 font-medium text-sm">
                                    View Campaign Details →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-red-800">
                                    <strong>Payment could not be completed.</strong><br>
                                    This could be due to insufficient funds, cancelled transaction, or connection issues. Please try again or use a different payment method.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <a href="{{ route('campaigns.donate', $donation->campaign->Campaign_ID) }}"
                           class="w-full block text-center bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Try Again
                        </a>

                        <a href="{{ route('campaigns.browse') }}"
                           class="block text-center bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                            Browse Campaigns
                        </a>

                        <a href="{{ route('dashboard') }}"
                           class="block text-center text-gray-600 hover:text-gray-900 px-6 py-3 rounded-lg font-medium transition-colors">
                            Return to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="bg-gray-50 px-8 py-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Need help?</strong>
                        </p>
                        <p class="text-sm text-gray-600">
                            If you believe this was an error or need assistance, please contact our support team
                            with your receipt number: <span class="font-mono font-semibold text-gray-900">{{ $donation->Receipt_No }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mt-3">
                            Email us at <a href="mailto:support@charityhub.com" class="text-indigo-600 hover:text-indigo-700 font-medium">support@charityhub.com</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Message -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 text-sm">
                    You can retry the payment or choose a different payment method. Your donation matters!
                </p>
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
</body>
</html>
