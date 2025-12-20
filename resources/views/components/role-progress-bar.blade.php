@props(['filled' => 0, 'total' => 1, 'showPercentage' => true])

@php
    $percentage = $total > 0 ? round(($filled / $total) * 100) : 0;

    // Color scheme based on fill percentage
    if ($percentage >= 100) {
        $barColor = 'bg-red-600';
        $textColor = 'text-red-800';
    } elseif ($percentage >= 91) {
        $barColor = 'bg-red-500';
        $textColor = 'text-red-700';
    } elseif ($percentage >= 76) {
        $barColor = 'bg-orange-500';
        $textColor = 'text-orange-700';
    } elseif ($percentage >= 51) {
        $barColor = 'bg-yellow-500';
        $textColor = 'text-yellow-700';
    } else {
        $barColor = 'bg-green-500';
        $textColor = 'text-green-700';
    }
@endphp

<div class="w-full">
    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
        <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-300"
             style="width: {{ min($percentage, 100) }}%"></div>
    </div>
    @if($showPercentage)
        <p class="text-xs {{ $textColor }} mt-1 font-medium">{{ $percentage }}% filled</p>
    @endif
</div>
