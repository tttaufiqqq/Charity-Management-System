@props(['title', 'description'])

<div x-data="{ showHelp: false }" class="relative inline-block">
    <button @click="showHelp = !showHelp"
            @click.away="showHelp = false"
            type="button"
            class="ml-2 inline-flex items-center justify-center w-5 h-5 text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors rounded-full hover:bg-indigo-50">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
        </svg>
    </button>

    <div x-show="showHelp"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 left-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border-2 border-indigo-100 p-5"
         style="display: none;">
        <div class="flex items-start gap-3 mb-3">
            <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-900 mb-1">{{ $title }}</h4>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $description }}</p>
            </div>
        </div>
        {{ $slot }}
    </div>
</div>
