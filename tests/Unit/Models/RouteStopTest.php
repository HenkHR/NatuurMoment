<?php

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Location;
use App\Models\RouteStop;
use App\Models\RouteStopAnswer;

beforeEach(function () {
    $this->location = Location::factory()->create();
    $this->game = Game::create([
        'location_id' => $this->location->id,
        'pin' => Game::generatePin(),
        'status' => 'lobby',
        'host_token' => Game::generateHostToken(),
    ]);
});

// Test getAvailableOptions with 2 options
it('getAvailableOptions returns only A and B when C and D are null', function () {
    $routeStop = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Test',
        'question_text' => 'Question?',
        'option_a' => 'Answer A',
        'option_b' => 'Answer B',
        'option_c' => null,
        'option_d' => null,
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    $options = $routeStop->getAvailableOptions();

    expect($options)->toHaveCount(2);
    expect($options)->toBe([
        'A' => 'Answer A',
        'B' => 'Answer B',
    ]);
});

// Test getAvailableOptions with 3 options
it('getAvailableOptions returns A, B, C when D is null', function () {
    $routeStop = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Test',
        'question_text' => 'Question?',
        'option_a' => 'Answer A',
        'option_b' => 'Answer B',
        'option_c' => 'Answer C',
        'option_d' => null,
        'correct_option' => 'C',
        'points' => 5,
        'sequence' => 1,
    ]);

    $options = $routeStop->getAvailableOptions();

    expect($options)->toHaveCount(3);
    expect($options)->toBe([
        'A' => 'Answer A',
        'B' => 'Answer B',
        'C' => 'Answer C',
    ]);
});

// Test getAvailableOptions with all 4 options
it('getAvailableOptions returns all 4 options when all are set', function () {
    $routeStop = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Test',
        'question_text' => 'Question?',
        'option_a' => 'Answer A',
        'option_b' => 'Answer B',
        'option_c' => 'Answer C',
        'option_d' => 'Answer D',
        'correct_option' => 'D',
        'points' => 5,
        'sequence' => 1,
    ]);

    $options = $routeStop->getAvailableOptions();

    expect($options)->toHaveCount(4);
    expect($options)->toBe([
        'A' => 'Answer A',
        'B' => 'Answer B',
        'C' => 'Answer C',
        'D' => 'Answer D',
    ]);
});

// Test isAnsweredBy returns false when no answer exists
it('isAnsweredBy returns false when player has not answered', function () {
    $routeStop = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Test',
        'question_text' => 'Question?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    expect($routeStop->isAnsweredBy($player->id))->toBeFalse();
});

// Test isAnsweredBy returns true when player has answered
it('isAnsweredBy returns true when player has answered', function () {
    $routeStop = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Test',
        'question_text' => 'Question?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    // Create an answer
    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $routeStop->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 5,
        'answered_at' => now(),
    ]);

    expect($routeStop->isAnsweredBy($player->id))->toBeTrue();
});

// Test isAnsweredBy returns false for different player
it('isAnsweredBy returns false when different player has answered', function () {
    $routeStop = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Test',
        'question_text' => 'Question?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    $player1 = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Player 1',
        'token' => 'token-1',
        'score' => 0,
    ]);

    $player2 = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Player 2',
        'token' => 'token-2',
        'score' => 0,
    ]);

    // Player 1 answers
    RouteStopAnswer::create([
        'game_player_id' => $player1->id,
        'route_stop_id' => $routeStop->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 5,
        'answered_at' => now(),
    ]);

    // Player 2 has not answered
    expect($routeStop->isAnsweredBy($player2->id))->toBeFalse();
});

// Test Game::routeStops relationship
it('game has routeStops relationship', function () {
    RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Question 1',
        'question_text' => 'Q1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Question 2',
        'question_text' => 'Q2?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'B',
        'points' => 10,
        'sequence' => 2,
    ]);

    $this->game->refresh();

    expect($this->game->routeStops)->toHaveCount(2);
    expect($this->game->routeStops->first()->name)->toBe('Question 1');
});

