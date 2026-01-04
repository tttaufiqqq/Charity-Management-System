<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Reports - CharityHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <!-- Navigation -->
    @include('navbar')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Detailed Donation Reports</h1>
            <p class="mt-2 text-sm text-gray-600">Comprehensive reports with complex SQL joins across multiple tables</p>
        </div>

        <!-- Livewire Component -->
        @livewire('donation-detail-report')
    </main>
</div>
@livewireScripts
</body>
</html>
