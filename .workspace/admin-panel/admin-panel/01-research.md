# Admin Panel - Research

## Stack Baseline
Zie `.claude/research/stack-baseline.md` voor algemene Laravel 12 + Blade + Alpine.js + Tailwind CSS patronen.

---

## Extend: survey-statistieken (2025-12-15)

### Framework Best Practices (Chart.js + Alpine.js)

#### Chart.js Configuration
- Gebruik `config` object met `type`, `data`, en `options` properties
- Type: 'bar', 'line', 'doughnut' - specificeert chart type
- Standaard `responsive: true` in options voor responsive charts
- Na wijzigingen: altijd `chart.update()` aanroepen

#### Alpine.js + Chart.js Integration
```javascript
// Initialisatie pattern
x-data="{
    chart: null,
    init() {
        this.chart = new Chart(this.$refs.canvas.getContext('2d'), config);
    }
}"

// Data update pattern
$watch('data', () => {
    this.chart.data.datasets[0].data = newData;
    this.chart.update();
})
```

#### Chart Types voor Dashboard
| Statistiek | Chart Type | Config |
|------------|------------|--------|
| Leeftijdsverdeling | Bar | `type: 'bar'` |
| Tevredenheid per leeftijd | Bar (grouped) | `scales.x.stacked: false` |
| Trends | Line | `type: 'line'` |
| Rating per locatie | Bar (horizontal) | `indexAxis: 'y'` |

#### Performance
- Bij veel data: `animation: false` en `parsing: false`
- Voor realtime updates: `chart.update('none')` om animaties te skippen

#### Cleanup
- Roep `chart.destroy()` aan bij component removal om memory leaks te voorkomen

### Architecture Patterns (Aggregation Queries)

#### Eloquent Aggregation
- `selectRaw()` voor complexe aggregaties (CASE expressions)
- `groupByRaw()` voor date functions
- `havingRaw()` voor filtering op aggregated results

#### SQLite Date Grouping
```php
// Week grouping
selectRaw("strftime('%Y-%W', created_at) as period, AVG(feedback_rating) as avg")
->groupByRaw("strftime('%Y-%W', created_at)")

// Month grouping
selectRaw("strftime('%Y-%m', created_at) as period, AVG(feedback_rating) as avg")
->groupByRaw("strftime('%Y-%m', created_at)")

// Year grouping
selectRaw("strftime('%Y', created_at) as period, AVG(feedback_rating) as avg")
->groupByRaw("strftime('%Y', created_at)")
```

#### Age Categorization (CASE/WHEN)
```php
selectRaw("CASE
    WHEN CAST(feedback_age AS INTEGER) <= 12 THEN '≤12'
    WHEN CAST(feedback_age AS INTEGER) BETWEEN 13 AND 15 THEN '13-15'
    WHEN CAST(feedback_age AS INTEGER) BETWEEN 16 AND 18 THEN '16-18'
    WHEN CAST(feedback_age AS INTEGER) BETWEEN 19 AND 21 THEN '19-21'
    ELSE '22+'
END as age_group, COUNT(*) as count")
->whereNotNull('feedback_age')
->groupBy('age_group')
```

#### Location Statistics
```php
// Rating per location
GamePlayer::join('games', 'game_players.game_id', '=', 'games.id')
    ->join('locations', 'games.location_id', '=', 'locations.id')
    ->selectRaw('locations.name, AVG(feedback_rating) as avg_rating, COUNT(*) as count')
    ->whereNotNull('feedback_rating')
    ->groupBy('locations.id', 'locations.name')
    ->orderByDesc('count')
    ->get();
```

#### Dashboard Query Optimization
- Execute independent statistics as separate focused queries
- Use `withCount()` voor efficient counting
- Overweeg caching met `Cache::remember()` voor 5 minuten

### Testing Strategy

#### Livewire Component Testing
```php
// Test star rating interaction
Livewire::test(PlayerFeedback::class, ['game' => $game])
    ->set('rating', 4)
    ->call('submitFeedback')
    ->assertSet('rating', 4);
```

#### Aggregation Query Testing
```php
// Test met factory data
test('calculates average rating correctly', function () {
    GamePlayer::factory()->count(5)->create(['feedback_rating' => 4]);

    $response = $this->actingAs($admin)->get('/admin/statistics');

    $response->assertViewHas('stats', function ($stats) {
        return $stats['average_rating'] === 4.0;
    });
});
```

#### Empty State Testing
```php
test('handles zero responses gracefully', function () {
    $response = $this->actingAs($admin)->get('/admin/statistics');

    $response->assertSee('Geen gegevens beschikbaar');
});
```