// Test GamePlayer::routeStopAnswers relationship
it('game player has routeStopAnswers relationship', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    $routeStop = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Test',
        'question_text' => 'Q?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $routeStop->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 5,
        'answered_at' => now(),
    ]);

    $player->refresh();

    expect($player->routeStopAnswers)->toHaveCount(1);
    expect($player->routeStopAnswers->first()->chosen_option)->toBe('A');
});

// ============================================
// Part 02: Sequential Unlock Tests
// ============================================

// REQ-001: Test getNextUnlocked returns first question for new player
it('REQ-001: getNextUnlocked returns first question for new player', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q1',
        'question_text' => 'Question 1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q2',
        'question_text' => 'Question 2?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'B',
        'points' => 10,
        'sequence' => 2,
    ]);

    $unlocked = RouteStop::getNextUnlocked($this->game->id, $player->id);

    expect($unlocked)->not->toBeNull();
    expect($unlocked->sequence)->toBe(1);
    expect($unlocked->name)->toBe('Q1');
});

// REQ-001: Test getNextUnlocked returns second question after first answered
it('REQ-001: getNextUnlocked returns second question after first answered', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    $q1 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q1',
        'question_text' => 'Question 1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    $q2 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q2',
        'question_text' => 'Question 2?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'B',
        'points' => 10,
        'sequence' => 2,
    ]);

    // Answer first question
    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $q1->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 5,
        'answered_at' => now(),
    ]);

    $unlocked = RouteStop::getNextUnlocked($this->game->id, $player->id);

    expect($unlocked)->not->toBeNull();
    expect($unlocked->sequence)->toBe(2);
    expect($unlocked->name)->toBe('Q2');
});

// REQ-001: Test getNextUnlocked returns null when all answered
it('REQ-001: getNextUnlocked returns null when all questions answered', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    $q1 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q1',
        'question_text' => 'Question 1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    // Answer the only question
    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $q1->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 5,
        'answered_at' => now(),
    ]);

    $unlocked = RouteStop::getNextUnlocked($this->game->id, $player->id);

    expect($unlocked)->toBeNull();
});

// REQ-001: Test isUnlockedFor returns true for first question
it('REQ-001: isUnlockedFor returns true for first question', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    $q1 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q1',
        'question_text' => 'Question 1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    expect($q1->isUnlockedFor($player->id))->toBeTrue();
});

// REQ-001: Test isUnlockedFor returns false for second question when first not answered
it('REQ-001: isUnlockedFor returns false for second question when first not answered', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q1',
        'question_text' => 'Question 1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    $q2 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q2',
        'question_text' => 'Question 2?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'B',
        'points' => 10,
        'sequence' => 2,
    ]);

    expect($q2->isUnlockedFor($player->id))->toBeFalse();
});

// REQ-001: Test isUnlockedFor returns true for second question when first answered
it('REQ-001: isUnlockedFor returns true for second question when first answered', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    $q1 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q1',
        'question_text' => 'Question 1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    $q2 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q2',
        'question_text' => 'Question 2?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'B',
        'points' => 10,
        'sequence' => 2,
    ]);

    // Answer first question
    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $q1->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 5,
        'answered_at' => now(),
    ]);

    expect($q2->isUnlockedFor($player->id))->toBeTrue();
});

// ============================================
// Part 03: GamePlayer Completion Tests
// ============================================

use App\Models\Photo;
use App\Models\BingoItem;

// REQ-011: Test hasCompletedQuestions returns true when all answered
it('REQ-011: hasCompletedQuestions returns true when all questions answered', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    $q1 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q1',
        'question_text' => 'Question 1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    $q2 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q2',
        'question_text' => 'Question 2?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'B',
        'points' => 10,
        'sequence' => 2,
    ]);

    // Answer both questions
    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $q1->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 5,
        'answered_at' => now(),
    ]);

    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $q2->id,
        'chosen_option' => 'B',
        'is_correct' => true,
        'score_awarded' => 10,
        'answered_at' => now(),
    ]);

    expect($player->hasCompletedQuestions())->toBeTrue();
});

