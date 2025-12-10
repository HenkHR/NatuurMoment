<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Location::firstOrCreate(
            ['name' => 'TestLocatie'],
            [
                'description' => 'Historische buitenplaats omringd door natuur, ideaal voor een natuuravontuur.',
                'image_path' => null,
                'province' => 'Gelderland',
                'distance' => 60.0,
            ]
        );
    }
}
