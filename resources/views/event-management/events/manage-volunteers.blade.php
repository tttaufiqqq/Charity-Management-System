<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Volunteers - {{ $event->Title }}</title>
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
                    <a href="{{ route('events.show', $event->Event_ID) }}" class="text-gray-700 hover:text-indigo-600">Back to Event</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manage Volunteers</h1>
                    <p class="text-gray-600">{{ $event->Title }}</p>
                </div>
                @if($event->Status === 'Completed')
                    <form action="{{ route('events.auto-calculate-hours', $event->Event_ID) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Auto-Calculate Hours
                        </button>
                    </form>
                @endif
            </div>

            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <p class="text-sm text-gray-700">
                    <strong>Event Duration:</strong>
                    {{ $event->Start_Date->format('M d, Y') }} - {{ $event->End_Date->format('M d, Y') }}
                    ({{ $event->Start_Date->diffInHours($event->End_Date) }} hours)
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm">
            @if($volunteers->count() > 0)
                <form id="bulkUpdateForm" action="{{ route('events.bulk-update-volunteers', $event->Event_ID) }}" method="POST">
                    @csrf
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600">
                                <span class="ml-2 text-sm text-gray-600">Select All</span>
                            </label>
                            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="">Set Status...</option>
                                <option value="Attended">Mark as Attended</option>
                                <option value="No-Show">Mark as No-Show</option>
                            </select>
                            <input type="number" name="hours" placeholder="Hours" step="0.5" min="0" max="24"
                                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-24">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                                Update Selected
                            </button>
                        </div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volunteer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($volunteers as $volunteer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="volunteer_ids[]" value="{{ $volunteer->Volunteer_ID }}"
                                           class="volunteer-checkbox rounded border-gray-300 text-indigo-600">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $volunteer->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $volunteer->City }}, {{ $volunteer->State }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $volunteer->Phone_Num }}</div>
                                    <div class="text-sm text-gray-500">{{ $volunteer->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($volunteer->pivot->created_at)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $volunteer->pivot->Status === 'Attended' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $volunteer->pivot->Status === 'Registered' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $volunteer->pivot->Status === 'No-Show' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $volunteer->pivot->Status === 'Cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ $volunteer->pivot->Status }}
                                            </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-900">{{ $volunteer->pivot->Total_Hours }}</span>
                                    <span class="text-sm text-gray-500">hrs</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" onclick="openEditModal({{ $volunteer->Volunteer_ID }}, '{{ $volunteer->pivot->Status }}', {{ $volunteer->pivot->Total_Hours }})"
                                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </form>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No volunteers registered yet</p>
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Update Volunteer Hours</h3>
        <form id="editForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="editStatus" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="Registered">Registered</option>
                    <option value="Attended">Attended</option>
                    <option value="No-Show">No-Show</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Hours</label>
                <input type="number" name="total_hours" id="editHours" step="0.5" min="0" max="24" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.volunteer-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Edit Modal
    function openEditModal(volunteerId, status, hours) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        form.action = `/events/{{ $event->Event_ID }}/volunteers/${volunteerId}/hours`;
        document.getElementById('editStatus').value = status;
        document.getElementById('editHours').value = hours;
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Close modal on outside click
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
</body>
</html>
