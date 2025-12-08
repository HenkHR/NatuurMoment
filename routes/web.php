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

Route::post('/play/{locationId}', [GameController::class, 'create'])->name('play.create');

Route::get('/join', [GameController::class, 'showJoin'])->name('player.join');


Route::get('/host/lobby/{game}', [GameController::class, 'hostLobby'])->name('host.lobby');
Route::get('/player/lobby/{game}', [GameController::class, 'playerLobby'])->name('player.lobby');

Route::get('/host/game/{game}', [GameController::class, 'hostGame'])->name('host.game');
Route::get('/player/game/{game}', [GameController::class, 'playerGame'])->name('player.game');


Route::get('/styleguide', function () {
    return view('styleguide');
})->name('styleguide');



Route::get('/bingo', function () {
    return view('player.bingo');
});



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


Route::get('/games/natuur-avontuur/{locationId}', [GameInfoController::class, 'show'])
    ->name('games.info');


require __DIR__.'/auth.php';

