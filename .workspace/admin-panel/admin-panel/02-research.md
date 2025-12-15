# Admin Panel - Research

## Context7 Research Summary

Coverage: 84% | Confidence: 87%

## Laravel Admin Authorization Patterns

### Policy with Admin Bypass
```php
// In Policy class
public function before(User $user): ?bool
{
    if ($user->is_admin) {
        return true; // Bypass all other checks
    }
    return null; // Fall through to specific methods
}
```

### Simple Middleware Approach (Recommended for this project)
```php
// app/Http/Middleware/IsAdmin.php
public function handle(Request $request, Closure $next): Response
{
    abort_if(!auth()->user()?->is_admin, 403);
    return $next($request);
}
```

### Middleware Registration (Laravel 12)
```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\IsAdmin::class,
    ]);
})
```

## Resource Controller Patterns

### Generation Command
```bash
php artisan make:controller Admin/LocationController --resource --model=Location
```

### Route Registration
```php
// routes/web.php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('locations', Admin\LocationController::class);
    Route::resource('locations.bingo-items', Admin\BingoItemController::class)->shallow();
    Route::resource('locations.route-stops', Admin\RouteStopController::class)->shallow();
    Route::resource('games', Admin\GameController::class)->only(['index', 'show', 'destroy']);
});
```

### Shallow Nesting
- `->shallow()` creates shorter URLs for edit/update/destroy
- `/admin/locations/{location}/bingo-items` for index/create/store
- `/admin/bingo-items/{bingo_item}` for show/edit/update/destroy

## Form Request Validation

### Store Request Pattern
```php
class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware handles auth
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:locations'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Naam is verplicht.',
            'name.unique' => 'Deze locatie naam bestaat al.',
        ];
    }
}
```

### Update Request with Ignore Current
```php
public function rules(): array
{
    return [
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('locations')->ignore($this->route('location'))
        ],
    ];
}
```

## Eloquent Model Patterns

### Location Model
```php
class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function bingoItems(): HasMany
    {
        return $this->hasMany(LocationBingoItem::class);
    }

    public function routeStops(): HasMany
    {
        return $this->hasMany(LocationRouteStop::class)->orderBy('sequence');
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
```

### Cascade Delete Check
```php
// In LocationController@destroy
public function destroy(Location $location)
{
    if ($location->games()->exists()) {
        return back()->with('error', 'Kan locatie niet verwijderen: er zijn nog games gekoppeld.');
    }

    $location->delete(); // Cascade deletes bingo items and route stops
    return redirect()->route('admin.locations.index')
        ->with('status', 'Locatie verwijderd.');
}
```

## Migration Patterns

### Add is_admin to Users
```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false)->after('password');
});
```

### Foreign Key with Cascade
```php
$table->foreignId('location_id')
    ->constrained()
    ->onDelete('cascade');
```

### Foreign Key with Restrict
```php
$table->foreignId('location_id')
    ->constrained()
    ->onDelete('restrict');
```

## Testing Patterns

### Admin Middleware Test
```php
test('admin routes require admin user', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/admin/locations')
        ->assertStatus(403);
});

test('admin can access admin routes', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/admin/locations')
        ->assertStatus(200);
});
```

### CRUD Test Pattern
```php
test('admin can create location', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->post('/admin/locations', [
            'name' => 'Test Locatie',
            'description' => 'Test beschrijving',
        ])
        ->assertRedirect('/admin/locations')
        ->assertSessionHas('status');

    $this->assertDatabaseHas('locations', ['name' => 'Test Locatie']);
});
```

## UI Patterns (Blade)

### Admin Layout Structure
```blade
{{-- resources/views/admin/layout.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-h2 text-forest">Admin Panel</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-container mx-auto px-4">
            {{-- Admin Navigation --}}
            <nav class="mb-6 flex gap-4">
                <a href="{{ route('admin.locations.index') }}"
                   class="text-forest hover:text-action {{ request()->routeIs('admin.locations.*') ? 'font-bold' : '' }}">
                    Locaties
                </a>
                <a href="{{ route('admin.games.index') }}"
                   class="text-forest hover:text-action {{ request()->routeIs('admin.games.*') ? 'font-bold' : '' }}">
                    Games
                </a>
            </nav>

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="mb-4 p-4 bg-forest-100 text-forest-700 rounded-card">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-card">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</x-app-layout>
```

