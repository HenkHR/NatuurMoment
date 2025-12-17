<div class="min-h-screen bg-[#f2f2f2] flex flex-col">

    {{-- Header --}}
    <header class="relative bg-forest-700 text-white text-center px-4 py-5 sm:py-6">
        <h1 class="text-xl sm:text-2xl font-bold">
            Bedankt voor het spelen!
        </h1>

    </header>

    {{-- Content — blauwe kaart--}}
    <main class="flex-1 flex items-center justify-center px-4 py-10">

        <div class="w-full max-w-sm">

            {{-- Blauwe kaart (centered) --}}
            <div class="bg-[#0076A8] rounded-xl shadow-lg px-4 sm:px-6 py-6 border-4 border-white">

                {{-- Vraag 1: Sterren rating (1-5) --}}
                <div class="mb-6">
                    <label class="block text-white font-semibold mb-4 text-center">
                        Wat vond je van het spel?
                    </label>

                    <div class="flex justify-center gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                wire:click="setRating({{ $i }})"
                                class="p-1 transition-transform hover:scale-110 focus:outline-none"
                            >
                                <svg
                                    class="w-12 h-12 sm:w-14 sm:h-14 transition-colors {{ $rating !== null && $rating >= $i ? 'text-yellow-400' : 'text-white/40 hover:text-yellow-300' }}"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>

                    {{-- Rating indicator --}}
                    @if($rating)
                        <p class="text-center text-white/80 text-sm mt-2">{{ $rating }}/5 sterren</p>
                    @endif

                    {{-- Validation error --}}
                    @error('rating')
                        <p class="text-center text-red-200 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Vraag 2 --}}
                <div class="mb-6">
                    <label class="block text-white font-semibold mb-3 text-center">
                        Hoe oud ben je?
                    </label>

                    <input
                        type="number"
                        wire:model.number="age"
                        placeholder="Leeftijd..."
                        min="1"
                        max="99"
                        inputmode="numeric"
                        class="w-full px-4 py-3 rounded-lg bg-[#0B84B9] border-0 text-center text-white
                               placeholder-white focus:ring-2 focus:ring-white focus:outline-none
                               @error('age') border-2 border-red-300 @enderror"
                    >

                    {{-- Validation error --}}
                    @error('age')
                        <p class="text-center text-red-200 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bevestigen --}}
                <button
                    wire:click="submitFeedback"
                    class="w-full py-3 bg-white text-[#0076A8] font-semibold rounded-lg transition hover:bg-gray-100">
                    Bevestigen
                </button>

            </div>

        </div>

    </main>

</div>
