<x-app-layout>
    <div class="py-6 bg-surface-light min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <h2 class="text-h2 text-deep-black">{{ __('Profile') }}</h2>

            <div class="mb-6">
                <a href="{{ route('admin.locations.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-md transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Terug naar locaties
                </a>
            </div>

            <div class="p-4 sm:p-8 bg-pure-white shadow-card rounded-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-pure-white shadow-card rounded-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-pure-white shadow-card rounded-card">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
