<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBingoItemRequest;
use App\Http\Requests\UpdateBingoItemRequest;
use App\Models\Location;
use App\Models\LocationBingoItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BingoItemController extends Controller
{
    public function index(Location $location): View
    {
        $bingoItems = $location->bingoItems()
            ->orderBy('label')
            ->paginate(15);

        return view('admin.bingo-items.index', compact('location', 'bingoItems'));
    }

    public function create(Location $location): View
    {
        return view('admin.bingo-items.create', compact('location'));
    }

    public function store(StoreBingoItemRequest $request, Location $location): RedirectResponse
    {
        $data = $request->safe()->only(['label', 'points']);

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('bingo-icons', 'public');
            if ($path === false) {
                return back()->withErrors(['icon' => 'Bestand kon niet worden opgeslagen.'])->withInput();
            }
            $data['icon'] = $path;
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
        $data = $request->safe()->only(['label', 'points']);

        if ($request->boolean('remove_icon')) {
            if ($bingoItem->icon && Storage::disk('public')->exists($bingoItem->icon)) {
                Storage::disk('public')->delete($bingoItem->icon);
            }
            $data['icon'] = null;
        } elseif ($request->hasFile('icon')) {
            if ($bingoItem->icon && Storage::disk('public')->exists($bingoItem->icon)) {
                Storage::disk('public')->delete($bingoItem->icon);
            }
            $path = $request->file('icon')->store('bingo-icons', 'public');
            if ($path === false) {
                return back()->withErrors(['icon' => 'Bestand kon niet worden opgeslagen.'])->withInput();
            }
            $data['icon'] = $path;
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

        if ($icon && Storage::disk('public')->exists($icon)) {
            Storage::disk('public')->delete($icon);
        }

        return redirect()
            ->route('admin.locations.bingo-items.index', $location)
            ->with('status', 'Bingo item verwijderd.');
    }
}
