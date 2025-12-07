<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Http\Request;
use App\Models\Location;

class GameController extends Controller
{

    public function create(Request $request, $locationId)
    {

        $location = Location::findOrFail($locationId);

        //game word aangemaakt in de database
        $game = Game::create([
            'location_id' => $location->id,
            'pin' => Game::generatePin(),
            'status' => 'lobby',
            'host_token' => Game::generateHostToken(),
        ]);

        $request->session()->put('hostToken_'.$game->id, $game->host_token);
        $request->session()->save();

        return redirect()->route('host.lobby', $game->id);
    }


    //Join lobby als host als er al een spel is aangemaakt (als je verbinding weg was gevallen bijv.)
    public function hostLobby(Request $request, $gameId)
    {
        $hostToken = session('hostToken_'.$gameId);

        if (!$hostToken) {
            return redirect()->route('home')->with('error', 'Geen toegang tot het spel');
        }

        $game = Game::where('id', $gameId)
        ->where('host_token', $hostToken)
        ->firstOrFail();

        // Redirect to game if already started
        if ($game->status === 'started') {
            return redirect()->route('host.game', $gameId);
        }

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

        $game = Game::findOrFail($gameId);

        if ($game->status === 'started') {
            return redirect()->route('player.game', $gameId);
        }

        return view('player.lobby', [
            'gameId' => $gameId,
            'playerToken' => $token
        ]);
    }

    public function showJoin()
    {
        return view('join');
    }


    // Host game page - voor de host dashboard tijdens de game. deze functie host dus niet de game
    public function hostGame(Request $request, $gameId)
    {
        $hostToken = session('hostToken_'.$gameId);

        if (!$hostToken) {
            return redirect()->route('home')->with('error', 'Geen toegang tot het spel');
        }

        $game = Game::where('id', $gameId)
            ->where('host_token', $hostToken)
            ->where('status', 'started')
            ->firstOrFail();

        return view('host.hostdashboard', ['gameId' => $gameId]);
    }


    public function playerGame(Request $request, $gameId)
    {
        $token = session('playerToken_'.$gameId);

        if (!$token) {
            return redirect()->route('player.join')->with('error', 'Geen toegang tot het spel');
        }

        $player = GamePlayer::where('token', $token)
            ->where('game_id', $gameId)
            ->firstOrFail();

        $game = Game::findOrFail($gameId);
        
        if ($game->status !== 'started') {
            return redirect()->route('player.lobby', $gameId)->with('error', 'Het spel is nog niet gestart');
        }

        return view('player.bingo', [
            'gameId' => $gameId,
            'playerToken' => $token,
        ]);
    }
}
