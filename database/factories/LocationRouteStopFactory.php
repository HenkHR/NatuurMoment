<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\LocationRouteStop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LocationRouteStop>
 */
class LocationRouteStopFactory extends Factory
{
    protected $model = LocationRouteStop::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        
        return [
            'location_id' => Location::factory(),
            'name' => $faker->words(3, true),
            'question_text' => $faker->sentence . '?',
            'option_a' => $faker->word,
            'option_b' => $faker->word,
            'option_c' => $faker->optional()->word,
            'option_d' => $faker->optional()->word,
            'correct_option' => $faker->randomElement(['A', 'B', 'C', 'D']),
            'points' => $faker->numberBetween(1, 10),
            'sequence' => $faker->numberBetween(0, 20),
        ];
    }
}
