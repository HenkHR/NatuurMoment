<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;


//mag weg als locatie pagina er is
use App\Models\Location;

Route::get('/', function () {
    return view('welcome');
});


//aanpassen als locatie pagina er is
Route::get('/play', function () {
    $location = Location::find(1);
    return view('play', compact('location'));
})->name('play');

Route::get('/play/{locationId}', function ($locationId) {
    $location = Location::findOrFail($locationId);
    return view('play', compact('location'));
})->name('play.location');


Route::post('/play/{locationId}', [GameController::class, 'create'])->name('play.create');

Route::get('/join', [GameController::class, 'showJoin'])->name('player.join');


Route::get('/host/lobby/{game}', [GameController::class, 'hostLobby'])->name('host.lobby');
Route::get('/player/lobby/{game}', [GameController::class, 'playerLobby'])->name('player.lobby');


Route::get('/styleguide', function () {
    return view('styleguide');
})->name('styleguide');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
