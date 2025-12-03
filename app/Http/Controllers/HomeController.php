<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search      = $request->get('search');
        $locationId  = $request->get('location');

        // Alle locaties voor de dropdown
        $locationOptions = Location::orderBy('name')->get();

        // Query voor de kaartjes (gefilterde lijst)
        $locationsQuery = Location::query();

        if ($locationId) {
            $locationsQuery->where('id', $locationId);
        }

        if ($search) {
            $locationsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $locations = $locationsQuery->orderBy('name')->get();

        $selectedLocation = $locationId
            ? $locationOptions->firstWhere('id', $locationId)
            : null;

        return view('home', [
            'locations'        => $locations,
            'locationOptions'  => $locationOptions,
            'search'           => $search,
            'selectedLocation' => $selectedLocation,
        ]);
    }
}
