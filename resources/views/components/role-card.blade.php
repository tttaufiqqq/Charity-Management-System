@props([
    'role',
    'variant' => 'view', // view, selectable, or input
    'selected' => false,
    'disabled' => false
])

@php
    $isFull = $role->Volunteers_Filled >= $role->Volunteers_Needed;
    $percentage = $role->Volunteers_Needed > 0 ? ($role->Volunteers_Filled / $role->Volunteers_Needed) * 100 : 0;

    // Border color based on state and fill percentage
    if ($disabled || $isFull) {
        $borderClass = 'border-red-300 bg-red-50';
        $iconClass = 'text-red-600';
    } elseif ($percentage >= 76) {
        $borderClass = 'border-orange-300 bg-orange-50';
        $iconClass = 'text-orange-600';
    } elseif ($percentage >= 51) {
        $borderClass = 'border-yellow-300 bg-yellow-50';
        $iconClass = 'text-yellow-600';
    } else {
        $borderClass = 'border-indigo-300 bg-white';
        $iconClass = 'text-indigo-600';
    }

    if ($selected && $variant === 'selectable') {
        $borderClass = 'border-indigo-500 bg-indigo-50';
    }

    $cursorClass = ($variant === 'selectable' && !$disabled) ? 'cursor-pointer hover:shadow-md' : '';
    $opacityClass = $disabled ? 'opacity-60' : '';
@endphp

<div class="border-2 rounded-lg p-4 transition-all duration-200 {{ $borderClass }} {{ $cursorClass }} {{ $opacityClass }}"
     {{ $attributes }}>

    <!-- Header with Icon and Name -->
    <div class="flex items-start mb-3">
        <svg class="w-6 h-6 mr-2 flex-shrink-0 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <div class="flex-1">
            <h4 class="font-semibold text-gray-900 text-base">{{ $role->Role_Name }}</h4>
            @if($isFull && $variant === 'selectable')
                <span class="text-xs text-red-600 font-medium">Full</span>
            @endif
        </div>

        @if($variant === 'selectable')
            <input type="radio"
                   name="role_id"
                   value="{{ $role->Role_ID }}"
                   {{ $selected ? 'checked' : '' }}
                   {{ $disabled ? 'disabled' : '' }}
                   class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
        @endif
    </div>

    <!-- Description -->
    @if($role->Role_Description)
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $role->Role_Description }}</p>
    @else
        <p class="text-sm text-gray-400 mb-3 italic">No description provided</p>
    @endif

    <!-- Progress Bar -->
    <div class="mb-2">
        <x-role-progress-bar :filled="$role->Volunteers_Filled" :total="$role->Volunteers_Needed" :showPercentage="false" />
    </div>

    <!-- Capacity Info -->
    <div class="flex items-center justify-between">
        <x-role-capacity-badge :filled="$role->Volunteers_Filled" :total="$role->Volunteers_Needed" />

        @php
            $slotsRemaining = max(0, $role->Volunteers_Needed - $role->Volunteers_Filled);
        @endphp

        @if($variant === 'selectable')
            <span class="text-xs {{ $slotsRemaining > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                @if($slotsRemaining > 0)
                    {{ $slotsRemaining }} {{ Str::plural('slot', $slotsRemaining) }} available
                @else
                    No slots available
                @endif
            </span>
        @endif
    </div>
</div>
