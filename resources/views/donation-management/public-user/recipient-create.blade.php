<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Recipient - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    @include('navbar')

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Register a Recipient</h1>
            <p class="text-lg text-gray-600">Help us identify individuals or families who need charitable support</p>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Progress Steps -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                <div class="flex items-center justify-between max-w-2xl mx-auto">
                    <div class="flex items-center text-white">
                        <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-30 rounded-full font-bold">
                            1
                        </div>
                        <span class="ml-3 font-medium hidden sm:inline">Fill Information</span>
                    </div>
                    <div class="flex-1 h-1 bg-white bg-opacity-30 mx-4"></div>
                    <div class="flex items-center text-white opacity-60">
                        <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-30 rounded-full font-bold">
                            2
                        </div>
                        <span class="ml-3 font-medium hidden sm:inline">Review</span>
                    </div>
                    <div class="flex-1 h-1 bg-white bg-opacity-30 mx-4"></div>
                    <div class="flex items-center text-white opacity-60">
                        <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-30 rounded-full font-bold">
                            3
                        </div>
                        <span class="ml-3 font-medium hidden sm:inline">Approved</span>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form action="{{ route('public.recipients.store') }}" method="POST" class="p-8" x-data="{
                nameCount: {{ old('name') ? strlen(old('name')) : 0 }},
                addressCount: {{ old('address') ? strlen(old('address')) : 0 }},
                needCount: {{ old('need_description') ? strlen(old('need_description')) : 0 }}
            }">
                @csrf

                <!-- Personal Information Section -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Personal Information</h2>
                    </div>

                    <!-- Recipient Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Recipient Full Name *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   maxlength="255"
                                   x-on:input="nameCount = $event.target.value.length"
                                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="Enter recipient's full name">
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            @error('name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="text-xs text-gray-500">Full name of the individual or family representative</p>
                            @enderror
                            <span class="text-xs text-gray-500" x-text="nameCount + '/255'"></span>
                        </div>
                    </div>

                    <!-- Contact Number -->
                    <div class="mb-6">
                        <label for="contact" class="block text-sm font-semibold text-gray-700 mb-2">
                            Contact Number *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <input type="text" name="contact" id="contact" value="{{ old('contact') }}" required
                                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="e.g., +60123456789">
                        </div>
                        @error('contact')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="mt-1 text-xs text-gray-500">Phone number where the recipient can be reached</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="mb-6">
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                            Full Address *
                        </label>
                        <div class="relative">
                            <div class="absolute top-3 left-0 pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <textarea name="address" id="address" rows="3" required
                                      maxlength="500"
                                      x-on:input="addressCount = $event.target.value.length"
                                      class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                      placeholder="Enter complete residential address...">{{ old('address') }}</textarea>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            @error('address')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="text-xs text-gray-500">Include street, city, state, and postcode</p>
                            @enderror
                            <span class="text-xs text-gray-500" x-text="addressCount + '/500'"></span>
                        </div>
                    </div>
                </div>

                <!-- Need Description Section -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Description of Need</h2>
                    </div>

                    <div class="mb-6">
                        <label for="need_description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Why does this person/family need assistance? *
                        </label>
                        <textarea name="need_description" id="need_description" rows="6" required
                                  maxlength="1000"
                                  x-on:input="needCount = $event.target.value.length"
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                  placeholder="Please provide detailed information about their situation, challenges faced, and how donations would help them...">{{ old('need_description') }}</textarea>
                        <div class="flex justify-between items-center mt-1">
                            @error('need_description')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="text-xs text-gray-500">Be specific about their circumstances and needs</p>
                            @enderror
                            <span class="text-xs text-gray-500" x-text="needCount + '/1000'"></span>
                        </div>
                    </div>

                    <!-- Helpful Tips -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-2">Tips for writing a good description:</h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li>• Describe their current situation and challenges</li>
                                    <li>• Explain why they need financial assistance</li>
                                    <li>• Mention specific needs (medical, education, food, etc.)</li>
                                    <li>• Include any relevant background information</li>
                                    <li>• Be honest and detailed to help reviewers understand</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- What Happens Next -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-6 rounded-lg mb-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-green-900 mb-3 text-lg">What happens after submission?</h3>
                            <div class="space-y-2 text-sm text-green-800">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Your submission will be <strong>reviewed by our admin team</strong></span>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>You'll receive <strong>notification of approval status</strong> via email</span>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Once approved, admins can <strong>suggest this recipient to campaigns</strong></span>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Organizers can <strong>allocate donations to help</strong> the recipient</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('public.recipients.index') }}"
                       class="flex-1 text-center px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                        Submit Registration
                    </button>
                </div>
            </form>
        </div>

        <!-- Additional Information -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Frequently Asked Questions
            </h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div>
                    <p class="font-semibold text-gray-900">How long does approval take?</p>
                    <p>Typically 2-3 business days. Our team reviews each submission carefully to ensure legitimacy.</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Can I edit after submission?</p>
                    <p>Yes, you can edit pending submissions. Once approved, contact us for changes.</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Will personal information be public?</p>
                    <p>No. Details are only shared with admins and organizers allocating funds.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} CharityHub. Making a difference together.</p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
