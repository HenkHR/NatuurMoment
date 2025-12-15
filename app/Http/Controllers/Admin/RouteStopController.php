<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\AdminPaginationTrait;
use App\Http\Controllers\Admin\Traits\HandlesFileUploads;
use App\Http\Requests\StoreRouteStopRequest;
use App\Http\Requests\UpdateRouteStopRequest;
use App\Models\Location;
use App\Models\LocationRouteStop;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RouteStopController extends Controller
{
    use AdminPaginationTrait;
    use HandlesFileUploads;

    public function index(Location $location): View
    {
        $perPage = $this->getPerPage();

        $routeStops = $location->routeStops()
            ->orderBy('sequence')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.route-stops.index', compact('location', 'routeStops', 'perPage'));
    }

    public function create(Location $location): View
    {
        $nextSequence = $location->routeStops()->max('sequence') + 1;

        return view('admin.route-stops.create', compact('location', 'nextSequence'));
    }

    public function store(StoreRouteStopRequest $request, Location $location): RedirectResponse
    {
        $data = $request->validated();

        $upload = $this->handleFileUpload($request, 'image', 'route-stops');
        if ($upload['error']) {
            return back()->withErrors(['image' => $upload['error']])->withInput();
        }
        if ($upload['path']) {
            $data['image_path'] = $upload['path'];
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

        $upload = $this->handleFileUpload($request, 'image', 'route-stops', $routeStop->image_path);
        if ($upload['error']) {
            return back()->withErrors(['image' => $upload['error']])->withInput();
        }
        if ($upload['path']) {
            $data['image_path'] = $upload['path'];
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

        $this->deleteStoredFile($routeStop->image_path);

        $routeStop->delete();

        return redirect()
            ->route('admin.locations.route-stops.index', $location)
            ->with('status', 'Vraag verwijderd.');
    }
}
