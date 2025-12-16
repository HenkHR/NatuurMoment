<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\AdminPaginationTrait;
use App\Http\Controllers\Admin\Traits\HandlesFileUploads;
use App\Http\Requests\StoreBingoItemRequest;
use App\Http\Requests\UpdateBingoItemRequest;
use App\Models\Location;
use App\Models\LocationBingoItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BingoItemController extends Controller
{
    use AdminPaginationTrait;
    use HandlesFileUploads;

    public function index(Location $location): View
    {
        $perPage = $this->getPerPage();

        $bingoItems = $location->bingoItems()
            ->orderBy('label')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.bingo-items.index', compact('location', 'bingoItems', 'perPage'));
    }

    public function create(Location $location): View
    {
        return view('admin.bingo-items.create', compact('location'));
    }

    public function store(StoreBingoItemRequest $request, Location $location): RedirectResponse
    {
        $data = $request->safe()->only(['label', 'points', 'fact']);

        $upload = $this->handleFileUpload($request, 'icon', 'bingo-icons');
        if ($upload['error']) {
            return back()->withErrors(['icon' => $upload['error']])->withInput();
        }
        if ($upload['path']) {
            $data['icon'] = $upload['path'];
        }

        $location->bingoItems()->create($data);

        return redirect()
            ->route('admin.locations.bingo-items.index', $location)
            ->with('status', 'Bingo item aangemaakt.');
    }

    public function edit(LocationBingoItem $bingoItem): View
    {
        $bingoItem->load('location');

        return view('admin.bingo-items.edit', compact('bingoItem'));
    }

    public function update(UpdateBingoItemRequest $request, LocationBingoItem $bingoItem): RedirectResponse
    {
        $data = $request->safe()->only(['label', 'points', 'fact']);

        if ($this->handleFileRemoval($request, 'remove_icon', $bingoItem->icon)) {
            $data['icon'] = null;
        } else {
            $upload = $this->handleFileUpload($request, 'icon', 'bingo-icons', $bingoItem->icon);
            if ($upload['error']) {
                return back()->withErrors(['icon' => $upload['error']])->withInput();
            }
            if ($upload['path']) {
                $data['icon'] = $upload['path'];
            }
        }

        $bingoItem->update($data);

        return redirect()
            ->route('admin.locations.bingo-items.index', $bingoItem->location)
            ->with('status', 'Bingo item bijgewerkt.');
    }

    public function destroy(LocationBingoItem $bingoItem): RedirectResponse
    {
        $location = $bingoItem->location;
        $icon = $bingoItem->icon;

        DB::transaction(function () use ($bingoItem) {
            $bingoItem->delete();
        });

        $this->deleteStoredFile($icon);

        return redirect()
            ->route('admin.locations.bingo-items.index', $location)
            ->with('status', 'Bingo item verwijderd.');
    }

    public function updateScoringConfig(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'bingo_three_in_row_points' => ['required', 'integer', 'min:1'],
            'bingo_full_card_points' => ['required', 'integer', 'min:1'],
        ], [
            'bingo_three_in_row_points.required' => 'Punten voor 3-op-een-rij is verplicht.',
            'bingo_three_in_row_points.integer' => 'Punten moet een geheel getal zijn.',
            'bingo_three_in_row_points.min' => 'Punten moet minimaal 1 zijn.',
            'bingo_full_card_points.required' => 'Punten voor volle kaart is verplicht.',
            'bingo_full_card_points.integer' => 'Punten moet een geheel getal zijn.',
            'bingo_full_card_points.min' => 'Punten moet minimaal 1 zijn.',
        ]);

        $location->update($validated);

        return redirect()
            ->route('admin.locations.bingo-items.index', $location)
            ->with('status', 'Bingo punten configuratie opgeslagen.');
    }
}
