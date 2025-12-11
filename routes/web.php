<?php

use App\Http\Controllers\Admin\BingoItemController;
use App\Http\Controllers\Admin\AdminGameController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\RouteStopController;
use App\Http\Controllers\GameInfoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/host/game/create/{locationId}', [GameController::class, 'showCreate'])->name('host.create');
Route::post('/play/{locationId}', [GameController::class, 'create'])->name('play.create');

Route::get('/join', [GameController::class, 'showJoin'])->name('player.join');


Route::get('/host/lobby/{game}', [GameController::class, 'hostLobby'])->name('host.lobby');
Route::get('/player/lobby/{game}', [GameController::class, 'playerLobby'])->name('player.lobby');

Route::get('/host/game/{game}', [GameController::class, 'hostGame'])->name('host.game');
Route::get('/host/finished-leaderboard/{game}', [GameController::class, 'hostFinishedLeaderboard'])->name('host.finished-leaderboard');
Route::get('/player/game/{game}', [GameController::class, 'playerGame'])->name('player.game');
Route::get('/player/route/{game}', [GameController::class, 'playerRoute'])->name('player.route');
Route::get('/player/leaderboard/{game}', [GameController::class, 'playerLeaderboard'])->name('player.leaderboard');
Route::get('/player/finished-leaderboard/{game}', [GameController::class, 'playerFinishedLeaderboard'])->name('player.finished-leaderboard');
Route::get('/player/feedback/{game}', [GameController::class, 'playerFeedback'])->name('player.feedback');


Route::get('/styleguide', function () {
    return view('styleguide');
})->name('styleguide');



Route::get('/bingo', function () {
    // Find the game ID from session
    $gameId = null;
    foreach (session()->all() as $key => $value) {
        if (str_starts_with($key, 'playerToken_')) {
            $gameId = str_replace('playerToken_', '', $key);
            break;
        }
    }
    
    if (!$gameId) {
        return redirect()->route('player.join')->with('error', 'Geen actief spel gevonden');
    }
    
    return redirect()->route('player.game', $gameId);
})->name('player.bingo');



Route::get('/speluitleg/{location?}', function ($location = null) {
    $locationId = $location ?: 1;
    return view('speluitleg', ['locationId' => $locationId]);
});

Route::get('/dashboard', function () {
    return redirect()->route('admin.locations.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('locations', LocationController::class)->except(['show']);
    Route::resource('locations.bingo-items', BingoItemController::class)->shallow()->except(['show']);
    Route::resource('locations.route-stops', RouteStopController::class)->shallow()->except(['show']);
    Route::resource('games', AdminGameController::class)->only(['index', 'show', 'destroy']);
});

//game info route

Route::get('/games/natuur-avontuur/{locationId?}', [GameInfoController::class, 'show'])
    ->name('games.info');

require __DIR__.'/auth.php';

