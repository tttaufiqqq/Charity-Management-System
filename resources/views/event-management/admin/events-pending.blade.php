<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Events - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

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

        <!-- Search Bar -->
        <x-admin.search-bar
            :action="route('admin.events.pending')"
            placeholder="Search events by title, description, location, or organizer..."
            :value="$search ?? ''"
        />

        <div class="bg-white rounded-lg shadow-sm">
            @if($events->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organizer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($events as $event)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $event->Title }}</div>
                                    <div class="text-sm text-gray-500 mt-1">{{ Str::limit($event->Description, 50) }}</div>
                                    @if($event->roles->count() > 0)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                                </svg>
                                                {{ $event->roles->count() }} role{{ $event->roles->count() !== 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $event->organization->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $event->organization->City }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ Str::limit($event->Location, 30) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">
                                        {{ $event->Start_Date->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $event->Start_Date->diffInDays($event->End_Date) + 1 }} day{{ $event->Start_Date->diffInDays($event->End_Date) !== 0 ? 's' : '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($event->roles->count() > 0)
                                            {{ $event->roles->sum('Volunteers_Needed') }} volunteers
                                        @else
                                            {{ $event->Capacity ?? 'Unlimited' }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $event->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-3">
                                        <button onclick="openModal({{ $event->Event_ID }}, '{{ addslashes($event->Title) }}')"
                                                class="inline-flex items-center text-blue-600 hover:text-blue-900 text-sm font-medium transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </button>
                                        <form action="{{ route('admin.events.approve', $event->Event_ID) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center text-green-600 hover:text-green-900 text-sm font-medium transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                        <button
                                            @click="$dispatch('reject-event-{{ $event->Event_ID }}')"
                                            class="inline-flex items-center text-red-600 hover:text-red-900 text-sm font-medium transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Hidden details for modal -->
                            <div id="event-{{ $event->Event_ID }}" class="hidden">
                                <div class="space-y-6">
                                    <!-- Event Description -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Description</h4>
                                        <p class="text-sm text-gray-600 leading-relaxed">{{ $event->Description ?? 'No description provided' }}</p>
                                    </div>

                                    <!-- Event Location -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Location</h4>
                                        <p class="text-sm text-gray-600">{{ $event->Location }}</p>
                                    </div>

                                    <!-- Event Details -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-700 mb-1">Start Date</h4>
                                            <p class="text-sm text-gray-600">{{ $event->Start_Date->format('F d, Y g:i A') }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-700 mb-1">End Date</h4>
                                            <p class="text-sm text-gray-600">{{ $event->End_Date->format('F d, Y g:i A') }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-700 mb-1">Capacity</h4>
                                            <p class="text-sm text-gray-600">
                                                @if($event->roles->count() > 0)
                                                    {{ $event->roles->sum('Volunteers_Needed') }} volunteers (across {{ $event->roles->count() }} roles)
                                                @else
                                                    {{ $event->Capacity ?? 'Unlimited' }} volunteers
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-700 mb-1">Duration</h4>
                                            <p class="text-sm text-gray-600">{{ $event->Start_Date->diffInDays($event->End_Date) + 1 }} day{{ $event->Start_Date->diffInDays($event->End_Date) !== 0 ? 's' : '' }}</p>
                                        </div>
                                    </div>

                                    <!-- Organization Details -->
                                    <div class="border-t border-gray-200 pt-4">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Organization Details</h4>
                                        <div class="space-y-2">
                                            <div class="flex items-start">
                                                <span class="text-sm font-medium text-gray-500 w-32 flex-shrink-0">Name:</span>
                                                <span class="text-sm text-gray-900">{{ $event->organization->user->name }}</span>
                                            </div>
                                            <div class="flex items-start">
                                                <span class="text-sm font-medium text-gray-500 w-32 flex-shrink-0">Registration No:</span>
                                                <span class="text-sm text-gray-900">{{ $event->organization->Register_No }}</span>
                                            </div>
                                            <div class="flex items-start">
                                                <span class="text-sm font-medium text-gray-500 w-32 flex-shrink-0">Phone:</span>
                                                <span class="text-sm text-gray-900">{{ $event->organization->Phone_No }}</span>
                                            </div>
                                            <div class="flex items-start">
                                                <span class="text-sm font-medium text-gray-500 w-32 flex-shrink-0">Address:</span>
                                                <span class="text-sm text-gray-900">{{ $event->organization->Address }}, {{ $event->organization->City }}, {{ $event->organization->State }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Event Roles -->
                                    <x-admin.event-roles :roles="$event->roles" />

                                    <!-- Organizer History -->
                                    <x-admin.organizer-history :stats="$event->organizerStats" itemType="event" />
                                </div>
                            </div>

                            <!-- Reject Modal -->
                            <x-admin.reject-modal
                                itemType="event"
                                :itemName="$event->Title"
                                :organizerName="$event->organization->user->name"
                                :itemId="$event->Event_ID"
                            >
                                <form action="{{ route('admin.events.reject', $event->Event_ID) }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto"
                                    >
                                        Confirm Rejection
                                    </button>
                                </form>
                            </x-admin.reject-modal>
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
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">
                        {{ isset($search) && $search ? 'No events found' : 'No pending events' }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ isset($search) && $search ? 'Try adjusting your search terms.' : 'All events have been reviewed.' }}
                    </p>
                    @if(isset($search) && $search)
                        <a href="{{ route('admin.events.pending') }}" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Clear search
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </main>
</div>

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

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
</body>
</html>
