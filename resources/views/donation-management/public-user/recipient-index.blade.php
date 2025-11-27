<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Recipients - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600">CharityHub</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('public.campaigns.browse') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Campaigns</a>
                    <a href="{{ route('public.events.browse') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Events</a>
                    <a href="{{ route('public.recipients.index') }}" class="text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Recipients</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Registered Recipients</h1>
                <p class="text-gray-600 mt-1">Help those in need by suggesting potential recipients</p>
            </div>
            <a href="{{ route('public.recipients.create') }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                + Register Recipient
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm">
            @if($recipients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Need</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recipients as $recipient)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $recipient->Name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($recipient->Address, 40) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $recipient->Contact }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($recipient->Need_Description, 50) }}
                                </td>
                                <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $recipient->Status === 'Approved' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $recipient->Status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $recipient->Status === 'Rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $recipient->Status }}
                                            </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $recipient->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('public.recipients.show', $recipient->Recipient_ID) }}"
                                       class="text-indigo-600 hover:text-indigo-900">View</a>
                                    @if($recipient->Status === 'Pending')
                                        <a href="{{ route('public.recipients.edit', $recipient->Recipient_ID) }}"
                                           class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form action="{{ route('public.recipients.destroy', $recipient->Recipient_ID) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this recipient?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($recipients->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $recipients->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No recipients registered</h3>
                    <p class="mt-1 text-sm text-gray-500">Help those in need by registering recipients.</p>
                    <div class="mt-6">
                        <a href="{{ route('public.recipients.create') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            + Register Recipient
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
