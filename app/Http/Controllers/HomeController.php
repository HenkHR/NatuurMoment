<?php
namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Location;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $search   = $request->get('search');
        $location = $request->get('location');   // location_id

        $locations = Location::orderBy('name')->get();

        $gamesQuery = Game::with('location');

        if ($location) {
            $gamesQuery->where('location_id', $location);
        }

        if ($search) {
            $gamesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('location', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $games = $gamesQuery->orderBy('name')->paginate(12)->withQueryString();

        $selectedLocation = $location
            ? $locations->firstWhere('id', $location)
            : null;

        return view('home', compact('games', 'location', 'selectedLocation'), [
            'locations'        => $locations,
            'games'            => $games,
            'search'           => $search,
            'selectedLocation' => $selectedLocation,
        ]);
    }
}

