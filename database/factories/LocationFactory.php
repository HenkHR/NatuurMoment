<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city() . ' ' . fake()->randomElement(['Bos', 'Park', 'Natuurgebied', 'Heide']),
            'description' => fake()->optional()->paragraph(),
            'province' => fake()->randomElement(['Noord-Holland', 'Zuid-Holland', 'Utrecht', 'Gelderland', 'Noord-Brabant', 'Limburg', 'Overijssel', 'Flevoland', 'Drenthe', 'Friesland', 'Groningen', 'Zeeland']),
            'distance' => fake()->numberBetween(30, 180),
        ];
    }
}
