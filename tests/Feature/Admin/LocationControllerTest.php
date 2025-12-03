<?php

use App\Models\Game;
use App\Models\Location;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
});

test('admin can view locations index', function () {
    $locations = Location::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->get('/admin/locations')
        ->assertStatus(200)
        ->assertSee($locations->first()->name);
});

test('admin can view create location form', function () {
    $this->actingAs($this->admin)
        ->get('/admin/locations/create')
        ->assertStatus(200)
        ->assertSee('Nieuwe locatie');
});

test('admin can create a location', function () {
    $this->actingAs($this->admin)
        ->post('/admin/locations', [
            'name' => 'Test Locatie',
            'description' => 'Test beschrijving',
        ])
        ->assertRedirect('/admin/locations')
        ->assertSessionHas('status');

    $this->assertDatabaseHas('locations', ['name' => 'Test Locatie']);
});

test('location name is required', function () {
    $this->actingAs($this->admin)
        ->post('/admin/locations', [
            'name' => '',
            'description' => 'Test beschrijving',
        ])
        ->assertSessionHasErrors('name');
});

test('location name must be unique', function () {
    Location::factory()->create(['name' => 'Bestaande Locatie']);

    $this->actingAs($this->admin)
        ->post('/admin/locations', [
            'name' => 'Bestaande Locatie',
        ])
        ->assertSessionHasErrors('name');
});

test('admin can view edit location form', function () {
    $location = Location::factory()->create();

    $this->actingAs($this->admin)
        ->get("/admin/locations/{$location->id}/edit")
        ->assertStatus(200)
        ->assertSee($location->name);
});

test('admin can update a location', function () {
    $location = Location::factory()->create();

    $this->actingAs($this->admin)
        ->put("/admin/locations/{$location->id}", [
            'name' => 'Bijgewerkte Naam',
            'description' => 'Bijgewerkte beschrijving',
        ])
        ->assertRedirect('/admin/locations')
        ->assertSessionHas('status');

    $this->assertDatabaseHas('locations', ['name' => 'Bijgewerkte Naam']);
});

test('admin can delete a location without games', function () {
    $location = Location::factory()->create();

    $this->actingAs($this->admin)
        ->delete("/admin/locations/{$location->id}")
        ->assertRedirect('/admin/locations')
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('locations', ['id' => $location->id]);
});

test('admin cannot delete a location with games', function () {
    $location = Location::factory()->create();
    Game::factory()->create(['location_id' => $location->id]);

    $this->actingAs($this->admin)
        ->delete("/admin/locations/{$location->id}")
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('locations', ['id' => $location->id]);
});

test('deleting location cascades to bingo items and route stops', function () {
    $location = Location::factory()
        ->has(\App\Models\LocationBingoItem::factory()->count(2), 'bingoItems')
        ->has(\App\Models\LocationRouteStop::factory()->count(2), 'routeStops')
        ->create();

    $this->actingAs($this->admin)
        ->delete("/admin/locations/{$location->id}");

    $this->assertDatabaseMissing('location_bingo_items', ['location_id' => $location->id]);
    $this->assertDatabaseMissing('location_route_stops', ['location_id' => $location->id]);
});
