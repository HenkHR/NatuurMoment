<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Http\Request;

class GameController extends Controller
{

    public function create(Request $request, $locationId)
    {

        //game word aangemaakt in de database
        $game = Game::create([
            'location_id' => $locationId,
            'pin' => Game::generatePin(),
            'status' => 'lobby',
            'host_token' => Game::generateHostToken(),
        ]);

        session(['hostToken_'.$game->id => $game->host_token]);

        return redirect()->route('host.lobby', $game->id);
    }


    //Join lobby als host als er al een spel is aangemaakt (als je verbinding weg was gevallen bijv.)
    public function hostLobby(Request $request, $gameId)
    {
        $hostToken = session('hostToken_'.$gameId);

        if (!$hostToken) {
            return redirect()->route('play')->with('error', 'Geen toegang tot het spel');
        }

        $game = Game::where('id', $gameId)
        ->where('host_token', $hostToken)
        ->firstOrFail();

        return view('host.lobby', ['gameId' => $gameId]);
    }

    //Join lobby als player 
    public function playerLobby(Request $request, $gameId)
    {

        $token = session('playerToken_'.$gameId);

        if (!$token) {
            return redirect()->route('player.join')->with('error', 'Geen toegang tot het spel');
        }

        $player = GamePlayer::where('token', $token)
        ->where('game_id', $gameId)
        ->firstOrFail();

        return view('player.lobby', [
            'gameId' => $gameId,
            'playerToken' => $token
        ]);
    }

    public function showJoin()
    {
        return view('join');
    }
}
