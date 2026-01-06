@props(['roles'])

<div class="mt-6 border-t border-gray-200 pt-6">
    <h4 class="text-sm font-semibold text-gray-900 flex items-center mb-4">
        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        Volunteer Roles ({{ $roles->count() }} role{{ $roles->count() !== 1 ? 's' : '' }})
    </h4>

    @if($roles->isNotEmpty())
        <div class="space-y-3">
            @foreach($roles as $role)
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 border border-indigo-100">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h5 class="text-sm font-semibold text-gray-900 mb-1">{{ $role->Role_Name }}</h5>
                            @if($role->Role_Description)
                                <p class="text-sm text-gray-600 mb-2">{{ $role->Role_Description }}</p>
                            @endif
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <div class="text-right">
                                <div class="text-lg font-bold text-indigo-700">{{ $role->Volunteers_Needed }}</div>
                                <div class="text-xs text-gray-500 uppercase tracking-wide">Needed</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-3">
                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                            <span>Volunteers Registered</span>
                            <span class="font-medium">{{ $role->Volunteers_Filled }} / {{ $role->Volunteers_Needed }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $role->Volunteers_Needed > 0
                                    ? ($role->Volunteers_Filled / $role->Volunteers_Needed) * 100
                                    : 0;
                                $percentage = min($percentage, 100);
                            @endphp
                            <div
                                class="h-2 rounded-full transition-all duration-300 {{ $percentage >= 100 ? 'bg-green-500' : 'bg-indigo-600' }}"
                                style="width: {{ $percentage }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Total Summary -->
        <div class="mt-4 bg-gray-50 rounded-lg p-3">
            <div class="flex items-center justify-between text-sm">
                <span class="font-medium text-gray-700">Total Volunteers Needed:</span>
                <span class="text-lg font-bold text-indigo-700">{{ $roles->sum('Volunteers_Needed') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm mt-1">
                <span class="font-medium text-gray-700">Currently Registered:</span>
                <span class="text-lg font-bold text-gray-900">{{ $roles->sum('Volunteers_Filled') }}</span>
            </div>
        </div>
    @else
        <p class="text-sm text-gray-500 italic">No volunteer roles defined for this event.</p>
    @endif
</div>
