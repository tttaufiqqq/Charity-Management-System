<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="eventForm()">
<div class="min-h-screen">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New Event</h1>

            <form action="{{ route('events.store') }}" method="POST">
                @csrf

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div class="mb-6">
                    <label for="goal_amount" class="block text-sm font-medium text-gray-700 mb-2">Capacity *</label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" min="1" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('capacity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div class="mb-6">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" id="location"
                           value="{{ old('location') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="e.g., Central Park, New York">
                    @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Capacity Summary (Live) -->
                <div x-show="roles.length > 0" class="mb-6 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-1">Event Capacity Summary</h3>
                            <p class="text-xs text-gray-600">Total volunteer capacity across all roles</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-indigo-600" x-text="totalVolunteersNeeded()"></div>
                            <div class="text-xs text-gray-600">
                                <span x-text="roles.length"></span> <span x-text="roles.length === 1 ? 'role' : 'roles'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Banner about Roles -->
                <div class="mb-6 bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-indigo-900 mb-2">💡 Organize Volunteers with Multiple Roles</h3>
                            <p class="text-indigo-800 mb-3">
                                Break down your event into specific volunteer roles for better organization and efficiency. Each role can have its own responsibilities and volunteer count.
                            </p>
                            <div class="bg-white rounded-lg p-4 border border-indigo-200">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Example roles you might need:</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full">Registration Desk</span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">Food Distribution</span>
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">Setup Crew</span>
                                    <span class="px-3 py-1 bg-pink-100 text-pink-700 text-xs font-medium rounded-full">Guest Support</span>
                                    <span class="px-3 py-1 bg-cyan-100 text-cyan-700 text-xs font-medium rounded-full">Clean-up Team</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volunteer Roles Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <label class="block text-lg font-bold text-gray-900">Volunteer Roles *</label>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold text-indigo-600">Define multiple roles</span> to organize volunteers and distribute responsibilities effectively
                            </p>
                        </div>
                        <button type="button" @click="addRole()"
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all text-sm font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Volunteer Role
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(role, index) in roles" :key="index">
                            <div class="border-2 border-indigo-200 rounded-lg p-5 bg-white shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-base font-semibold text-gray-800" x-text="'Role ' + (index + 1)"></h4>
                                    </div>
                                    <button type="button" @click="removeRole(index)" class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
                                        <input type="text" :name="'roles[' + index + '][name]'" x-model="role.name" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="e.g., Food Distributor, Setup Crew">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Volunteers Needed *</label>
                                        <input type="number" :name="'roles[' + index + '][volunteers_needed]'" x-model="role.volunteers_needed" required min="1"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="10">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea :name="'roles[' + index + '][description]'" x-model="role.description" rows="2"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                              placeholder="Describe the responsibilities for this role... (e.g., Help distribute meals to families in need)"></textarea>
                                </div>
                            </div>
                        </template>

                        <div x-show="roles.length === 0" class="text-center py-16 border-2 border-dashed border-indigo-300 rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50">
                            <div class="w-20 h-20 mx-auto mb-4 bg-indigo-600 rounded-full flex items-center justify-center animate-pulse">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">No volunteer roles yet - Let's add some!</h3>
                            <p class="text-gray-600 mb-4 max-w-md mx-auto">
                                Click the <span class="font-semibold text-indigo-600">"Add Volunteer Role"</span> button above to create your first volunteer position.
                            </p>
                            <p class="text-sm text-gray-500">
                                💡 Tip: Most events need 3-5 different roles for smooth operation
                            </p>
                        </div>
                    </div>
                    @error('roles')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('events.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
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
            totalRoles() {
                return this.roles.length;
            }
        }
    }
</script>
</body>
</html>
