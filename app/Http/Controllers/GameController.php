<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Http\Request;
use App\Models\Location;

class GameController extends Controller
{

    public function showCreate($locationId)
    {
        $location = Location::findOrFail($locationId);
        return view('host.create', ['locationId' => $locationId]);
    }

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

        session(['hostToken_'.$game->id => $game->host_token]);

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

        // Redirect if game is finished
        if ($game->status === 'finished') {
            return redirect()->route('host.finished-leaderboard', $gameId)->with('error', 'Het spel is al beëindigd');
        }

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

        // Redirect if game is finished
        if ($game->status === 'finished') {
            return redirect()->route('player.finished-leaderboard', $gameId)->with('error', 'Het spel is al beëindigd');
        }

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
            ->firstOrFail();

        // Redirect to lobby if game is not started
        if ($game->status === 'lobby') {
            return redirect()->route('host.lobby', $gameId)->with('error', 'Het spel is nog niet gestart');
        }

        // Redirect to finished leaderboard if game is finished
        if ($game->status === 'finished') {
            return redirect()->route('host.finished-leaderboard', $gameId)->with('error', 'Het spel is al beëindigd');
        }

        // Allow access if game is started
        return view('host.hostdashboard', ['gameId' => $gameId]);
    }

    public function hostFinishedLeaderboard(Request $request, $gameId)
    {
        $hostToken = session('hostToken_'.$gameId);

        if (!$hostToken) {
            return redirect()->route('home')->with('error', 'Geen toegang tot het spel');
        }

        $game = Game::where('id', $gameId)
            ->where('host_token', $hostToken)
            ->firstOrFail();
        
        // If game is not finished, redirect to game
        if ($game->status !== 'finished') {
            return redirect()->route('host.game', $gameId);
        }

        return view('host.finished-leaderboard', [
            'gameId' => $gameId,
        ]);
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
        
        // Redirect if game is finished
        if ($game->status === 'finished') {
            return redirect()->route('player.finished-leaderboard', $gameId)->with('error', 'Het spel is al beëindigd');
        }
        
        if ($game->status !== 'started') {
            return redirect()->route('player.lobby', $gameId)->with('error', 'Het spel is nog niet gestart');
        }

        return view('player.bingo', [
            'gameId' => $gameId,
            'playerToken' => $token,
            'game' => $game,
        ]);
    }

    public function playerRoute(Request $request, $gameId)
    {
        $token = session('playerToken_'.$gameId);

        if (!$token) {
            return redirect()->route('player.join')->with('error', 'Geen toegang tot het spel');
        }

        $player = GamePlayer::where('token', $token)
            ->where('game_id', $gameId)
            ->firstOrFail();

        $game = Game::with('location.routeStops')->findOrFail($gameId);
        
        // Redirect if game is finished
        if ($game->status === 'finished') {
            return redirect()->route('player.finished-leaderboard', $gameId)->with('error', 'Het spel is al beëindigd');
        }
        
        if ($game->status !== 'started') {
            return redirect()->route('player.lobby', $gameId)->with('error', 'Het spel is nog niet gestart');
        }

        $routeStops = $game->location->routeStops;

        return view('player.route', [
            'gameId' => $gameId,
            'playerToken' => $token,
            'game' => $game,
            'routeStops' => $routeStops,
        ]);
    }

    public function playerLeaderboard(Request $request, $gameId)
    {
        $token = session('playerToken_'.$gameId);

        if (!$token) {
            return redirect()->route('player.join')->with('error', 'Geen toegang tot het spel');
        }

        $player = GamePlayer::where('token', $token)
            ->where('game_id', $gameId)
            ->firstOrFail();

        $game = Game::findOrFail($gameId);
        
        // Redirect if game is finished
        if ($game->status === 'finished') {
            return redirect()->route('player.finished-leaderboard', $gameId)->with('error', 'Het spel is al beëindigd');
        }
        
        if ($game->status !== 'started') {
            return redirect()->route('player.lobby', $gameId)->with('error', 'Het spel is nog niet gestart');
        }

        return view('player.leaderboard', [
            'gameId' => $gameId,
            'playerToken' => $token,
        ]);
    }

    public function playerFinishedLeaderboard(Request $request, $gameId)
    {
        $token = session('playerToken_'.$gameId);

        if (!$token) {
            return redirect()->route('player.join')->with('error', 'Geen toegang tot het spel');
        }

        $player = GamePlayer::where('token', $token)
            ->where('game_id', $gameId)
            ->firstOrFail();

        $game = Game::findOrFail($gameId);
        
        // If game is not finished, redirect to game
        if ($game->status !== 'finished') {
            return redirect()->route('player.game', $gameId);
        }

        return view('player.finished-leaderboard', [
            'gameId' => $gameId,
            'playerToken' => $token,
        ]);
    }

    public function playerFeedback(Request $request, $gameId)
    {
        $token = session('playerToken_'.$gameId);

        if (!$token) {
            return redirect()->route('player.join')->with('error', 'Geen toegang tot het spel');
        }

        $player = GamePlayer::where('token', $token)
            ->where('game_id', $gameId)
            ->firstOrFail();

        $game = Game::findOrFail($gameId);
        
        // If game is not finished, redirect to game
        if ($game->status !== 'finished') {
            return redirect()->route('player.game', $gameId);
        }

        return view('player.feedback', [
            'gameId' => $gameId,
            'playerToken' => $token,
        ]);
    }
}
