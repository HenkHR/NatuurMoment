<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['label' => 'Home', 'url' => url('/')],
        ];

        $search = $request->get('search');
        $locationId = $request->get('location');

        // Hardcoded NL provincies
        $allProvinces = collect(config('provinces'));

        // Alleen provincies die in de database bestaan
        $dbProvinces = Location::select('province')
            ->pluck('province')
            ->map(fn($p) => trim((string)$p))
            ->filter()
            ->unique();

        $locationOptions = $allProvinces
            ->intersect($dbProvinces)
            ->sort()
            ->values()
            ->toArray();

        $locationsQuery = Location::query();

        $selectedLocation = null;
        $selectedProvince = null;

        if ($locationId !== null && $locationId !== '') {
            if (is_numeric($locationId)) {
                $selectedLocation = Location::find($locationId);
                if ($selectedLocation) {
                    $selectedProvince = $selectedLocation->province;
                    $locationsQuery->where('id', $locationId);
                } else {
                    $locationsQuery->whereRaw('0 = 1');
                }
            } else {
                $selectedProvince = trim((string)$locationId);
                $locationsQuery->where('province', $selectedProvince);
                $selectedLocation = Location::where('province', $selectedProvince)->first();
            }
        }

        if ($search) {
            $locationsQuery->where(fn($q) =>
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }

        $locations = $locationsQuery
            ->orderBy('name')
            ->paginate(6)
            ->withQueryString();

// AJAX request: return the partial including cards + pagination
        if ($request->ajax()) {
            return view('partials.location-cards', ['locations' => $locations])->render();
        }

        // Normale pagina load
        return view('home', [
            'locations' => $locations,
            'locationOptions' => $locationOptions,
            'search' => $search,
            'selectedLocation' => $selectedLocation,
            'selectedProvince' => $selectedProvince,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
