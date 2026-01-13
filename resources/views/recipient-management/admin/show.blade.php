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
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
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


</body>
</html>
