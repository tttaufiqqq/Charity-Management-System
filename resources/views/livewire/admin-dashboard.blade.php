<div>
    <!-- Date Range Filter -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <label class="text-sm font-medium text-gray-700 mr-2">Date Range:</label>
            <select wire:model="dateRange" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="90">Last 90 Days</option>
                <option value="365">Last Year</option>
            </select>
        </div>
        <div wire:loading class="text-sm text-gray-500">
            Loading analytics...
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Total Users</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Organizations</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalOrganizations) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Campaigns</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalCampaigns) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Events</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalEvents) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Volunteers</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalVolunteers) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Donations</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalDonations) }}</div>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow-lg p-6 text-white">
            <div class="text-sm text-green-100 mb-1">Total Raised</div>
            <div class="text-3xl font-bold">RM {{ number_format($totalRaised, 2) }}</div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
            <div class="text-sm text-blue-100 mb-1">Total Allocated</div>
            <div class="text-3xl font-bold">RM {{ number_format($totalAllocated, 2) }}</div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg shadow-lg p-6 text-white">
            <div class="text-sm text-purple-100 mb-1">Available Funds</div>
            <div class="text-3xl font-bold">RM {{ number_format($totalRaised - $totalAllocated, 2) }}</div>
        </div>
    </div>

    <!-- Pending Approvals Alert -->
    @if($pendingApprovals['campaigns'] > 0 || $pendingApprovals['events'] > 0 || $pendingApprovals['recipients'] > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Pending Approvals</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            @if($pendingApprovals['campaigns'] > 0)
                                <li>{{ $pendingApprovals['campaigns'] }} campaigns awaiting approval</li>
                            @endif
                            @if($pendingApprovals['events'] > 0)
                                <li>{{ $pendingApprovals['events'] }} events awaiting approval</li>
                            @endif
                            @if($pendingApprovals['recipients'] > 0)
                                <li>{{ $pendingApprovals['recipients'] }} recipients awaiting approval</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Charts -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Donations Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Donations Over Time</h3>
            <div class="h-64" wire:ignore>
                <canvas id="donationsChart"></canvas>
            </div>
        </div>

        <!-- User Growth Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Growth</h3>
            <div class="h-64" wire:ignore>
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Campaigns Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaigns Created</h3>
            <div class="h-64" wire:ignore>
                <canvas id="campaignsChart"></canvas>
            </div>
        </div>

        <!-- Events Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Events Created</h3>
            <div class="h-64">
                <canvas id="eventsChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let donationsChart, userGrowthChart, campaignsChart, eventsChart;

            function initCharts() {
                // Donations Chart
                const donationsCanvas = document.getElementById('donationsChart');
                if (!donationsCanvas) return;

                const donationsCtx = donationsCanvas.getContext('2d');
                const donationsData = @json($donationsChart ?? []);

                if (donationsChart) donationsChart.destroy();
                donationsChart = new Chart(donationsCtx, {
                    type: 'line',
                    data: {
                        labels: donationsData.map(d => d.date),
                        datasets: [{
                            label: 'Donations (RM)',
                            data: donationsData.map(d => d.amount),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true }
                        }
                    }
                });

                // User Growth Chart
                const userGrowthCanvas = document.getElementById('userGrowthChart');
                if (!userGrowthCanvas) return;

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
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true }
                        }
                    }
                });

                // Campaigns Chart
                const campaignsCanvas = document.getElementById('campaignsChart');
                if (!campaignsCanvas) return;

                const campaignsCtx = campaignsCanvas.getContext('2d');
                const campaignsData = @json($campaignsChart ?? []);

                if (campaignsChart) campaignsChart.destroy();
                campaignsChart = new Chart(campaignsCtx, {
                    type: 'bar',
                    data: {
                        labels: campaignsData.map(d => d.date),
                        datasets: [{
                            label: 'Campaigns',
                            data: campaignsData.map(d => d.count),
                            backgroundColor: 'rgba(249, 115, 22, 0.8)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true }
                        }
                    }
                });

                // Events Chart
                const eventsCanvas = document.getElementById('eventsChart');
                if (!eventsCanvas) return;

                const eventsCtx = eventsCanvas.getContext('2d');
                const eventsData = @json($eventsChart ?? []);

                if (eventsChart) eventsChart.destroy();
                eventsChart = new Chart(eventsCtx, {
                    type: 'bar',
                    data: {
                        labels: eventsData.map(d => d.date),
                        datasets: [{
                            label: 'Events',
                            data: eventsData.map(d => d.count),
                            backgroundColor: 'rgba(168, 85, 247, 0.8)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true }
                        }
                    }
                });
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
        });
    </script>
@endpush
