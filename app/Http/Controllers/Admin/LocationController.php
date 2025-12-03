<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::withCount(['bingoItems', 'routeStops', 'games'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        Location::create($request->validated());

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Locatie aangemaakt.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Locatie bijgewerkt.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->games()->exists()) {
            return back()->with('error', 'Kan locatie niet verwijderen: er zijn nog games gekoppeld. Verwijder eerst alle games.');
        }

        $location->delete();

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Locatie verwijderd.');
    }
}
