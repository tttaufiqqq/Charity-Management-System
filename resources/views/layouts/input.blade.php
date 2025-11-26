@props(['disabled' => false, 'type' => 'text'])

<input {{ $disabled ? 'disabled' : '' }}
       type="{{ $type }}"
    {!! $attributes->merge(['class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors']) !!}>
