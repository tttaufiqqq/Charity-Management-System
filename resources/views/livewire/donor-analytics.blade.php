<!-- ============================================================================ -->
<!-- File: resources/views/livewire/donor-analytics.blade.php -->
<!-- ============================================================================ -->
<div>
    <!-- Summary Cards -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Donors</h3>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalDonors) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Active Donors</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($activeDonors) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Average Donation</h3>
            <p class="text-3xl font-bold text-indigo-600">RM {{ number_format($averageDonation, 2) }}</p>
        </div>
    </div>

    <!-- Donor Tier Breakdown -->
    @if(!empty($donorTierBreakdown))
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Donor Tier Distribution</h3>
        <p class="text-sm text-gray-600 mb-4">Donors are classified into tiers based on their total contribution amount.</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @php
                $tierStyles = [
                    'Platinum' => ['bg' => 'bg-gradient-to-br from-gray-700 to-gray-900', 'text' => 'text-white', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'desc' => 'RM10,000+'],
                    'Gold' => ['bg' => 'bg-gradient-to-br from-yellow-400 to-amber-500', 'text' => 'text-amber-900', 'icon' => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z', 'desc' => 'RM5,000+'],
                    'Silver' => ['bg' => 'bg-gradient-to-br from-gray-300 to-gray-400', 'text' => 'text-gray-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'desc' => 'RM1,000+'],
                    'Bronze' => ['bg' => 'bg-gradient-to-br from-orange-400 to-amber-600', 'text' => 'text-orange-900', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'desc' => 'RM100+'],
                    'Supporter' => ['bg' => 'bg-gradient-to-br from-rose-400 to-pink-500', 'text' => 'text-white', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6', 'desc' => 'New'],
                ];
                $tierOrder = ['Platinum', 'Gold', 'Silver', 'Bronze', 'Supporter'];
            @endphp
            @foreach($tierOrder as $tier)
                @php $count = $donorTierBreakdown[$tier] ?? 0; @endphp
                <div class="text-center p-4 {{ $tierStyles[$tier]['bg'] }} rounded-lg shadow-md">
                    <div class="flex justify-center mb-2">
                        <svg class="w-8 h-8 {{ $tierStyles[$tier]['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tierStyles[$tier]['icon'] }}"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold {{ $tierStyles[$tier]['text'] }}">{{ $count }}</div>
                    <div class="text-sm font-medium {{ $tierStyles[$tier]['text'] }} opacity-90">{{ $tier }}</div>
                    <div class="text-xs {{ $tierStyles[$tier]['text'] }} opacity-75 mt-1">{{ $tierStyles[$tier]['desc'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Donations by Payment Method -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Donations by Payment Method</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($donationsByMethod as $method => $count)
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                    <div class="text-sm text-gray-600">{{ $method }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Donors -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Donors by Total Donated</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Donor Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Donated</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Donations</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campaigns</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tier</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($topDonors as $index => $donor)
                    @php
                        $tier = $donor->donor_tier ?? 'Supporter';
                        $tierBadgeStyles = [
                            'Platinum' => 'bg-gradient-to-r from-gray-700 to-gray-900 text-white',
                            'Gold' => 'bg-gradient-to-r from-yellow-400 to-amber-500 text-amber-900',
                            'Silver' => 'bg-gradient-to-r from-gray-300 to-gray-400 text-gray-800',
                            'Bronze' => 'bg-gradient-to-r from-orange-400 to-amber-600 text-orange-900',
                            'Supporter' => 'bg-gradient-to-r from-rose-400 to-pink-500 text-white',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($index < 3)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                    {{ $index === 0 ? 'bg-yellow-400 text-yellow-900' : ($index === 1 ? 'bg-gray-300 text-gray-700' : 'bg-orange-400 text-orange-900') }}
                                    font-bold text-sm">
                                    {{ $index + 1 }}
                                </span>
                            @else
                                <span class="text-sm font-medium text-gray-500">#{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $donor->donor_name }}</div>
                            <div class="text-xs text-gray-500">Since {{ $donor->donor_since ? \Carbon\Carbon::parse($donor->donor_since)->format('M Y') : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                            RM {{ number_format($donor->cached_total_donated, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $donor->completed_donation_count ?? 0 }} donations
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $donor->campaigns_supported ?? 0 }} campaigns
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-bold rounded-full shadow-sm {{ $tierBadgeStyles[$tier] ?? $tierBadgeStyles['Supporter'] }}">
                                {{ $tier }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
