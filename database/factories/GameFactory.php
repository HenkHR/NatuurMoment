<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'pin' => strtoupper(Str::random(6)),
            'status' => \fake()->randomElement(['lobby', 'started', 'finished']),
            'host_token' => Str::random(64),
            'started_at' => \fake()->optional()->dateTimeThisMonth(),
            'finished_at' => \fake()->optional()->dateTimeThisMonth(),
        ];
    }

    public function lobby(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'lobby',
            'started_at' => null,
            'finished_at' => null,
        ]);
    }

    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'started',
            'started_at' => now(),
            'finished_at' => null,
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'finished',
            'started_at' => now()->subHour(),
            'finished_at' => now(),
        ]);
    }
}
