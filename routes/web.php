<?php

use App\Http\Controllers\Admin\BingoItemController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\RouteStopController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
    Route::resource('games', GameController::class)->only(['index', 'show', 'destroy']);
});

require __DIR__.'/auth.php';