### Table Pattern
```blade
<table class="w-full bg-pure-white rounded-card shadow-card">
    <thead class="bg-surface-light">
        <tr>
            <th class="px-4 py-3 text-left text-small font-medium text-forest">Naam</th>
            <th class="px-4 py-3 text-right text-small font-medium text-forest">Acties</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-surface-medium">
        @foreach ($locations as $location)
            <tr>
                <td class="px-4 py-3">{{ $location->name }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.locations.edit', $location) }}"
                       class="text-sky hover:text-sky-700">Bewerk</a>
                    <button @click="$dispatch('open-modal', 'delete-{{ $location->id }}')"
                            class="text-red-600 hover:text-red-800">Verwijder</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### Delete Confirmation Modal
```blade
<x-modal name="delete-{{ $location->id }}" focusable>
    <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" class="p-6">
        @csrf
        @method('DELETE')

        <h2 class="text-h3 text-forest">Locatie verwijderen?</h2>
        <p class="mt-2 text-body text-gray-600">
            Weet je zeker dat je "{{ $location->name }}" wilt verwijderen?
            Alle gekoppelde bingo items en vragen worden ook verwijderd.
        </p>

        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')">Annuleren</x-secondary-button>
            <x-danger-button>Verwijderen</x-danger-button>
        </div>
    </form>
</x-modal>
```

## Common Pitfalls

1. **N+1 Queries**: Gebruik `with()` eager loading op index pages
   ```php
   Location::with(['bingoItems', 'routeStops'])->paginate(15)
   ```

2. **Mass Assignment**: Altijd `$fillable` definiëren op models

3. **Missing CSRF**: Altijd `@csrf` in forms, `@method('DELETE')` voor delete

4. **Validation Rule Order**: `unique` rule moet `ignore()` hebben bij updates

5. **Cascade vs Restrict**: Games gebruiken restrict (admin moet eerst games verwijderen), bingo/route stops gebruiken cascade

## File Structure

```
app/
├── Http/
│   ├── Controllers/Admin/
│   │   ├── LocationController.php
│   │   ├── BingoItemController.php
│   │   ├── RouteStopController.php
│   │   └── GameController.php
│   ├── Middleware/
│   │   └── IsAdmin.php
│   └── Requests/
│       ├── StoreLocationRequest.php
│       ├── UpdateLocationRequest.php
│       ├── StoreBingoItemRequest.php
│       ├── UpdateBingoItemRequest.php
│       ├── StoreRouteStopRequest.php
│       └── UpdateRouteStopRequest.php
├── Models/
│   ├── Location.php
│   ├── LocationBingoItem.php
│   ├── LocationRouteStop.php
│   └── Game.php
resources/views/
├── admin/
│   ├── layout.blade.php
│   ├── locations/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── bingo-items/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── route-stops/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── games/
│       ├── index.blade.php
│       └── show.blade.php
```

## Implementation Order

1. **Foundation**: Migration is_admin, User model update, IsAdmin middleware
2. **Models**: Location, LocationBingoItem, LocationRouteStop, Game met relationships
3. **Routes**: Admin route group in web.php
4. **Controllers**: LocationController eerst (simpelste), dan rest
5. **Form Requests**: Per controller pair (Store/Update)
6. **Views**: Admin layout, dan per entiteit index → create → edit
7. **Testing**: Middleware test, dan CRUD tests per controller

---

## Extend: search-filter (2025-12-15)

### Context7 Research Summary
Coverage: 83% | Confidence: 86%

### Laravel Search/Filter/Pagination Patterns

#### Query String Preservation
```php
// Preserve all query parameters across pagination links
$locations = Location::query()
    ->when($request->filled('search'), fn($q) =>
        $q->where('province', 'like', '%' . $request->search . '%')
    )
    ->when($request->filled('regio'), fn($q) =>
        $q->where('province', $request->regio)
    )
    ->paginate(15)
    ->withQueryString();
```

#### AJAX Fragment Rendering
```php
// Controller returns partial for AJAX, full view for normal requests
public function index(Request $request)
{
    $locations = Location::query()
        ->when($request->filled('search'), ...)
        ->paginate(15)
        ->withQueryString();

    $provinces = config('provinces');

    // Return partial for AJAX requests
    if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
        return view('admin.locations.partials.table', compact('locations'));
    }

    return view('admin.locations.index', compact('locations', 'provinces'));
}
```

#### Blade Component Pattern
```blade
{{-- resources/views/components/admin/search-bar.blade.php --}}
@props(['model' => 'search', 'placeholder' => 'Zoeken...'])

