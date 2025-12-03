<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRouteStopRequest;
use App\Http\Requests\UpdateRouteStopRequest;
use App\Models\Location;
use App\Models\LocationRouteStop;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RouteStopController extends Controller
{
    public function index(Location $location): View
    {
        $routeStops = $location->routeStops()
            ->orderBy('sequence')
            ->paginate(15);

        return view('admin.route-stops.index', compact('location', 'routeStops'));
    }

    public function create(Location $location): View
    {
        $nextSequence = $location->routeStops()->max('sequence') + 1;

        return view('admin.route-stops.create', compact('location', 'nextSequence'));
    }

    public function store(StoreRouteStopRequest $request, Location $location): RedirectResponse
    {
        $location->routeStops()->create($request->validated());

        return redirect()
            ->route('admin.locations.route-stops.index', $location)
            ->with('status', 'Vraag aangemaakt.');
    }

    public function edit(LocationRouteStop $routeStop): View
    {
        $routeStop->load('location');

        return view('admin.route-stops.edit', compact('routeStop'));
    }

    public function update(UpdateRouteStopRequest $request, LocationRouteStop $routeStop): RedirectResponse
    {
        $routeStop->update($request->validated());

        return redirect()
            ->route('admin.locations.route-stops.index', $routeStop->location)
            ->with('status', 'Vraag bijgewerkt.');
    }

    public function destroy(LocationRouteStop $routeStop): RedirectResponse
    {
        $location = $routeStop->location;
        $routeStop->delete();

        return redirect()
            ->route('admin.locations.route-stops.index', $location)
            ->with('status', 'Vraag verwijderd.');
    }
}