// REQ-011: Test hasCompletedQuestions returns false when not all answered
it('REQ-011: hasCompletedQuestions returns false when not all answered', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    $q1 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q1',
        'question_text' => 'Question 1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q2',
        'question_text' => 'Question 2?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'B',
        'points' => 10,
        'sequence' => 2,
    ]);

    // Only answer first question
    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $q1->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 5,
        'answered_at' => now(),
    ]);

    expect($player->hasCompletedQuestions())->toBeFalse();
});

// REQ-011: Test hasCompletedQuestions returns false when no questions exist
it('REQ-011: hasCompletedQuestions returns false when no questions exist', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    // No questions created
    expect($player->hasCompletedQuestions())->toBeFalse();
});

// REQ-012: Test hasCompletedBingo returns true with 9 approved photos
it('REQ-012: hasCompletedBingo returns true with 9 approved photos', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    // Create 9 approved photos
    for ($i = 0; $i < 9; $i++) {
        $bingoItem = BingoItem::create([
            'game_id' => $this->game->id,
            'label' => "Item $i",
            'position' => $i,
            'points' => 1,
        ]);

        Photo::create([
            'game_id' => $this->game->id,
            'game_player_id' => $player->id,
            'bingo_item_id' => $bingoItem->id,
            'path' => "photos/test-$i.jpg",
            'status' => 'approved',
            'taken_at' => now(),
        ]);
    }

    expect($player->hasCompletedBingo())->toBeTrue();
});

// REQ-012: Test hasCompletedBingo returns false with less than 9 photos
it('REQ-012: hasCompletedBingo returns false with less than 9 approved photos', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    // Create only 5 approved photos
    for ($i = 0; $i < 5; $i++) {
        $bingoItem = BingoItem::create([
            'game_id' => $this->game->id,
            'label' => "Item $i",
            'position' => $i,
            'points' => 1,
        ]);

        Photo::create([
            'game_id' => $this->game->id,
            'game_player_id' => $player->id,
            'bingo_item_id' => $bingoItem->id,
            'path' => "photos/test-$i.jpg",
            'status' => 'approved',
            'taken_at' => now(),
        ]);
    }

    expect($player->hasCompletedBingo())->toBeFalse();
});

// REQ-012: Test hasCompletedAll returns true only when both complete
it('REQ-012: hasCompletedAll returns true when both bingo and questions complete', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    // Create and answer 1 question
    $q1 = RouteStop::create([
        'game_id' => $this->game->id,
        'name' => 'Q1',
        'question_text' => 'Question 1?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_option' => 'A',
        'points' => 5,
        'sequence' => 1,
    ]);

    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $q1->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 5,
        'answered_at' => now(),
    ]);

    // Create 9 approved photos
    for ($i = 0; $i < 9; $i++) {
        $bingoItem = BingoItem::create([
            'game_id' => $this->game->id,
            'label' => "Item $i",
            'position' => $i,
            'points' => 1,
        ]);

        Photo::create([
            'game_id' => $this->game->id,
            'game_player_id' => $player->id,
            'bingo_item_id' => $bingoItem->id,
            'path' => "photos/test-$i.jpg",
            'status' => 'approved',
            'taken_at' => now(),
        ]);
    }

    expect($player->hasCompletedAll())->toBeTrue();
});

// REQ-012: Test hasCompletedAll returns true if no questions but bingo complete
it('REQ-012: hasCompletedAll returns true if no questions but bingo complete', function () {
    $player = GamePlayer::create([
        'game_id' => $this->game->id,
        'name' => 'Test Player',
        'token' => 'test-token',
        'score' => 0,
    ]);

    // No questions created

    // Create 9 approved photos
    for ($i = 0; $i < 9; $i++) {
        $bingoItem = BingoItem::create([
            'game_id' => $this->game->id,
            'label' => "Item $i",
            'position' => $i,
            'points' => 1,
        ]);

        Photo::create([
            'game_id' => $this->game->id,
            'game_player_id' => $player->id,
            'bingo_item_id' => $bingoItem->id,
            'path' => "photos/test-$i.jpg",
            'status' => 'approved',
            'taken_at' => now(),
        ]);
    }

    expect($player->hasCompletedAll())->toBeTrue();
});