<input
    type="text"
    x-model="{{ $model }}"
    @input.debounce.300ms="fetchResults()"
    placeholder="{{ $placeholder }}"
    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-action-500"
>
```

```blade
{{-- resources/views/components/admin/regio-filter.blade.php --}}
@props(['model' => 'regio', 'provinces' => []])

<select
    x-model="{{ $model }}"
    @change="fetchResults()"
    class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-action-500"
>
    <option value="">Alle regio's</option>
    @foreach($provinces as $province)
        <option value="{{ $province }}">{{ $province }}</option>
    @endforeach
</select>
```

#### Alpine.js State Management
```javascript
// In admin view
x-data="{
    search: new URLSearchParams(window.location.search).get('search') || '',
    regio: new URLSearchParams(window.location.search).get('regio') || '',
    loading: false,

    fetchResults() {
        this.loading = true;
        const params = new URLSearchParams();
        if (this.search) params.set('search', this.search);
        if (this.regio) params.set('regio', this.regio);

        const url = window.location.pathname + '?' + params.toString();

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('results-container').innerHTML = html;
            history.replaceState({}, '', url);
        })
        .finally(() => this.loading = false);
    }
}"
```

### Testing Patterns

#### Pagination Test
```php
test('locations index paginates 15 items', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Location::factory()->count(20)->create();

    $this->actingAs($admin)
        ->get('/admin/locations')
        ->assertOk()
        ->assertViewHas('locations', fn($l) => $l->perPage() === 15);
});
```

#### Filter Test
```php
test('locations can be filtered by region', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Location::factory()->create(['province' => 'Noord-Holland']);
    Location::factory()->create(['province' => 'Zuid-Holland']);

    $this->actingAs($admin)
        ->get('/admin/locations?regio=Noord-Holland')
        ->assertOk()
        ->assertSee('Noord-Holland')
        ->assertDontSee('Zuid-Holland');
});
```

#### AJAX Test
```php
test('AJAX request returns partial view', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Location::factory()->count(5)->create();

    $this->actingAs($admin)
        ->get('/admin/locations', ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->assertViewIs('admin.locations.partials.table');
});
```

### Files to Create

| File | Purpose |
|------|---------|
| `resources/views/components/admin/search-bar.blade.php` | Herbruikbare search input |
| `resources/views/components/admin/regio-filter.blade.php` | Herbruikbare regio dropdown |
| `resources/views/components/admin/empty-state.blade.php` | Lege resultaten melding |
| `resources/views/admin/locations/partials/table.blade.php` | Extracted table voor AJAX |
| `resources/views/admin/bingo-items/partials/table.blade.php` | Extracted table voor AJAX |
| `resources/views/admin/route-stops/partials/table.blade.php` | Extracted table voor AJAX |

### Files to Modify

| File | Change |
|------|--------|
| `app/Http/Controllers/Admin/LocationController.php` | Add search/filter logic, AJAX detection |
| `app/Http/Controllers/Admin/BingoItemController.php` | Add withQueryString() to pagination |
| `app/Http/Controllers/Admin/RouteStopController.php` | Add withQueryString() to pagination |
| `resources/views/admin/locations/index.blade.php` | Add search-bar, regio-filter, Alpine.js |
| `resources/views/admin/bingo-items/index.blade.php` | Add AJAX pagination |
| `resources/views/admin/route-stops/index.blade.php` | Add AJAX pagination |

### Implementation Sequence

1. **Phase 1: Components** - Create Blade components (search-bar, regio-filter, empty-state)
2. **Phase 2: Partials** - Extract table partials from existing views
3. **Phase 3: Controllers** - Add filter logic + AJAX detection
4. **Phase 4: Views** - Integrate components + Alpine.js
5. **Phase 5: Testing** - Add Pest tests for filters and pagination

### Architecture Decision
**Selected: Pragmatic Balance**
- Herbruikbare Blade components (geen code duplicatie)
- Inline when() clauses in controllers (geen query scopes - YAGNI)
- Progressive enhancement (werkt zonder JS)
- Alpine.js voor state management + vanilla fetch() voor AJAX
