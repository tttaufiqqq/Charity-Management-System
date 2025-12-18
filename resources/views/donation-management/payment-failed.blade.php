<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <!-- Failed Status Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Error Icon -->
            <div class="mx-auto w-24 h-24 mb-6 relative">
                <div class="relative flex items-center justify-center w-24 h-24 bg-red-500 rounded-full">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>

            <!-- Status Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h1>
            <p class="text-gray-600 mb-6">Your payment was not completed successfully</p>

            <!-- Donation Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Donation Details</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Campaign:</span>
                        <span class="font-medium text-gray-900">{{ $donation->campaign->Title }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium text-gray-900">RM {{ number_format($donation->Amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Receipt No:</span>
                        <span class="font-medium text-gray-900">{{ $donation->Receipt_No }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-medium text-gray-900">{{ $donation->Payment_Method }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $donation->Payment_Status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1 text-left">
                        <p class="text-sm text-red-800">
                            <strong>Payment could not be completed.</strong><br>
                            This could be due to insufficient funds, cancelled transaction, or connection issues.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('campaigns.donate', $donation->campaign->Campaign_ID) }}"
                   class="block w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Try Again
                </a>
                <a href="{{ route('campaigns.show.donate', $donation->campaign->Campaign_ID) }}"
                   class="block w-full bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Back to Campaign
                </a>
                <a href="{{ route('campaigns.browse') }}"
                   class="block w-full text-gray-600 px-6 py-3 rounded-lg font-medium hover:text-gray-900 transition-colors">
                    Browse Other Campaigns
                </a>
            </div>

            <!-- Help Section -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    <strong>Need help?</strong><br>
                    If you believe this was an error or need assistance, please contact our support team
                    with your receipt number: <strong>{{ $donation->Receipt_No }}</strong>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
