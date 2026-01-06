<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Campaign - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50" x-data="campaignForm()">
<div class="min-h-screen">
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Campaign</h1>
                    <p class="text-gray-600 mt-1">Share your mission and start making a difference</p>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3" :class="currentStep >= 1 ? 'opacity-100' : 'opacity-50'">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                             :class="currentStep >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'">
                            1
                        </div>
                        <span class="text-sm font-medium" :class="currentStep >= 1 ? 'text-gray-900' : 'text-gray-500'">Basic Info</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 rounded" :class="currentStep >= 2 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                    <div class="flex items-center gap-3" :class="currentStep >= 2 ? 'opacity-100' : 'opacity-50'">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                             :class="currentStep >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'">
                            2
                        </div>
                        <span class="text-sm font-medium" :class="currentStep >= 2 ? 'text-gray-900' : 'text-gray-500'">Goal & Timeline</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 rounded" :class="currentStep >= 3 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                    <div class="flex items-center gap-3" :class="currentStep >= 3 ? 'opacity-100' : 'opacity-50'">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                             :class="currentStep >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'">
                            3
                        </div>
                        <span class="text-sm font-medium" :class="currentStep >= 3 ? 'text-gray-900' : 'text-gray-500'">Review</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
            <form action="{{ route('campaigns.store') }}" method="POST" x-on:submit="validateForm">
                @csrf

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                        Campaign Title
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           x-model="title"
                           x-on:input="updateStep()"
                           maxlength="100"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                           placeholder="e.g., Support Education for Underprivileged Children">
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-xs text-gray-500">Create a compelling title that describes your cause</p>
                        <span class="text-xs font-medium" :class="title.length > 80 ? 'text-orange-600' : 'text-gray-500'">
                            <span x-text="title.length"></span>/100
                        </span>
                    </div>
                    @error('title')
                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        Campaign Description
                    </label>
                    <textarea name="description" id="description" rows="6"
                              x-model="description"
                              x-on:input="updateStep()"
                              maxlength="1000"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                              placeholder="Tell your story... Explain what you're raising funds for, who it will help, and the impact it will make. Be specific and heartfelt!">{{ old('description') }}</textarea>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-xs text-gray-500">💡 Tip: Share your mission, impact, and why people should support you</p>
                        <span class="text-xs font-medium" :class="description.length > 800 ? 'text-orange-600' : 'text-gray-500'">
                            <span x-text="description.length"></span>/1000
                        </span>
                    </div>
                    @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Goal Amount with Suggestions -->
                <div class="mb-6">
                    <label for="goal_amount" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Fundraising Goal (RM)
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">RM</span>
                        <input type="number" name="goal_amount" id="goal_amount" value="{{ old('goal_amount') }}" step="0.01" min="100" required
                               x-model="goalAmount"
                               x-on:input="updateStep()"
                               class="w-full pl-14 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-lg font-semibold"
                               placeholder="10000.00">
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button type="button" @click="goalAmount = 1000" class="px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">RM 1,000</button>
                        <button type="button" @click="goalAmount = 5000" class="px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">RM 5,000</button>
                        <button type="button" @click="goalAmount = 10000" class="px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">RM 10,000</button>
                        <button type="button" @click="goalAmount = 25000" class="px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">RM 25,000</button>
                        <button type="button" @click="goalAmount = 50000" class="px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">RM 50,000</button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">💡 Quick select: Choose a common goal amount or enter your custom target</p>
                    @error('goal_amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Range -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Start Date
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}" required
                               x-model="startDate"
                               x-on:input="updateStep(); validateDates()"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <p class="mt-2 text-xs text-gray-500">When will your campaign begin?</p>
                        @error('start_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            End Date
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}" required
                               x-model="endDate"
                               x-on:input="updateStep(); validateDates()"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <p class="mt-2 text-xs text-gray-500" x-show="campaignDuration > 0">
                            <span class="font-semibold text-indigo-600" x-text="campaignDuration"></span> day<span x-show="campaignDuration !== 1">s</span> campaign duration
                        </p>
                        @error('end_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p x-show="dateError" class="mt-2 text-sm text-red-600" x-text="dateError"></p>
                    </div>
                </div>

                <!-- Campaign Preview Card -->
                <div x-show="currentStep >= 2" class="mb-8 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6 border-2 border-indigo-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Campaign Preview
                    </h3>
                    <div class="bg-white rounded-lg p-5 shadow-sm">
                        <h4 class="font-bold text-xl text-gray-900 mb-2" x-text="title || 'Your Campaign Title'"></h4>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3" x-text="description || 'Your campaign description will appear here...'"></p>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Goal Amount</p>
                                <p class="text-2xl font-bold text-indigo-600">RM <span x-text="parseFloat(goalAmount || 0).toLocaleString('en-MY', {minimumFractionDigits: 2})"></span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Duration</p>
                                <p class="text-lg font-semibold text-gray-900"><span x-text="campaignDuration || 0"></span> days</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Banner -->
                <div class="mb-6 bg-blue-50 border-2 border-blue-200 rounded-xl p-5 flex gap-4">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-semibold text-blue-900 mb-1">Your campaign will need admin approval</h4>
                        <p class="text-sm text-blue-800">After submission, an administrator will review your campaign. You'll be notified once it's approved and goes live!</p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-between items-center pt-6 border-t-2 border-gray-200">
                    <a href="{{ route('campaigns.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit"
                            :disabled="currentStep < 2"
                            :class="currentStep >= 2 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700' : 'bg-gray-300 cursor-not-allowed'"
                            class="px-8 py-3 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Create Campaign
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    function campaignForm() {
        return {
            title: '{{ old('title') }}',
            description: '{{ old('description') }}',
            goalAmount: {{ old('goal_amount') ?? 0 }},
            startDate: '{{ old('start_date') }}',
            endDate: '{{ old('end_date') }}',
            currentStep: 1,
            dateError: '',

            updateStep() {
                // Determine current step based on filled fields
                if (this.title && this.description) {
                    this.currentStep = 2;
                } else {
                    this.currentStep = 1;
                }

                if (this.goalAmount > 0 && this.startDate && this.endDate && !this.dateError) {
                    this.currentStep = 3;
                }
            },

            validateDates() {
                this.dateError = '';
                if (this.startDate && this.endDate) {
                    const start = new Date(this.startDate);
                    const end = new Date(this.endDate);

                    if (end <= start) {
                        this.dateError = 'End date must be after start date';
                    }
                }
            },

            get campaignDuration() {
                if (!this.startDate || !this.endDate) return 0;
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                const diffTime = Math.abs(end - start);
                return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            },

            validateForm(e) {
                if (this.currentStep < 2) {
                    e.preventDefault();
                    alert('Please complete all required fields');
                    return false;
                }
                if (this.dateError) {
                    e.preventDefault();
                    alert(this.dateError);
                    return false;
                }
            }
        }
    }
</script>
</body>
</html>
