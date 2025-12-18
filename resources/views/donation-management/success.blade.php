<!-- resources/views/donation-management/success.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donation Successful - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-3xl w-full">
            <!-- Success Card -->
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Success Header -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Thank You for Your Donation!</h1>
                    <p class="text-green-100 text-lg">Your generosity is making a real difference</p>
                </div>

                <!-- Donation Details -->
                <div class="px-8 py-8">
                    <!-- Amount Display -->
                    <div class="text-center mb-8 pb-8 border-b border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">Donation Amount</p>
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
                                    <p class="text-sm text-gray-600 mb-1">Donor Name</p>
                                    <p class="font-semibold text-gray-900">{{ $donation->donor->Full_Name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaign Supported</h3>
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-900 text-lg mb-2">{{ $donation->campaign->Title }}</h4>
                            <p class="text-sm text-indigo-600 font-medium mb-3">{{ $donation->campaign->organization->user->name ?? 'N/A' }}</p>
                            <p class="text-gray-700 text-sm">{{ Str::limit($donation->campaign->Description, 150) }}</p>

                            <div class="mt-4 pt-4 border-t border-indigo-200">
                                <a href="{{ route('campaigns.show', $donation->campaign->Campaign_ID) }}"
                                   class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                    View Campaign Details →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt Information -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-blue-800">
                                    <strong>Receipt Available:</strong> Your official donation receipt is ready for download.
                                    This receipt can be used for tax deduction purposes.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <a href="{{ route('donation.receipt', $donation->Donation_ID) }}"
                           class="w-full block text-center bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Receipt
                        </a>

                        <div class="grid md:grid-cols-2 gap-3">
                            <a href="{{ route('campaigns.browse') }}"
                               class="block text-center bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                                Browse More Campaigns
                            </a>
                            <a href="{{ route('donations.my') }}"
                               class="block text-center bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                                View Donation History
                            </a>
                        </div>

                        <a href="{{ route('dashboard') }}"
                           class="block text-center text-gray-600 hover:text-gray-900 px-6 py-3 rounded-lg font-medium transition-colors">
                            Return to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Share Section (Optional) -->
                <div class="bg-gray-50 px-8 py-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">Help spread the word about this campaign</p>
                        <div class="flex justify-center gap-3">
                            <button onclick="shareOnSocial('facebook')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Share
                            </button>
                            <button onclick="shareOnSocial('twitter')"
                                    class="inline-flex items-center px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                                Tweet
                            </button>
                            <button onclick="copyLink()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Copy Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Message -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 text-sm">
                    Questions about your donation? Contact us at <a href="mailto:support@charityhub.com" class="text-indigo-600 hover:text-indigo-700 font-medium">support@charityhub.com</a>
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

<script>
    function shareOnSocial(platform) {
        const url = encodeURIComponent('{{ route("campaigns.show", $donation->campaign->Campaign_ID) }}');
        const text = encodeURIComponent('I just donated to {{ $donation->campaign->Title }} on CharityHub! Join me in making a difference.');

        let shareUrl;
        if (platform === 'facebook') {
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
        } else if (platform === 'twitter') {
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
        }

        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    }

    function copyLink() {
        const url = '{{ route("campaigns.show", $donation->campaign->Campaign_ID) }}';
        navigator.clipboard.writeText(url).then(() => {
            alert('Campaign link copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }

    // Confetti effect (optional - simple version)
    window.addEventListener('load', function() {
        // You can add a confetti library here if desired
        console.log('Donation successful! 🎉');
    });
</script>

</body>
</html>
