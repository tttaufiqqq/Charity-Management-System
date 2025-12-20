<div x-data="{ activeTab: '{{ $activeTab }}' }" x-init="$watch('activeTab', value => $wire.set('activeTab', value))">
    <!-- Header with Filters -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 mr-2">Date Range:</label>
                <select wire:model.live="dateRange" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">Last Year</option>
                </select>
            </div>
            <div wire:loading class="text-sm text-indigo-600 flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Loading...</span>
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
                <button
                    @click="activeTab = 'geography'"
                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'geography', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'geography' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Geography
                </button>
            </nav>
        </div>
    </div>

    <!-- Overview Tab -->
    <div x-show="activeTab === 'overview'" style="display: block;">
        <!-- Charts Grid -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Donations Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Donations Over Time</h3>
                    <span class="text-xs text-gray-500">Amount (RM)</span>
                </div>
                <div class="h-64" wire:ignore>
                    <canvas id="donationsChart"></canvas>
                </div>
            </div>

            <!-- User Growth Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">User Growth</h3>
                    <span class="text-xs text-gray-500">New Users</span>
                </div>
                <div class="h-64" wire:ignore>
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>

            <!-- Campaign Status Distribution -->
            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Campaign Status</h3>
                    <span class="text-xs text-gray-500">Distribution</span>
                </div>
                <div class="h-64" wire:ignore>
                    <canvas id="campaignStatusChart"></canvas>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Methods</h3>
                    <span class="text-xs text-gray-500">By Amount</span>
                </div>
                <div class="h-64" wire:ignore>
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Additional Charts Row -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Geographic Distribution Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Top States by Fundraising</h3>
                    <span class="text-xs text-gray-500">Amount Raised</span>
                </div>
                <div class="h-64" wire:ignore>
                    <canvas id="geoDistributionChart"></canvas>
                </div>
            </div>

            <!-- Fund Allocation Efficiency -->
            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Fund Allocation Overview</h3>
                    <span class="text-xs text-gray-500">Efficiency %</span>
                </div>
                <div class="h-64" wire:ignore>
                    <canvas id="allocationChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Campaign Success Rate -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaign Performance Summary</h3>
            <div class="grid md:grid-cols-5 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $campaignSuccessRate['total'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Total Campaigns</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $campaignSuccessRate['successful'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Reached Goal</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $campaignSuccessRate['active'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Active</div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $campaignSuccessRate['success_rate'] }}%</div>
                    <div class="text-sm text-gray-600 mt-1">Success Rate</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $campaignSuccessRate['avg_achievement_rate'] }}%</div>
                    <div class="text-sm text-gray-600 mt-1">Avg Achievement</div>
                </div>
            </div>
            <!-- Campaign Success Funnel Chart -->
            <div class="h-64" wire:ignore>
                <canvas id="campaignFunnelChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
            <div class="space-y-3">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0">
                            @if($activity->type === 'donation')
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity->actor }}</p>
                            <p class="text-sm text-gray-600">{{ $activity->description }}</p>
                        </div>
                        <div class="flex-shrink-0 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($activity->activity_date)->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Campaigns Tab -->
    <div x-show="activeTab === 'campaigns'" x-cloak>
        <!-- Top Campaigns Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Top 10 Campaigns by Amount Raised</h3>
                <span class="text-xs text-gray-500">Visual Comparison</span>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
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
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $org->City }}, {{ $org->State }}</td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-green-600">RM {{ number_format($org->total_raised ?? 0, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-blue-600">{{ number_format($org->total_campaigns) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-purple-600">{{ number_format($org->total_events) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-emerald-600">{{ number_format($org->active_campaigns) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">No organization data available</td>
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
                <h3 class="text-lg font-semibold text-gray-900">Top 10 Donors by Total Contribution</h3>
                <span class="text-xs text-gray-500">Donor Leaderboard</span>
            </div>
            <div class="h-96" wire:ignore>
                <canvas id="topDonorsChart"></canvas>
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
                <h3 class="text-lg font-semibold text-gray-900">Event Fill Rates & Volunteer Hours</h3>
                <span class="text-xs text-gray-500">Participation Analysis</span>
            </div>
            <div class="h-96" wire:ignore>
                <canvas id="eventMetricsChart"></canvas>
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

    <!-- Geography Tab -->
    <div x-show="activeTab === 'geography'" x-cloak>
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Geographic Distribution</h3>
                <p class="text-sm text-gray-600 mt-1">Fundraising performance by location</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">State</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Organizations</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Campaigns</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Raised</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($geographicDistribution as $location)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $location->State }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $location->City }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-purple-600">{{ number_format($location->org_count) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-blue-600">{{ number_format($location->campaign_count) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-green-600">RM {{ number_format($location->total_raised ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No geographic data available</td>
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

                let donationsChart, userGrowthChart, campaignStatusChart, paymentMethodChart;
                let geoDistributionChart, allocationChart, campaignFunnelChart;
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
                            labels: donationsData.map(d => d.date),
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
                                        label: function(context) {
                                            return 'RM ' + context.parsed.y.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        }
                                    }
                                }
                            },
                            scales: {
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

                // User Growth Chart
                const userGrowthCanvas = document.getElementById('userGrowthChart');
                if (userGrowthCanvas) {
                    const userGrowthCtx = userGrowthCanvas.getContext('2d');
                    const userGrowthData = @json($userGrowthChart ?? []);

                    if (userGrowthChart) userGrowthChart.destroy();
                    userGrowthChart = new Chart(userGrowthCtx, {
                        type: 'line',
                        data: {
                            labels: userGrowthData.map(d => d.date),
                            datasets: [{
                                label: 'New Users',
                                data: userGrowthData.map(d => d.count),
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'rgb(99, 102, 241)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'bottom' }
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

                // Payment Method Chart (Pie)
                const paymentMethodCanvas = document.getElementById('paymentMethodChart');
                if (paymentMethodCanvas) {
                    const paymentMethodCtx = paymentMethodCanvas.getContext('2d');
                    const paymentMethodData = @json($donationsByMethodChart ?? []);

                    if (paymentMethodChart) paymentMethodChart.destroy();
                    paymentMethodChart = new Chart(paymentMethodCtx, {
                        type: 'doughnut',
                        data: {
                            labels: paymentMethodData.map(d => d.method),
                            datasets: [{
                                data: paymentMethodData.map(d => d.amount),
                                backgroundColor: [
                                    'rgb(99, 102, 241)',   // Online Banking - indigo
                                    'rgb(249, 115, 22)',   // Credit/Debit - orange
                                    'rgb(168, 85, 247)',   // E-Wallet - purple
                                    'rgb(236, 72, 153)'    // Other - pink
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
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': RM ' + context.parsed.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Geographic Distribution Chart (Bar Chart)
                const geoCanvas = document.getElementById('geoDistributionChart');
                if (geoCanvas) {
                    const geoCtx = geoCanvas.getContext('2d');
                    const geoData = @json($geographicDistribution ?? []);

                    if (geoDistributionChart) geoDistributionChart.destroy();
                    geoDistributionChart = new Chart(geoCtx, {
                        type: 'bar',
                        data: {
                            labels: geoData.map(d => `${d.City}, ${d.State}`).slice(0, 10),
                            datasets: [{
                                label: 'Amount Raised (RM)',
                                data: geoData.map(d => parseFloat(d.total_raised || 0)).slice(0, 10),
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: 'rgb(59, 130, 246)',
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
                            initCharts();
                        });
                    });
                });
            }); // End waitForChart callback
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush
