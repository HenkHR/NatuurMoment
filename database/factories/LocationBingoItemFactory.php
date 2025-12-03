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
        $faker = \Faker\Factory::create();
        
        return [
            'location_id' => Location::factory(),
            'label' => $faker->words(3, true),
            'points' => $faker->numberBetween(1, 10),
            'icon' => $faker->optional()->word(),
        ];
    }
}
