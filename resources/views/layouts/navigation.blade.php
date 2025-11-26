<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">CharityHub</a>
            </div>
            <div class="flex items-center space-x-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</nav>
