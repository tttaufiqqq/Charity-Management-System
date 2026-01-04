<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Pending - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta http-equiv="refresh" content="5">
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <!-- Pending Status Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Animated Icon -->
            <div class="mx-auto w-24 h-24 mb-6 relative">
                <div class="absolute inset-0 bg-yellow-100 rounded-full animate-ping"></div>
                <div class="relative flex items-center justify-center w-24 h-24 bg-yellow-500 rounded-full">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Status Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Pending</h1>
            <p class="text-gray-600 mb-6">We're waiting for payment confirmation from ToyyibPay</p>

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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $donation->Payment_Status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Info Message -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1 text-left">
                        <p class="text-sm text-blue-800">
                            <strong>This page will auto-refresh every 5 seconds.</strong><br>
                            Your payment is being processed. This usually takes a few moments.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <button onclick="location.reload()"
                    class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                Check Status Now
            </button>

            <!-- Troubleshooting -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    <strong>Payment not updating?</strong><br>
                    If you've completed the payment but the status hasn't updated after a few minutes,
                    please contact support with your receipt number.
                </p>
            </div>
        </div>

        <!-- Loading Animation -->
        <div class="mt-6 text-center">
            <div class="inline-flex items-center space-x-2 text-gray-500">
                <div class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                <div class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
            </div>
            <p class="text-sm text-gray-500 mt-2">Checking payment status...</p>
        </div>
    </div>
</div>
</body>
</html>
