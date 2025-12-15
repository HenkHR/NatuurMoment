<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\RouteStop;
use App\Models\RouteStopAnswer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PlayerRouteQuestion extends Component
{
    // ============================================
    // CONFIG SECTION
    // ============================================

    #[Locked]
    public int $gameId;

    #[Locked]
    public $playerToken;

    public ?string $selectedOption = null;
    public ?string $feedbackMessage = null;
    public ?string $feedbackType = null; // 'success' or 'error'
    public ?string $answeredOption = null; // Track which option was answered for inline feedback
    public ?int $answeredQuestionId = null; // Track which question the feedback is for

    // Cached player ID to avoid repeated lookups
    private ?int $cachedPlayerId = null;

    // ============================================
    // LIFECYCLE SECTION
    // ============================================

    public function mount(int $gameId, $playerToken): void
    {
        $this->gameId = $gameId;
        $this->playerToken = $playerToken;
        $this->validatePlayerAccess($gameId, $playerToken);
    }

    // ============================================
    // ACTIONS SECTION
    // ============================================

    /**
     * Submit an answer for a route stop question
     * REQ-002: Antwoord selecteren en submitten
     * REQ-003: Fout antwoord definitief
     * REQ-004: Goed antwoord kent punten toe
     * REQ-014: Duplicate prevention
     */
    public function submitAnswer(int $routeStopId): void
    {
        $this->validatePlayerAccess($this->gameId, $this->playerToken);
        $playerId = $this->getPlayerId();

        // Validation
        if (empty($this->selectedOption)) {
            $this->feedbackMessage = 'Selecteer een antwoord';
            $this->feedbackType = 'error';
            return;
        }

        $routeStop = RouteStop::where('id', $routeStopId)
            ->where('game_id', $this->gameId)
            ->firstOrFail();

        // Security: Validate chosen_option against available options
        $availableOptions = array_keys($routeStop->getAvailableOptions());
        if (!in_array($this->selectedOption, $availableOptions, true)) {
            $this->feedbackMessage = 'Ongeldig antwoord geselecteerd';
            $this->feedbackType = 'error';
            return;
        }

        // REQ-014: Check if already answered (duplicate prevention)
        if ($routeStop->isAnsweredBy($playerId)) {
            $this->feedbackMessage = 'Je hebt deze vraag al beantwoord';
            $this->feedbackType = 'error';
            return;
        }

        // REQ-001: Check sequential unlock (previous questions answered)
        if (!$routeStop->isUnlockedFor($playerId)) {
            $this->feedbackMessage = 'Beantwoord eerst de vorige vragen';
            $this->feedbackType = 'error';
            return;
        }

        // Calculate correctness and score
        $isCorrect = $this->selectedOption === $routeStop->correct_option;
        $scoreAwarded = $isCorrect ? $routeStop->points : 0;

        // Data integrity: use transaction for atomic answer + score update
        try {
            DB::transaction(function () use ($playerId, $routeStopId, $isCorrect, $scoreAwarded) {
                // Create answer with explicit setting of computed fields (not mass assigned for security)
                $answer = new RouteStopAnswer([
                    'game_player_id' => $playerId,
                    'route_stop_id' => $routeStopId,
                    'chosen_option' => $this->selectedOption,
                    'answered_at' => now(),
                ]);
                // Set computed fields explicitly (protected from mass assignment)
                $answer->is_correct = $isCorrect;
                $answer->score_awarded = $scoreAwarded;
                $answer->save();

                // REQ-004: Update player score
                $player = GamePlayer::findOrFail($playerId);
                $player->increment('score', $scoreAwarded);
            });

            // Set feedback (outside transaction - UI state only)
            $this->feedbackType = $isCorrect ? 'success' : 'error';
            $this->answeredOption = $this->selectedOption; // Remember which option for inline styling
            $this->answeredQuestionId = $routeStopId; // Remember which question this feedback is for

            // Reset selection for next question
            $this->selectedOption = null;

            // Dispatch event for Alpine.js to handle auto-advance
            $this->dispatch('answer-submitted', isCorrect: $isCorrect);

        } catch (QueryException $e) {
            // Handle duplicate entry (DB unique constraint)
            if (str_contains($e->getMessage(), '1062') || str_contains($e->getMessage(), '23000')) {
                $this->feedbackMessage = 'Je hebt deze vraag al beantwoord';
                $this->feedbackType = 'error';
            } else {
                // Log unexpected database errors for debugging
                Log::error('Unexpected database error in submitAnswer', [
                    'game_id' => $this->gameId,
                    'player_id' => $playerId,
                    'route_stop_id' => $routeStopId,
                    'error' => $e->getMessage(),
                ]);
                $this->feedbackMessage = 'Er ging iets mis. Probeer opnieuw.';
                $this->feedbackType = 'error';
            }
        }
    }

    /**
     * Clear feedback and refresh component (called after 2s delay)
     * REQ-007: Auto volgende vraag
     * Note: REQ-011 redirect is handled in render() after feedback clears
     */
    public function clearFeedback(): void
    {
        $this->feedbackMessage = null;
        $this->feedbackType = null;
        $this->answeredOption = null;
        $this->answeredQuestionId = null;
        // Component will re-render, and render() will handle redirect if all questions done
    }

    // ============================================
    // SECURITY SECTION
    // ============================================

    /**
     * Validate that the player token is valid and belongs to the game
     * Caches the player ID to avoid repeated lookups
     */
    private function validatePlayerAccess(int $gameId, $playerToken): void
    {
        // Use cached player ID if available
        if ($this->cachedPlayerId !== null) {
            return;
        }

        $player = GamePlayer::where('token', $playerToken)
            ->where('game_id', $gameId)
            ->first();

        if (!$player) {
            abort(403, 'Ongeldige toegang');
        }

        // Cache the player ID
        $this->cachedPlayerId = $player->id;

        // Verify game is active
        $game = Game::findOrFail($gameId);
        if ($game->status !== 'started') {
            abort(403, 'Het spel is niet actief');
        }
    }

    /**
     * Get the cached player ID or fetch it if not cached
     */
    private function getPlayerId(): int
    {
        if ($this->cachedPlayerId === null) {
            $this->validatePlayerAccess($this->gameId, $this->playerToken);
        }

        return $this->cachedPlayerId;
    }

    // ============================================
    // RENDER SECTION
    // ============================================

    public function render()
    {
        $this->validatePlayerAccess($this->gameId, $this->playerToken);
        $playerId = $this->getPlayerId();

        // Get all questions with player's answers eager loaded (prevents N+1)
        $allQuestions = RouteStop::where('game_id', $this->gameId)
            ->with(['answers' => fn($q) => $q->where('game_player_id', $playerId)])
            ->orderBy('sequence')
            ->get();

        // Count answered using eager loaded relation (no extra queries)
        $answeredCount = $allQuestions->filter(fn($q) => $q->answers->isNotEmpty())->count();

        // If showing feedback, show the answered question instead of next
        if ($this->answeredQuestionId !== null) {
            $currentQuestion = $allQuestions->firstWhere('id', $this->answeredQuestionId);
            // Adjust count: show progress before this answer was recorded
            $answeredCount = max(0, $answeredCount - 1);
        } else {
            // REQ-001: Get current unlocked question (first unanswered in sequence)
            $currentQuestion = $allQuestions->first(fn($q) => $q->answers->isEmpty());
        }

        $totalQuestions = $allQuestions->count();

        // REQ-011: Redirect when all questions answered and no feedback showing
        if ($currentQuestion === null && $this->answeredQuestionId === null && $totalQuestions > 0) {
            $player = GamePlayer::findOrFail($playerId);
            // REQ-012: Check if also bingo is complete → go to leaderboard
            if ($player->hasCompletedBingo()) {
                $this->redirectRoute('player.leaderboard', ['game' => $this->gameId]);
            } else {
                $this->redirectRoute('player.game', ['game' => $this->gameId]);
            }
        }

        return view('livewire.player-route-question', [
            'currentQuestion' => $currentQuestion,
            'answeredCount' => $answeredCount,
            'totalQuestions' => $totalQuestions,
            'allQuestions' => $allQuestions,
            'playerId' => $playerId,
        ]);
    }
}
