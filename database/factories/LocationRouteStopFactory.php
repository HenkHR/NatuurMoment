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
        return [
            'location_id' => Location::factory(),
            'name' => fake()->words(3, true),
            'question_text' => fake()->sentence() . '?',
            'option_a' => fake()->word(),
            'option_b' => fake()->word(),
            'option_c' => fake()->optional()->word(),
            'option_d' => fake()->optional()->word(),
            'correct_option' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'points' => fake()->numberBetween(1, 10),
            'sequence' => fake()->numberBetween(0, 20),
        ];
    }
}
