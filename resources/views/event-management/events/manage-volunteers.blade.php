<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Attendance - {{ $event->Title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

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

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Volunteer Attendance</h1>
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

        <!-- Role Distribution Summary -->
        @if($roles->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Role Distribution</h2>
                <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($roles as $role)
                        <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $role->Role_Name }}</h3>
                            <x-role-progress-bar :filled="$role->Volunteers_Filled" :total="$role->Volunteers_Needed" :showPercentage="false" />
                            <div class="mt-2 flex justify-between items-center">
                                <x-role-capacity-badge :filled="$role->Volunteers_Filled" :total="$role->Volunteers_Needed" />
                                <span class="text-xs text-gray-600">{{ $roleStats[$role->Role_ID] ?? 0 }} assigned</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm">
            @if($volunteers->count() > 0)
                <form id="bulkUpdateForm" action="{{ route('events.bulk-update-volunteers', $event->Event_ID) }}" method="POST">
                    @csrf
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center space-x-4 flex-wrap gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600">
                                <span class="ml-2 text-sm text-gray-600">Select All</span>
                            </label>
                            <div>
                                <select name="status" id="bulkStatus" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="handleBulkStatusChange()" required>
                                    <option value="">Set Status...</option>
                                    <option value="Attended">Mark as Attended</option>
                                    <option value="No-Show">Mark as No-Show</option>
                                </select>
                            </div>
                            <div>
                                <input type="number" name="hours" id="bulkHours" placeholder="Hours (required for Attended)" step="1" min="0" max="24"
                                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-48">
                                <p class="text-xs text-gray-500 mt-1" id="bulkHoursHint">Enter hours for attended volunteers</p>
                                @error('hours')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
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
                                <td class="px-6 py-4">
                                    @php
                                        $volunteerRole = $roles->firstWhere('Role_ID', $volunteer->pivot->Role_ID);
                                    @endphp
                                    @if($volunteerRole)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $volunteerRole->Role_Name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No role assigned</span>
                                    @endif
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
                                    <button type="button" onclick="openEditModal({{ $volunteer->Volunteer_ID }}, '{{ $volunteer->pivot->Status }}', {{ $volunteer->pivot->Total_Hours }}, {{ $volunteer->pivot->Role_ID ?? 'null' }})"
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
        <h3 class="text-lg font-medium text-gray-900 mb-4">Update Volunteer</h3>
        <form id="editForm" method="POST">
            @csrf
            @if($roles->count() > 0)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role_id" id="editRole" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">No role assigned</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->Role_ID }}" data-filled="{{ $role->Volunteers_Filled }}" data-needed="{{ $role->Volunteers_Needed }}">
                                {{ $role->Role_Name }} ({{ $role->Volunteers_Filled }}/{{ $role->Volunteers_Needed }} filled)
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Change volunteer's assigned role</p>
                </div>
            @endif
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="editStatus" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" onchange="handleStatusChange()">
                    <option value="Registered">Registered</option>
                    <option value="Attended">Attended</option>
                    <option value="No-Show">No-Show</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="mb-4" id="hoursContainer">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Total Hours <span id="hoursRequired" class="text-red-500">*</span>
                </label>
                <input type="number" name="total_hours" id="editHours" step="1" min="0" max="24"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <p class="mt-1 text-xs text-gray-500" id="hoursHint">
                    Hours must be greater than 0 for "Attended" status
                </p>
                @error('total_hours')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
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

    // Handle bulk status change
    function handleBulkStatusChange() {
        const status = document.getElementById('bulkStatus').value;
        const hoursInput = document.getElementById('bulkHours');
        const hoursHint = document.getElementById('bulkHoursHint');

        if (status === 'Attended') {
            hoursInput.disabled = false;
            hoursInput.required = true;
            hoursInput.min = '1';
            hoursInput.value = '';
            hoursInput.placeholder = 'Hours (required)';
            hoursHint.textContent = 'Hours are required for marking as Attended';
            hoursHint.classList.remove('text-gray-500');
            hoursHint.classList.add('text-red-600', 'font-medium');
        } else if (status === 'No-Show') {
            hoursInput.disabled = true;
            hoursInput.required = false;
            hoursInput.value = '0';
            hoursInput.min = '0';
            hoursInput.placeholder = 'Hours (locked at 0)';
            hoursHint.textContent = 'No-Show volunteers cannot have hours logged';
            hoursHint.classList.remove('text-red-600', 'font-medium');
            hoursHint.classList.add('text-gray-500');
        } else {
            hoursInput.disabled = false;
            hoursInput.required = false;
            hoursInput.min = '0';
            hoursInput.value = '';
            hoursInput.placeholder = 'Hours';
            hoursHint.textContent = 'Select a status first';
            hoursHint.classList.remove('text-red-600', 'font-medium');
            hoursHint.classList.add('text-gray-500');
        }
    }

    // Validate bulk form before submission
    document.getElementById('bulkUpdateForm').addEventListener('submit', function(e) {
        const status = document.getElementById('bulkStatus').value;
        const hours = document.getElementById('bulkHours').value;
        const selectedVolunteers = document.querySelectorAll('.volunteer-checkbox:checked');

        if (selectedVolunteers.length === 0) {
            e.preventDefault();
            alert('Please select at least one volunteer');
            return false;
        }

        if (!status) {
            e.preventDefault();
            alert('Please select a status');
            return false;
        }

        if (status === 'Attended' && (!hours || parseFloat(hours) <= 0)) {
            e.preventDefault();
            alert('Hours must be provided and greater than 0 for "Attended" status');
            return false;
        }

        if (status === 'No-Show' && hours && parseFloat(hours) > 0) {
            e.preventDefault();
            alert('Hours must be 0 for "No-Show" status. Volunteers who did not show cannot have hours logged.');
            return false;
        }
    });

    // Handle status change for conditional hours requirement
    function handleStatusChange() {
        const status = document.getElementById('editStatus').value;
        const hoursInput = document.getElementById('editHours');
        const hoursRequired = document.getElementById('hoursRequired');
        const hoursHint = document.getElementById('hoursHint');

        if (status === 'Attended') {
            hoursInput.disabled = false;
            hoursInput.required = true;
            hoursInput.min = '1';
            hoursRequired.classList.remove('hidden');
            hoursHint.textContent = 'Hours must be greater than 0 for "Attended" status';
            hoursHint.classList.remove('text-gray-500');
            hoursHint.classList.add('text-red-600');
        } else if (status === 'No-Show') {
            hoursInput.disabled = true;
            hoursInput.required = false;
            hoursInput.value = '0';
            hoursInput.min = '0';
            hoursRequired.classList.add('hidden');
            hoursHint.textContent = 'No-Show volunteers cannot have hours logged (locked at 0)';
            hoursHint.classList.remove('text-red-600');
            hoursHint.classList.add('text-gray-500');
        } else {
            hoursInput.disabled = false;
            hoursInput.required = false;
            hoursInput.min = '0';
            hoursRequired.classList.add('hidden');
            hoursHint.textContent = 'Optional for this status';
            hoursHint.classList.remove('text-red-600');
            hoursHint.classList.add('text-gray-500');
        }
    }

    // Edit Modal
    function openEditModal(volunteerId, status, hours, roleId) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        form.action = `/events/{{ $event->Event_ID }}/volunteers/${volunteerId}/hours`;
        document.getElementById('editStatus').value = status;
        document.getElementById('editHours').value = hours;

        @if($roles->count() > 0)
        const roleSelect = document.getElementById('editRole');
        if (roleSelect) {
            roleSelect.value = roleId || '';
        }
        @endif

        // Update hours field based on initial status
        handleStatusChange();

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
