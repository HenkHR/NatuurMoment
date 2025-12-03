<?php

use App\Models\Game;
use App\Models\Location;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
});

test('admin can view games index', function () {
    $games = Game::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->get('/admin/games')
        ->assertStatus(200)
        ->assertSee($games->first()->pin);
});

test('admin can view game details', function () {
    $game = Game::factory()->create();

    $this->actingAs($this->admin)
        ->get("/admin/games/{$game->id}")
        ->assertStatus(200)
        ->assertSee($game->pin)
        ->assertSee($game->location->name);
});

test('admin can delete a game', function () {
    $game = Game::factory()->create();

    $this->actingAs($this->admin)
        ->delete("/admin/games/{$game->id}")
        ->assertRedirect('/admin/games')
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('games', ['id' => $game->id]);
});

test('games index shows correct status badges', function () {
    Game::factory()->lobby()->create();
    Game::factory()->started()->create();
    Game::factory()->finished()->create();

    $this->actingAs($this->admin)
        ->get('/admin/games')
        ->assertStatus(200)
        ->assertSee('Lobby')
        ->assertSee('Gestart')
        ->assertSee('Afgelopen');
});

test('games index shows location name', function () {
    $location = Location::factory()->create(['name' => 'Test Natuurgebied']);
    Game::factory()->create(['location_id' => $location->id]);

    $this->actingAs($this->admin)
        ->get('/admin/games')
        ->assertStatus(200)
        ->assertSee('Test Natuurgebied');
});

test('game show page displays timestamps', function () {
    $game = Game::factory()->finished()->create();

    $this->actingAs($this->admin)
        ->get("/admin/games/{$game->id}")
        ->assertStatus(200)
        ->assertSee('Gestart op')
        ->assertSee('Afgelopen op');
});
