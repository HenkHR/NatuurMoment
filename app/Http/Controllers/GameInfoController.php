<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class GameInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($locationId = null)
    {
        if (!$locationId) {
            $locationId = request('location');
        }
        
        if (!$locationId) {
            abort(404, 'Location is required');
        }
        $locationModel = Location::findOrFail($locationId);
        $game = [
            'title' => 'Natuur Avontuur',
            'location' => $locationModel->name,
            'players_min' => 4,
            'players_max' => 12,
            'needs_materials' => false,
            'organisers' => 1,
        ];

        $rules = [
            'Volg de route door het natuurgebied',
            'Tijdens de route maak je foto’s om de bingokaart te vullen',
            'Ook krijg je tijdens de route quizvragen bij bezienswaardigheden',
            'Let jij het beste op tijdens de route en haal je het binnen de tijd?',
        ];

        return view('games.info', compact('game', 'rules', 'locationId'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
