<html lang="nl">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-white">
<main>
    @livewire('player-feedback', [
        'gameId' => $gameId,
        'playerToken' => $playerToken,
    ])
</main>
@livewireScripts
</body>
</html>

