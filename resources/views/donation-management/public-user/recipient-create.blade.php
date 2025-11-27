<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Recipient - CharityHub</title>
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
                    <a href="{{ route('public.recipients.index') }}" class="text-gray-700 hover:text-indigo-600">Back to Recipients</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Register a Recipient</h1>
            <p class="text-gray-600 mb-6">Help us identify individuals or families who need support</p>

            <form action="{{ route('public.recipients.store') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Recipient Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="contact" class="block text-sm font-medium text-gray-700 mb-2">Contact Number *</label>
                    <input type="text" name="contact" id="contact" value="{{ old('contact') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('contact')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                    <textarea name="address" id="address" rows="3" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('address') }}</textarea>
                    @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="need_description" class="block text-sm font-medium text-gray-700 mb-2">Description of Need *</label>
                    <textarea name="need_description" id="need_description" rows="5" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Please describe why this person/family needs assistance...">{{ old('need_description') }}</textarea>
                    @error('need_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Provide details about their situation, challenges, and how donations would help.</p>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2">What happens next?</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>✓ Your submission will be reviewed by our team</li>
                        <li>✓ You'll be notified of the approval status</li>
                        <li>✓ Approved recipients may receive donations from campaigns</li>
                    </ul>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('public.recipients.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Submit Registration
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
