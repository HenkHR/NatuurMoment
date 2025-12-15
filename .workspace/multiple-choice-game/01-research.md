# Multiple Choice Game - Research

## Tech Stack
- Laravel 12.x
- Livewire 3.x
- Alpine.js 3.4.2
- Tailwind CSS 3.4.18
- Session-based player auth (tokens)

## Existing Patterns to Follow

### Template → Instance Pattern
**Source:** `HostLobby.php:181-204`
```php
// Copy LocationRouteStop → RouteStop using replicate()
foreach ($location->routeStops()->orderBy('sequence')->get() as $template) {
    $routeStop = $template->replicate(['id', 'location_id']);
    $routeStop->game_id = $game->id;
    $routeStop->save();
}
```

### Token-based Session Auth
**Source:** `PlayerPhotoCapture.php:151-174`
```php
#[Locked]
public int $gameId;

#[Locked]
public string $playerToken;

private function validatePlayerAccess(): int
{
    $player = GamePlayer::where('game_id', $this->gameId)
        ->where('token', $this->playerToken)
        ->first();

    if (!$player) {
        abort(403, 'Ongeldige toegang');
    }

    return $player->id;
}
```

### Polling for Updates
**Source:** `host-game.blade.php`
```blade
<div wire:poll.5s.visible="refreshStatuses">
```

## Framework Best Practices

### Livewire Component Patterns
- Use `#[Locked]` on security-sensitive properties (gameId, playerToken, routeStopId)
- Use `wire:loading.attr="disabled"` on submit buttons to prevent double-click
- Use `wire:model` for radio button binding
- Use `$this->dispatch('event-name')` for Alpine.js communication

### Form Handling
- Validate with `$this->validate()` before persistence
- Use `Rule::in(['A', 'B', 'C', 'D'])` for option validation
- Catch `QueryException` with code `23000` for duplicate entries

### Sequential Unlock Pattern
```php
// RouteStop.php - Query scope
public function scopeUnlocked($query, int $gamePlayerId)
{
    $answeredCount = RouteStopAnswer::where('game_player_id', $gamePlayerId)
        ->whereIn('route_stop_id', function($q) {
            $q->select('id')->from('route_stops')->where('game_id', $this->game_id);
        })
        ->count();

    return $query->where('game_id', $this->game_id)
        ->orderBy('sequence')
        ->skip($answeredCount)
        ->first();
}
```

## Architecture Patterns

### Model Structure

#### RouteStop Model
```php
// app/Models/RouteStop.php
class RouteStop extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'points',
        'sequence',
        'image_path',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(RouteStopAnswer::class);
    }

    public function isAnsweredBy(int $gamePlayerId): bool
    {
        return $this->answers()->where('game_player_id', $gamePlayerId)->exists();
    }

    // Get available options (only non-null)
    public function getAvailableOptions(): array
    {
        return collect(['A' => $this->option_a, 'B' => $this->option_b, 'C' => $this->option_c, 'D' => $this->option_d])
            ->filter()
            ->toArray();
    }
}
```

#### RouteStopAnswer Model
```php
// app/Models/RouteStopAnswer.php
class RouteStopAnswer extends Model
{
    protected $fillable = [
        'game_player_id',
        'route_stop_id',
        'chosen_option',
        'is_correct',
        'score_awarded',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function gamePlayer(): BelongsTo
    {
        return $this->belongsTo(GamePlayer::class);
    }

    public function routeStop(): BelongsTo
    {
        return $this->belongsTo(RouteStop::class);
    }
}
```

### Aggregate Calculations
```php
// For host dashboard - % answered per player
$players = $game->players()
    ->withCount(['routeStopAnswers as answered_questions_count'])
    ->get();

$totalQuestions = $game->routeStops()->count();

foreach ($players as $player) {
    $player->questions_percentage = $totalQuestions > 0
        ? round(($player->answered_questions_count / $totalQuestions) * 100)
        : 0;
}
```

## Testing Strategy

### Unit Tests for Sequential Unlock
```php
// tests/Unit/Models/RouteStopTest.php
public function test_first_question_is_unlocked_for_new_player()
{
    $game = Game::factory()->create();
    $player = GamePlayer::factory()->create(['game_id' => $game->id]);
    RouteStop::factory()->count(3)->create(['game_id' => $game->id]);

    $unlocked = RouteStop::unlocked($player->id)->first();

    $this->assertEquals(1, $unlocked->sequence);
}

public function test_second_question_unlocked_after_first_answered()
{
    // ... setup
    RouteStopAnswer::create([
        'game_player_id' => $player->id,
        'route_stop_id' => $firstQuestion->id,
        'chosen_option' => 'A',
        'is_correct' => true,
        'score_awarded' => 10,
    ]);

    $unlocked = RouteStop::unlocked($player->id)->first();

    $this->assertEquals(2, $unlocked->sequence);
}
```

