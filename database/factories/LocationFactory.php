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
        $faker = \Faker\Factory::create();
        
        return [
            'name' => $faker->unique()->city . ' ' . $faker->randomElement(['Bos', 'Park', 'Natuurgebied', 'Heide']),
            'description' => $faker->optional()->paragraph,
        ];
    }
}
