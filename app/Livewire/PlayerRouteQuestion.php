<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\RouteStop;
use App\Models\RouteStopAnswer;
use Illuminate\Database\QueryException;
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

        // Data integrity: handle duplicates gracefully via DB constraint
        try {
            RouteStopAnswer::create([
                'game_player_id' => $playerId,
                'route_stop_id' => $routeStopId,
                'chosen_option' => $this->selectedOption,
                'is_correct' => $isCorrect,
                'score_awarded' => $scoreAwarded,
                'answered_at' => now(),
            ]);

            // REQ-004: Update player score
            $player = GamePlayer::findOrFail($playerId);
            $player->increment('score', $scoreAwarded);

            // Set feedback
            $this->feedbackMessage = $isCorrect
                ? "Correct! +{$scoreAwarded} punten"
                : "Helaas, dat is niet het goede antwoord";
            $this->feedbackType = $isCorrect ? 'success' : 'error';

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
                throw $e;
            }
        }
    }

    /**
     * Clear feedback and refresh component (called after 2s delay)
     * REQ-007: Auto volgende vraag
     */
    public function clearFeedback(): void
    {
        $this->feedbackMessage = null;
        $this->feedbackType = null;
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

        // Get all questions for this game
        $allQuestions = RouteStop::where('game_id', $this->gameId)
            ->orderBy('sequence')
            ->get();

        // REQ-001: Get current unlocked question (sequential unlock)
        $currentQuestion = RouteStop::getNextUnlocked($this->gameId, $playerId);

        // Count answered questions
        $answeredCount = $allQuestions->filter(fn($q) => $q->isAnsweredBy($playerId))->count();
        $totalQuestions = $allQuestions->count();

        return view('livewire.player-route-question', [
            'currentQuestion' => $currentQuestion,
            'answeredCount' => $answeredCount,
            'totalQuestions' => $totalQuestions,
            'allQuestions' => $allQuestions,
            'playerId' => $playerId,
        ]);
    }
}
