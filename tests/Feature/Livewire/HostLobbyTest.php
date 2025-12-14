<?php

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Location;
use App\Models\LocationBingoItem;
use App\Models\LocationRouteStop;
use App\Models\RouteStop;
use App\Livewire\HostLobby;
use Livewire\Livewire;

beforeEach(function () {
    // Create a location with enough bingo items
    $this->location = Location::factory()->create();

    // Create 9 bingo items for the location
    for ($i = 0; $i < 9; $i++) {
        LocationBingoItem::create([
            'location_id' => $this->location->id,
            'label' => "Item {$i}",
            'points' => 1,
            'icon' => 'default.png',
        ]);
    }

    // Create a game in lobby status
    $this->game = Game::create([
        'location_id' => $this->location->id,
        'pin' => Game::generatePin(),
        'status' => 'lobby',
        'host_token' => Game::generateHostToken(),
    ]);

    // Set host session token
    session(["hostToken_{$this->game->id}" => $this->game->host_token]);
});

// REQ-019: Spelers overzicht met verwijder-optie
it('REQ-019: shows players list in lobby', function () {
    // Create players
    $player1 = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Speler 1',
        'token' => 'token1',
    ]);
    $player2 = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Speler 2',
        'token' => 'token2',
    ]);

    Livewire::test(HostLobby::class, ['gameId' => $this->game->id])
        ->assertSee('Speler 1')
        ->assertSee('Speler 2')
        ->assertSee('Verwijderen');
});

// REQ-020: Host kan speler verwijderen met bevestiging
it('REQ-020: host can remove player from lobby', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Te Verwijderen Speler',
        'token' => 'remove-token',
    ]);

    expect(GamePlayer::find($player->id))->not->toBeNull();

    Livewire::test(HostLobby::class, ['gameId' => $this->game->id])
        ->call('removePlayer', $player->id);

    expect(GamePlayer::find($player->id))->toBeNull();
});

// REQ-020: Non-host cannot remove player
it('REQ-020: non-host cannot remove player', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Protected Player',
        'token' => 'protected-token',
    ]);

    // Clear host session
    session()->forget("hostToken_{$this->game->id}");

    Livewire::test(HostLobby::class, ['gameId' => $this->game->id])
        ->call('removePlayer', $player->id)
        ->assertForbidden();
});

// REQ-002: Timer start automatisch wanneer spel start
it('REQ-002: timer starts automatically when game starts', function () {
    // Add a player (required to start)
    GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
    ]);

    Livewire::test(HostLobby::class, ['gameId' => $this->game->id])
        ->set('timerDuration', 30)
        ->call('startGame');

    $this->game->refresh();

    expect($this->game->timer_enabled)->toBeTrue();
    expect($this->game->timer_ends_at)->not->toBeNull();
    expect($this->game->timer_duration_minutes)->toBe(30);
});

// Timer duration is required to start game
it('requires timer duration to start game', function () {
    GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
    ]);

    Livewire::test(HostLobby::class, ['gameId' => $this->game->id])
        ->set('timerDuration', null)
        ->call('startGame')
        ->assertSessionHas('error', 'Selecteer een speelduur om te starten');

    $this->game->refresh();
    expect($this->game->status)->toBe('lobby');
});

// REQ-001: Timer duur selecteren
it('REQ-001: can select timer duration', function () {
    Livewire::test(HostLobby::class, ['gameId' => $this->game->id])
        ->set('timerDuration', 60)
        ->assertSet('timerDuration', 60);
});

// Timer durations constant is available
it('has correct timer duration options', function () {
    expect(HostLobby::TIMER_DURATIONS)->toBe([15, 30, 45, 60, 90, 120]);
});

// REQ-005: Vragen worden gekopieerd van LocationRouteStop naar RouteStop bij game start
it('REQ-005: copies route stops from location to game on start', function () {
    // Create location route stops (multiple choice questions)
    LocationRouteStop::create([
        'location_id' => $this->location->id,
        'name' => 'Vraag 1',
        'question_text' => 'Wat is de hoofdstad van Nederland?',
        'option_a' => 'Amsterdam',
        'option_b' => 'Den Haag',
        'option_c' => 'Rotterdam',
        'option_d' => 'Utrecht',
        'correct_option' => 'A',
        'points' => 10,
        'sequence' => 1,
    ]);

    LocationRouteStop::create([
        'location_id' => $this->location->id,
        'name' => 'Vraag 2',
        'question_text' => 'Hoeveel provincies heeft Nederland?',
        'option_a' => '10',
        'option_b' => '12',
        'option_c' => '14',
        'option_d' => null,
        'correct_option' => 'B',
        'points' => 5,
        'sequence' => 2,
    ]);

    // Add a player (required to start)
    GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    // Enable timer (required to start)
    $this->game->update([
        'timer_enabled' => true,
        'timer_duration_minutes' => 30,
    ]);

    // Start the game
    Livewire::test(HostLobby::class, ['gameId' => $this->game->id])
        ->call('startGame');

    // Assert route stops were copied
    $routeStops = RouteStop::where('game_id', $this->game->id)->orderBy('sequence')->get();

    expect($routeStops)->toHaveCount(2);
    expect($routeStops[0]->name)->toBe('Vraag 1');
    expect($routeStops[0]->question_text)->toBe('Wat is de hoofdstad van Nederland?');
    expect($routeStops[0]->correct_option)->toBe('A');
    expect($routeStops[0]->points)->toBe(10);
    expect($routeStops[0]->sequence)->toBe(1);
    expect($routeStops[1]->name)->toBe('Vraag 2');
    expect($routeStops[1]->sequence)->toBe(2);
});

// REQ-005: Route stops are not duplicated on multiple start attempts
it('REQ-005: does not duplicate route stops if already generated', function () {
    // Create one location route stop
    LocationRouteStop::create([
        'location_id' => $this->location->id,
        'name' => 'Vraag 1',
        'question_text' => 'Test vraag',
        'option_a' => 'Optie A',
        'option_b' => 'Optie B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    // Pre-create a route stop for this game
    RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Existing',
        'question_text' => 'Existing question',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    // Add a player (required to start)
    GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    // Enable timer
    $this->game->update([
        'timer_enabled' => true,
        'timer_duration_minutes' => 30,
    ]);

    // Start the game
    Livewire::test(HostLobby::class, ['gameId' => $this->game->id])
        ->call('startGame');

    // Assert only 1 route stop exists (not duplicated)
    $routeStops = RouteStop::where('game_id', $this->game->id)->get();
    expect($routeStops)->toHaveCount(1);
    expect($routeStops[0]->name)->toBe('Existing');
});
