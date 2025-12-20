<!-- resources/views/volunteer-management/skill/index.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Skills - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        .skill-card {
            transition: all 0.3s ease;
        }
        .skill-card:hover {
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex flex-col">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">My Skills Portfolio</h1>
                    <p class="text-gray-600">Showcase your expertise and help us match you with the right opportunities</p>
                </div>
                <button onclick="openAddModal()" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Skill
                </button>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg shadow-sm animate-slide-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm animate-slide-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($volunteerSkills->count() > 0)
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Total Skills -->
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Skills</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $volunteerSkills->count() }}</p>
                        </div>
                        <div class="bg-indigo-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Expert Level Skills -->
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Expert Level</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $volunteerSkills->where('pivot.Skill_Level', 'Expert')->count() }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Advanced Level Skills -->
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Advanced</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $volunteerSkills->where('pivot.Skill_Level', 'Advanced')->count() }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Intermediate/Beginner -->
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Learning</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $volunteerSkills->whereIn('pivot.Skill_Level', ['Beginner', 'Intermediate'])->count() }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchSkills" placeholder="Search your skills..."
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="md:w-64">
                        <select id="filterLevel" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Levels</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                            <option value="Expert">Expert</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Skills Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" id="skillsGrid">
                @foreach($volunteerSkills as $skill)
                    <div class="skill-card bg-white rounded-xl shadow-md hover:shadow-2xl p-6 border-t-4
                        @if($skill->pivot->Skill_Level === 'Beginner') border-blue-400
                        @elseif($skill->pivot->Skill_Level === 'Intermediate') border-green-400
                        @elseif($skill->pivot->Skill_Level === 'Advanced') border-purple-400
                        @else border-orange-400
                        @endif"
                        data-skill-name="{{ strtolower($skill->Skill_Name) }}"
                        data-skill-level="{{ $skill->pivot->Skill_Level }}">

                        <!-- Skill Icon & Title -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-md">
                                        <span class="text-2xl text-white font-bold">{{ substr($skill->Skill_Name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ $skill->Skill_Name }}</h3>
                                        @if($skill->Description)
                                            <p class="text-xs text-gray-500 line-clamp-1">{{ $skill->Description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Skill Level Badge and Progress -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600">Proficiency</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    @if($skill->pivot->Skill_Level === 'Beginner') bg-blue-100 text-blue-800
                                    @elseif($skill->pivot->Skill_Level === 'Intermediate') bg-green-100 text-green-800
                                    @elseif($skill->pivot->Skill_Level === 'Advanced') bg-purple-100 text-purple-800
                                    @else bg-orange-100 text-orange-800
                                    @endif">
                                    {{ $skill->pivot->Skill_Level }}
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full transition-all
                                    @if($skill->pivot->Skill_Level === 'Beginner') bg-gradient-to-r from-blue-400 to-blue-600
                                    @elseif($skill->pivot->Skill_Level === 'Intermediate') bg-gradient-to-r from-green-400 to-green-600
                                    @elseif($skill->pivot->Skill_Level === 'Advanced') bg-gradient-to-r from-purple-400 to-purple-600
                                    @else bg-gradient-to-r from-orange-400 to-orange-600
                                    @endif"
                                    style="width:
                                    @if($skill->pivot->Skill_Level === 'Beginner') 25%
                                    @elseif($skill->pivot->Skill_Level === 'Intermediate') 50%
                                    @elseif($skill->pivot->Skill_Level === 'Advanced') 75%
                                    @else 100%
                                    @endif">
                                </div>
                            </div>
                        </div>

                        <!-- Level Description -->
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600">
                                @if($skill->pivot->Skill_Level === 'Beginner')
                                    <strong>Beginner:</strong> Learning the basics and fundamentals
                                @elseif($skill->pivot->Skill_Level === 'Intermediate')
                                    <strong>Intermediate:</strong> Comfortable with regular tasks
                                @elseif($skill->pivot->Skill_Level === 'Advanced')
                                    <strong>Advanced:</strong> Can handle complex challenges
                                @else
                                    <strong>Expert:</strong> Deep expertise and mastery
                                @endif
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pt-4 border-t border-gray-200">
                            <button onclick="openEditModal({{ $skill->Skill_ID }}, '{{ $skill->Skill_Name }}', '{{ $skill->pivot->Skill_Level }}')"
                                    class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-md">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Update
                            </button>
                            <button onclick="confirmDelete({{ $skill->Skill_ID }}, '{{ $skill->Skill_Name }}')"
                                    class="bg-red-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:bg-red-700 transition-all transform hover:scale-105 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden bg-white rounded-xl shadow-md p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No skills found</h3>
                <p class="text-gray-600">Try adjusting your search or filter criteria</p>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Build Your Skills Portfolio</h3>
                    <p class="text-gray-600 mb-8">Start showcasing your talents! Add skills to help us match you with volunteer opportunities that align with your expertise.</p>

                    <!-- Popular Skills Suggestions -->
                    <div class="mb-8">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Popular skills to get started:</p>
                        <div class="flex flex-wrap gap-2 justify-center">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-sm font-medium">Teaching</span>
                            <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-sm font-medium">Event Planning</span>
                            <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm font-medium">Communication</span>
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">Leadership</span>
                            <span class="px-3 py-1 bg-orange-50 text-orange-700 rounded-full text-sm font-medium">Fundraising</span>
                        </div>
                    </div>

                    <button onclick="openAddModal()" class="inline-flex items-center bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-4 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Your First Skill
                    </button>
                </div>
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
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden animate-slide-in">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-white">Add New Skill</h3>
                <button onclick="closeAddModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form method="POST" action="{{ route('volunteer.skills.store') }}" class="p-6">
            @csrf

            <div class="mb-5">
                <label for="skill_id" class="block text-sm font-semibold text-gray-700 mb-2">Select Skill</label>
                <div class="relative">
                    <select id="skill_id" name="skill_id" required
                            class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Choose a skill...</option>
                        @foreach($allSkills as $skill)
                            @if(!$volunteerSkills->contains('Skill_ID', $skill->Skill_ID))
                                <option value="{{ $skill->Skill_ID }}">{{ $skill->Skill_Name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>

            <div class="mb-6">
                <label for="skill_level" class="block text-sm font-semibold text-gray-700 mb-2">Proficiency Level</label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-all">
                        <input type="radio" name="skill_level" value="Beginner" required class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">Beginner</span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">25%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Learning the basics and fundamentals</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-all">
                        <input type="radio" name="skill_level" value="Intermediate" required class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">Intermediate</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">50%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Comfortable with regular tasks</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-all">
                        <input type="radio" name="skill_level" value="Advanced" required class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">Advanced</span>
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full font-medium">75%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Can handle complex challenges</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-all">
                        <input type="radio" name="skill_level" value="Expert" required class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">Expert</span>
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full font-medium">100%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Deep expertise and mastery</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeAddModal()"
                        class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
                    Add Skill
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Skill Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden animate-slide-in">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-white">Update Skill Level</h3>
                <button onclick="closeEditModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="editForm" method="POST" action="" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Skill Name</label>
                <input type="text" id="edit_skill_name" readonly
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-700 font-medium">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Update Proficiency Level</label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-all">
                        <input type="radio" name="skill_level" value="Beginner" required class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">Beginner</span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">25%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Learning the basics and fundamentals</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-all">
                        <input type="radio" name="skill_level" value="Intermediate" required class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">Intermediate</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">50%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Comfortable with regular tasks</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-all">
                        <input type="radio" name="skill_level" value="Advanced" required class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">Advanced</span>
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full font-medium">75%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Can handle complex challenges</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-all">
                        <input type="radio" name="skill_level" value="Expert" required class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">Expert</span>
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full font-medium">100%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Deep expertise and mastery</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
                    Update Level
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-slide-in">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-500 to-orange-600 px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-white">Confirm Removal</h3>
                </div>
                <button onclick="closeDeleteModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <p class="text-gray-700 text-lg">Are you sure you want to remove <strong id="delete_skill_name" class="text-gray-900"></strong> from your skills?</p>
                <p class="text-sm text-gray-500 mt-2">This action cannot be undone.</p>
            </div>

            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-red-600 to-orange-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-red-700 hover:to-orange-700 transition-all shadow-lg">
                        Yes, Remove
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Search and Filter Functionality
    const searchInput = document.getElementById('searchSkills');
    const filterSelect = document.getElementById('filterLevel');
    const skillCards = document.querySelectorAll('.skill-card');
    const noResults = document.getElementById('noResults');

    function filterSkills() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const selectedLevel = filterSelect?.value || '';
        let visibleCount = 0;

        skillCards.forEach(card => {
            const skillName = card.getAttribute('data-skill-name');
            const skillLevel = card.getAttribute('data-skill-level');

            const matchesSearch = skillName.includes(searchTerm);
            const matchesLevel = !selectedLevel || skillLevel === selectedLevel;

            if (matchesSearch && matchesLevel) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (noResults) {
            if (visibleCount === 0 && skillCards.length > 0) {
                noResults.classList.remove('hidden');
                document.getElementById('skillsGrid').classList.add('hidden');
            } else {
                noResults.classList.add('hidden');
                document.getElementById('skillsGrid').classList.remove('hidden');
            }
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterSkills);
    if (filterSelect) filterSelect.addEventListener('change', filterSkills);

    // Add Modal Functions
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Edit Modal Functions
    function openEditModal(skillId, skillName, skillLevel) {
        document.getElementById('edit_skill_name').value = skillName;

        // Select the correct radio button
        const radios = document.querySelectorAll('#editModal input[name="skill_level"]');
        radios.forEach(radio => {
            if (radio.value === skillLevel) {
                radio.checked = true;
                radio.closest('label').classList.add('border-indigo-500', 'bg-indigo-50');
            } else {
                radio.closest('label').classList.remove('border-indigo-500', 'bg-indigo-50');
            }
        });

        document.getElementById('editForm').action = `/volunteer/skills/${skillId}`;
        document.getElementById('editModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Delete Modal Functions
    function confirmDelete(skillId, skillName) {
        document.getElementById('delete_skill_name').textContent = skillName;
        document.getElementById('deleteForm').action = `/volunteer/skills/${skillId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Radio button visual feedback
    document.querySelectorAll('input[type="radio"][name="skill_level"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove styling from all labels in the same form
            const form = this.closest('form');
            form.querySelectorAll('input[type="radio"][name="skill_level"]').forEach(r => {
                r.closest('label').classList.remove('border-indigo-500', 'bg-indigo-50');
            });
            // Add styling to selected label
            if (this.checked) {
                this.closest('label').classList.add('border-indigo-500', 'bg-indigo-50');
            }
        });
    });

    // Close modals on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAddModal();
            closeEditModal();
            closeDeleteModal();
        }
    });

    // Close modal when clicking outside
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'addModal') closeAddModal();
                if (this.id === 'editModal') closeEditModal();
                if (this.id === 'deleteModal') closeDeleteModal();
            }
        });
    });
</script>

</body>
</html>
