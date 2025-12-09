<?php

namespace App\Livewire\Concerns;

use App\Models\GamePlayer;

/**
 * Trait for loading leaderboard data in Livewire components
 *
 * Extracted to eliminate duplicate code between HostGame and PlayerPhotoCapture
 */
trait LoadsLeaderboard
{
    /**
     * Load leaderboard data sorted by score
     *
     * @param int $gameId The game ID to load leaderboard for
     * @return array Array of player data with rank, id, name, and score
     */
    protected function loadLeaderboardData(int $gameId): array
    {
        return GamePlayer::where('game_id', $gameId)
            ->orderByDesc('score')
            ->orderBy('name')
            ->get()
            ->map(fn($player, $index) => [
                'rank' => $index + 1,
                'id' => $player->id,
                'name' => $player->name,
                'score' => $player->score ?? 0,
            ])
            ->toArray();
    }
}
