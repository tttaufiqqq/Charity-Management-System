<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Role Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <p class="mb-4">
                        Welcome, <strong>{{ auth()->user()->name }}</strong>
                    </p>

                    <p class="text-gray-700">
                        Your assigned role is:
                    </p>

                    <span class="mt-2 inline-block px-4 py-2 bg-blue-100 text-blue-800 rounded-full font-semibold">
                        {{ auth()->user()->getRoleNames()->first() }}
                    </span>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
