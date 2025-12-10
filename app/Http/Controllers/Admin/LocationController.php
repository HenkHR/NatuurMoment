<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $data = $request->safe()->only(['name', 'description', 'province', 'distance']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('location-images', 'public');
            if ($path === false) {
                return back()->withErrors(['image' => 'Bestand kon niet worden opgeslagen.'])->withInput();
            }
            $data['image_path'] = $path;
        }

        Location::create($data);

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
        $data = $request->safe()->only(['name', 'description', 'province', 'distance']);

        if ($request->boolean('remove_image')) {
            if ($location->image_path && Storage::disk('public')->exists($location->image_path)) {
                Storage::disk('public')->delete($location->image_path);
            }
            $data['image_path'] = null;
        } elseif ($request->hasFile('image')) {
            if ($location->image_path && Storage::disk('public')->exists($location->image_path)) {
                Storage::disk('public')->delete($location->image_path);
            }
            $path = $request->file('image')->store('location-images', 'public');
            if ($path === false) {
                return back()->withErrors(['image' => 'Bestand kon niet worden opgeslagen.'])->withInput();
            }
            $data['image_path'] = $path;
        }

        $location->update($data);

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Locatie bijgewerkt.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->games()->exists()) {
            return back()->with('error', 'Kan locatie niet verwijderen: er zijn nog games gekoppeld. Verwijder eerst alle games.');
        }

        DB::transaction(function () use ($location) {
            $location->delete();
        });

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Locatie verwijderd.');
    }
}
