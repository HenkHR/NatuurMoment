<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play</title>
</head>
<body>
    <div>
        <h1>Locatie:</h1>
        <p>{{ $location->name }}</p>
        <p>{{ $location->description }}</p>
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div>
            <h2>Play</h2>
            <form action="{{ route('play.create', $location->id) }}" method="POST">
                @csrf
                <button type="submit">Start Game (Host)</button>
            </form>
            <a href="{{ route('player.join') }}">
                Join Game
            </a>
        </div>
    </div>
</body>
</html>