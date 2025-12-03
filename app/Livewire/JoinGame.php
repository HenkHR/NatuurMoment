<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use Livewire\Component;

class JoinGame extends Component
{
    public $pin = '';
    public $name = '';
    public $step = 1;
    public $gameId = null;

    protected $rules = [
        'pin' => 'required|size:6',
        'name' => 'required|min:2|max:20',
    ];


    //stap 1 van de join functie
    public function checkPin()
    {
        //validatie van de PIN input in de form
        $this->validate(['pin' => 'required|size:6']);

        //kijkt of het spel bestaat en of het in de lobby is of al gestart is. Dit is een boolean
        $game = Game::where('pin', $this->pin)
            ->where('status', 'lobby')
            ->first();

        //als het spel niet bestaat of al gestart is, word er een foutmelding gegeven. wordt in blade template getoond met @error('pin') <span>{{ $message }}</span> @enderror
        if (!$game) {
            $this->addError('pin', 'Spel niet gevonden of al gestart');
            return;
        }

        $this->gameId = $game->id;

        $existingToken = session('playerToken_'.$game->id);

        //als de player al bestaat (rejoin)
        if ($existingToken) {
            $existingPlayer = GamePlayer::where('token', $existingToken)
            ->where('game_id', $game->id)
            ->first();

            if ($existingPlayer) {
                return redirect()->route('player.lobby', $game->id);
            }
        }

        $this->step = 2;
        
    }

    //stap 2 van de join functie
    public function join()
    {
        $this->validate(['name' => 'required|min:2|max:20']);

        $game = Game::findOrFail($this->gameId);
        
        //check of de naam al gebruikt
        $duplicateName = GamePlayer::where('game_id', $game->id)
        ->where('name', $this->name)
        ->exists();

        if ($duplicateName) {
            $this->addError('name', 'Deze naam is al in gebruik, kies een andere naam');
            return;
        }

        $player = GamePlayer::create([
            'game_id' => $game->id,
            'name' => $this->name,
            'token' => GamePlayer::generateToken(),
            'score' => 0,
        ]);

        session(['playerToken_'.$game->id => $player->token]);

        return redirect()->route('player.lobby', $game->id);
    }

    //terug naar stap 1
    public function backToPin()
    {
        $this->step = 1;
        $this->reset(['gameId', 'name']);
    }

    public function render()
    {
        return view('livewire.join-game');
    }
}