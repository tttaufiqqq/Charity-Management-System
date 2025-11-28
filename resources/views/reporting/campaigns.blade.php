<!-- ============================================================================ -->
<!-- File: resources/views/reporting/campaigns.blade.php -->
<!-- ============================================================================ -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Analytics - CharityHub Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-indigo-600">CharityHub Admin</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.analytics.dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Analytics</a>
                    <a href="{{ route('admin.analytics.campaigns') }}" class="text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Campaigns</a>
                    <a href="{{ route('admin.analytics.events') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Events</a>
                    <a href="{{ route('admin.analytics.donors') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Donors</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Campaign Analytics</h1>
        @livewire('campaign-analytics')
    </main>
</div>
@livewireScripts
</body>
</html>
