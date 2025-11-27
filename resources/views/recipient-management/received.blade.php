<!-- ================================= -->
<!-- resources/views/recipient-management/received.blade.php (For Public Users) -->
<!-- ================================= -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Received Allocations - CharityHub</title>
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
                    <a href="{{ route('public.recipients.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">My Recipients</a>
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
            <a href="{{ route('public.recipients.show', $recipient->Recipient_ID) }}" class="text-indigo-600 hover:text-indigo-700 font-medium flex items-center mb-4">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Recipient Details
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Received Allocations</h1>
            <p class="text-gray-600 mt-1">Recipient: {{ $recipient->Name }}</p>
        </div>

        <!-- Summary Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">Total Funds Received</p>
                <p class="text-4xl font-bold text-green-600">RM {{ number_format($totalReceived, 2) }}</p>
                <p class="text-sm text-gray-500 mt-2">From {{ $allocations->count() }} allocation(s)</p>
            </div>
        </div>

        <!-- Allocations Grid -->
        @if($allocations->count() > 0)
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                @foreach($allocations as $allocation)
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 mb-1">{{ $allocation->campaign->Title }}</h3>
                                <p class="text-sm text-indigo-600 font-medium">{{ $allocation->campaign->organization->Organization_Name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">RM {{ number_format($allocation->Amount_Allocated, 2) }}</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm text-gray-600 mb-1">
                                <strong>Allocated On:</strong> {{ \Carbon\Carbon::parse($allocation->Allocated_At)->format('F d, Y') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($allocation->Allocated_At)->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $allocations->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No allocations received yet</h3>
                <p class="text-gray-600">This recipient hasn't received any fund allocations from campaigns.</p>
            </div>
        @endif

        <!-- Information Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">About Fund Allocations</h4>
                    <p class="text-sm text-blue-800">
                        Funds are allocated to recipients by campaign organizers from donations collected.
                        The allocations shown here represent the total assistance this recipient has received through CharityHub campaigns.
                    </p>
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
