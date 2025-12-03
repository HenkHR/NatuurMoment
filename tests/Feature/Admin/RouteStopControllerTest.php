<?php

use App\Models\Location;
use App\Models\LocationRouteStop;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->location = Location::factory()->create();
});

test('admin can view route stops index for a location', function () {
    $routeStops = LocationRouteStop::factory()->count(3)->create([
        'location_id' => $this->location->id,
    ]);

    $this->actingAs($this->admin)
        ->get("/admin/locations/{$this->location->id}/route-stops")
        ->assertStatus(200)
        ->assertSee($routeStops->first()->name);
});

test('admin can view create route stop form', function () {
    $this->actingAs($this->admin)
        ->get("/admin/locations/{$this->location->id}/route-stops/create")
        ->assertStatus(200)
        ->assertSee('Nieuwe vraag');
});

test('admin can create a route stop', function () {
    $this->actingAs($this->admin)
        ->post("/admin/locations/{$this->location->id}/route-stops", [
            'name' => 'Vraag 1',
            'question_text' => 'Wat is de kleur van gras?',
            'option_a' => 'Rood',
            'option_b' => 'Groen',
            'option_c' => 'Blauw',
            'option_d' => 'Geel',
            'correct_option' => 'B',
            'points' => 5,
            'sequence' => 0,
        ])
        ->assertRedirect("/admin/locations/{$this->location->id}/route-stops")
        ->assertSessionHas('status');

    $this->assertDatabaseHas('location_route_stops', [
        'location_id' => $this->location->id,
        'name' => 'Vraag 1',
        'correct_option' => 'B',
    ]);
});

test('route stop question_text is required', function () {
    $this->actingAs($this->admin)
        ->post("/admin/locations/{$this->location->id}/route-stops", [
            'name' => 'Test',
            'question_text' => '',
            'option_a' => 'A',
            'option_b' => 'B',
            'correct_option' => 'A',
            'points' => 1,
            'sequence' => 0,
        ])
        ->assertSessionHasErrors('question_text');
});

test('route stop correct_option must be valid', function () {
    $this->actingAs($this->admin)
        ->post("/admin/locations/{$this->location->id}/route-stops", [
            'name' => 'Test',
            'question_text' => 'Test vraag?',
            'option_a' => 'A',
            'option_b' => 'B',
            'correct_option' => 'E',
            'points' => 1,
            'sequence' => 0,
        ])
        ->assertSessionHasErrors('correct_option');
});

test('route stop option_a and option_b are required', function () {
    $this->actingAs($this->admin)
        ->post("/admin/locations/{$this->location->id}/route-stops", [
            'name' => 'Test',
            'question_text' => 'Test vraag?',
            'option_a' => '',
            'option_b' => '',
            'correct_option' => 'A',
            'points' => 1,
            'sequence' => 0,
        ])
        ->assertSessionHasErrors(['option_a', 'option_b']);
});

test('admin can view edit route stop form', function () {
    $routeStop = LocationRouteStop::factory()->create([
        'location_id' => $this->location->id,
    ]);

    $this->actingAs($this->admin)
        ->get("/admin/route-stops/{$routeStop->id}/edit")
        ->assertStatus(200)
        ->assertSee($routeStop->name);
});

test('admin can update a route stop', function () {
    $routeStop = LocationRouteStop::factory()->create([
        'location_id' => $this->location->id,
    ]);

    $this->actingAs($this->admin)
        ->put("/admin/route-stops/{$routeStop->id}", [
            'name' => 'Bijgewerkte Naam',
            'question_text' => 'Bijgewerkte vraag?',
            'option_a' => 'Nieuw A',
            'option_b' => 'Nieuw B',
            'correct_option' => 'A',
            'points' => 10,
            'sequence' => 5,
        ])
        ->assertRedirect("/admin/locations/{$this->location->id}/route-stops")
        ->assertSessionHas('status');

    $this->assertDatabaseHas('location_route_stops', [
        'id' => $routeStop->id,
        'name' => 'Bijgewerkte Naam',
    ]);
});

test('admin can delete a route stop', function () {
    $routeStop = LocationRouteStop::factory()->create([
        'location_id' => $this->location->id,
    ]);

    $this->actingAs($this->admin)
        ->delete("/admin/route-stops/{$routeStop->id}")
        ->assertRedirect("/admin/locations/{$this->location->id}/route-stops")
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('location_route_stops', ['id' => $routeStop->id]);
});