### Common Pitfalls

#### Division by Zero
- AVG op lege set retourneert null in SQLite
- Check in view: `{{ $stats['average'] ?? 'N/A' }}`

#### N+1 Queries
- Gebruik joins voor location data, niet eager loading
- Aggregaties in single query, niet in loop

#### Chart.js Data Format
- Pass data als JSON: `@json($chartData)`
- Ensure arrays, not collections: `->values()->toArray()`

#### Wire:key in Loops
- Bij Livewire loops: altijd `wire:key` toevoegen

### Context7 Sources

Coverage: 81% (average across domains)
Confidence: 86% (average across findings)

Queries executed:
- Chart.js official documentation (1160 snippets)
- Laravel 12 Eloquent aggregation
- Laravel 12 SQLite date functions
- Livewire 4.x testing patterns

---

## Earlier Extends

### search-filter
Geen specifieke research nodig - standaard Laravel pagination + Alpine.js patterns (gedekt door baseline).

### game-modes
Geen specifieke research nodig - standaard Laravel JSON casting + Eloquent accessors (gedekt door baseline).

---

## Extend: bingo-scoring-config (2025-12-16)

### Framework Best Practices

#### Integer Validation
- Gebruik `integer` voor standaard validatie, `integer:strict` voor strikte type enforcement
- Combineer met `min:value` en `max:value` voor range constraints
- Safe retrieval via `$request->integer('field_name')`

#### Mass Assignment
- Voeg nieuwe kolommen toe aan `$fillable` array in model
- Gebruik casting via `casts()` method: `'field' => 'integer'`

#### Form Repopulation
- Pattern: `old('field_name', $model->field_name)` voor input values
- Validatie errors via `@error('field')` directive

### Architecture Patterns

#### Migration voor Bestaande Tabel
```php
// Schema::table() voor column additions
Schema::table('locations', function (Blueprint $table) {
    $table->integer('bingo_three_in_row_points')->default(50)->after('game_modes');
    $table->integer('bingo_full_card_points')->default(100)->after('bingo_three_in_row_points');
});
```

#### FormRequest Pattern
```php
class UpdateBingoScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'bingo_three_in_row_points' => ['required', 'integer', 'min:1'],
            'bingo_full_card_points' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'bingo_three_in_row_points.required' => 'Punten voor 3-op-een-rij is verplicht.',
            'bingo_three_in_row_points.min' => 'Punten moet minimaal 1 zijn.',
            'bingo_full_card_points.required' => 'Punten voor volle kaart is verplicht.',
            'bingo_full_card_points.min' => 'Punten moet minimaal 1 zijn.',
        ];
    }
}
```

#### Controller Method
```php
public function updateScoring(UpdateBingoScoreRequest $request, Location $location): RedirectResponse
{
    $location->update($request->validated());

    return back()->with('status', 'Bingo scoring configuratie opgeslagen.');
}
```

### Testing Strategy

#### Feature Test Pattern
```php
test('admin can update bingo scoring config', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $location = Location::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.locations.bingo-scoring.update', $location), [
            'bingo_three_in_row_points' => 75,
            'bingo_full_card_points' => 150,
        ])
        ->assertRedirect()
        ->assertSessionHas('status');

    $this->assertDatabaseHas('locations', [
        'id' => $location->id,
        'bingo_three_in_row_points' => 75,
        'bingo_full_card_points' => 150,
    ]);
});

test('validation rejects negative points', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $location = Location::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.locations.bingo-scoring.update', $location), [
            'bingo_three_in_row_points' => -10,
            'bingo_full_card_points' => 100,
        ])
        ->assertInvalid(['bingo_three_in_row_points']);
});

test('new locations get default scoring values', function () {
    $location = Location::factory()->create();

    expect($location->bingo_three_in_row_points)->toBe(50);
    expect($location->bingo_full_card_points)->toBe(100);
});
```

### Common Pitfalls

#### Validation
- Test beide velden, niet alleen één
- Vergeet niet beide positive integer validatie (min:1)
- Controleer database persistence, niet alleen HTTP response

#### Migration
- Gebruik `->default()` voor backward compatibility met bestaande records
- Test dat bestaande locaties default waardes krijgen

### Context7 Sources

Coverage: 83% (average across domains)
Confidence: 85% (average across findings)

Queries executed:
- Laravel 12 validation numeric rules
- Laravel 12 migration add columns
- Laravel 12 FormRequest patterns
- Pest PHP feature testing
