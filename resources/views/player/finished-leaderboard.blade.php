<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Eindstand</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/website-icon.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white font-sans antialiased overflow-hidden">
<main>
    @livewire('player-finished-leaderboard', [
        'gameId' => $gameId,
        'playerToken' => $playerToken,
    ])
</main>
@livewireScripts
</body>
</html>
