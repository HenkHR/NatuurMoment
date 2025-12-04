<x-layouts.admin>
    <div class="py-6 bg-surface-light min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="mb-4 p-4 bg-forest-100 text-forest-800 rounded-card">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-action-100 text-action-800 rounded-card">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</x-layouts.admin>
