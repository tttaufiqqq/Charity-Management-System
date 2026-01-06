<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-50 to-purple-50" x-data="eventForm()">
<div class="min-h-screen">
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Event</h1>
                    <p class="text-gray-600 mt-1">Organize volunteers and make an impact in your community</p>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-700">Form Progress</span>
                    <span class="text-sm font-semibold text-purple-600"><span x-text="formProgress"></span>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + formProgress + '%'"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
            <form action="{{ route('events.store') }}" method="POST">
                @csrf

                <!-- Basic Information Section -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-purple-100">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Basic Information</h2>
                    </div>

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            Event Title
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               x-model="title"
                               maxlength="100"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                               placeholder="e.g., Community Food Drive & Distribution">
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs text-gray-500">Give your event an engaging and descriptive name</p>
                            <span class="text-xs font-medium" :class="title.length > 80 ? 'text-orange-600' : 'text-gray-500'">
                                <span x-text="title.length"></span>/100
                            </span>
                        </div>
                        @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            Event Description
                        </label>
                        <textarea name="description" id="description" rows="5"
                                  x-model="description"
                                  maxlength="1000"
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                  placeholder="Describe your event... What will volunteers do? What's the impact? What should they know?">{{ old('description') }}</textarea>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs text-gray-500">💡 Tip: Include activities, goals, and what volunteers can expect</p>
                            <span class="text-xs font-medium" :class="description.length > 800 ? 'text-orange-600' : 'text-gray-500'">
                                <span x-text="description.length"></span>/1000
                            </span>
                        </div>
                        @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location and Capacity -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="location" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Event Location
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" required
                                   x-model="location"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                   placeholder="e.g., Community Center, Kuala Lumpur">
                            <p class="mt-2 text-xs text-gray-500">Include the full address or landmark</p>
                            @error('location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="capacity" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Max Capacity
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" min="1" required
                                   x-model="capacity"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                   placeholder="50">
                            <p class="mt-2 text-xs text-gray-500">Total volunteers you can accommodate</p>
                            @error('capacity')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Schedule Section -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-purple-100">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Event Schedule</h2>
                    </div>

                    <!-- Date Range -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Start Date & Time
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                   x-model="startDate"
                                   min="{{ date('Y-m-d\TH:i') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                            <p class="mt-2 text-xs text-gray-500">When does the event start?</p>
                            @error('start_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                End Date & Time
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                   x-model="endDate"
                                   min="{{ date('Y-m-d\TH:i') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                            <p class="mt-2 text-xs text-gray-500" x-show="eventDuration > 0">
                                Duration: <span class="font-semibold text-purple-600" x-text="eventDuration"></span> hour<span x-show="eventDuration !== 1">s</span>
                            </p>
                            @error('end_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Capacity Summary (Live) -->
                <div x-show="roles.length > 0" class="mb-8 p-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border-2 border-purple-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Volunteer Roles Summary</h3>
                            <p class="text-sm text-gray-600">Total positions needed across all roles</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-purple-600" x-text="totalVolunteersNeeded()"></div>
                            <div class="text-sm text-gray-600">
                                <span x-text="roles.length"></span> <span x-text="roles.length === 1 ? 'role' : 'roles'"></span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700">Event Capacity</span>
                            <span class="font-semibold text-gray-900"><span x-text="capacity || 0"></span> volunteers</span>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-2 rounded-full transition-all"
                                 :style="'width: ' + Math.min((totalVolunteersNeeded() / (capacity || 1)) * 100, 100) + '%'"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500" x-show="totalVolunteersNeeded() > capacity">
                            ⚠️ Roles exceed capacity - consider increasing capacity or reducing role needs
                        </p>
                    </div>
                </div>

                <!-- Volunteer Roles Section -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-purple-100">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-gray-900">Volunteer Roles</h2>
                            <p class="text-sm text-gray-600 mt-1">Define specific roles to organize your volunteers effectively</p>
                        </div>
                        <button type="button" @click="addRole()"
                                class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all text-sm font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Role
                        </button>
                    </div>

                    <!-- Info Banner about Roles -->
                    <div x-show="roles.length === 0" class="mb-6 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-purple-900 mb-2">💡 Organize Volunteers with Roles</h3>
                                <p class="text-purple-800 mb-3">
                                    Break down your event into specific volunteer roles for better organization and efficiency. Each role can have its own responsibilities and volunteer count.
                                </p>
                                <div class="bg-white rounded-lg p-4 border border-purple-200">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Popular role examples:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">Registration Desk</span>
                                        <span class="px-3 py-1 bg-pink-100 text-pink-700 text-xs font-medium rounded-full">Food Distribution</span>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">Setup Crew</span>
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Guest Support</span>
                                        <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">Clean-up Team</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles List -->
                    <div class="space-y-4">
                        <template x-for="(role, index) in roles" :key="index">
                            <div class="border-2 border-purple-200 rounded-xl p-6 bg-gradient-to-r from-white to-purple-50 shadow-sm hover:shadow-md transition-all">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900" x-text="role.name || ('Role ' + (index + 1))"></h4>
                                            <p class="text-sm text-gray-600" x-text="role.volunteers_needed ? (role.volunteers_needed + ' volunteers needed') : 'No volunteers set'"></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeRole(index)"
                                            class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Remove
                                    </button>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
                                        <input type="text" :name="'roles[' + index + '][name]'" x-model="role.name" required
                                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                               placeholder="e.g., Food Distributor">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Volunteers Needed *</label>
                                        <input type="number" :name="'roles[' + index + '][volunteers_needed]'" x-model="role.volunteers_needed" required min="1"
                                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                               placeholder="10">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Description</label>
                                    <textarea :name="'roles[' + index + '][description]'" x-model="role.description" rows="2"
                                              class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                              placeholder="Describe responsibilities... (e.g., Help distribute meals to families in need)"></textarea>
                                </div>
                            </div>
                        </template>

                        <div x-show="roles.length === 0" class="text-center py-16 border-2 border-dashed border-purple-300 rounded-xl bg-gradient-to-br from-purple-50 to-pink-50">
                            <div class="w-20 h-20 mx-auto mb-4 bg-purple-600 rounded-full flex items-center justify-center animate-pulse">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">No volunteer roles yet</h3>
                            <p class="text-gray-600 mb-4 max-w-md mx-auto">
                                Click the <span class="font-semibold text-purple-600">"Add Role"</span> button above to create your first volunteer position.
                            </p>
                            <p class="text-sm text-gray-500">
                                💡 Tip: Most events need 3-5 different roles for smooth operation
                            </p>
                        </div>
                    </div>
                    @error('roles')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Banner -->
                <div class="mb-6 bg-blue-50 border-2 border-blue-200 rounded-xl p-5 flex gap-4">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-semibold text-blue-900 mb-1">Your event needs admin approval</h4>
                        <p class="text-sm text-blue-800">After submission, an administrator will review your event. You'll be notified once it's approved and visible to volunteers!</p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-between items-center pt-6 border-t-2 border-gray-200">
                    <a href="{{ route('events.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Create Event
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    function eventForm() {
        return {
            title: '{{ old('title') }}',
            description: '{{ old('description') }}',
            location: '{{ old('location') }}',
            capacity: {{ old('capacity') ?? 0 }},
            startDate: '{{ old('start_date') }}',
            endDate: '{{ old('end_date') }}',
            roles: [],

            addRole() {
                this.roles.push({
                    name: '',
                    description: '',
                    volunteers_needed: 1
                });
            },

            removeRole(index) {
                this.roles.splice(index, 1);
            },

            totalVolunteersNeeded() {
                return this.roles.reduce((total, role) => {
                    return total + (parseInt(role.volunteers_needed) || 0);
                }, 0);
            },

            get eventDuration() {
                if (!this.startDate || !this.endDate) return 0;
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                const diffMs = Math.abs(end - start);
                return Math.round(diffMs / (1000 * 60 * 60)); // Convert to hours
            },

            get formProgress() {
                let progress = 0;
                if (this.title) progress += 20;
                if (this.description) progress += 15;
                if (this.location) progress += 15;
                if (this.capacity > 0) progress += 10;
                if (this.startDate) progress += 15;
                if (this.endDate) progress += 15;
                if (this.roles.length > 0) progress += 10;
                return progress;
            }
        }
    }
</script>
</body>
</html>
