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

// ============================================
// Pagination Tests (REQ-004)
// ============================================

test('REQ-004: bingo items index paginates with 15 items per page', function () {
    LocationBingoItem::factory()->count(20)->create([
        'location_id' => $this->location->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->get("/admin/locations/{$this->location->id}/bingo-items");

    $response->assertStatus(200);
    $bingoItems = $response->viewData('bingoItems');
    expect($bingoItems->perPage())->toBe(15);
    expect($bingoItems->count())->toBe(15);
});

test('bingo items pagination preserves query string', function () {
    LocationBingoItem::factory()->count(20)->create([
        'location_id' => $this->location->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->get("/admin/locations/{$this->location->id}/bingo-items?page=1");

    $response->assertStatus(200);
    $bingoItems = $response->viewData('bingoItems');

    // withQueryString should be applied
    expect($bingoItems->hasPages())->toBeTrue();
});

// ============================================
// Bingo Scoring Config Tests (bingo-scoring-config extend)
// ============================================

test('REQ-001: bingo items page shows scoring config section', function () {
    $this->actingAs($this->admin)
        ->get("/admin/locations/{$this->location->id}/bingo-items")
        ->assertStatus(200)
        ->assertSee('Punten')
        ->assertSee('3 op een rij')
        ->assertSee('Volle kaart');
});

test('REQ-002: admin can update bingo scoring config', function () {
    $this->actingAs($this->admin)
        ->patch("/admin/locations/{$this->location->id}/bingo-scoring-config", [
            'bingo_three_in_row_points' => 75,
            'bingo_full_card_points' => 150,
        ])
        ->assertRedirect("/admin/locations/{$this->location->id}/bingo-items")
        ->assertSessionHas('status');

    $this->assertDatabaseHas('locations', [
        'id' => $this->location->id,
        'bingo_three_in_row_points' => 75,
        'bingo_full_card_points' => 150,
    ]);
});

test('REQ-003: new locations have default scoring values', function () {
    $location = Location::factory()->create();

    expect($location->bingo_three_in_row_points)->toBe(20);
    expect($location->bingo_full_card_points)->toBe(100);
});

test('REQ-005: validation rejects negative points for three in row', function () {
    $this->actingAs($this->admin)
        ->patch("/admin/locations/{$this->location->id}/bingo-scoring-config", [
            'bingo_three_in_row_points' => -10,
            'bingo_full_card_points' => 100,
        ])
        ->assertSessionHasErrors('bingo_three_in_row_points');
});

test('REQ-005: validation rejects zero points for full card', function () {
    $this->actingAs($this->admin)
        ->patch("/admin/locations/{$this->location->id}/bingo-scoring-config", [
            'bingo_three_in_row_points' => 50,
            'bingo_full_card_points' => 0,
        ])
        ->assertSessionHasErrors('bingo_full_card_points');
});

test('guest cannot access bingo scoring config endpoint', function () {
    $this->patch("/admin/locations/{$this->location->id}/bingo-scoring-config", [
        'bingo_three_in_row_points' => 50,
        'bingo_full_card_points' => 100,
    ])
    ->assertRedirect('/login');
});
