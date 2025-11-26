<div class="max-w-md w-full">
    <!-- Card -->
    <div class="bg-white rounded-lg shadow-xl p-8">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-gray-900">{{ $title }}</h2>
            @isset($subtitle)
            <p class="text-gray-600 mt-2">{{ $subtitle }}</p>
            @endisset
        </div>

        {{ $slot }}
    </div>

    <!-- Additional Info -->
    @isset($footer)
    <div class="mt-6 text-center">
        {{ $footer }}
    </div>
    @endisset
</div>
