<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Test: {{ $procedureName }}
            </h2>
            <a href="{{ route('procedures.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; Back to Procedures
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Procedure Info -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 mb-6 text-white">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold font-mono">{{ $procedureName }}</h3>
                        <p class="text-blue-100 mt-1">Database: {{ $database }}</p>
                        <p class="text-sm text-blue-200 mt-2">Returns user statistics grouped by role, including user count and registration dates.</p>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h4 class="font-semibold text-gray-900 mb-4">Filter Options</h4>
                <form action="{{ route('procedures.user-role-stats') }}" method="GET" class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role Name (optional)</label>
                        <select name="role" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Roles</option>
                            <option value="admin" {{ $filterRole === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="organizer" {{ $filterRole === 'organizer' ? 'selected' : '' }}>Organizer</option>
                            <option value="volunteer" {{ $filterRole === 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                            <option value="donor" {{ $filterRole === 'donor' ? 'selected' : '' }}>Donor</option>
                            <option value="public" {{ $filterRole === 'public' ? 'selected' : '' }}>Public</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        Execute Procedure
                    </button>
                </form>
            </div>

            <!-- SQL Preview -->
            <div class="bg-gray-900 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs text-gray-400">SQL Command:</span>
                </div>
                <code class="text-green-400 text-sm font-mono">
                    CALL {{ $procedureName }}({{ $filterRole ? "'$filterRole'" : 'NULL' }}, 'session_id');
                </code>
            </div>

            <!-- Results -->
            @if(!empty($stats))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-900">Results ({{ count($stats) }} rows)</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latest User Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oldest User Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($stats as $stat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                @if($stat['role_name'] === 'admin') bg-red-100 text-red-800
                                                @elseif($stat['role_name'] === 'organizer') bg-purple-100 text-purple-800
                                                @elseif($stat['role_name'] === 'volunteer') bg-green-100 text-green-800
                                                @elseif($stat['role_name'] === 'donor') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($stat['role_name']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-2xl font-bold text-gray-900">{{ number_format($stat['user_count']) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            @if($stat['latest_user_created'])
                                                {{ \Carbon\Carbon::parse($stat['latest_user_created'])->format('M d, Y H:i') }}
                                                <br>
                                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($stat['latest_user_created'])->diffForHumans() }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            @if($stat['oldest_user_created'])
                                                {{ \Carbon\Carbon::parse($stat['oldest_user_created'])->format('M d, Y H:i') }}
                                                <br>
                                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($stat['oldest_user_created'])->diffForHumans() }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Visual Chart -->
                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">User Distribution by Role</h4>
                    <div class="flex items-end gap-4 h-48">
                        @php $maxCount = collect($stats)->max('user_count') ?: 1; @endphp
                        @foreach($stats as $stat)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-indigo-500 rounded-t-lg transition-all"
                                     style="height: {{ ($stat['user_count'] / $maxCount) * 100 }}%">
                                </div>
                                <div class="text-xs font-medium text-gray-600 mt-2">{{ ucfirst($stat['role_name']) }}</div>
                                <div class="text-sm font-bold text-gray-900">{{ $stat['user_count'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                    <svg class="w-12 h-12 text-yellow-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-yellow-800 font-medium">No results returned from the procedure.</p>
                    <p class="text-yellow-600 text-sm mt-1">Click "Execute Procedure" to run the query.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
