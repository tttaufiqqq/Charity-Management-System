<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Events - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">CharityHub</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                    <a href="{{ route('admin.campaigns.pending') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Campaigns</a>
                    <a href="{{ route('admin.events.pending') }}" class="text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Events</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Pending Event Approvals</h1>

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

        <div class="bg-white rounded-lg shadow-sm">
            @if($events->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organizer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($events as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $event->Title }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($event->Description, 50) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $event->organization->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $event->organization->City }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ Str::limit($event->Location, 30) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $event->Start_Date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $event->Capacity ?? 'Unlimited' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $event->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button onclick="openModal({{ $event->Event_ID }}, '{{ addslashes($event->Title) }}')"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        View
                                    </button>
                                    <form action="{{ route('admin.events.approve', $event->Event_ID) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.events.reject', $event->Event_ID) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to reject this event?');">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Hidden details for modal -->
                            <div id="event-{{ $event->Event_ID }}" class="hidden">
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-700">Description:</h4>
                                        <p class="text-gray-600">{{ $event->Description ?? 'No description' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-700">Location:</h4>
                                        <p class="text-gray-600">{{ $event->Location }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-700">Start Date:</h4>
                                            <p class="text-gray-600">{{ $event->Start_Date->format('F d, Y g:i A') }}</p>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-700">End Date:</h4>
                                            <p class="text-gray-600">{{ $event->End_Date->format('F d, Y g:i A') }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-700">Capacity:</h4>
                                            <p class="text-gray-600">{{ $event->Capacity ?? 'Unlimited' }} volunteers</p>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-700">Duration:</h4>
                                            <p class="text-gray-600">{{ $event->Start_Date->diffInDays($event->End_Date) + 1 }} days</p>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-700">Organization:</h4>
                                        <p class="text-gray-600">{{ $event->organization->user->name }}</p>
                                        <p class="text-sm text-gray-500">Reg No: {{ $event->organization->Register_No }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($events->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $events->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending events</h3>
                    <p class="mt-1 text-sm text-gray-500">All events have been reviewed.</p>
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Modal -->
<div id="detailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-lg font-medium text-gray-900"></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<script>
    function openModal(eventId, title) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalContent').innerHTML = document.getElementById('event-' + eventId).innerHTML;
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
</body>
</html>
