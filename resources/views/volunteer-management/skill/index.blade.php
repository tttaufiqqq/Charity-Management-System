<!-- resources/views/volunteer-management/skill/index.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Skills - CharityHub</title>
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
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Skills</h1>
                    <p class="text-gray-600 mt-1">Manage your skills and expertise levels</p>
                </div>
                <button onclick="openAddModal()" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Skill
                </button>
            </div>
        </div>

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

        <!-- Skills Grid -->
        @if($volunteerSkills->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($volunteerSkills as $skill)
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $skill->Skill_Name }}</h3>
                                @if($skill->Description)
                                    <p class="text-sm text-gray-600 mb-3">{{ $skill->Description }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Skill Level Badge -->
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($skill->pivot->Skill_Level === 'Beginner') bg-blue-100 text-blue-800
                                @elseif($skill->pivot->Skill_Level === 'Intermediate') bg-green-100 text-green-800
                                @elseif($skill->pivot->Skill_Level === 'Advanced') bg-purple-100 text-purple-800
                                @else bg-orange-100 text-orange-800
                                @endif">
                                {{ $skill->pivot->Skill_Level }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pt-4 border-t border-gray-200">
                            <button onclick="openEditModal({{ $skill->Skill_ID }}, '{{ $skill->Skill_Name }}', '{{ $skill->pivot->Skill_Level }}')"
                                    class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                Update Level
                            </button>
                            <button onclick="confirmDelete({{ $skill->Skill_ID }}, '{{ $skill->Skill_Name }}')"
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                                Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No skills added yet</h3>
                <p class="text-gray-600 mb-6">Start building your profile by adding your skills and expertise</p>
                <button onclick="openAddModal()" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                    Add Your First Skill
                </button>
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

<!-- Add Skill Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Add New Skill</h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('volunteer.skills.store') }}">
            @csrf

            <div class="mb-4">
                <label for="skill_id" class="block text-sm font-medium text-gray-700 mb-1">Select Skill</label>
                <select id="skill_id" name="skill_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Choose a skill...</option>
                    @foreach($allSkills as $skill)
                        @if(!$volunteerSkills->contains('Skill_ID', $skill->Skill_ID))
                            <option value="{{ $skill->Skill_ID }}">{{ $skill->Skill_Name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="skill_level" class="block text-sm font-medium text-gray-700 mb-1">Skill Level</label>
                <select id="skill_level" name="skill_level" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select level...</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                    <option value="Expert">Expert</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeAddModal()"
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Add Skill
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Skill Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Update Skill Level</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Skill Name</label>
                <input type="text" id="edit_skill_name" readonly
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
            </div>

            <div class="mb-6">
                <label for="edit_skill_level" class="block text-sm font-medium text-gray-700 mb-1">Skill Level</label>
                <select id="edit_skill_level" name="skill_level" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                    <option value="Expert">Expert</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Remove Skill</h3>
            <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <p class="text-gray-600 mb-6">Are you sure you want to remove <strong id="delete_skill_name"></strong> from your skills?</p>

        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')

            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors">
                    Remove
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Add Modal Functions
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    // Edit Modal Functions
    function openEditModal(skillId, skillName, skillLevel) {
        document.getElementById('edit_skill_name').value = skillName;
        document.getElementById('edit_skill_level').value = skillLevel;
        document.getElementById('editForm').action = `/volunteer/skills/${skillId}`;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Delete Modal Functions
    function confirmDelete(skillId, skillName) {
        document.getElementById('delete_skill_name').textContent = skillName;
        document.getElementById('deleteForm').action = `/volunteer/skills/${skillId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modals on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAddModal();
            closeEditModal();
            closeDeleteModal();
        }
    });
</script>

</body>
</html>
