<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\LocationBingoItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LocationBingoItem>
 */
class LocationBingoItemFactory extends Factory
{
    protected $model = LocationBingoItem::class;

    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'label' => fake()->randomElement(['Eekhoorn', 'Paddenstoel', 'Vogelnest', 'Konijn', 'Kever', 'Vlinder', 'Eikel', 'Blad']),
            'points' => fake()->numberBetween(1, 5),
            'icon' => fake()->optional()->emoji(),
        ];
    }
}
