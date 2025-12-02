<html lang="nl">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bingokaart</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">
<main class="relative min-h-screen overflow-hidden">
    <!-- Schuine achtergrondlaag -->
    <div
        class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 to-blue-100"
        style="clip-path: polygon(0 0, 100% 10%, 100% 100%, 0 90%);">
    </div>

    <!-- Content container -->
    <div class="mx-auto w-full max-w-md px-4 pt-6 pb-24">
        <h1 class="text-3xl font-bold text-green-800 mb-3">Foto Bingo</h1>

        <div class="flex justify-end mb-4">
            <x-game.game-nav
                href=""
                class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded">
                Speluitleg
            </x-game.game-nav>
        </div>

        <!-- Voorbeeld grid (ontcommentariseer in Blade) -->
        {{--
        <div class="grid grid-cols-3 gap-4">
          @foreach (['Dennenappel','Paddenstoel','Eekhoorn','Mos','Vijver','Vogel','Bloem','Boom','Blad'] as $item)
            <div class="bg-white rounded shadow p-4 text-center text-green-700 font-semibold">
              {{ $item }}
            </div>
          @endforeach
        </div>
        --}}
    </div>

    <!-- Bottom nav, gecentreerd en overlapt niet door extra padding onderin -->
    <nav class="fixed bottom-0 left-0 right-0 bg-blue-700 border-t">
        <div class="mx-auto w-full max-w-md flex justify-around py-2">
            <button class="text-green-100 font-semibold">bingo</button>
            <button class="text-green-100 font-semibold">home</button>
            <button class="text-green-100 font-semibold">route</button>
        </div>
    </nav>
</main>
</body>
</html>