### Feature Tests for Answer Submission
```php
// tests/Feature/Livewire/PlayerRouteQuestionTest.php
public function test_correct_answer_awards_points()
{
    Livewire::test(PlayerRouteQuestion::class, [
        'gameId' => $game->id,
        'playerToken' => $player->token,
    ])
    ->call('submitAnswer', 'A') // correct answer
    ->assertSet('isCorrect', true);

    $this->assertEquals(10, $player->fresh()->score);
}

public function test_duplicate_answer_prevented()
{
    // First answer
    RouteStopAnswer::create([...]);

    Livewire::test(PlayerRouteQuestion::class, [...])
        ->call('submitAnswer', 'B')
        ->assertHasErrors();
}
```

## Common Pitfalls & Edge Cases

### Pitfalls to Avoid
1. **Async state mutation**: DO NOT use `#[Async]` on submitAnswer - causes race conditions
2. **Missing wire:loading**: Users can submit multiple times without it
3. **Redirect before DB commit**: Ensure transaction completes before redirect
4. **Missing wire:key in loops**: Causes "Snapshot missing" errors

### Edge Cases to Handle
1. **No questions for location**: Hide vragen-tab entirely
2. **Browser refresh**: State persisted in DB, reload current question
3. **Direct URL to locked question**: Validate unlock status, redirect if locked
4. **Race condition on submit**: Database unique constraint catches this

### Feedback UI Pattern
```blade
{{-- Alpine.js for feedback display --}}
<div x-data="{ showFeedback: false, isCorrect: false }"
     x-on:show-feedback.window="showFeedback = true; isCorrect = $event.detail.correct; setTimeout(() => $wire.proceedToNext(), 2000)">

    {{-- Feedback overlay --}}
    <div x-show="showFeedback" x-transition class="fixed inset-0 flex items-center justify-center bg-black/50">
        <div :class="isCorrect ? 'bg-green-500' : 'bg-red-500'" class="rounded-full p-8">
            <template x-if="isCorrect">
                <svg><!-- checkmark --></svg>
            </template>
            <template x-if="!isCorrect">
                <svg><!-- X mark --></svg>
            </template>
        </div>
    </div>
</div>
```

## Context7 Sources
- Coverage: 87% overall
- best-practices: 82.5% (Livewire 3.x patterns, forms, polling)
- architecture: 90% (Laravel 12.x relationships, scopes, services)
- testing: 88% (Livewire testing, edge cases)

---

## Part 01: Foundation - Research

### Files to Create
| File | Purpose |
|------|---------|
| `app/Models/RouteStop.php` | Game instance question model |
| `app/Models/RouteStopAnswer.php` | Player answer tracking |

### Files to Modify
| File | Change |
|------|--------|
| `app/Models/Game.php` | Add `routeStops()` relationship |
| `app/Models/GamePlayer.php` | Add `routeStopAnswers()` relationship |
| `app/Livewire/HostLobby.php` | Add `generateRouteStops()` call in startGame() |

### Key Implementation Notes
- Use `$template->replicate(['id', 'location_id'])` to copy questions
- Preserve `sequence` field for ordering
- Add after existing `generateBingoItems()` call

---

## Part 02: Player Game - Research

### Files to Create
| File | Purpose |
|------|---------|
| `app/Livewire/PlayerRouteQuestion.php` | Main quiz component |
| `resources/views/livewire/player-route-question.blade.php` | Quiz UI |

### Files to Modify
| File | Change |
|------|--------|
| `app/Models/RouteStop.php` | Add `unlocked()` query scope |
| `resources/views/player/route.blade.php` | Replace TODO with component |

### Key Implementation Notes
- Clone structure from PlayerPhotoCapture
- Use Alpine.js for 2s feedback timer
- `wire:loading.attr="disabled"` on submit button
- Radio buttons with `wire:model="selectedOption"`

---

## Part 03: Integration - Research

### Files to Modify
| File | Change |
|------|--------|
| `app/Livewire/HostGame.php` | Add route stop progress to loadPlayers() |
| `resources/views/livewire/host-game.blade.php` | Display "Vragen: X%" |
| `app/Livewire/PlayerRouteQuestion.php` | Add completion redirect logic |
| `resources/views/components/player-navigation.blade.php` | Conditional tab visibility |
| `routes/web.php` | Add player.questions route |

### Key Implementation Notes
- Use `withCount()` for efficient aggregation
- Check `$location->routeStops()->count() > 0` for tab visibility
- Redirect order: questions done → bingo, bingo done → leaderboard
