<x-app-layout>
    <div class="py-6 bg-surface-light min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <h2 class="text-h2 text-deep-black">Instellingen</h2>

            <div class="mb-6">
                <a href="{{ route('admin.locations.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-md transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Terug naar locaties
                </a>
            </div>

            {{-- Admin Preferences --}}
            <div class="p-4 sm:p-8 bg-pure-white shadow-card rounded-card">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-deep-black">
                                Admin voorkeuren
                            </h2>
                            <p class="mt-1 text-sm text-deep-black">
                                Pas je admin panel voorkeuren aan.
                            </p>
                        </header>

                        <form method="post" action="{{ route('settings.preferences') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="admin_per_page" value="Items per pagina" />
                                <select
                                    id="admin_per_page"
                                    name="admin_per_page"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-deep-black bg-white"
                                >
                                    @foreach([10, 15, 25, 50, 100] as $option)
                                        <option value="{{ $option }}" @selected($user->admin_per_page == $option)>
                                            {{ $option }} items
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs text-surface-dark">
                                    Dit bepaalt hoeveel items er standaard worden getoond in de admin overzichten.
                                </p>
                                <x-input-error class="mt-2" :messages="$errors->get('admin_per_page')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>Opslaan</x-primary-button>

                                @if (session('status') === 'preferences-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-deep-black"
                                    >Opgeslagen.</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            {{-- Profile Information --}}
            <div class="p-4 sm:p-8 bg-pure-white shadow-card rounded-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="p-4 sm:p-8 bg-pure-white shadow-card rounded-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="p-4 sm:p-8 bg-pure-white shadow-card rounded-card">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
