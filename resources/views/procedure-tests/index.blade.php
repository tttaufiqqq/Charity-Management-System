<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Database Stored Procedures Testing
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Info Banner -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-800">Stored Procedures Testing Interface</h3>
                        <p class="text-sm text-blue-700 mt-1">
                            This page allows you to test the stored procedures implemented across the 5 distributed databases.
                            Each procedure uses <code class="bg-blue-100 px-1 rounded">CREATE OR REPLACE PROCEDURE</code> syntax.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Procedures Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($procedures as $procedure)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $procedure['type'] === 'READ' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $procedure['type'] }}
                                    </span>
                                </div>
                                <div class="p-2 bg-indigo-100 rounded-lg">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                    </svg>
                                </div>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-2 font-mono">{{ $procedure['name'] }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ $procedure['database'] }}</p>
                            <p class="text-sm text-gray-600 mb-4">{{ $procedure['description'] }}</p>

                            <a href="{{ $procedure['route'] }}"
                               class="inline-flex items-center justify-center w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Test Procedure
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Database Architecture Info -->
            <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Database Architecture</h3>
                <div class="grid md:grid-cols-5 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-sm font-bold text-blue-800">izzhilmy</div>
                        <div class="text-xs text-blue-600">PostgreSQL</div>
                        <div class="text-xs text-gray-500 mt-1">Users & Auth</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-sm font-bold text-green-800">hannah</div>
                        <div class="text-xs text-green-600">MySQL</div>
                        <div class="text-xs text-gray-500 mt-1">Finance</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-sm font-bold text-purple-800">izzati</div>
                        <div class="text-xs text-purple-600">PostgreSQL</div>
                        <div class="text-xs text-gray-500 mt-1">Operations</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="text-sm font-bold text-orange-800">sashvini</div>
                        <div class="text-xs text-orange-600">MariaDB</div>
                        <div class="text-xs text-gray-500 mt-1">Volunteers</div>
                    </div>
                    <div class="text-center p-4 bg-pink-50 rounded-lg">
                        <div class="text-sm font-bold text-pink-800">adam</div>
                        <div class="text-xs text-pink-600">MySQL</div>
                        <div class="text-xs text-gray-500 mt-1">Recipients</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
