<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Campaign - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Campaign</h1>

            <!-- Security Notice for Campaigns with Donations -->
            @if($campaign->Collected_Amount > 0)
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-amber-800">Campaign Has Received Donations</h3>
                            <p class="text-sm text-amber-700 mt-1">
                                This campaign has collected <strong>RM {{ number_format($campaign->Collected_Amount, 2) }}</strong> from donors.
                                For security reasons, the goal amount cannot be reduced below this amount.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('campaigns.update', $campaign->Campaign_ID) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Campaign Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $campaign->Title) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $campaign->Description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Goal Amount -->
                <div class="mb-6">
                    <label for="goal_amount" class="block text-sm font-medium text-gray-700 mb-2">Goal Amount (RM) *</label>
                    <input type="number" name="goal_amount" id="goal_amount"
                           value="{{ old('goal_amount', $campaign->Goal_Amount) }}"
                           step="0.01"
                           min="{{ $campaign->Collected_Amount }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @if($campaign->Collected_Amount > 0)
                        <p class="mt-1 text-sm text-gray-500">
                            Minimum: RM {{ number_format($campaign->Collected_Amount, 2) }} (collected donations)
                        </p>
                    @endif
                    @error('goal_amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Collected Amount (Read-only info) -->
                @if($campaign->Collected_Amount > 0)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Collected Amount (Read-only)</label>
                    <div class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                        RM {{ number_format($campaign->Collected_Amount, 2) }}
                        <span class="text-sm text-gray-500 ml-2">(from {{ $campaign->donations->count() }} donations)</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">This amount is automatically calculated from donations and cannot be modified.</p>
                </div>
                @endif

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $campaign->Start_Date->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $campaign->End_Date->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Active" {{ old('status', $campaign->Status) === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Completed" {{ old('status', $campaign->Status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ old('status', $campaign->Status) === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('campaigns.show', $campaign->Campaign_ID) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Update Campaign
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
