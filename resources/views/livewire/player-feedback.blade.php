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

                {{-- Vraag 1 --}}
                <div class="mb-6">
                    <label class="block text-white font-semibold mb-4 text-center">
                        Wat vond je van het spel?
                    </label>

                    <div class="grid grid-cols-5 gap-2 justify-items-center">
                        @foreach(range(1, 10) as $num)
                            <button
                                wire:click="setRating({{ $num }})"
                                class="w-10 h-10 rounded-lg font-bold text-white transition
                                    {{ $rating === $num ? 'ring-4 ring-yellow-400 scale-110' : '' }}
                                    {{ $num <= 2 ? 'bg-red-500 hover:bg-red-600' : '' }}
                                    {{ $num >= 3 && $num <= 4 ? 'bg-orange-500 hover:bg-orange-600' : '' }}
                                    {{ $num >= 5 && $num <= 6 ? 'bg-yellow-500 hover:bg-yellow-600' : '' }}
                                    {{ $num >= 7 && $num <= 8 ? 'bg-lime-500 hover:bg-lime-600' : '' }}
                                    {{ $num >= 9 ? 'bg-green-500 hover:bg-green-600' : '' }}">
                                {{ $num }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Vraag 2 --}}
                <div class="mb-6">
                    <label class="block text-white font-semibold mb-3 text-center">
                        Hoe oud ben je?
                    </label>

                    <input
                        type="text"
                        wire:model="age"
                        placeholder="Leeftijd..."
                        class="w-full px-4 py-3 rounded-lg bg-[#0B84B9] border-0 text-center text-white
                               placeholder-white focus:ring-2 focus:ring-white focus:outline-none"
                    >
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
