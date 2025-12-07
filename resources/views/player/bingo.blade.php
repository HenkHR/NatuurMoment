<html lang="nl">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bingokaart</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-white">
<main class="relative min-h-screen overflow-hidden">
    <!-- Schuine achtergrondlaag -->
    <div
        class="absolute inset-0 -z-10 bg-gradient-to-br from-[#FFFFFF] to-blue-100"
        style="clip-path: polygon(0 0, 100% 10%, 100% 100%, 0 90%);">
    </div>

    <div class="w-full px-4 pt-6 pb-24 bg-[#2E7D32]"
         style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), 0 100%);">
        <h1 class="text-4xl font-bold text-[#FFFFFF] mb-2 text-left">Foto Bingo</h1>
    </div>

    {{--link naar speluitleg--}}
    <div class="flex justify-end mb-4 mt-5">
        <x-game.game-nav
            href="speluitleg"
            class="bg-[#2E7D32] hover:bg-green-600 text-white px-4 py-2 rounded mr-4">
            Speluitleg
        </x-game.game-nav>
    </div>

    <!-- Photo Capture Component (includes bingo card) -->
    @livewire('player-photo-capture', [
        'gameId' => $gameId,
        'playerToken' => $playerToken,
        'bingoItemId' => null
    ])

    <nav class="fixed bottom-0 left-0 right-0 bg-[#0076A8]">
        <div class="mx-auto w-full max-w-lg flex justify-around py-4 sm:py-6">
            <a href="/bingo">
                <img src="{{ asset('img/Grid.svg') }}" alt="Bingo" class="w-8 h-8 sm:w-10 sm:h-10">
            </a>
            <a href="/home">
                <img src="{{ asset('img/Home.svg') }}" alt="Home" class="w-8 h-8 sm:w-10 sm:h-10">
            </a>
            <a href="/route">
                <img src="{{ asset('img/mingcute_route-fill.svg') }}" alt="Route" class="w-8 h-8 sm:w-10 sm:h-10">
            </a>
        </div>
    </nav>

</main>
@livewireScripts
</body>
</html>