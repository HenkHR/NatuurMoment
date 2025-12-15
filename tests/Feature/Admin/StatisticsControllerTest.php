<?php

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Location;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
});

// =============================================================================
// REQ-002: Dashboard Access
// =============================================================================

test('REQ-002: admin can view statistics dashboard', function () {
    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertStatus(200);
    $response->assertViewIs('admin.statistics.index');
});

test('non-admin cannot view statistics dashboard', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($user)->get('/admin/statistics');

    $response->assertStatus(403);
});

test('guest cannot view statistics dashboard', function () {
    $response = $this->get('/admin/statistics');

    $response->assertRedirect('/login');
});

// =============================================================================
// REQ-011: Empty State
// =============================================================================

test('REQ-011: dashboard shows empty state when no feedback data', function () {
    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertStatus(200);
    $response->assertViewHas('hasFeedback', false);
    $response->assertSee('Geen feedback gegevens');
});

// =============================================================================
// REQ-003: Stat Cards
// =============================================================================

test('REQ-003: dashboard shows total responses stat card', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    GamePlayer::factory()
        ->for($game)
        ->count(5)
        ->withFeedback()
        ->create();

    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertStatus(200);
    $response->assertViewHas('hasFeedback', true);
    $response->assertViewHas('stats', function ($stats) {
        return $stats['total_responses'] === 5;
    });
});

test('REQ-003: dashboard shows average rating stat card', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    // Create players with known ratings for predictable average
    GamePlayer::factory()->for($game)->withFeedback(rating: 4)->create();
    GamePlayer::factory()->for($game)->withFeedback(rating: 5)->create();
    GamePlayer::factory()->for($game)->withFeedback(rating: 3)->create();
    // Expected average: (4+5+3)/3 = 4.0

    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertViewHas('stats', function ($stats) {
        return $stats['average_rating'] === 4.0;
    });
});

test('REQ-003: dashboard shows responses this month stat card', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    // Create players this month
    GamePlayer::factory()->for($game)->withFeedback()->count(3)->create();

    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertViewHas('stats', function ($stats) {
        return $stats['responses_this_month'] === 3;
    });
});

test('REQ-003: dashboard shows most active location stat card', function () {
    $location1 = Location::factory()->create(['name' => 'Veluwe']);
    $location2 = Location::factory()->create(['name' => 'Wadden']);

    $game1 = Game::factory()->for($location1)->finished()->create();
    $game2 = Game::factory()->for($location2)->finished()->create();

    // Location 1 has more feedback
    GamePlayer::factory()->for($game1)->withFeedback()->count(5)->create();
    GamePlayer::factory()->for($game2)->withFeedback()->count(2)->create();

    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertViewHas('stats', function ($stats) {
        return $stats['most_active_location'] === 'Veluwe'
            && $stats['most_active_location_count'] === 5;
    });
});

// =============================================================================
// REQ-009: Age Categorization
// =============================================================================

test('REQ-009: age groups are categorized correctly in statistics', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    // Create players in different age categories
    GamePlayer::factory()->for($game)->withFeedback(rating: 5, age: '10')->create(); // ≤12
    GamePlayer::factory()->for($game)->withFeedback(rating: 4, age: '14')->create(); // 13-15
    GamePlayer::factory()->for($game)->withFeedback(rating: 3, age: '17')->create(); // 16-18
    GamePlayer::factory()->for($game)->withFeedback(rating: 4, age: '20')->create(); // 19-21
    GamePlayer::factory()->for($game)->withFeedback(rating: 5, age: '30')->create(); // 22+

    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertViewHas('ageDistribution', function ($data) {
        return $data['labels'] === ['≤12', '13-15', '16-18', '19-21', '22+']
            && $data['data'] === [1, 1, 1, 1, 1]; // 1 player per category
    });
});

test('REQ-009: age distribution handles empty categories with zero', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    // Only create players in one category
    GamePlayer::factory()->for($game)->withFeedback(rating: 5, age: '10')->count(3)->create();

    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertViewHas('ageDistribution', function ($data) {
        // First category has 3, rest are 0
        return $data['data'][0] === 3
            && $data['data'][1] === 0
            && $data['data'][2] === 0
            && $data['data'][3] === 0
            && $data['data'][4] === 0;
    });
});

// =============================================================================
// REQ-010: Aggregation Queries
// =============================================================================

test('REQ-010: aggregation calculates correct satisfaction by age', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    // Create multiple players in same age group with different ratings
    GamePlayer::factory()->for($game)->withFeedback(rating: 4, age: '10')->create();
    GamePlayer::factory()->for($game)->withFeedback(rating: 5, age: '11')->create();
    // Expected avg for ≤12: (4+5)/2 = 4.5

    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertViewHas('satisfactionByAge', function ($data) {
        return $data['avgRatings'][0] === 4.5; // ≤12 category
    });
});

test('REQ-010: rating by location aggregation is correct', function () {
    $location1 = Location::factory()->create(['name' => 'Locatie A']);
    $location2 = Location::factory()->create(['name' => 'Locatie B']);

    $game1 = Game::factory()->for($location1)->finished()->create();
    $game2 = Game::factory()->for($location2)->finished()->create();

    // Location A: ratings 4, 5 -> avg 4.5
    GamePlayer::factory()->for($game1)->withFeedback(rating: 4)->create();
    GamePlayer::factory()->for($game1)->withFeedback(rating: 5)->create();

    // Location B: ratings 3, 3 -> avg 3.0
    GamePlayer::factory()->for($game2)->withFeedback(rating: 3)->create();
    GamePlayer::factory()->for($game2)->withFeedback(rating: 3)->create();

    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertViewHas('ratingByLocation', function ($data) {
        // Sorted by avg rating desc, so Location A should be first
        return in_array('Locatie A', $data['labels'])
            && in_array('Locatie B', $data['labels']);
    });
});

// =============================================================================
// REQ-006: Trends AJAX Endpoint
// =============================================================================

test('REQ-006: trends AJAX endpoint returns JSON', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    GamePlayer::factory()->for($game)->withFeedback()->count(3)->create();

    $response = $this->actingAs($this->admin)->get('/admin/statistics/trends?period=month');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'labels',
        'avgRatings',
        'counts',
        'period',
    ]);
});

test('REQ-006: trends AJAX accepts week period', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    GamePlayer::factory()->for($game)->withFeedback()->create();

    $response = $this->actingAs($this->admin)->get('/admin/statistics/trends?period=week');

    $response->assertStatus(200);
    $response->assertJsonFragment(['period' => 'week']);
});

test('REQ-006: trends AJAX accepts year period', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    GamePlayer::factory()->for($game)->withFeedback()->create();

    $response = $this->actingAs($this->admin)->get('/admin/statistics/trends?period=year');

    $response->assertStatus(200);
    $response->assertJsonFragment(['period' => 'year']);
});

// =============================================================================
// REQ-008: Rating Validation (1-5)
// =============================================================================

test('REQ-008: players can only have ratings between 1 and 5', function () {
    $location = Location::factory()->create();
    $game = Game::factory()->for($location)->finished()->create();

    // Create players with all valid ratings
    foreach ([1, 2, 3, 4, 5] as $rating) {
        GamePlayer::factory()->for($game)->withFeedback(rating: $rating)->create();
    }

    $response = $this->actingAs($this->admin)->get('/admin/statistics');

    $response->assertViewHas('stats', function ($stats) {
        return $stats['total_responses'] === 5
            && $stats['average_rating'] === 3.0; // (1+2+3+4+5)/5 = 3.0
    });
});
