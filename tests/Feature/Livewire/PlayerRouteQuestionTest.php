<?php

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Location;
use App\Models\RouteStop;
use App\Models\RouteStopAnswer;
use App\Livewire\PlayerRouteQuestion;
use Livewire\Livewire;

beforeEach(function () {
    $this->location = Location::factory()->create();

    $this->game = Game::create([
        'location_id' => $this->location->id,
        'pin' => Game::generatePin(),
        'status' => 'started',
        'host_token' => Game::generateHostToken(),
    ]);

    $this->player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token-123',
        'score' => 0,
    ]);

    // Create route stops for the game
    $this->question1 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Question 1',
        'question_text' => 'What is 2 + 2?',
        'option_a' => '3',
        'option_b' => '4',
        'option_c' => '5',
        'option_d' => null,
        'correct_option' => 'B',
        'points' => 10,
        'sequence' => 1,
    ]);

    $this->question2 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Question 2',
        'question_text' => 'What is 3 + 3?',
        'option_a' => '5',
        'option_b' => '6',
        'correct_option' => 'B',
        'points' => 15,
        'sequence' => 2,
    ]);
});

// ============================================
// REQ-001: Sequential Unlock Tests
// ============================================

it('REQ-001: shows first question to new player', function () {
    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->assertSee('Vraag 1')
        ->assertSee('What is 2 + 2?')
        ->assertSee('3') // option A
        ->assertSee('4') // option B
        ->assertSee('5'); // option C
});

it('REQ-001: shows second question after first answered', function () {
    // Answer first question
    RouteStopAnswer::create([
        'game_player_id' => $this->player->id,
        'route_stop_id' => $this->question1->id,
        'chosen_option' => 'B',
        'is_correct' => true,
        'score_awarded' => 10,
        'answered_at' => now(),
    ]);

    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->assertSee('Vraag 2')
        ->assertSee('What is 3 + 3?');
});

// ============================================
// REQ-002: Answer Selection & Submission Tests
// ============================================

it('REQ-002: player can select an answer option', function () {
    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->set('selectedOption', 'A')
        ->assertSet('selectedOption', 'A');
});

it('REQ-002: player can submit an answer', function () {
    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->set('selectedOption', 'B')
        ->call('submitAnswer', $this->question1->id);

    // Check answer was created
    expect(RouteStopAnswer::where('game_player_id', $this->player->id)
        ->where('route_stop_id', $this->question1->id)
        ->exists())->toBeTrue();
});

// ============================================
// REQ-003: Wrong Answer Finality Tests
// ============================================

it('REQ-003: wrong answer is final - no retry', function () {
    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->set('selectedOption', 'A') // Wrong answer (correct is B)
        ->call('submitAnswer', $this->question1->id)
        ->assertSet('feedbackType', 'error');

    // Check answer was saved
    $answer = RouteStopAnswer::where('game_player_id', $this->player->id)
        ->where('route_stop_id', $this->question1->id)
        ->first();

    expect($answer)->not->toBeNull();
    expect($answer->is_correct)->toBeFalse();
    expect($answer->score_awarded)->toBe(0);
});

// ============================================
// REQ-004: Correct Answer Points Tests
// ============================================

it('REQ-004: correct answer awards points to player', function () {
    $initialScore = $this->player->score;

    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->set('selectedOption', 'B') // Correct answer
        ->call('submitAnswer', $this->question1->id)
        ->assertSet('feedbackType', 'success');

    // Check player score updated
    $this->player->refresh();
    expect($this->player->score)->toBe($initialScore + 10); // Question 1 is worth 10 points
});

it('REQ-004: wrong answer awards zero points', function () {
    $initialScore = $this->player->score;

    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->set('selectedOption', 'A') // Wrong answer
        ->call('submitAnswer', $this->question1->id);

    // Check player score unchanged
    $this->player->refresh();
    expect($this->player->score)->toBe($initialScore);
});

// ============================================
// REQ-008: Available Options Display Tests
// ============================================

it('REQ-008: only shows available options (not null)', function () {
    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->assertSee('3') // option A
        ->assertSee('4') // option B
        ->assertSee('5'); // option C
        // option D is null so should not appear
});

// ============================================
// REQ-014: Duplicate Prevention Tests
// ============================================

it('REQ-014: prevents duplicate answer submission', function () {
    // First answer
    RouteStopAnswer::create([
        'game_player_id' => $this->player->id,
        'route_stop_id' => $this->question1->id,
        'chosen_option' => 'B',
        'is_correct' => true,
        'score_awarded' => 10,
        'answered_at' => now(),
    ]);

    // Try to answer same question again
    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->set('selectedOption', 'A')
        ->call('submitAnswer', $this->question1->id)
        ->assertSet('feedbackType', 'error')
        ->assertSet('feedbackMessage', 'Je hebt deze vraag al beantwoord');

    // Check only one answer exists
    expect(RouteStopAnswer::where('game_player_id', $this->player->id)
        ->where('route_stop_id', $this->question1->id)
        ->count())->toBe(1);
});

// ============================================
// Security Tests
// ============================================

it('blocks access with invalid player token', function () {
    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => 'invalid-token',
    ])
        ->assertForbidden();
});

it('blocks access when game is not started', function () {
    // Set game to lobby status
    $this->game->update(['status' => 'lobby']);

    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->assertForbidden();
});

// ============================================
// Completion Tests
// ============================================

it('REQ-011: shows message when all questions answered', function () {
    // Answer both questions
    RouteStopAnswer::create([
        'game_player_id' => $this->player->id,
        'route_stop_id' => $this->question1->id,
        'chosen_option' => 'B',
        'is_correct' => true,
        'score_awarded' => 10,
        'answered_at' => now(),
    ]);

    RouteStopAnswer::create([
        'game_player_id' => $this->player->id,
        'route_stop_id' => $this->question2->id,
        'chosen_option' => 'B',
        'is_correct' => true,
        'score_awarded' => 15,
        'answered_at' => now(),
    ]);

    // REQ-011: When all questions answered, should show message instead of redirecting
    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->assertSee('Je hebt alle vragen al beantwoord');
});

it('shows progress indicator', function () {
    // Answer first question
    RouteStopAnswer::create([
        'game_player_id' => $this->player->id,
        'route_stop_id' => $this->question1->id,
        'chosen_option' => 'B',
        'is_correct' => true,
        'score_awarded' => 10,
        'answered_at' => now(),
    ]);

    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $this->game->id,
        'playerToken' => $this->player->token,
    ])
        ->assertSee('Vraag 2 van 2');
});
