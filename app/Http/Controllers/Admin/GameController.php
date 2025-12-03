<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(): View
    {
        $games = Game::with('location')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.games.index', compact('games'));
    }

    public function show(Game $game): View
    {
        $game->load('location');

        return view('admin.games.show', compact('game'));
    }

    public function destroy(Game $game): RedirectResponse
    {
        $game->delete();

        return redirect()
            ->route('admin.games.index')
            ->with('status', 'Game verwijderd.');
    }
}
