<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GamePlayer>
 */
class GamePlayerFactory extends Factory
{
    protected $model = GamePlayer::class;

    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'name' => fake()->firstName(),
            'token' => GamePlayer::generateToken(),
            'score' => fake()->numberBetween(0, 100),
            'feedback_rating' => null,
            'feedback_age' => null,
        ];
    }

    /**
     * Player with feedback (1-5 stars)
     */
    public function withFeedback(?int $rating = null, ?string $age = null): static
    {
        return $this->state(fn (array $attributes) => [
            'feedback_rating' => $rating ?? fake()->numberBetween(1, 5),
            'feedback_age' => $age ?? (string) fake()->numberBetween(8, 25),
        ]);
    }

    /**
     * Player in specific age category
     */
    public function ageCategory(string $category): static
    {
        $age = match ($category) {
            '≤12' => fake()->numberBetween(6, 12),
            '13-15' => fake()->numberBetween(13, 15),
            '16-18' => fake()->numberBetween(16, 18),
            '19-21' => fake()->numberBetween(19, 21),
            '22+' => fake()->numberBetween(22, 40),
            default => fake()->numberBetween(10, 20),
        };

        return $this->state(fn (array $attributes) => [
            'feedback_age' => (string) $age,
        ]);
    }

    /**
     * Player with specific rating
     */
    public function rating(int $rating): static
    {
        return $this->state(fn (array $attributes) => [
            'feedback_rating' => $rating,
        ]);
    }
}
