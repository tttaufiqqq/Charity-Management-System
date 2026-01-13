<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Recipients - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pending Recipient Approvals</h1>
            @if(isset($stats))
            <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
                @if($stats['overdue'] > 0)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-red-500 to-red-600 text-white shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $stats['overdue'] }} Overdue
                </span>
                @endif
                @if($stats['needs_attention'] > 0)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-amber-400 to-amber-500 text-amber-900 shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    {{ $stats['needs_attention'] }} Need Attention
                </span>
                @endif
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-blue-400 to-blue-500 text-white shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ $stats['pending'] }} Total Pending
                </span>
            </div>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Search Bar -->
        <x-admin.search-bar
            :action="route('admin.recipients.pending')"
            placeholder="Search recipients by name, contact, or address..."
            :value="request('search') ?? ''"
        />

        <div class="bg-white rounded-lg shadow-sm">
            @if($recipients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recipients as $recipient)
                            @php
                                $priorityStyles = [
                                    'Overdue' => 'border-l-4 border-red-500 bg-red-50',
                                    'Needs Attention' => 'border-l-4 border-amber-500 bg-amber-50',
                                    'In Review' => 'border-l-4 border-blue-500 bg-blue-50',
                                ];
                                $priorityBadgeStyles = [
                                    'Overdue' => 'bg-gradient-to-r from-red-500 to-red-600 text-white',
                                    'Needs Attention' => 'bg-gradient-to-r from-amber-400 to-amber-500 text-amber-900',
                                    'In Review' => 'bg-gradient-to-r from-blue-400 to-blue-500 text-white',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-100 transition-colors {{ $priorityStyles[$recipient->review_priority] ?? '' }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $recipient->recipient_name }}</div>
                                    <div class="text-sm text-gray-500 mt-1">{{ Str::limit($recipient->need_summary, 50) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $recipient->applicant_full_name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $recipient->applicant_email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $recipient->recipient_contact }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($recipient->recipient_address, 25) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold shadow-sm {{ $priorityBadgeStyles[$recipient->review_priority] ?? 'bg-gray-200 text-gray-700' }}">
                                        @if($recipient->review_priority === 'Overdue')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($recipient->review_priority === 'Needs Attention')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        {{ $recipient->review_priority }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">{{ $recipient->days_since_application }} days</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($recipient->application_submitted_at)->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-3">
                                        <button onclick="openModal({{ $recipient->Recipient_ID }}, '{{ addslashes($recipient->recipient_name) }}')"
                                                class="inline-flex items-center text-blue-600 hover:text-blue-900 text-sm font-medium transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </button>
                                        <form action="{{ route('admin.recipients.approve', $recipient->Recipient_ID) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center text-green-600 hover:text-green-900 text-sm font-medium transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                        <button
                                            onclick="openRejectModal({{ $recipient->Recipient_ID }}, '{{ addslashes($recipient->recipient_name) }}', '{{ addslashes($recipient->applicant_full_name ?? 'N/A') }}')"
                                            class="inline-flex items-center text-red-600 hover:text-red-900 text-sm font-medium transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($recipients->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $recipients->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">
                        {{ request('search') ? 'No recipients found' : 'No pending recipients' }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ request('search') ? 'Try adjusting your search terms.' : 'All recipients have been reviewed.' }}
                    </p>
                    @if(request('search'))
                        <a href="{{ route('admin.recipients.pending') }}" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Clear search
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Hidden details for view modal -->
@foreach($recipients as $recipient)
    @php
        $priorityBadgeStyles = [
            'Overdue' => 'bg-gradient-to-r from-red-500 to-red-600 text-white',
            'Needs Attention' => 'bg-gradient-to-r from-amber-400 to-amber-500 text-amber-900',
            'In Review' => 'bg-gradient-to-r from-blue-400 to-blue-500 text-white',
        ];
    @endphp
    <div id="recipient-{{ $recipient->Recipient_ID }}" class="hidden">
        <div class="space-y-6">
            <!-- Priority Banner -->
            @if($recipient->review_priority === 'Overdue')
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold text-red-700">Overdue Review - {{ $recipient->days_since_application }} days pending</span>
                </div>
                <p class="text-xs text-red-600 mt-1">This application has been waiting for more than 14 days. Immediate action recommended.</p>
            </div>
            @elseif($recipient->review_priority === 'Needs Attention')
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-amber-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    <span class="text-sm font-semibold text-amber-700">Needs Attention - {{ $recipient->days_since_application }} days pending</span>
                </div>
                <p class="text-xs text-amber-600 mt-1">This application has been waiting for 7-14 days. Please review soon.</p>
            </div>
            @else
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold text-blue-700">In Review - {{ $recipient->days_since_application }} days pending</span>
                </div>
                <p class="text-xs text-blue-600 mt-1">This application is within the normal review period.</p>
            </div>
            @endif

            <!-- Description of Need -->
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Description of Need</h4>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $recipient->need_summary ?? 'No description provided' }}</p>
            </div>

            <!-- Recipient Details -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Contact Number</h4>
                    <p class="text-sm text-gray-600">{{ $recipient->recipient_contact }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Address</h4>
                    <p class="text-sm text-gray-600">{{ $recipient->recipient_address }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Submission Date</h4>
                    <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($recipient->application_submitted_at)->format('F d, Y h:i A') }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Days Since Submission</h4>
                    <p class="text-sm text-gray-600">{{ $recipient->days_since_application }} days ({{ \Carbon\Carbon::parse($recipient->application_submitted_at)->diffForHumans() }})</p>
                </div>
            </div>

            <!-- Registration Details -->
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Registered By</h4>
                <div class="space-y-2">
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-gray-500 w-32 flex-shrink-0">Name:</span>
                        <span class="text-sm text-gray-900">{{ $recipient->applicant_full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-gray-500 w-32 flex-shrink-0">Email:</span>
                        <span class="text-sm text-gray-900">{{ $recipient->applicant_email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-gray-500 w-32 flex-shrink-0">Phone:</span>
                        <span class="text-sm text-gray-900">{{ $recipient->applicant_phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Detail Modal -->
<div id="detailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border w-11/12 max-w-3xl shadow-lg rounded-lg bg-white my-10">
        <div class="flex justify-between items-start mb-4">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-900"></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="modalContent" class="max-h-[70vh] overflow-y-auto pr-2"></div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-11/12 max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mt-3 text-center sm:mt-0 sm:text-left">
            <h3 class="text-lg font-semibold leading-6 text-gray-900">Confirm Rejection</h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500">Are you sure you want to reject this recipient? This action can be reversed later.</p>
                <div class="mt-3 space-y-1">
                    <p class="text-sm">
                        <span class="font-medium text-gray-700">Recipient:</span>
                        <span id="reject_recipient_name" class="text-gray-900"></span>
                    </p>
                    <p class="text-sm">
                        <span class="font-medium text-gray-700">Registered By:</span>
                        <span id="reject_registrant_name" class="text-gray-900"></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-5 sm:mt-4 flex gap-3">
            <button type="button" onclick="closeRejectModal()" class="flex-1 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </button>
            <form id="rejectForm" method="POST" action="" class="flex-1">
                @csrf
                <button type="submit" class="w-full rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                    Confirm Rejection
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(recipientId, name) {
        document.getElementById('modalTitle').textContent = name;
        document.getElementById('modalContent').innerHTML = document.getElementById('recipient-' + recipientId).innerHTML;
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    function openRejectModal(recipientId, recipientName, registrantName) {
        document.getElementById('reject_recipient_name').textContent = recipientName;
        document.getElementById('reject_registrant_name').textContent = registrantName;
        document.getElementById('rejectForm').action = '/recipients/' + recipientId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeRejectModal();
        }
    });
</script>
</body>
</html>
