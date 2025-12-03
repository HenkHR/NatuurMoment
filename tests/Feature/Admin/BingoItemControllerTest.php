<?php

use App\Models\Location;
use App\Models\LocationBingoItem;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->location = Location::factory()->create();
});

test('admin can view bingo items index for a location', function () {
    $bingoItems = LocationBingoItem::factory()->count(3)->create([
        'location_id' => $this->location->id,
    ]);

    $this->actingAs($this->admin)
        ->get("/admin/locations/{$this->location->id}/bingo-items")
        ->assertStatus(200)
        ->assertSee($bingoItems->first()->label);
});

test('admin can view create bingo item form', function () {
    $this->actingAs($this->admin)
        ->get("/admin/locations/{$this->location->id}/bingo-items/create")
        ->assertStatus(200)
        ->assertSee('Nieuw bingo item');
});

test('admin can create a bingo item', function () {
    $this->actingAs($this->admin)
        ->post("/admin/locations/{$this->location->id}/bingo-items", [
            'label' => 'Eekhoorn',
            'points' => 5,
            'icon' => null,
        ])
        ->assertRedirect("/admin/locations/{$this->location->id}/bingo-items")
        ->assertSessionHas('status');

    $this->assertDatabaseHas('location_bingo_items', [
        'location_id' => $this->location->id,
        'label' => 'Eekhoorn',
        'points' => 5,
    ]);
});

test('bingo item label is required', function () {
    $this->actingAs($this->admin)
        ->post("/admin/locations/{$this->location->id}/bingo-items", [
            'label' => '',
            'points' => 5,
        ])
        ->assertSessionHasErrors('label');
});

test('bingo item points must be at least 1', function () {
    $this->actingAs($this->admin)
        ->post("/admin/locations/{$this->location->id}/bingo-items", [
            'label' => 'Test',
            'points' => 0,
        ])
        ->assertSessionHasErrors('points');
});

test('admin can view edit bingo item form', function () {
    $bingoItem = LocationBingoItem::factory()->create([
        'location_id' => $this->location->id,
    ]);

    $this->actingAs($this->admin)
        ->get("/admin/bingo-items/{$bingoItem->id}/edit")
        ->assertStatus(200)
        ->assertSee($bingoItem->label);
});

test('admin can update a bingo item', function () {
    $bingoItem = LocationBingoItem::factory()->create([
        'location_id' => $this->location->id,
    ]);

    $this->actingAs($this->admin)
        ->put("/admin/bingo-items/{$bingoItem->id}", [
            'label' => 'Bijgewerkt Label',
            'points' => 10,
        ])
        ->assertRedirect("/admin/locations/{$this->location->id}/bingo-items")
        ->assertSessionHas('status');

    $this->assertDatabaseHas('location_bingo_items', [
        'id' => $bingoItem->id,
        'label' => 'Bijgewerkt Label',
        'points' => 10,
    ]);
});

test('admin can delete a bingo item', function () {
    $bingoItem = LocationBingoItem::factory()->create([
        'location_id' => $this->location->id,
    ]);

    $this->actingAs($this->admin)
        ->delete("/admin/bingo-items/{$bingoItem->id}")
        ->assertRedirect("/admin/locations/{$this->location->id}/bingo-items")
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('location_bingo_items', ['id' => $bingoItem->id]);
});
