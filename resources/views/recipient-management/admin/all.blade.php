<!-- resources/views/recipient-management/admin/all.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Recipients - Admin - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">All Recipients</h1>
            <p class="text-gray-600 mt-1">View and manage all recipient registrations</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-600">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid md:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow-lg p-6 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-yellow-700 mb-1">Pending</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg shadow-lg p-6 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-700 mb-1">Approved</p>
                        <p class="text-2xl font-bold text-green-700">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-red-50 rounded-lg shadow-lg p-6 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-red-700 mb-1">Rejected</p>
                        <p class="text-2xl font-bold text-red-700">{{ $stats['rejected'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @if(isset($stats['overdue']) && $stats['overdue'] > 0)
            <div class="bg-gradient-to-r from-red-500 to-orange-500 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Overdue</p>
                        <p class="text-2xl font-bold">{{ $stats['overdue'] }}</p>
                        <p class="text-xs opacity-75">14+ days</p>
                    </div>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif
            @if(isset($stats['needs_attention']) && $stats['needs_attention'] > 0)
            <div class="bg-gradient-to-r from-amber-400 to-yellow-500 rounded-lg shadow-lg p-6 text-amber-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Needs Attention</p>
                        <p class="text-2xl font-bold">{{ $stats['needs_attention'] }}</p>
                        <p class="text-xs opacity-75">7-14 days</p>
                    </div>
                    <div class="w-10 h-10 bg-white/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Review Priority Legend -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-8 border border-blue-100">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold text-blue-900">Review Priority Guide</span>
            </div>
            <div class="flex flex-wrap gap-4 text-sm">
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-gray-700"><strong>Overdue:</strong> 14+ days pending</span>
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="text-gray-700"><strong>Needs Attention:</strong> 7-14 days pending</span>
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-gray-700"><strong>In Review:</strong> Less than 7 days</span>
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-gray-700"><strong>Active:</strong> Approved recipients</span>
                </span>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('admin.recipients.all') }}" class="grid md:grid-cols-3 gap-4">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <!-- Recipients Table -->
        @if($recipients->count() > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recipients as $recipient)
                            @php
                                $priorityStyles = [
                                    'Overdue' => 'border-l-4 border-red-500 bg-red-50',
                                    'Needs Attention' => 'border-l-4 border-amber-500 bg-amber-50',
                                    'In Review' => 'border-l-4 border-blue-500 bg-blue-50',
                                    'Active' => '',
                                    'Closed' => 'bg-gray-50',
                                ];
                                $priorityBadgeStyles = [
                                    'Overdue' => 'bg-gradient-to-r from-red-500 to-red-600 text-white',
                                    'Needs Attention' => 'bg-gradient-to-r from-amber-400 to-amber-500 text-amber-900',
                                    'In Review' => 'bg-gradient-to-r from-blue-400 to-blue-500 text-white',
                                    'Active' => 'bg-gradient-to-r from-green-400 to-green-500 text-white',
                                    'Closed' => 'bg-gray-400 text-white',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $priorityStyles[$recipient->review_priority] ?? '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $recipient->recipient_name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($recipient->recipient_address, 30) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $recipient->recipient_contact }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $recipient->applicant_full_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($recipient->application_status === 'Approved') bg-green-100 text-green-800
                                            @elseif($recipient->application_status === 'Pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $recipient->application_status }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold shadow-sm {{ $priorityBadgeStyles[$recipient->review_priority] ?? 'bg-gray-200 text-gray-700' }}">
                                        @if($recipient->review_priority === 'Overdue')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($recipient->review_priority === 'Needs Attention')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                            </svg>
                                        @elseif($recipient->review_priority === 'In Review')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($recipient->review_priority === 'Active')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        {{ $recipient->review_priority }}
                                    </span>
                                    @if($recipient->application_status === 'Pending')
                                        <div class="text-xs text-gray-500 mt-1">{{ $recipient->days_since_application }} days</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($recipient->application_submitted_at)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.recipients.show', $recipient->Recipient_ID) }}"
                                       class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $recipients->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No recipients found</h3>
                <p class="text-gray-600">Try adjusting your filters</p>
            </div>
        @endif
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
