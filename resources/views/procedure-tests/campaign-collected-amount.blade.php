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
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl shadow-lg p-6 mb-6 text-white">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold font-mono">{{ $procedureName }}</h3>
                        <p class="text-purple-100 mt-1">Database: {{ $database }}</p>
                        <p class="text-sm text-purple-200 mt-2">Updates campaign collected amount with ADD, SUBTRACT, or SET operations.</p>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid lg:grid-cols-2 gap-6">
                <!-- Input Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Execute Procedure</h4>
                    <form action="{{ route('procedures.campaign-collected') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Campaign</label>
                            <select name="campaign_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a campaign...</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->Campaign_ID }}"
                                            {{ old('campaign_id') == $campaign->Campaign_ID ? 'selected' : '' }}>
                                        {{ $campaign->Title }} (Current: RM {{ number_format($campaign->Collected_Amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount (RM)</label>
                            <input type="number" name="amount" step="0.01" min="0" required
                                   value="{{ old('amount', '100.00') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Operation</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ old('operation') === 'ADD' ? 'border-indigo-500 ring-2 ring-indigo-500' : 'border-gray-300' }}">
                                    <input type="radio" name="operation" value="ADD" class="sr-only" {{ old('operation', 'ADD') === 'ADD' ? 'checked' : '' }}>
                                    <span class="flex flex-1 flex-col">
                                        <span class="block text-sm font-medium text-gray-900">ADD</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">Add to current</span>
                                    </span>
                                </label>
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ old('operation') === 'SUBTRACT' ? 'border-indigo-500 ring-2 ring-indigo-500' : 'border-gray-300' }}">
                                    <input type="radio" name="operation" value="SUBTRACT" class="sr-only" {{ old('operation') === 'SUBTRACT' ? 'checked' : '' }}>
                                    <span class="flex flex-1 flex-col">
                                        <span class="block text-sm font-medium text-gray-900">SUBTRACT</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">Remove from current</span>
                                    </span>
                                </label>
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ old('operation') === 'SET' ? 'border-indigo-500 ring-2 ring-indigo-500' : 'border-gray-300' }}">
                                    <input type="radio" name="operation" value="SET" class="sr-only" {{ old('operation') === 'SET' ? 'checked' : '' }}>
                                    <span class="flex flex-1 flex-col">
                                        <span class="block text-sm font-medium text-gray-900">SET</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">Set exact value</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                            Execute Procedure
                        </button>
                    </form>

                    <!-- SQL Preview -->
                    <div class="mt-4 bg-gray-900 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs text-gray-400">SQL Command:</span>
                        </div>
                        <code class="text-green-400 text-sm font-mono">
                            CALL {{ $procedureName }}(campaign_id, amount, 'operation', 'session_id');
                        </code>
                    </div>
                </div>

                <!-- Result Display -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Procedure Result</h4>

                    @if($result)
                        <div class="space-y-4">
                            <!-- Status -->
                            <div class="p-4 rounded-lg {{ $result->success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                                <div class="flex items-center gap-3">
                                    @if($result->success)
                                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                    <div>
                                        <div class="font-bold {{ $result->success ? 'text-green-800' : 'text-red-800' }}">
                                            {{ $result->success ? 'Success' : 'Failed' }}
                                        </div>
                                        <div class="text-sm {{ $result->success ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $result->message }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($selectedCampaign)
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="text-sm text-gray-500 mb-1">Campaign</div>
                                    <div class="font-semibold text-gray-900">{{ $selectedCampaign->Title }}</div>
                                </div>
                            @endif

                            <!-- Amount Details -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-blue-50 rounded-lg text-center">
                                    <div class="text-sm text-blue-600 mb-1">New Collected Amount</div>
                                    <div class="text-2xl font-bold text-blue-800">
                                        RM {{ number_format($result->new_collected_amount, 2) }}
                                    </div>
                                </div>
                                <div class="p-4 bg-purple-50 rounded-lg text-center">
                                    <div class="text-sm text-purple-600 mb-1">Goal Amount</div>
                                    <div class="text-2xl font-bold text-purple-800">
                                        RM {{ number_format($result->goal_amount, 2) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-600">Progress</span>
                                    <span class="text-sm font-bold text-indigo-600">{{ number_format($result->progress_percentage, 2) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-4 rounded-full transition-all"
                                         style="width: {{ min($result->progress_percentage, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500">Execute the procedure to see results</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Make radio buttons highlight on selection
        document.querySelectorAll('input[name="operation"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="operation"]').forEach(r => {
                    r.closest('label').classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-500');
                    r.closest('label').classList.add('border-gray-300');
                });
                this.closest('label').classList.remove('border-gray-300');
                this.closest('label').classList.add('border-indigo-500', 'ring-2', 'ring-indigo-500');
            });
        });
    </script>
</x-app-layout>
