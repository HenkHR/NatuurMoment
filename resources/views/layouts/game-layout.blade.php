<?php
?>

    <!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Foto Bingo' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/website-icon.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-white">
<main class="relative min-h-screen overflow-hidden">
    {{ $slot }}
</main>
</body>
</html>
