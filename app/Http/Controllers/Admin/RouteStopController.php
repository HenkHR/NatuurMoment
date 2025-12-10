<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRouteStopRequest;
use App\Http\Requests\UpdateRouteStopRequest;
use App\Models\Location;
use App\Models\LocationRouteStop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('route-stops', 'public');
        }

        unset($data['image']);
        $location->routeStops()->create($data);

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
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($routeStop->image_path) {
                Storage::disk('public')->delete($routeStop->image_path);
            }
            $data['image_path'] = $request->file('image')->store('route-stops', 'public');
        }

        unset($data['image']);
        $routeStop->update($data);

        return redirect()
            ->route('admin.locations.route-stops.index', $routeStop->location)
            ->with('status', 'Vraag bijgewerkt.');
    }

    public function destroy(LocationRouteStop $routeStop): RedirectResponse
    {
        $location = $routeStop->location;

        if ($routeStop->image_path) {
            Storage::disk('public')->delete($routeStop->image_path);
        }

        $routeStop->delete();

        return redirect()
            ->route('admin.locations.route-stops.index', $location)
            ->with('status', 'Vraag verwijderd.');
    }
}
