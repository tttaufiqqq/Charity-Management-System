<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Campaigns - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Pending Campaign Approvals</h1>

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

        <div class="bg-white rounded-lg shadow-sm">
            @if($campaigns->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organizer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Goal Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($campaigns as $campaign)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $campaign->Title }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($campaign->Description, 60) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $campaign->organization->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $campaign->organization->City }}, {{ $campaign->organization->State }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    RM {{ number_format($campaign->Goal_Amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $campaign->Start_Date->format('M d') }} - {{ $campaign->End_Date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $campaign->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button onclick="openModal({{ $campaign->Campaign_ID }}, '{{ addslashes($campaign->Title) }}')"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        View
                                    </button>
                                    <form action="{{ route('admin.campaigns.approve', $campaign->Campaign_ID) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.campaigns.reject', $campaign->Campaign_ID) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to reject this campaign?');">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Hidden details for modal -->
                            <div id="campaign-{{ $campaign->Campaign_ID }}" class="hidden">
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-700">Description:</h4>
                                        <p class="text-gray-600">{{ $campaign->Description ?? 'No description' }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-700">Start Date:</h4>
                                            <p class="text-gray-600">{{ $campaign->Start_Date->format('F d, Y') }}</p>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-700">End Date:</h4>
                                            <p class="text-gray-600">{{ $campaign->End_Date->format('F d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-700">Organization:</h4>
                                        <p class="text-gray-600">{{ $campaign->organization->user->name }}</p>
                                        <p class="text-sm text-gray-500">Reg No: {{ $campaign->organization->Register_No }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($campaigns->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending campaigns</h3>
                    <p class="mt-1 text-sm text-gray-500">All campaigns have been reviewed.</p>
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Modal -->
<div id="detailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-lg font-medium text-gray-900"></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<script>
    function openModal(campaignId, title) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalContent').innerHTML = document.getElementById('campaign-' + campaignId).innerHTML;
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
</body>
</html>
