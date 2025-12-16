<?php

namespace Database\Factories;

use App\Constants\GameMode;
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
            'game_modes' => [], // REQ-006: Default all modes OFF
            'bingo_three_in_row_points' => 50, // Default scoring
            'bingo_full_card_points' => 100, // Default scoring
        ];
    }

    /**
     * State: Bingo mode enabled
     */
    public function withBingoMode(): static
    {
        return $this->state(fn(array $attributes) => [
            'game_modes' => array_merge($attributes['game_modes'] ?? [], [GameMode::BINGO]),
        ]);
    }

    /**
     * State: Vragen mode enabled
     */
    public function withVragenMode(): static
    {
        return $this->state(fn(array $attributes) => [
            'game_modes' => array_merge($attributes['game_modes'] ?? [], [GameMode::VRAGEN]),
        ]);
    }

    /**
     * State: Both modes enabled
     */
    public function withAllModes(): static
    {
        return $this->state(fn(array $attributes) => [
            'game_modes' => GameMode::ALL_MODES,
        ]);
    }
}
