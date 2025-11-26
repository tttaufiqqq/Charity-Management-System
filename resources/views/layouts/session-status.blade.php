@if (session('status'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm text-green-600">{{ session('status') }}</p>
    </div>
@endif
