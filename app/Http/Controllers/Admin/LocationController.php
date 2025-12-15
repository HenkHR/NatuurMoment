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
        $hasRegioFilter = request()->filled('regio');
        $perPage = request('per_page', auth()->user()->admin_per_page ?? 15);

        $locations = Location::withCount(['bingoItems', 'routeStops', 'games'])
            ->when(request('search'), function ($q, $search) use ($hasRegioFilter) {
                // If regio dropdown is selected, only search by name
                // Otherwise search by both name and province
                if ($hasRegioFilter) {
                    $q->where('name', 'like', "%{$search}%");
                } else {
                    $q->where(fn($query) =>
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('province', 'like', "%{$search}%")
                    );
                }
            })
            ->when(request('regio'), fn($q, $regio) =>
                $q->where('province', $regio)
            )
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $provinces = config('provinces', []);
        $hasFilters = request()->hasAny(['search', 'regio']);

        return view('admin.locations.index', compact('locations', 'provinces', 'hasFilters', 'perPage'));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'description', 'province', 'distance']);

        // REQ-006: Default game modes to empty array (all OFF) for new locations
        $data['game_modes'] = $request->input('game_modes', []);

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
        // Load counts for game mode status indicators
        $location->loadCount(['bingoItems', 'routeStops']);

        return view('admin.locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'description', 'province', 'distance']);

        // Handle game_modes - if not provided, keep existing
        $data['game_modes'] = $request->input('game_modes', []);

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
