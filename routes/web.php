<?php

use App\Http\Controllers\Admin\BingoItemController;
use App\Http\Controllers\Admin\AdminGameController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\RouteStopController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;


//mag weg als locatie pagina er is
use App\Models\Location;

Route::get('/', [HomeController::class, 'index'])->name('home');


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

require __DIR__.'/auth.php';
