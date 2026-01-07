<div x-data="{ activeTab: '{{ $activeTab }}' }" x-init="$watch('activeTab', value => $wire.set('activeTab', value))">
    <!-- Loading Indicator -->
    <div wire:loading class="mb-6">
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-indigo-900">Loading analytics data...</span>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-sm p-4 border border-blue-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-blue-700">Total Users</div>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalUsers) }}</div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-sm p-4 border border-purple-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-purple-700">Organizations</div>
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalOrganizations) }}</div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-sm p-4 border border-green-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-green-700">Campaigns</div>
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalCampaigns) }}</div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow-sm p-4 border border-orange-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-orange-700">Events</div>
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalEvents) }}</div>
        </div>

        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg shadow-sm p-4 border border-indigo-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-indigo-700">Volunteers</div>
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalVolunteers) }}</div>
        </div>

        <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg shadow-sm p-4 border border-pink-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-pink-700">Donations</div>
                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalDonations) }}</div>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-green-100 mb-1 font-medium">Total Raised</div>
                    <div class="text-3xl font-bold">RM {{ number_format($totalRaised, 2) }}</div>
                    <div class="text-xs text-green-100 mt-2">{{ number_format($totalDonations) }} transactions</div>
                </div>
                <svg class="w-16 h-16 text-green-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-blue-100 mb-1 font-medium">Total Allocated</div>
                    <div class="text-3xl font-bold">RM {{ number_format($totalAllocated, 2) }}</div>
                    <div class="text-xs text-blue-100 mt-2">{{ number_format(($totalAllocated / max($totalRaised, 1)) * 100, 1) }}% allocated</div>
                </div>
                <svg class="w-16 h-16 text-blue-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-purple-100 mb-1 font-medium">Available Funds</div>
                    <div class="text-3xl font-bold">RM {{ number_format($totalRaised - $totalAllocated, 2) }}</div>
                    <div class="text-xs text-purple-100 mt-2">Ready for allocation</div>
                </div>
                <svg class="w-16 h-16 text-purple-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <!-- Suggest Recipients -->
        <a href="{{ route('admin.campaigns.suggestions') }}"
           class="block bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 border-2 border-transparent hover:shadow-2xl transition-all transform hover:scale-[1.02] group">
            <div class="flex items-start gap-4 mb-4">
                <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl group-hover:bg-white/30 transition-colors">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2">
                        Suggest Recipients for Campaigns
                    </h3>
                    <p class="text-indigo-100 text-sm leading-relaxed">
                        Review active campaigns and suggest approved recipients who match campaign needs.
                    </p>
                </div>
                <svg class="w-6 h-6 text-white/80 group-hover:text-white group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </a>

        <!-- Register Recipient -->
        <a href="{{ route('admin.recipients.create') }}"
           class="block bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-lg p-6 border-2 border-transparent hover:shadow-2xl transition-all transform hover:scale-[1.02] group">
            <div class="flex items-start gap-4 mb-4">
                <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl group-hover:bg-white/30 transition-colors">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2">
                        Register New Recipient
                    </h3>
                    <p class="text-emerald-100 text-sm leading-relaxed">
                        Add a new recipient to the system. Auto-approved and immediately available for allocation.
                    </p>
                </div>
                <svg class="w-6 h-6 text-white/80 group-hover:text-white group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </a>
    </div>

    <!-- Pending Approvals Alert -->
    @if($pendingApprovals['campaigns'] > 0 || $pendingApprovals['events'] > 0 || $pendingApprovals['recipients'] > 0)
        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-yellow-400 p-4 mb-8 rounded-r-lg shadow">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-yellow-800">Pending Approvals Require Your Attention</h3>
                    <div class="mt-2 grid md:grid-cols-3 gap-3">
                        @if($pendingApprovals['campaigns'] > 0)
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-2xl font-bold text-yellow-600">{{ $pendingApprovals['campaigns'] }}</div>
                                <div class="text-xs text-gray-600">Campaigns awaiting approval</div>
                            </div>
                        @endif
                        @if($pendingApprovals['events'] > 0)
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-2xl font-bold text-yellow-600">{{ $pendingApprovals['events'] }}</div>
                                <div class="text-xs text-gray-600">Events awaiting approval</div>
                            </div>
                        @endif
                        @if($pendingApprovals['recipients'] > 0)
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-2xl font-bold text-yellow-600">{{ $pendingApprovals['recipients'] }}</div>
                                <div class="text-xs text-gray-600">Recipients awaiting approval</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <button
                    @click="activeTab = 'overview'"
                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'overview', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'overview' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Overview
                </button>
                <button
                    @click="activeTab = 'campaigns'"
                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'campaigns', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'campaigns' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Top Campaigns
                </button>
                <button
                    @click="activeTab = 'organizations'"
                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'organizations', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'organizations' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Organizations
                </button>
                <button
                    @click="activeTab = 'donors'"
                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'donors', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'donors' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Donor Insights
                </button>
                <button
                    @click="activeTab = 'events'"
                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'events', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'events' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Event Metrics
                </button>
            </nav>
        </div>
    </div>

    <!-- Overview Tab -->
    <div x-show="activeTab === 'overview'" style="display: block;">
        <!-- Performance Metrics Section -->
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900">Performance Analytics</h2>
            </div>

            <div class="grid lg:grid-cols-2 gap-8">
                <!-- Donations Chart -->
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <h3 class="text-xl font-bold text-gray-900">Donations Over Time</h3>
                                <x-chart-help title="Understanding Donations Over Time" description="This chart shows the total amount of donations received each day over the last 90 days.">
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <p class="text-xs font-semibold text-gray-700 mb-2">📊 How to Read:</p>
                                        <ul class="text-xs text-gray-600 space-y-1">
                                            <li>• <span class="font-medium">Blue line</span> = Daily donation amounts in RM</li>
                                            <li>• <span class="font-medium">Peaks</span> = High donation days</li>
                                            <li>• <span class="font-medium">Valleys</span> = Low donation days</li>
                                        </ul>
                                        <p class="text-xs font-semibold text-gray-700 mt-3 mb-1">💡 What to Look For:</p>
                                        <p class="text-xs text-gray-600">• Upward trends indicate growing donor engagement<br>• Spikes may correlate with campaigns or events<br>• Flat periods may need marketing push</p>
                                    </div>
                                </x-chart-help>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Total amount received per day (RM) - Last 90 days</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="h-96" wire:ignore>
                        <canvas id="donationsChart"></canvas>
                    </div>
                    <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-100">
                        <p class="text-xs text-green-800"><span class="font-semibold">💡 Tip:</span> Monitor spikes to identify successful campaigns. Use insights to plan future fundraising strategies.</p>
                    </div>
                </div>

                <!-- User Growth Chart -->
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <h3 class="text-xl font-bold text-gray-900">User Growth by Role</h3>
                                <x-chart-help title="Understanding User Growth" description="This stacked area chart shows how many new users registered each day, broken down by their role type.">
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <p class="text-xs font-semibold text-gray-700 mb-2">🎨 Color Legend:</p>
                                        <div class="space-y-1.5 text-xs">
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded bg-blue-500"></div>
                                                <span class="text-gray-700"><span class="font-medium">Blue</span> = Volunteers</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded bg-green-500"></div>
                                                <span class="text-gray-700"><span class="font-medium">Green</span> = Donors</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded bg-purple-500"></div>
                                                <span class="text-gray-700"><span class="font-medium">Purple</span> = Organizers</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded bg-gray-400"></div>
                                                <span class="text-gray-700"><span class="font-medium">Gray</span> = Public Users</span>
                                            </div>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-700 mt-3 mb-1">💡 What This Means:</p>
                                        <p class="text-xs text-gray-600">Stacked areas show total growth. Wider sections indicate more registrations of that role type.</p>
                                    </div>
                                </x-chart-help>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Daily new user registrations by role - Last 90 days</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="h-96" wire:ignore>
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                    <div class="mt-4 p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <p class="text-xs text-purple-800"><span class="font-semibold">💡 Insight:</span> Growing volunteer and donor numbers indicate healthy platform growth. Focus marketing on underrepresented roles.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Status Distribution Section -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-gradient-to-b from-blue-500 to-cyan-600 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900">Campaign Status Distribution</h2>
            </div>

            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h3 class="text-lg font-bold text-gray-900">Campaign Status Distribution</h3>
                            <x-chart-help title="Campaign Status Breakdown" description="This pie chart shows the proportion of campaigns in each status category.">
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs font-semibold text-gray-700 mb-2">📊 Status Types:</p>
                                    <ul class="text-xs text-gray-600 space-y-1">
                                        <li>• <span class="font-medium text-green-600">Active</span> = Currently accepting donations</li>
                                        <li>• <span class="font-medium text-yellow-600">Pending</span> = Awaiting admin approval</li>
                                        <li>• <span class="font-medium text-blue-600">Completed</span> = Goal reached or ended</li>
                                        <li>• <span class="font-medium text-gray-600">Cancelled</span> = Stopped/rejected campaigns</li>
                                    </ul>
                                    <p class="text-xs font-semibold text-gray-700 mt-3 mb-1">⚡ Action Items:</p>
                                    <p class="text-xs text-gray-600">Large "Pending" slice? Review and approve campaigns. Low "Active"? Encourage organizers to create new campaigns.</p>
                                </div>
                            </x-chart-help>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Percentage breakdown of all campaigns</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                    </div>
                </div>
                <div class="h-64" wire:ignore>
                    <canvas id="campaignStatusChart"></canvas>
                </div>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-xs text-blue-800"><span class="font-semibold">💡 Tip:</span> Hover over each slice to see exact numbers. Click legend items to show/hide specific statuses.</p>
                </div>
            </div>
        </div>

        <!-- Fund Allocation Section -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-gradient-to-b from-amber-500 to-orange-600 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900">Fund Allocation</h2>
            </div>

            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-6 border border-gray-100">
                <!-- Important Note -->
                <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-900 mb-1">Note about Fund Allocation Data</p>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                This chart shows allocated vs unallocated funds for the <span class="font-semibold">top 10 campaigns by amount raised</span>.
                                The totals here may differ from the "Total Allocated" card above, which shows allocations across <span class="font-semibold">all campaigns</span> (including smaller ones not shown in this chart).
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h3 class="text-lg font-bold text-gray-900">Fund Allocation Overview</h3>
                            <x-chart-help title="Fund Allocation Efficiency" description="This horizontal bar chart shows what percentage of raised funds have been allocated to recipients for the top 10 campaigns.">
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs font-semibold text-gray-700 mb-2">🎨 Bar Colors:</p>
                                    <div class="space-y-1.5 text-xs">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-green-500"></div>
                                            <span class="text-gray-700"><span class="font-medium">Green</span> = 80-100% allocated (Excellent)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-blue-500"></div>
                                            <span class="text-gray-700"><span class="font-medium">Blue</span> = 50-79% allocated (Good)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-orange-500"></div>
                                            <span class="text-gray-700"><span class="font-medium">Orange</span> = 1-49% allocated (Needs attention)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-gray-400"></div>
                                            <span class="text-gray-700"><span class="font-medium">Gray</span> = 0% allocated (Not started)</span>
                                        </div>
                                    </div>
                                    <p class="text-xs font-semibold text-gray-700 mt-3 mb-1">⚡ What to Do:</p>
                                    <p class="text-xs text-gray-600">Low percentages indicate funds waiting to be distributed. Work with organizers to allocate to approved recipients.</p>
                                </div>
                            </x-chart-help>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Top 10 Campaigns - Allocation Efficiency %</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="h-72">
                    <canvas id="allocationChart" wire:ignore.self data-allocation='@json($allocationEfficiency ?? [])'></canvas>
                </div>
            </div>
        </div>

        <!-- Campaign Recipients Report Section -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-gradient-to-b from-purple-500 to-fuchsia-600 rounded-full"></div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">Campaign Recipients Report</h2>
                    <p class="text-sm text-gray-600 mt-1">Track fund allocations and recipient impact across all campaigns</p>
                </div>
                <!-- Efficiency Filter -->
                <div class="flex items-center gap-2">
                    <label for="efficiencyFilter" class="text-sm font-medium text-gray-700">Filter:</label>
                    <select wire:model.live="efficiencyFilter" id="efficiencyFilter"
                            class="px-4 py-2 border border-purple-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        <option value="all">All Campaigns</option>
                        <option value="most_efficient">Most Efficient First</option>
                        <option value="least_efficient">Least Efficient First</option>
                    </select>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid md:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-br from-purple-50 to-fuchsia-100 rounded-xl p-6 border border-purple-200 shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm font-semibold text-purple-700">Total Campaigns</div>
                        <div class="p-2 bg-purple-200 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ count($allocationEfficiency) }}</div>
                </div>

                <div class="bg-gradient-to-br from-emerald-50 to-green-100 rounded-xl p-6 border border-emerald-200 shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm font-semibold text-emerald-700">Total Raised</div>
                        <div class="p-2 bg-emerald-200 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">RM {{ number_format($allocationEfficiency->sum('Collected_Amount'), 2) }}</div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200 shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm font-semibold text-blue-700">Total Allocated</div>
                        <div class="p-2 bg-blue-200 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">RM {{ number_format($allocationEfficiency->sum('allocated_amount'), 2) }}</div>
                </div>

                <div class="bg-gradient-to-br from-amber-50 to-orange-100 rounded-xl p-6 border border-amber-200 shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm font-semibold text-amber-700">Recipients Helped</div>
                        <div class="p-2 bg-amber-200 rounded-lg">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($totalRecipientsHelped) }}</div>
                    <div class="text-xs text-amber-600 mt-2">Unique recipients across all campaigns</div>
                </div>
            </div>

            <!-- Detailed Report Table -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-fuchsia-50 border-b border-purple-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Campaign-wise Allocation Details</h3>
                            <p class="text-xs text-gray-600 mt-0.5">Fund distribution and recipient impact analysis</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th wire:click="sortRecipients('Title')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <span>Campaign</span>
                                        @if($recipientSortBy === 'Title')
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                @if($recipientSortDirection === 'asc')
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                @else
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th wire:click="sortRecipients('Collected_Amount')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center justify-end gap-2">
                                        <span>Raised</span>
                                        @if($recipientSortBy === 'Collected_Amount')
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                @if($recipientSortDirection === 'asc')
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                @else
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th wire:click="sortRecipients('allocated_amount')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center justify-end gap-2">
                                        <span>Allocated</span>
                                        @if($recipientSortBy === 'allocated_amount')
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                @if($recipientSortDirection === 'asc')
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                @else
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th wire:click="sortRecipients('unallocated_amount')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center justify-end gap-2">
                                        <span>Remaining</span>
                                        @if($recipientSortBy === 'unallocated_amount')
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                @if($recipientSortDirection === 'asc')
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                @else
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th wire:click="sortRecipients('allocation_percentage')" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center justify-center gap-2">
                                        <span>Efficiency</span>
                                        @if($recipientSortBy === 'allocation_percentage')
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                @if($recipientSortDirection === 'asc')
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                @else
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th wire:click="sortRecipients('recipient_count')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center justify-end gap-2">
                                        <span>Recipients</span>
                                        @if($recipientSortBy === 'recipient_count')
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                @if($recipientSortDirection === 'asc')
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                @else
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($allocationEfficiency as $allocation)
                                <tr class="hover:bg-purple-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $allocation->Title }}</td>
                                    <td class="px-6 py-4 text-sm text-right font-bold text-emerald-600">RM {{ number_format($allocation->Collected_Amount, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-right font-bold text-blue-600">RM {{ number_format($allocation->allocated_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-right font-semibold text-amber-600">RM {{ number_format($allocation->unallocated_amount, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $allocation->allocation_percentage >= 80 ? 'bg-gradient-to-r from-green-500 to-emerald-600' : ($allocation->allocation_percentage >= 50 ? 'bg-gradient-to-r from-yellow-500 to-amber-600' : 'bg-gradient-to-r from-red-500 to-rose-600') }}"
                                                     style="width: {{ min($allocation->allocation_percentage ?? 0, 100) }}%"></div>
                                            </div>
                                            <span class="text-sm font-bold {{ $allocation->allocation_percentage >= 80 ? 'text-green-600' : ($allocation->allocation_percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $allocation->allocation_percentage ?? 0 }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right font-bold text-purple-600">{{ number_format($allocation->recipient_count) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-500">No allocation data available</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1 bg-gradient-to-b from-pink-500 to-rose-600 rounded-full"></div>
                    <h2 class="text-2xl font-bold text-gray-900">Recent Activity</h2>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg">
                    <button wire:click="$set('activityFilter', 'all')"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $activityFilter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        All Activity
                    </button>
                    <button wire:click="$set('activityFilter', 'donation')"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $activityFilter === 'donation' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Donations
                    </button>
                    <button wire:click="$set('activityFilter', 'campaign')"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $activityFilter === 'campaign' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Campaigns
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="space-y-3">
                    @forelse($recentActivity as $activity)
                        <div class="flex items-start gap-4 p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100 hover:shadow-sm transition-all">
                            <div class="flex-shrink-0">
                                @if($activity->type === 'donation')
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-emerald-200 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 mb-1">{{ $activity->actor }}</p>
                                <p class="text-sm text-gray-600">{{ $activity->description }}</p>
                            </div>
                            <div class="flex-shrink-0 text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                {{ \Carbon\Carbon::parse($activity->activity_date)->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-500">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns Tab -->
    <div x-show="activeTab === 'campaigns'" x-cloak>
        <!-- Top Campaigns Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Top 10 Campaigns by Amount Raised</h3>
                        <x-chart-help title="Top Performing Campaigns" description="This bar chart ranks the 10 campaigns that have raised the most funds, helping you identify your most successful fundraising efforts.">
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 mb-2">📊 How to Read:</p>
                                <ul class="text-xs text-gray-600 space-y-1">
                                    <li>• <span class="font-medium">Longer bars</span> = More money raised</li>
                                    <li>• <span class="font-medium">Top position</span> = Highest-performing campaign</li>
                                    <li>• <span class="font-medium">Bar color gradient</span> = Visual distinction</li>
                                </ul>
                                <p class="text-xs font-semibold text-gray-700 mt-3 mb-1">💡 Use This To:</p>
                                <p class="text-xs text-gray-600">• Identify what makes successful campaigns work<br>• Replicate winning strategies<br>• Recognize top-performing organizers</p>
                            </div>
                        </x-chart-help>
                    </div>
                    <span class="text-xs text-gray-500 mt-1 block">Visual comparison of fundraising performance</span>
                </div>
            </div>
            <div class="h-96" wire:ignore>
                <canvas id="topCampaignsChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top Performing Campaigns</h3>
                <p class="text-sm text-gray-600 mt-1">Campaigns ranked by total amount raised with donor analytics</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organizer</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Raised</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Goal</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Achievement</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Donors</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Donations</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topCampaigns as $campaign)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $campaign->campaign_title }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $campaign->organizer_name }}</td>
                                <td class="px-6 py-4 text-sm text-right font-semibold text-green-600">RM {{ number_format($campaign->Collected_Amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-600">RM {{ number_format($campaign->Goal_Amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <div class="w-20">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2 rounded-full" style="width: {{ min($campaign->achievement_percentage, 100) }}%"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-700">{{ $campaign->achievement_percentage }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-blue-600">{{ number_format($campaign->donor_count) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-purple-600">{{ number_format($campaign->donation_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">No campaign data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Allocation Efficiency -->
        <div class="mt-6 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Fund Allocation Efficiency</h3>
                <p class="text-sm text-gray-600 mt-1">Track how raised funds are being distributed to recipients</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Raised</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Allocated</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unallocated</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Efficiency</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($allocationEfficiency as $allocation)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $allocation->Title }}</td>
                                <td class="px-6 py-4 text-sm text-right font-semibold text-green-600">RM {{ number_format($allocation->Collected_Amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-semibold text-blue-600">RM {{ number_format($allocation->allocated_amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-semibold text-orange-600">RM {{ number_format($allocation->unallocated_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <div class="text-sm font-medium {{ $allocation->allocation_percentage >= 80 ? 'text-green-600' : ($allocation->allocation_percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $allocation->allocation_percentage ?? 0 }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-purple-600">{{ number_format($allocation->recipient_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No allocation data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Organizations Tab -->
    <div x-show="activeTab === 'organizations'" x-cloak>
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Organization Leaderboard</h3>
                <p class="text-sm text-gray-600 mt-1">Top performing organizations by fundraising impact</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Raised</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Campaigns</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Events</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($organizationLeaderboard as $index => $org)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-700' : ($index === 1 ? 'bg-gray-100 text-gray-700' : ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-50 text-gray-600')) }} font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $org->organizer_name }}</td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-green-600">RM {{ number_format($org->total_raised ?? 0, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-blue-600">{{ number_format($org->total_campaigns) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-purple-600">{{ number_format($org->total_events) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-emerald-600">{{ number_format($org->active_campaigns) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No organization data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Donors Tab -->
    <div x-show="activeTab === 'donors'" x-cloak>
        <!-- Top Donors Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Top 10 Donors by Total Contribution</h3>
                        <x-chart-help title="Top Donor Recognition" description="This bar chart highlights your most generous donors based on their total lifetime contributions to all campaigns.">
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 mb-2">📊 What You See:</p>
                                <ul class="text-xs text-gray-600 space-y-1">
                                    <li>• <span class="font-medium">Each bar</span> = One donor's total contributions</li>
                                    <li>• <span class="font-medium">Bar height</span> = Amount donated (RM)</li>
                                    <li>• <span class="font-medium">Ranking</span> = From highest to lowest contributor</li>
                                </ul>
                                <p class="text-xs font-semibold text-gray-700 mt-3 mb-1">💡 Actions to Take:</p>
                                <p class="text-xs text-gray-600">• Thank and recognize top donors<br>• Engage them for recurring campaigns<br>• Identify potential major gift prospects</p>
                            </div>
                        </x-chart-help>
                    </div>
                    <span class="text-xs text-gray-500 mt-1 block">Your most generous supporters - Donor leaderboard</span>
                </div>
            </div>
            <div class="h-96" wire:ignore>
                <canvas id="topDonorsChart"></canvas>
            </div>
            <div class="mt-4 p-3 bg-pink-50 rounded-lg border border-pink-100">
                <p class="text-xs text-pink-800"><span class="font-semibold">💡 Tip:</span> Consider creating VIP donor recognition programs for these top contributors to maintain their engagement.</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top Donor Insights</h3>
                <p class="text-sm text-gray-600 mt-1">Most generous donors and their contribution patterns</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donor</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Donated</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Donations</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Donation</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Campaigns</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First / Last</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($donorInsights as $donor)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $donor->donor_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $donor->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-green-600">RM {{ number_format($donor->total_donated, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-blue-600">{{ number_format($donor->donation_count) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-700">RM {{ number_format($donor->avg_donation, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-purple-600">{{ number_format($donor->campaigns_supported) }}</td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    <div>First: {{ \Carbon\Carbon::parse($donor->first_donation)->format('M d, Y') }}</div>
                                    <div>Last: {{ \Carbon\Carbon::parse($donor->last_donation)->format('M d, Y') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No donor data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Events Tab -->
    <div x-show="activeTab === 'events'" x-cloak>
        <!-- Event Fill Rates Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Event Fill Rates & Volunteer Hours</h3>
                        <x-chart-help title="Event Participation Metrics" description="This dual-axis chart shows both volunteer registration fill rates (bars) and total hours contributed (line) for your top 10 events.">
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 mb-2">📊 Reading the Chart:</p>
                                <ul class="text-xs text-gray-600 space-y-1">
                                    <li>• <span class="font-medium text-blue-600">Blue bars</span> = Fill rate % (registrations vs capacity)</li>
                                    <li>• <span class="font-medium text-green-600">Green line</span> = Total volunteer hours contributed</li>
                                    <li>• <span class="font-medium">Left axis</span> = Fill rate percentage (0-100%)</li>
                                    <li>• <span class="font-medium">Right axis</span> = Total hours</li>
                                </ul>
                                <p class="text-xs font-semibold text-gray-700 mt-3 mb-1">💡 What This Shows:</p>
                                <p class="text-xs text-gray-600">• High fill rates (>80%) = Strong interest<br>• Low fill rates (<50%) = May need more promotion<br>• High hours = High volunteer engagement</p>
                            </div>
                        </x-chart-help>
                    </div>
                    <span class="text-xs text-gray-500 mt-1 block">Volunteer participation and time contribution analysis</span>
                </div>
            </div>
            <div class="h-96" wire:ignore>
                <canvas id="eventMetricsChart"></canvas>
            </div>
            <div class="mt-4 p-3 bg-orange-50 rounded-lg border border-orange-100">
                <p class="text-xs text-orange-800"><span class="font-semibold">💡 Tip:</span> Events with low fill rates but high hours indicate dedicated volunteers. Events with high fill rates but low hours may need better volunteer engagement strategies.</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Event Participation Metrics</h3>
                <p class="text-sm text-gray-600 mt-1">Events ranked by volunteer engagement and participation</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organizer</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Fill Rate</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($eventMetrics as $event)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $event->event_title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $event->organizer_name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $event->Status === 'Completed' ? 'bg-green-100 text-green-800' : ($event->Status === 'Ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $event->Status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-700">{{ number_format($event->Capacity ?? 0) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-blue-600">{{ number_format($event->volunteers_registered) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full" style="width: {{ min($event->fill_rate ?? 0, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-700">{{ $event->fill_rate ?? 0 }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-purple-600">{{ number_format($event->total_hours ?? 0) }}h</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">No event data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Wait for Chart.js to load
        function waitForChart(callback) {
            if (typeof Chart !== 'undefined') {
                callback();
            } else {
                console.log('Waiting for Chart.js to load...');
                setTimeout(() => waitForChart(callback), 100);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOMContentLoaded - Initializing charts');

            waitForChart(function() {
                console.log('Chart.js loaded successfully!');

                let donationsChart, userGrowthChart, campaignStatusChart;
                let allocationChart, campaignFunnelChart;
                let topCampaignsChart, orgPerformanceChart, topDonorsChart, eventMetricsChart;

                function initCharts() {
                    console.log('Initializing all charts...');
                // Donations Chart
                const donationsCanvas = document.getElementById('donationsChart');
                if (donationsCanvas) {
                    const donationsCtx = donationsCanvas.getContext('2d');
                    const donationsData = @json($donationsChart ?? []);

                    if (donationsChart) donationsChart.destroy();
                    donationsChart = new Chart(donationsCtx, {
                        type: 'line',
                        data: {
                            labels: donationsData.map(d => {
                                const date = new Date(d.date);
                                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                            }),
                            datasets: [{
                                label: 'Amount (RM)',
                                data: donationsData.map(d => d.amount),
                                borderColor: 'rgb(16, 185, 129)',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'rgb(16, 185, 129)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'bottom' },
                                tooltip: {
                                    callbacks: {
                                        title: function(context) {
                                            return donationsData[context[0].dataIndex].date;
                                        },
                                        label: function(context) {
                                            return 'RM ' + context.parsed.y.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        autoSkip: true,
                                        maxTicksLimit: 10
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'RM ' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // User Growth Chart - Stacked by Role
                const userGrowthCanvas = document.getElementById('userGrowthChart');
                if (userGrowthCanvas) {
                    const userGrowthCtx = userGrowthCanvas.getContext('2d');
                    const userGrowthData = @json($userGrowthChart ?? []);

                    if (userGrowthChart) userGrowthChart.destroy();
                    userGrowthChart = new Chart(userGrowthCtx, {
                        type: 'line',
                        data: {
                            labels: userGrowthData.map(d => {
                                const date = new Date(d.date);
                                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                            }),
                            datasets: [
                                {
                                    label: 'Volunteers',
                                    data: userGrowthData.map(d => d.volunteer),
                                    borderColor: 'rgb(99, 102, 241)',
                                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: 'rgb(99, 102, 241)',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 3
                                },
                                {
                                    label: 'Donors',
                                    data: userGrowthData.map(d => d.donor),
                                    borderColor: 'rgb(236, 72, 153)',
                                    backgroundColor: 'rgba(236, 72, 153, 0.7)',
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: 'rgb(236, 72, 153)',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 3
                                },
                                {
                                    label: 'Organizers',
                                    data: userGrowthData.map(d => d.organizer),
                                    borderColor: 'rgb(168, 85, 247)',
                                    backgroundColor: 'rgba(168, 85, 247, 0.7)',
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: 'rgb(168, 85, 247)',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 3
                                },
                                {
                                    label: 'Public Users',
                                    data: userGrowthData.map(d => d.public),
                                    borderColor: 'rgb(34, 197, 94)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: 'rgb(34, 197, 94)',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: { size: 11 }
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        title: function(context) {
                                            return userGrowthData[context[0].dataIndex].date;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        autoSkip: true,
                                        maxTicksLimit: 10
                                    }
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }

                // Campaign Status Chart (Pie)
                const campaignStatusCanvas = document.getElementById('campaignStatusChart');
                if (campaignStatusCanvas) {
                    const campaignStatusCtx = campaignStatusCanvas.getContext('2d');
                    const campaignStatusData = @json($campaignStatusChart ?? []);

                    if (campaignStatusChart) campaignStatusChart.destroy();
                    campaignStatusChart = new Chart(campaignStatusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: campaignStatusData.map(d => d.status),
                            datasets: [{
                                data: campaignStatusData.map(d => d.count),
                                backgroundColor: [
                                    'rgb(34, 197, 94)',    // Active - green
                                    'rgb(234, 179, 8)',    // Pending - yellow
                                    'rgb(59, 130, 246)',   // Completed - blue
                                    'rgb(239, 68, 68)'     // Other - red
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: { size: 12 }
                                    }
                                }
                            }
                        }
                    });
                }


                // Allocation Efficiency Chart (Doughnut)
                const allocCanvas = document.getElementById('allocationChart');
                if (allocCanvas) {
                    const allocCtx = allocCanvas.getContext('2d');
                    const allocData = @json($allocationEfficiency ?? []);

                    const totalRaised = allocData.reduce((sum, item) => sum + parseFloat(item.Collected_Amount || 0), 0);
                    const totalAllocated = allocData.reduce((sum, item) => sum + parseFloat(item.allocated_amount || 0), 0);
                    const unallocated = totalRaised - totalAllocated;

                    if (allocationChart) allocationChart.destroy();
                    allocationChart = new Chart(allocCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Allocated', 'Unallocated'],
                            datasets: [{
                                data: [totalAllocated, unallocated],
                                backgroundColor: [
                                    'rgb(34, 197, 94)',
                                    'rgb(251, 191, 36)'
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                                            return context.label + ': RM ' + context.parsed.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Campaign Funnel Chart
                const funnelCanvas = document.getElementById('campaignFunnelChart');
                if (funnelCanvas) {
                    const funnelCtx = funnelCanvas.getContext('2d');
                    const funnelData = @json($campaignSuccessRate ?? []);

                    if (campaignFunnelChart) campaignFunnelChart.destroy();
                    campaignFunnelChart = new Chart(funnelCtx, {
                        type: 'bar',
                        data: {
                            labels: ['Total Campaigns', 'Active', 'Successful', 'Pending'],
                            datasets: [{
                                label: 'Count',
                                data: [
                                    funnelData.total || 0,
                                    funnelData.active || 0,
                                    funnelData.successful || 0,
                                    funnelData.pending || 0
                                ],
                                backgroundColor: [
                                    'rgba(107, 114, 128, 0.8)',
                                    'rgba(59, 130, 246, 0.8)',
                                    'rgba(34, 197, 94, 0.8)',
                                    'rgba(251, 191, 36, 0.8)'
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }

                // Top Campaigns Chart
                const topCampCanvas = document.getElementById('topCampaignsChart');
                if (topCampCanvas) {
                    const topCampCtx = topCampCanvas.getContext('2d');
                    const topCampData = @json($topCampaigns ?? []);

                    if (topCampaignsChart) topCampaignsChart.destroy();
                    topCampaignsChart = new Chart(topCampCtx, {
                        type: 'bar',
                        data: {
                            labels: topCampData.map(d => d.campaign_title),
                            datasets: [{
                                label: 'Amount Raised (RM)',
                                data: topCampData.map(d => parseFloat(d.Collected_Amount || 0)),
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderColor: 'rgb(16, 185, 129)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'RM ' + context.parsed.x.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'RM ' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Organization Performance Chart
                const orgPerfCanvas = document.getElementById('orgPerformanceChart');
                if (orgPerfCanvas) {
                    const orgPerfCtx = orgPerfCanvas.getContext('2d');
                    const orgPerfData = @json($organizationLeaderboard ?? []);

                    if (orgPerformanceChart) orgPerformanceChart.destroy();
                    orgPerformanceChart = new Chart(orgPerfCtx, {
                        type: 'bar',
                        data: {
                            labels: orgPerfData.map(d => d.organizer_name),
                            datasets: [{
                                label: 'Total Raised (RM)',
                                data: orgPerfData.map(d => parseFloat(d.total_raised || 0)),
                                backgroundColor: 'rgba(139, 92, 246, 0.8)',
                                borderColor: 'rgb(139, 92, 246)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'RM ' + context.parsed.x.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'RM ' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Top Donors Chart
                const topDonorsCanvas = document.getElementById('topDonorsChart');
                if (topDonorsCanvas) {
                    const topDonorsCtx = topDonorsCanvas.getContext('2d');
                    const topDonorsData = @json($donorInsights ?? []);

                    if (topDonorsChart) topDonorsChart.destroy();
                    topDonorsChart = new Chart(topDonorsCtx, {
                        type: 'bar',
                        data: {
                            labels: topDonorsData.map(d => d.donor_name),
                            datasets: [{
                                label: 'Total Donated (RM)',
                                data: topDonorsData.map(d => parseFloat(d.total_donated || 0)),
                                backgroundColor: 'rgba(236, 72, 153, 0.8)',
                                borderColor: 'rgb(236, 72, 153)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'RM ' + context.parsed.x.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'RM ' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Event Metrics Chart (Fill Rate)
                const eventMetCanvas = document.getElementById('eventMetricsChart');
                if (eventMetCanvas) {
                    const eventMetCtx = eventMetCanvas.getContext('2d');
                    const eventMetData = @json($eventMetrics ?? []);

                    if (eventMetricsChart) eventMetricsChart.destroy();
                    eventMetricsChart = new Chart(eventMetCtx, {
                        type: 'bar',
                        data: {
                            labels: eventMetData.map(d => d.event_title),
                            datasets: [
                                {
                                    label: 'Fill Rate (%)',
                                    data: eventMetData.map(d => parseFloat(d.fill_rate || 0)),
                                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                    yAxisID: 'y',
                                },
                                {
                                    label: 'Total Hours',
                                    data: eventMetData.map(d => parseFloat(d.total_hours || 0)),
                                    backgroundColor: 'rgba(168, 85, 247, 0.8)',
                                    yAxisID: 'y1',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    beginAtZero: true,
                                    max: 100,
                                    title: {
                                        display: true,
                                        text: 'Fill Rate (%)'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Volunteer Hours'
                                    },
                                    grid: {
                                        drawOnChartArea: false,
                                    }
                                }
                            }
                        }
                    });
                }
            }

                // Initial load
                initCharts();

                // Re-init on Livewire updates
                Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                    succeed(({ snapshot, effect }) => {
                        queueMicrotask(() => {
                            // Update allocation chart with new data from data attribute
                            const allocCanvas = document.getElementById('allocationChart');
                            if (allocCanvas && allocationChart) {
                                try {
                                    const allocData = JSON.parse(allocCanvas.getAttribute('data-allocation') || '[]');
                                    const totalRaised = allocData.reduce((sum, item) => sum + parseFloat(item.Collected_Amount || 0), 0);
                                    const totalAllocated = allocData.reduce((sum, item) => sum + parseFloat(item.allocated_amount || 0), 0);
                                    const unallocated = totalRaised - totalAllocated;

                                    console.log('Updating allocation chart - Raised:', totalRaised, 'Allocated:', totalAllocated);
                                    allocationChart.data.datasets[0].data = [totalAllocated, unallocated];
                                    allocationChart.update('none'); // Update without animation for faster response
                                } catch (e) {
                                    console.error('Error updating allocation chart:', e);
                                }
                            }

                            // Re-init other charts
                            initCharts();
                        });
                    });
                });

                // Listen for chart filter updates (Livewire v3 event listener)
                window.addEventListener('chartsUpdated', (event) => {
                    console.log('Charts updated event received, re-rendering charts...');
                    // Small delay to ensure Livewire data is updated
                    setTimeout(() => {
                        console.log('Calling initCharts() after filter change');
                        initCharts();
                    }, 100);
                });
            }); // End waitForChart callback
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush
