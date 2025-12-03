<?php

namespace App\Http\Controllers;

use App\Models\Game;
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

        //redirect naar de host lobby
        return redirect()->route('host.lobby', $game->id);
    }


    //Join lobby als host als er al een spel is aangemaakt (als je verbinding weg was gevallen bijv.)
    public function hostLobby($gameId)
    {

        $game = Game::findOrFail($gameId);
        return view('host.lobby', ['gameId' => $gameId]);
    }

    //Join lobby als player 
    public function playerLobby(Request $request, $gameId)
    {

        $token = $request->query('token');
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
