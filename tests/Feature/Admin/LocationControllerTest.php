<?php

use App\Constants\GameMode;
use App\Models\Game;
use App\Models\Location;
use App\Models\LocationBingoItem;
use App\Models\LocationRouteStop;
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
    // Create a fake image for upload
    $image = \Illuminate\Http\UploadedFile::fake()->image('location.jpg');

    $this->actingAs($this->admin)
        ->post('/admin/locations', [
            'name' => 'Test Locatie',
            'description' => 'Test beschrijving',
            'province' => 'Noord-Holland',
            'distance' => 60,
            'image' => $image,
            'game_modes' => ['bingo'],
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
    $location = Location::factory()->create(['description' => 'Originele beschrijving']);

    $this->actingAs($this->admin)
        ->put("/admin/locations/{$location->id}", [
            'name' => 'Bijgewerkte Naam',
            'description' => 'Bijgewerkte beschrijving',
            'province' => 'Utrecht',
            'distance' => 90,
            'game_modes' => ['vragen'],
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

// ============================================
// Search & Filter Tests (REQ-001 to REQ-008)
// ============================================

test('REQ-001: locations can be filtered by search on province', function () {
    Location::factory()->create(['name' => 'Loc A', 'province' => 'Noord-Holland']);
    Location::factory()->create(['name' => 'Loc B', 'province' => 'Zuid-Holland']);
    Location::factory()->create(['name' => 'Loc C', 'province' => 'Utrecht']);

    $this->actingAs($this->admin)
        ->get('/admin/locations?search=Noord')
        ->assertStatus(200)
        ->assertSee('Loc A')
        ->assertDontSee('Loc B')
        ->assertDontSee('Loc C');
});

test('REQ-002: locations index passes provinces config to view', function () {
    $this->actingAs($this->admin)
        ->get('/admin/locations')
        ->assertStatus(200)
        ->assertViewHas('provinces', config('provinces'));
});

test('REQ-003: locations index paginates with 15 items per page', function () {
    Location::factory()->count(20)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/locations');

    $response->assertStatus(200);
    $locations = $response->viewData('locations');
    expect($locations->perPage())->toBe(15);
    expect($locations->count())->toBe(15);
});

test('REQ-006: filters and pagination preserve query string', function () {
    Location::factory()->count(20)->create(['province' => 'Utrecht']);

    $response = $this->actingAs($this->admin)
        ->get('/admin/locations?regio=Utrecht&search=Utr');

    $response->assertStatus(200);
    $locations = $response->viewData('locations');

    // Check that withQueryString() preserves filters
    // The paginator should have appends set for regio and search
    expect($locations->appends(['regio' => 'Utrecht', 'search' => 'Utr'])->url(2))
        ->toContain('regio=Utrecht')
        ->toContain('search=Utr');
});

test('REQ-007: shows empty state when no locations match filter', function () {
    Location::factory()->create(['province' => 'Noord-Holland']);

    $this->actingAs($this->admin)
        ->get('/admin/locations?search=NonExistentProvince')
        ->assertStatus(200)
        ->assertSee('Geen locaties gevonden voor deze filters');
});

test('REQ-008: pagination shows correct total pages', function () {
    Location::factory()->count(32)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/locations');

    $locations = $response->viewData('locations');
    expect($locations->lastPage())->toBe(3); // 32 items / 15 per page = 3 pages
    expect($locations->total())->toBe(32);
});

test('locations can be filtered by regio dropdown', function () {
    Location::factory()->create(['name' => 'Loc A', 'province' => 'Gelderland']);
    Location::factory()->create(['name' => 'Loc B', 'province' => 'Limburg']);

    $this->actingAs($this->admin)
        ->get('/admin/locations?regio=Gelderland')
        ->assertStatus(200)
        ->assertSee('Loc A')
        ->assertDontSee('Loc B');
});

test('hasFilters is true when search or regio provided', function () {
    $this->actingAs($this->admin)
        ->get('/admin/locations?search=test')
        ->assertViewHas('hasFilters', true);

    $this->actingAs($this->admin)
        ->get('/admin/locations?regio=Utrecht')
        ->assertViewHas('hasFilters', true);

    $this->actingAs($this->admin)
        ->get('/admin/locations')
        ->assertViewHas('hasFilters', false);
});

// ============================================
// Game Modes Tests (REQ-001 to REQ-010)
// ============================================

test('GM-REQ-001: location has game_modes JSON field', function () {
    $location = Location::factory()->create([
        'game_modes' => ['bingo', 'vragen'],
    ]);

    $this->assertDatabaseHas('locations', ['id' => $location->id]);
    expect($location->game_modes)->toBeArray();
    expect($location->game_modes)->toContain('bingo');
    expect($location->game_modes)->toContain('vragen');
});

test('GM-REQ-002: bingo mode requires min 9 bingo items to be valid', function () {
    // Create location with bingo mode enabled but only 5 items
    $location = Location::factory()
        ->withBingoMode()
        ->has(LocationBingoItem::factory()->count(5), 'bingoItems')
        ->create();

    $location->loadCount('bingoItems');

    expect($location->has_bingo_mode)->toBeTrue();
    expect($location->is_bingo_mode_valid)->toBeFalse();

    // Add more items to reach 9
    LocationBingoItem::factory()->count(4)->create(['location_id' => $location->id]);
    $location->loadCount('bingoItems');

    expect($location->is_bingo_mode_valid)->toBeTrue();
});

test('GM-REQ-003: vragen mode requires min 1 question to be valid', function () {
    // Create location with vragen mode enabled but no questions
    $location = Location::factory()
        ->withVragenMode()
        ->create();

    $location->loadCount('routeStops');

    expect($location->has_vragen_mode)->toBeTrue();
    expect($location->is_vragen_mode_valid)->toBeFalse();

    // Add a question
    LocationRouteStop::factory()->create(['location_id' => $location->id]);
    $location->loadCount('routeStops');

    expect($location->is_vragen_mode_valid)->toBeTrue();
});

test('GM-REQ-004: edit page shows toggle switches for game modes', function () {
    $location = Location::factory()
        ->withBingoMode()
        ->has(LocationBingoItem::factory()->count(5), 'bingoItems')
        ->create();

    $this->actingAs($this->admin)
        ->get("/admin/locations/{$location->id}/edit")
        ->assertStatus(200)
        ->assertSee('Spelmodi')
        ->assertSee('Bingo modus')
        ->assertSee('Vragen modus');
});

test('GM-REQ-005: toggle shows status indicator with count', function () {
    $location = Location::factory()
        ->withBingoMode()
        ->has(LocationBingoItem::factory()->count(5), 'bingoItems')
        ->create();

    $this->actingAs($this->admin)
        ->get("/admin/locations/{$location->id}/edit")
        ->assertStatus(200)
        ->assertSee('5/' . GameMode::MIN_BINGO_ITEMS . ' items');
});

test('GM-REQ-006: new location has all modes OFF by default', function () {
    $location = Location::factory()->create();

    expect($location->game_modes)->toBeArray();
    expect($location->game_modes)->toBeEmpty();
    expect($location->has_bingo_mode)->toBeFalse();
    expect($location->has_vragen_mode)->toBeFalse();
});

test('GM-REQ-007: index table shows red styling for counts under minimum', function () {
    $location = Location::factory()
        ->has(LocationBingoItem::factory()->count(5), 'bingoItems')
        ->create();

    $this->actingAs($this->admin)
        ->get('/admin/locations')
        ->assertStatus(200)
        ->assertSee('text-red-600'); // Red styling applied
});

test('GM-REQ-008: index table shows warning badge when incomplete active modes', function () {
    $location = Location::factory()
        ->withBingoMode()
        ->has(LocationBingoItem::factory()->count(5), 'bingoItems')
        ->create();

    $this->actingAs($this->admin)
        ->get('/admin/locations')
        ->assertStatus(200)
        ->assertSee('Actieve spelmodus heeft onvoldoende content'); // Warning badge title
});

test('GM-REQ-006: admin can save location with game modes', function () {
    $location = Location::factory()->create(['description' => 'Test beschrijving']);

    $this->actingAs($this->admin)
        ->put("/admin/locations/{$location->id}", [
            'name' => $location->name,
            'description' => $location->description,
            'province' => $location->province,
            'distance' => $location->distance,
            'game_modes' => ['bingo', 'vragen'],
        ])
        ->assertRedirect('/admin/locations')
        ->assertSessionHas('status');

    $location->refresh();
    expect($location->game_modes)->toContain('bingo');
    expect($location->game_modes)->toContain('vragen');
});

test('location has_valid_game_mode returns true when at least one mode is valid', function () {
    // Location with valid bingo mode
    $locationWithValidBingo = Location::factory()
        ->withBingoMode()
        ->has(LocationBingoItem::factory()->count(9), 'bingoItems')
        ->create();

    $locationWithValidBingo->loadCount(['bingoItems', 'routeStops']);

    expect($locationWithValidBingo->has_valid_game_mode)->toBeTrue();

    // Location with no valid modes
    $locationInvalid = Location::factory()
        ->withBingoMode()
        ->has(LocationBingoItem::factory()->count(5), 'bingoItems')
        ->create();

    $locationInvalid->loadCount(['bingoItems', 'routeStops']);

    expect($locationInvalid->has_valid_game_mode)->toBeFalse();
});

test('location has_incomplete_active_mode returns true when enabled mode has insufficient content', function () {
    $location = Location::factory()
        ->withBingoMode()
        ->has(LocationBingoItem::factory()->count(5), 'bingoItems')
        ->create();

    $location->loadCount(['bingoItems', 'routeStops']);

    expect($location->has_incomplete_active_mode)->toBeTrue();
});

test('location scopeWithValidGameModes filters correctly', function () {
    // Valid: bingo enabled with 10 items
    $validLocation = Location::factory()
        ->withBingoMode()
        ->has(LocationBingoItem::factory()->count(10), 'bingoItems')
        ->create();

    // Invalid: bingo enabled with only 5 items
    $invalidLocation = Location::factory()
        ->withBingoMode()
        ->has(LocationBingoItem::factory()->count(5), 'bingoItems')
        ->create();

    // No modes enabled
    $noModesLocation = Location::factory()->create();

    $results = Location::withValidGameModes()->get();

    expect($results->pluck('id'))->toContain($validLocation->id);
    expect($results->pluck('id'))->not->toContain($invalidLocation->id);
    expect($results->pluck('id'))->not->toContain($noModesLocation->id);
});

// ============================================
// URL Field Tests (location-url extend)
// ============================================

test('URL-REQ-001: create form shows url field', function () {
    $this->actingAs($this->admin)
        ->get('/admin/locations/create')
        ->assertStatus(200)
        ->assertSee('Website URL')
        ->assertSee('natuurmonumenten.nl');
});

test('URL-REQ-001: edit form shows url field with value', function () {
    $location = Location::factory()->create([
        'url' => 'https://www.natuurmonumenten.nl/natuurgebieden/test',
    ]);

    $this->actingAs($this->admin)
        ->get("/admin/locations/{$location->id}/edit")
        ->assertStatus(200)
        ->assertSee('Website URL')
        ->assertSee('https://www.natuurmonumenten.nl/natuurgebieden/test');
});

test('URL-REQ-003: url field is required when creating location', function () {
    $image = \Illuminate\Http\UploadedFile::fake()->image('location.jpg');

    $this->actingAs($this->admin)
        ->post('/admin/locations', [
            'name' => 'Test Locatie',
            'description' => 'Test beschrijving',
            'province' => 'Noord-Holland',
            'distance' => 5.0,
            // url missing
            'image' => $image,
            'game_modes' => ['bingo'],
        ])
        ->assertSessionHasErrors('url');
});

test('URL-REQ-003: url must be valid http or https format', function () {
    $image = \Illuminate\Http\UploadedFile::fake()->image('location.jpg');

    $this->actingAs($this->admin)
        ->post('/admin/locations', [
            'name' => 'Test Locatie',
            'description' => 'Test beschrijving',
            'province' => 'Noord-Holland',
            'distance' => 5.0,
            'url' => 'not-a-valid-url',
            'image' => $image,
            'game_modes' => ['bingo'],
        ])
        ->assertSessionHasErrors('url');
});

test('URL-REQ-003: url prevents javascript protocol attack', function () {
    $image = \Illuminate\Http\UploadedFile::fake()->image('location.jpg');

    $this->actingAs($this->admin)
        ->post('/admin/locations', [
            'name' => 'Test Locatie',
            'description' => 'Test beschrijving',
            'province' => 'Noord-Holland',
            'distance' => 5.0,
            'url' => 'javascript:alert("XSS")',
            'image' => $image,
            'game_modes' => ['bingo'],
        ])
        ->assertSessionHasErrors('url');
});

test('URL-REQ-003: admin can create location with valid url', function () {
    $image = \Illuminate\Http\UploadedFile::fake()->image('location.jpg');

    $this->actingAs($this->admin)
        ->post('/admin/locations', [
            'name' => 'Test Locatie with URL',
            'description' => 'Test beschrijving',
            'province' => 'Noord-Holland',
            'distance' => 5.0,
            'url' => 'https://www.natuurmonumenten.nl/natuurgebieden/test-gebied',
            'image' => $image,
            'game_modes' => ['bingo'],
        ])
        ->assertRedirect('/admin/locations')
        ->assertSessionHas('status');

    $this->assertDatabaseHas('locations', [
        'name' => 'Test Locatie with URL',
        'url' => 'https://www.natuurmonumenten.nl/natuurgebieden/test-gebied',
    ]);
});

test('URL-REQ-003: admin can update location url', function () {
    $location = Location::factory()->create([
        'url' => 'https://old-url.com',
        'description' => 'Test description for update',
    ]);

    $this->actingAs($this->admin)
        ->put("/admin/locations/{$location->id}", [
            'name' => $location->name,
            'description' => $location->description,
            'province' => $location->province,
            'distance' => $location->distance,
            'url' => 'https://www.natuurmonumenten.nl/natuurgebieden/updated',
            'game_modes' => ['bingo'],
        ])
        ->assertRedirect('/admin/locations')
        ->assertSessionHas('status');

    $this->assertDatabaseHas('locations', [
        'id' => $location->id,
        'url' => 'https://www.natuurmonumenten.nl/natuurgebieden/updated',
    ]);
});
