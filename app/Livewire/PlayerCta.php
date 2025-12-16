<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use Livewire\Component;
use Livewire\Attributes\Locked;

class PlayerCta extends Component
{
    #[Locked]
    public int $gameId;

    #[Locked]
    public string $playerToken;

    public Game $game;

    public function mount($gameId, $playerToken)
    {
        $this->gameId = (int) $gameId;
        $this->playerToken = (string) $playerToken;

        GamePlayer::where('token', $this->playerToken)
            ->where('game_id', $this->gameId)
            ->firstOrFail();

        $this->game = Game::findOrFail($this->gameId);

        if ($this->game->status !== 'finished') {
            return redirect()->route('player.game', $this->gameId);
        }
    }

    public function render()
    {
        $player = GamePlayer::where('token', $this->playerToken)
            ->where('game_id', $this->gameId)
            ->first();

        $age = $player?->feedback_age;
        $showMembership = is_numeric($age) && (int) $age >= 16;

        return view('livewire.player-cta', [
            'cards' => config('cta.cards', []),
            'showMembership' => $showMembership,
        ]);
    }


}
