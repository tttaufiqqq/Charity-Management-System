<!-- resources/views/recipient-management/admin/campaign-recipients-detail.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $campaign->Title }} - Recipients - Admin - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Campaign Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $campaign->Title }}</h1>
                    <p class="text-gray-600 mt-2">{{ $campaign->Description }}</p>
                    <div class="mt-4 flex items-center gap-4">
                        <span class="text-sm text-gray-600">
                            <span class="font-medium">Organizer:</span> {{ $campaign->organization->user->name ?? 'N/A' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($campaign->Status === 'Active') bg-green-100 text-green-800
                            @elseif($campaign->Status === 'Pending') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ $campaign->Status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-sm text-gray-600 mb-1">Total Collected</p>
                <p class="text-2xl font-bold text-green-600">RM {{ number_format($stats['total_collected'], 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-sm text-gray-600 mb-1">Total Allocated</p>
                <p class="text-2xl font-bold text-blue-600">RM {{ number_format($stats['total_allocated'], 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-sm text-gray-600 mb-1">Remaining</p>
                <p class="text-2xl font-bold text-orange-600">RM {{ number_format($stats['remaining_amount'], 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-sm text-gray-600 mb-1">Recipients</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_recipients'] }}</p>
            </div>
        </div>

        <!-- Allocation Progress -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Allocation Progress</span>
                <span class="text-sm font-medium text-gray-900">{{ number_format($stats['allocation_percentage'], 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-500"
                     style="width: {{ min($stats['allocation_percentage'], 100) }}%"></div>
            </div>
        </div>

        <!-- Recipients Allocations Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recipient Allocations</h2>
            </div>

            @if($allocations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Need Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Allocated</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allocated At</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($allocations as $allocation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $allocation->recipient->Name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($allocation->recipient->Address, 30) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $allocation->recipient->Contact }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($allocation->recipient->Need_Description, 60) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-green-600">
                                    RM {{ number_format($allocation->Amount_Allocated, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($allocation->Allocated_At)->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $allocations->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No allocations yet</h3>
                    <p class="text-gray-600">No funds have been allocated to recipients for this campaign</p>
                </div>
            @endif
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
