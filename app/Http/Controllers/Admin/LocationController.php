<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\AdminPaginationTrait;
use App\Http\Controllers\Admin\Traits\HandlesFileUploads;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LocationController extends Controller
{
    use AdminPaginationTrait;
    use HandlesFileUploads;

    public function index(): View
    {
        $hasRegioFilter = request()->filled('regio');
        $perPage = $this->getPerPage();

        $locations = Location::withCount(['bingoItems', 'routeStops', 'games'])
            ->when(request('search'), function ($q, $search) use ($hasRegioFilter) {
                // Escape LIKE wildcards to prevent SQL injection
                $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);

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

        $upload = $this->handleFileUpload($request, 'image', 'location-images');
        if ($upload['error']) {
            return back()->withErrors(['image' => $upload['error']])->withInput();
        }
        if ($upload['path']) {
            $data['image_path'] = $upload['path'];
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

        if ($this->handleFileRemoval($request, 'remove_image', $location->image_path)) {
            $data['image_path'] = null;
        } else {
            $upload = $this->handleFileUpload($request, 'image', 'location-images', $location->image_path);
            if ($upload['error']) {
                return back()->withErrors(['image' => $upload['error']])->withInput();
            }
            if ($upload['path']) {
                $data['image_path'] = $upload['path'];
            }
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
