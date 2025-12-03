<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use Livewire\Component;

class JoinGame extends Component
{
    public $pin = '';
    public $name = '';

    protected $rules = [
        'pin' => 'required|size:6',
        'name' => 'required|min:2|max:20',
    ];

    public function join()
    {
        //validatie van de input in de form
        $this->validate();

        //kijkt of het spel bestaat en of het in de lobby is of al gestart is. Dit is een boolean
        $game = Game::where('pin', $this->pin)
            ->where('status', 'lobby')
            ->first();

        //als het spel niet bestaat of al gestart is, word er een foutmelding gegeven. wordt in blade template getoond met @error('pin') <span>{{ $message }}</span> @enderror
        if (!$game) {
            $this->addError('pin', 'Spel niet gevonden of al gestart');
            return;
        }

        //player word aangemaakt in de database
        $player = GamePlayer::create([
            'game_id' => $game->id,
            'name' => $this->name,
            'token' => GamePlayer::generateToken(),
            'score' => 0,
        ]);

        //redirect naar de player lobby function in GameController.php
        return redirect()->route('player.lobby', [
            'game' => $game->id,
            'token' => $player->token
        ]);
    }

    public function render()
    {
        return view('livewire.join-game');
    }
}