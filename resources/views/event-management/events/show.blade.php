<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->Title }} - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif
        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->Title }}</h1>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        {{ $event->Status === 'Upcoming' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $event->Status === 'Ongoing' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $event->Status === 'Completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ $event->Status }}
                    </span>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('events.edit', $event->Event_ID) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Edit
                    </a>
                    <form action="{{ route('events.destroy', $event->Event_ID) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <!-- Event Details -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Location</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $event->Location }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Capacity</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $event->Capacity ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Start Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $event->Start_Date->format('F d, Y') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">End Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $event->End_Date->format('F d, Y') }}</p>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Description</h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $event->Description ?? 'No description provided.' }}</p>
            </div>

            <!-- Volunteers -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Volunteers ({{ $volunteers->total() }})</h2>
                @if($volunteers->count() > 0)
                    <div class="space-y-4">
                        @foreach($volunteers as $volunteer)
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 last:border-0">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $volunteer->Full_Name }}</p>
                                    <p class="text-sm text-gray-500">Status: {{ $volunteer->pivot->Status }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Total Hours: {{ $volunteer->pivot->Total_Hours ?? 0 }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $volunteers->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No volunteers yet</p>
                @endif
            </div>
        </div>
    </main>
</div>
</body>
</html>
