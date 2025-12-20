@props(['filled' => 0, 'total' => 1])

@php
    $percentage = $total > 0 ? ($filled / $total) * 100 : 0;

    // Color scheme based on fill percentage
    if ($percentage >= 100) {
        $bgColor = 'bg-red-100';
        $textColor = 'text-red-800';
        $borderColor = 'border-red-300';
    } elseif ($percentage >= 91) {
        $bgColor = 'bg-red-50';
        $textColor = 'text-red-700';
        $borderColor = 'border-red-200';
    } elseif ($percentage >= 76) {
        $bgColor = 'bg-orange-100';
        $textColor = 'text-orange-800';
        $borderColor = 'border-orange-300';
    } elseif ($percentage >= 51) {
        $bgColor = 'bg-yellow-100';
        $textColor = 'text-yellow-800';
        $borderColor = 'border-yellow-300';
    } else {
        $bgColor = 'bg-green-100';
        $textColor = 'text-green-800';
        $borderColor = 'border-green-300';
    }
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $bgColor }} {{ $textColor }} border {{ $borderColor }}">
    {{ $filled }}/{{ $total }} filled
</span>
