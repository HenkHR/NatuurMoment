<?php

namespace App\Livewire\Concerns;

use App\Models\Game;

/**
 * Trait for verifying host access in Livewire components
 *
 * Extracted to eliminate duplicate authorization code between HostGame and HostLobby
 */
trait VerifiesHostAccess
{
    /**
     * Verify that the current session is the game host
     *
     * Components using this trait must have:
     * - $gameId property (int)
     * - $game property (optional, will be loaded if null)
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function verifyHostAccess(): void
    {
        $game = $this->game ?? Game::findOrFail($this->gameId);

        if (session("hostToken_{$this->gameId}") !== $game->host_token) {
            abort(403, 'Unauthorized: Not the game host');
        }
    }
}
