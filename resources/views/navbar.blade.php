<!-- resources/views/navbar.blade.php -->
<nav class="bg-white shadow-sm" x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo / Site Name -->
            <div class="flex items-center gap-3">
                <!-- Back Button -->
                <button onclick="window.history.back()" class="text-gray-600 hover:text-indigo-600 p-2 rounded-md hover:bg-gray-50 transition-colors" title="Go back">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span class="hidden sm:inline">CharityHub</span>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden md:flex items-center space-x-1">
                @auth
                    <!-- Admin Navigation -->
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('welcome') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Home
                        </a>
                        <a href="{{ route('admin.analytics.dashboard') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.manage.users') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Users
                        </a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-1">
                                Approvals
                                <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('admin.recipients.pending') }}" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Recipients</a>
                                <a href="{{ route('admin.campaigns.pending') }}" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Campaigns</a>
                                <a href="{{ route('admin.events.pending') }}" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Events</a>
                            </div>
                        </div>

                    <!-- Organizer Navigation -->
                    @elseif(auth()->user()->hasRole('organizer'))
                        <a href="{{ route('welcome') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Home
                        </a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-1">
                                Campaigns
                                <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('campaigns.index') }}" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Campaigns</a>
                                <a href="{{ route('campaigns.create') }}" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Create Campaign</a>
                                <a href="{{ route('campaigns.index') }}" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Campaigns</a>
                            </div>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-1">
                                Events
                                <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('events.index') }}" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Events</a>
                                <a href="{{ route('events.create') }}" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Create Event</a>
                            </div>
                        </div>
                        <a href="{{ route('organizer.allocations.all') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Allocations
                        </a>

                    <!-- Donor Navigation -->
                    @elseif(auth()->user()->hasRole('donor'))
                        <a href="{{ route('campaigns.browse') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Browse Campaigns
                        </a>
                        <a href="{{ route('donations.my') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            My Donations
                        </a>
                        <a href="{{ route('public.events.browse') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Events
                        </a>

                    <!-- Volunteer Navigation -->
                    @elseif(auth()->user()->hasRole('volunteer'))
                        <a href="{{ route('volunteer.dashboard') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                        <a href="{{ route('volunteer.events.browse') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Find Events
                        </a>
                        <a href="{{ route('volunteer.dashboard') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            My Events
                        </a>
                        <a href="{{ route('volunteer.schedule') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Schedule
                        </a>
                        <a href="{{ route('volunteer.skills.index') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Skills
                        </a>

                    <!-- Public Navigation -->
                    @elseif(auth()->user()->hasRole('public'))
                        <a href="{{ route('public.campaigns.browse') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Campaigns
                        </a>
                        <a href="{{ route('public.events.browse') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Events
                        </a>
                        <a href="{{ route('public.recipients.index') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Recipients
                        </a>
                    @endif

                    <!-- User Menu Dropdown -->
                    <div class="relative ml-3" x-data="{ open: false }" @click.away="open = false">
                        <button @click.stop="open = !open" type="button" class="flex items-center gap-2 text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-indigo-600 font-semibold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                            </div>
                            <span class="hidden lg:inline">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm text-gray-900 font-medium">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ auth()->user()->email }}</p>
                                <p class="text-xs text-indigo-600 font-medium mt-1">
                                    @if(auth()->user()->hasRole('admin'))
                                        Administrator
                                    @elseif(auth()->user()->hasRole('organizer'))
                                        Organization
                                    @elseif(auth()->user()->hasRole('donor'))
                                        Donor
                                    @elseif(auth()->user()->hasRole('volunteer'))
                                        Volunteer
                                    @elseif(auth()->user()->hasRole('public'))
                                        Public User
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('profile.edit') }}" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    My Profile
                                </div>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Log out
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Guest Navigation -->
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        Register
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-indigo-600 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
            @auth
                <!-- User Info -->
                <div class="px-3 py-2 mb-2 bg-gray-50 rounded-md">
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    <p class="text-xs text-indigo-600 font-medium mt-1">
                        @if(auth()->user()->hasRole('admin'))
                            Administrator
                        @elseif(auth()->user()->hasRole('organizer'))
                            Organization
                        @elseif(auth()->user()->hasRole('donor'))
                            Donor
                        @elseif(auth()->user()->hasRole('volunteer'))
                            Volunteer
                        @elseif(auth()->user()->hasRole('public'))
                            Public User
                        @endif
                    </p>
                </div>

                <!-- Admin Mobile Menu -->
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.analytics.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Dashboard</a>
                    <a href="{{ route('admin.analytics.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Analytics</a>
                    <a href="{{ route('admin.manage.users') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Users</a>
                    <a href="{{ route('admin.recipients.pending') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Recipients Approval</a>
                    <a href="{{ route('admin.campaigns.pending') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Campaigns Approval</a>
                    <a href="{{ route('admin.events.pending') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Events Approval</a>

                <!-- Organizer Mobile Menu -->
                @elseif(auth()->user()->hasRole('organizer'))
                    <a href="{{ route('welcome') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Home</a>
                    <a href="{{ route('campaigns.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">My Campaigns</a>
                    <a href="{{ route('campaigns.create') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Create Campaign</a>
                    <a href="{{ route('campaigns.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">All Campaigns</a>
                    <a href="{{ route('events.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">My Events</a>
                    <a href="{{ route('events.create') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Create Event</a>
                    <a href="{{ route('public.campaigns.browse') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Fund Allocations</a>

                <!-- Donor Mobile Menu -->
                @elseif(auth()->user()->hasRole('donor'))
                    <a href="{{ route('welcome') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Home</a>
                    <a href="{{ route('campaigns.browse') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Browse Campaigns</a>
                    <a href="{{ route('donations.my') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">My Donations</a>

                <!-- Volunteer Mobile Menu -->
                @elseif(auth()->user()->hasRole('volunteer'))
                    <a href="{{ route('welcome') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Home</a>
                    <a href="{{ route('volunteer.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Dashboard</a>
                    <a href="{{ route('volunteer.events.browse') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Find Events</a>
                    <a href="{{ route('volunteer.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">My Events</a>
                    <a href="{{ route('volunteer.schedule') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">My Schedule</a>
                    <a href="{{ route('volunteer.skills.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">My Skills</a>

                <!-- Public Mobile Menu -->
                @elseif(auth()->user()->hasRole('public'))
                    <a href="{{ route('welcome') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Home</a>
                    <a href="{{ route('public.campaigns.browse') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Campaigns</a>
                    <a href="{{ route('public.events.browse') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Events</a>
                    <a href="{{ route('public.recipients.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Recipients</a>
                @endif

                <!-- Common Mobile Menu Items -->
                <div class="border-t border-gray-200 mt-2 pt-2">
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">My Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-md">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <!-- Guest Mobile Menu -->
                <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded-md">Login</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-medium bg-indigo-600 text-white hover:bg-indigo-700 rounded-md">Register</a>
            @endauth
        </div>
    </div>
</nav>
