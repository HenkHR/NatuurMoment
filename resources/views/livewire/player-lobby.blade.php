<div wire:poll.2s.visible="checkGameStatus" class="h-screen w-full bg-surface-light flex flex-col overflow-hidden">

    <x-nav.lobby :title="'Game Lobby'" :subtitle="$locationName" />

    <main class="flex-1 overflow-hidden min-h-0 pb-28">
        <div class="max-w-5xl mx-auto px-4 lg:px-8 pt-6 pb-4 h-full flex flex-col">

            <div class="flex flex-row items-center justify-between gap-3 mb-5">
                <div
                    class="
                        w-[45%] sm:w-[180px] md:w-[190px]
                        bg-sky-500 text-pure-white text-sm font-semibold
                        rounded-button px-4 py-2 shadow-card
                        flex items-center justify-center
                        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                        focus-visible:ring-sky-500 focus-visible:ring-offset-surface-light
                    "
                    tabindex="0"
                    aria-label="Spelcode {{ $pin }}"
                >
                    Code: {{ $pin }}
                </div>

                <a
                    href="{{ url('/speluitleg') }}"
                    class="
                        w-[45%] sm:w-[180px] md:w-[190px]
                        text-center bg-forest-700 hover:bg-forest-600 text-white rounded-lg font-semibold transition px-4 py-2
                        flex items-center justify-center
                    "
                >
                    Speluitleg
                </a>
            </div>

            <section class="bg-pure-white shadow-card rounded-card overflow-hidden flex-1 flex flex-col min-h-0">
                <div class="px-4 pt-4 pb-2 flex-shrink-0">
                    <p class="text-sm font-semibold text-deep-black mb-3">
                        Aantal spelers: {{ $playerCount }}
                    </p>
                </div>

                    @if(count($players))
                    <div class="flex-1 overflow-y-auto min-h-0 px-4 pb-4">
                        <ul class="space-y-3 pr-2 pt-3 pb-3">
                            @foreach($players as $player)
                                @php
                                    $isSelf = $player['name'] === $playerName;
                                @endphp

                                <li
                                    wire:key="player-{{ $player['id'] }}"
                                    class="flex items-center justify-between bg-surface-medium rounded-card px-4 py-3 shadow-card
                                        @if($isSelf) ring-2 ring-forest-500 ring-offset-2 ring-offset-pure-white @endif"
                                    @if($isSelf) aria-current="true" @endif
                                >
                                    <span class="font-semibold text-deep-black @if($isSelf) underline @endif">
                                        {{ $player['name'] }}
                                    </span>

                                    @if($isSelf)
                                        <span
                                            class="text-xs font-semibold bg-pure-white text-forest-700 px-2 py-1 rounded-badge shadow-card flex-shrink-0"
                                        >
                                            Jij
                                        </span>
                                    @else
                                        <span class="text-xs text-deep-black/60">
                                            &nbsp;
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <div class="px-4 pb-4 flex-shrink-0">
                        <p class="text-sm text-deep-black/70 py-4">
                            Nog geen spelers...
                        </p>
                    </div>
                    @endif
            </section>
        </div>
    </main>

    <footer class="fixed bottom-0 left-0 right-0 bg-sky-500 py-6 pb-safe">
        <div class="max-w-5xl mx-auto px-4 lg:px-8">
            <div class="bg-pure-white text-sky-700 font-semibold text-sm md:text-base px-6 py-3 rounded-card shadow-card text-center">
                Wachten tot spel is gestart...
            </div>
        </div>
    </footer>
</div>
