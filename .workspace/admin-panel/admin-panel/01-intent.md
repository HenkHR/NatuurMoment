# Admin Panel - Intent

## Overview
Admin panel voor CRUD beheer van games, locaties, bingokaarten en multiple choice vragen in NatuurMoment kinderspel.

## Task Type
FEATURE (nieuw)

## Functional Requirements

### Locations (CRUD)
- Aanmaken nieuwe locatie (naam, beschrijving)
- Overzicht alle locaties
- Bewerken locatie gegevens
- Verwijderen locatie (met cascade naar bingo items en route stops)

### Bingo Items (CRUD per locatie)
- Aanmaken bingo item binnen locatie (label, punten, icon)
- Overzicht bingo items per locatie
- Bewerken bingo item
- Verwijderen bingo item

### Route Stops / Vragen (CRUD per locatie)
- Aanmaken vraag binnen locatie:
  - Naam
  - Vraagtekst
  - 4 antwoordopties (A, B, C, D)
  - Correct antwoord (A/B/C/D)
  - Punten
  - Volgorde (sequence)
- Overzicht vragen per locatie
- Bewerken vraag
- Verwijderen vraag

### Games (Read/Delete only)
- Overzicht alle games (met locatie, status, PIN)
- Bekijken game details
- Verwijderen game (moderatie)
- GEEN create via admin (games worden door spelers aangemaakt)

## Data Models

### Relationships
```
Location
  ├── hasMany → LocationBingoItem
  ├── hasMany → LocationRouteStop
  └── hasMany → Game

Game
  └── belongsTo → Location
```

### Existing Schema (from migrations)
- `locations`: id, name, description
- `location_bingo_items`: id, location_id, label, points, icon
- `location_route_stops`: id, location_id, name, question_text, option_a/b/c/d, correct_option, points, sequence
- `games`: id, location_id, pin, status, host_token

## UI Components

### Per Entiteit
- **Index page**: Tabel met alle records, create/edit/delete knoppen
- **Create page**: Formulier voor nieuwe record
- **Edit page**: Formulier met huidige data

### Hergebruik Bestaande Components
- `<x-primary-button>` voor acties
- `<x-text-input>` voor form fields
- `<x-input-label>` voor labels
- `<x-input-error>` voor validatie errors
- `<x-modal>` voor delete bevestiging
- `<x-dropdown>` voor select opties

## Authentication & Authorization

- **Admin-only toegang**: Simpele `is_admin` boolean op User model
- **Middleware**: `IsAdmin` middleware op alle `/admin/*` routes
- **Geen user registration nodig**: Alleen admin logt in

## Edge Cases

1. **Verwijderen locatie met games**: Restrict - toon foutmelding, verwijder eerst games
2. **Verwijderen locatie zonder games**: Cascade delete naar bingo items en route stops
3. **Duplicate location names**: Validatie - naam moet uniek zijn
4. **Vraag zonder correct antwoord**: Validatie - correct_option is required
5. **Lege antwoordopties**: Validatie - minimaal option_a en option_b required

## Validation Rules

### Location
- `name`: required, string, max:255, unique:locations
- `description`: nullable, string

### Bingo Item
- `label`: required, string, max:255
- `points`: required, integer, min:1
- `icon`: nullable, string

### Route Stop
- `name`: required, string, max:255
- `question_text`: required, string
- `option_a`: required, string, max:255
- `option_b`: required, string, max:255
- `option_c`: nullable, string, max:255
- `option_d`: nullable, string, max:255
- `correct_option`: required, in:a,b,c,d
- `points`: required, integer, min:1
- `sequence`: required, integer, min:0

## Success Criteria

1. Admin kan inloggen en admin panel zien
2. Volledige CRUD voor locations werkt
3. Volledige CRUD voor bingo items (binnen locatie context) werkt
4. Volledige CRUD voor route stops (binnen locatie context) werkt
5. Games kunnen bekeken en verwijderd worden
6. Alle validatie regels worden gehandhaafd
7. Delete acties vragen om bevestiging
8. Success/error messages worden getoond na acties

---

## Extend: search-filter (2025-12-15)

### Overview
Search bar, regio filter en pagination toevoegen aan admin panel pagina's.

### Task Type
EXTEND

### Scope
- **Locaties overzicht**: Search bar (filtert op regio), regio dropdown filter, pagination (15 items)
- **Bingo items per locatie**: Pagination only (15 items)
- **Vragen per locatie**: Pagination only (15 items)

### Functional Requirements

#### Locaties Overzicht
- Search bar boven de tabel die filtert op regio/provincie
- Regio dropdown met zelfde opties als home pagina (config/provinces.php)
- Pagination met 15 items per pagina
- Filters en pagination behouden state bij navigeren
- Lege resultaten tonen "geen resultaten" melding

#### Bingo Items & Vragen
- Pagination met 15 items per pagina
- Query string preservation voor page parameter

### Testable Requirements

| ID | Description | Category | Test Type | Passes |
|----|-------------|----------|-----------|--------|
| REQ-001 | Locaties overzicht toont search bar die filtert op regio | core | automated_ui | true |
| REQ-002 | Locaties overzicht toont regio filter dropdown met zelfde opties als home | core | automated_ui | true |
| REQ-003 | Locaties overzicht toont pagination met 15 items per pagina | core | automated_ui | true |
| REQ-004 | Bingo items per locatie toont pagination met 15 items per pagina | core | automated_ui | true |
| REQ-005 | Vragen per locatie toont pagination met 15 items per pagina | ui | automated_ui | true |
| REQ-006 | Filters en pagination behouden state bij navigeren | ui | automated_ui | true |
| REQ-007 | Lege resultaten tonen "geen resultaten" melding | edge_case | automated_ui | true |
| REQ-008 | Pagination toont correcte totaal aantal pagina's | edge_case | automated_unit | true |

### UI Components

#### Nieuwe Blade Components
- `<x-admin.search-bar>` - Herbruikbare search input met Alpine.js binding
- `<x-admin.regio-filter>` - Herbruikbare regio dropdown
- `<x-admin.pagination>` - Pagination wrapper met AJAX support

#### Bestaande Components (hergebruik)
- Bestaande admin tabel/cards layout
- config/provinces.php voor regio opties

### Data Flow
1. User typt in search of selecteert regio
2. Alpine.js triggert fetch() AJAX request
3. Controller past when() filters toe + paginate(15)->withQueryString()
4. Controller returnt fragmentIf() partial HTML
5. Alpine.js update DOM + browser URL

### Edge Cases
1. Lege zoekresultaten → "Geen locaties gevonden" melding
2. Pagination buiten bereik → Redirect naar laatste geldige pagina
3. Speciale karakters in search → Eloquent bindings handelen SQL injection

### Success Criteria
1. Search bar filtert locaties correct op regio
2. Regio dropdown toont alle provincies uit config
3. Pagination werkt op alle 3 admin pagina's
4. Filters blijven behouden bij pagination navigatie
5. AJAX updates werken smooth zonder page reload
6. Pagina werkt ook zonder JavaScript (progressive enhancement)

---

## Extend: game-modes (2025-12-15)

### Overview
Game modes systeem met validatie per locatie. Elke locatie krijgt configureerbare game modes (Bingo, Vragen) met minimum content vereisten.

### Task Type
EXTEND

### Scope
- Game modes per locatie: Bingo (min 9 items), Vragen (min 1 vraag)
- Toggle switches op edit/create pagina
- Visuele status in tabel (rode counts, ⚠️ badge)
- Locatie zonder valide modes niet zichtbaar op home
- Nieuwe locatie: modes standaard UIT

### Functional Requirements

#### Database
- `game_modes` JSON kolom op locations tabel
- Opslaat als array: `['bingo', 'vragen']` of `[]`
- Default: lege array (alle modes uit)

#### Admin Edit/Create
- Toggle switches per game mode (Bingo, Vragen)
- Status indicator per toggle:
  - ✓ groen als mode valide (voldoende content)
  - ⚠️ oranje/rood als mode enabled maar onvoldoende content
- Count weergave naast toggle (bijv. "9/9 items")

#### Admin Index Tabel
- Rode tekst voor counts onder minimum (bingo < 9, vragen < 1)
- ⚠️ badge achter locatienaam bij incomplete actieve modes
- Bestaande count badges blijven klikbaar

#### Home Page Filtering
- Locaties zonder minstens 1 valide actieve mode worden verborgen
- Valide = mode enabled EN voldoende content

#### Game Logic
- Bingo selecteert random 9 items als locatie meer dan 9 heeft

### Testable Requirements

| ID | Description | Category | Test Type | Passes |
|----|-------------|----------|-----------|--------|
| REQ-001 | Locatie heeft game_modes JSON veld met enabled modes | core | automated_unit | true |
| REQ-002 | Bingo modus vereist minimaal 9 bingo items | core | automated_unit | true |
| REQ-003 | Vragen modus vereist minimaal 1 vraag | core | automated_unit | true |
| REQ-004 | Edit/create pagina toont toggle switches per game mode | ui | automated_ui | true |
| REQ-005 | Toggle toont status indicator (✓/⚠️) met count | ui | automated_ui | true |
| REQ-006 | Nieuwe locatie heeft alle modes standaard UIT | core | automated_unit | true |
| REQ-007 | Tabel toont rode tekst voor counts onder minimum | ui | automated_ui | true |
| REQ-008 | Tabel toont ⚠️ badge achter naam bij incomplete actieve modes | ui | automated_ui | true |
| REQ-009 | Locatie zonder valide actieve modes niet zichtbaar op home | core | automated_api | true |
| REQ-010 | Bingo selecteert random 9 items als er meer dan 9 zijn | core | automated_unit | deferred |

### Data Models

#### Location Model Extensions
```php
// Fillable
protected $fillable = [..., 'game_modes'];

// Casts
protected function casts(): array {
    return ['game_modes' => 'array'];
}

// Accessors
public function getHasBingoModeAttribute(): bool
public function getHasVragenModeAttribute(): bool
public function getIsBingoModeValidAttribute(): bool
public function getIsVragenModeValidAttribute(): bool
public function getHasValidGameModeAttribute(): bool

// Scope
public function scopeWithValidGameModes($query)
```

### UI Components

#### Toggle Switch (inline Alpine.js)
```blade
<div x-data="{ enabled: {{ $location->has_bingo_mode ? 'true' : 'false' }} }">
    <input type="checkbox" x-model="enabled" name="game_modes[]" value="bingo">
    <span x-show="enabled && {{ $bingoCount }} >= 9">✓</span>
    <span x-show="enabled && {{ $bingoCount }} < 9">⚠️</span>
</div>
```

#### Status Badge (index tabel)
```blade
@if(!$location->has_valid_game_mode && ($location->has_bingo_mode || $location->has_vragen_mode))
    <span class="text-yellow-600">⚠️</span>
@endif
```

### Edge Cases
1. Nieuwe locatie zonder content → beide modes uit, geen waarschuwing
2. Locatie met bingo enabled maar 0 items → ⚠️ badge, niet op home
3. Locatie met alleen vragen mode valide → zichtbaar op home
4. Locatie met beide modes valide → zichtbaar op home
5. Bestaande locaties na migratie → game_modes = [] (alle uit)

### Success Criteria
1. Toggle switches werken op create/edit pagina's
2. Status indicators tonen correcte validatie state
3. Index tabel toont rode counts en warning badges
4. Home page filtert incorrect geconfigureerde locaties
5. Bestaande functionaliteit blijft werken
6. Nieuwe locaties starten met alle modes uit

---

## Extend: survey-statistieken (2025-12-15)

### Overview
Survey feedback systeem aanpassen naar sterren-rating en statistieken dashboard toevoegen aan admin panel. Bestaande PlayerFeedback component wijzigen van 1-10 cijfers naar 1-5 sterren. Nieuw dashboard met stat cards en grafieken voor inzicht in feedback data.

### Task Type
EXTEND

### Scope
- **PlayerFeedback wijziging**: 1-10 cijfer → 1-5 sterren rating
- **Statistics Dashboard**: Nieuwe admin pagina met:
  - 4 stat cards (totaal responses, gem. rating, responses deze maand, meest actieve locatie)
  - 4 grafieken (leeftijdsverdeling, tevredenheid per leeftijd, trends, rating per locatie)
- **AJAX dropdown**: Trends grafiek filtert op week/maand/jaar

### Functional Requirements

#### PlayerFeedback Component
- Validatie wijzigen van 1-10 naar 1-5
- UI wijzigen van 10 nummer-knoppen naar 5 klikbare sterren
- Bestaande leeftijdsveld behouden

#### Stat Cards (4 stuks)
1. **Totaal responses**: COUNT van alle feedback entries
2. **Gemiddelde rating**: AVG van feedback_rating (weergave als sterren)
3. **Responses deze maand**: COUNT van huidige maand
4. **Meest actieve locatie**: Locatie met hoogste COUNT

#### Grafieken
| Grafiek | Type | Data |
|---------|------|------|
| Leeftijdsverdeling | Staafdiagram | 5 categorieën: ≤12, 13-15, 16-18, 19-21, 22+ |
| Tevredenheid per leeftijd | Grouped staafdiagram | Gem. rating per leeftijdscategorie |
| Trends over tijd | Lijndiagram | Rating over tijd, dropdown filter (week/maand/jaar) |
| Rating per locatie | Horizontaal staafdiagram | Gem. rating per locatie |

#### AJAX Trend Filter
- Dropdown met opties: Week, Maand, Jaar
- Bij wijziging: fetch() naar `/admin/statistics?period=X`
- Controller retourneert JSON met trend data
- Alpine.js update Chart.js grafiek met `chart.update()`

### Testable Requirements

| ID | Description | Category | Test Type | Passes |
|----|-------------|----------|-----------|--------|
| REQ-001 | Feedback formulier toont 1-5 sterren i.p.v. 1-10 cijfer | core | manual | true |
| REQ-002 | Statistieken dashboard pagina beschikbaar in admin panel | core | automated_ui | true |
| REQ-003 | 4 stat cards tonen: totaal responses, gem. rating, responses deze maand, meest actieve locatie | core | automated_ui | true |
| REQ-004 | Staafdiagram toont leeftijdsverdeling in 5 categorieën (≤12, 13-15, 16-18, 19-21, 22+) | ui | manual | true |
| REQ-005 | Grouped staafdiagram toont tevredenheid per leeftijdscategorie | ui | manual | true |
| REQ-006 | Lijndiagram toont trends met dropdown filter (week/maand/jaar) | ui | manual | true |
| REQ-007 | Horizontaal staafdiagram toont gemiddelde rating per locatie | ui | manual | true |
| REQ-008 | Rating wordt opgeslagen als 1-5 integer | api | automated_unit | true |
| REQ-009 | Leeftijd wordt gecategoriseerd in 5 groepen voor statistieken | api | automated_unit | true |
| REQ-010 | Aggregatie queries berekenen AVG rating, COUNT per categorie, GROUP BY tijd/locatie/leeftijd | api | automated_unit | true |
| REQ-011 | Dashboard toont lege staat message als geen feedback data aanwezig | edge_case | automated_ui | true |
| REQ-012 | Grafieken renderen correct met 0 responses in bepaalde categorieën | edge_case | manual | true |

### Leeftijdscategorieën
| Categorie | Leeftijd | Doelgroep |
|-----------|----------|-----------|
| ≤12 | 0-12 jaar | Kinderen (Oerr programma) |
| 13-15 | 13-15 jaar | Onderbouw middelbaar |
| 16-18 | 16-18 jaar | Bovenbouw middelbaar |
| 19-21 | 19-21 jaar | Jong volwassen |
| 22+ | 22+ jaar | Buiten primaire doelgroep |

### Data Models

#### Bestaande Schema (game_players tabel)
- `feedback_rating`: unsignedTinyInteger (wijzigen validatie naar 1-5)
- `feedback_age`: string (leeftijd)
- Via `Game` relatie: `location_id` beschikbaar

#### Aggregatie Queries (in Controller)
```php
// Stat cards
GamePlayer::whereNotNull('feedback_rating')->count();
GamePlayer::avg('feedback_rating');
GamePlayer::whereMonth('created_at', now()->month)->count();

// Leeftijdsverdeling (CASE/WHEN)
selectRaw("CASE
    WHEN CAST(feedback_age AS INTEGER) <= 12 THEN '≤12'
    WHEN CAST(feedback_age AS INTEGER) BETWEEN 13 AND 15 THEN '13-15'
    ...
END as age_group, COUNT(*) as count")
->groupBy('age_group')

// Trends (SQLite strftime)
selectRaw("strftime('%Y-%W', created_at) as period, AVG(feedback_rating) as avg")
->groupByRaw("strftime('%Y-%W', created_at)")
```

### UI Components

#### Stat Card Pattern
```blade
<div class="bg-pure-white rounded-card shadow-card p-6">
    <div class="text-surface-dark text-sm">{{ $label }}</div>
    <div class="text-h2 text-forest-700">{{ $value }}</div>
</div>
```

#### Chart.js Initialisatie (Alpine.js)
```blade
<div x-data="{
    chart: null,
    init() {
        this.chart = new Chart(this.$refs.canvas, config);
    }
}">
    <canvas x-ref="canvas"></canvas>
</div>
```

### Files to Create
| File | Purpose |
|------|---------|
| `app/Http/Controllers/Admin/StatisticsController.php` | Aggregatie queries, JSON endpoint voor trends |
| `resources/views/admin/statistics/index.blade.php` | Dashboard met stat cards en Chart.js grafieken |

### Files to Modify
| File | Change |
|------|--------|
| `app/Livewire/PlayerFeedback.php` | Validatie 1-10 → 1-5 |
| `resources/views/livewire/player-feedback.blade.php` | 10 buttons → 5 sterren |
| `routes/web.php` | Route toevoegen: `admin.statistics.index` |
| `resources/views/layouts/admin-navigation.blade.php` | Nav link "Statistieken" toevoegen |

### Dependencies
- **Chart.js 4.x** via CDN (geen npm install nodig)
- **Alpine.js** (reeds aanwezig)
- **Tailwind CSS** (reeds aanwezig, gebruik forest-* kleuren)

### Edge Cases
1. **Geen feedback data**: Dashboard toont "Geen gegevens beschikbaar" message
2. **Lege leeftijdscategorieën**: Grafieken tonen 0 voor ontbrekende categorieën
3. **Nieuwe locatie zonder feedback**: Wordt niet getoond in locatie grafiek
4. **Division by zero**: AVG op lege set retourneert null, handle in view

### Success Criteria
1. PlayerFeedback toont 5 klikbare sterren
2. Rating 1-5 wordt correct opgeslagen in database
3. Statistics dashboard toont 4 stat cards met correcte data
4. Alle 4 grafieken renderen correct met Chart.js
5. Trends dropdown filter werkt via AJAX zonder page reload
6. Dashboard is responsive (stat cards stacken op mobile)
7. Admin navigatie bevat link naar "Statistieken"

---

## Extend: bingo-scoring-config (2025-12-16)

### Overview
Bingo punten configuratie per locatie toevoegen aan bingo items admin pagina. Admin kan instellen hoeveel punten "3 op een rij" en "volle kaart" opleveren.

### Task Type
EXTEND

### Scope
- Config sectie onderaan bingo items pagina (na pagination)
- Twee number inputs: 3-op-een-rij punten, volle kaart punten
- Default waardes: 50 en 100 punten
- Opslag in locations tabel als integer kolommen

### Functional Requirements

#### Database
- `bingo_three_in_row_points` INTEGER kolom op locations tabel (default: 50)
- `bingo_full_card_points` INTEGER kolom op locations tabel (default: 100)

#### Bingo Items Admin Page
- Config sectie onderaan pagina, na pagination
- Twee number inputs naast elkaar
- "Opslaan" button
- Validatie errors inline getoond

#### UI Layout
```
[Bingo items tabel]
[Pagination]

┌─ Bingo Punten Configuratie ─────────────────────────────┐
│                                                          │
│   3 op een rij:  [  50  ] punten    Volle kaart:  [ 100 ] punten   │
│                                                  [ Opslaan ] │
└──────────────────────────────────────────────────────────┘
```

### Testable Requirements

| ID | Description | Category | Test Type | Passes |
|----|-------------|----------|-----------|--------|
| REQ-001 | Bingo items pagina toont config sectie met 3-op-een-rij en volle kaart punten inputs | core | automated_ui | false |
| REQ-002 | Config waardes worden opgeslagen per locatie in database | core | automated_unit | false |
| REQ-003 | Nieuwe locaties krijgen standaard waardes (50 / 100 punten) | core | automated_unit | false |
| REQ-004 | Config sectie staat onderaan pagina, onder de pagination | ui | manual | false |
| REQ-005 | Validatie: punten moeten positieve integers zijn (min 1) | edge_case | automated_unit | false |

### Data Models

#### Location Model Extensions
```php
// Fillable
protected $fillable = [..., 'bingo_three_in_row_points', 'bingo_full_card_points'];

// Casts
protected function casts(): array {
    return [
        ...,
        'bingo_three_in_row_points' => 'integer',
        'bingo_full_card_points' => 'integer',
    ];
}
```

### Validation Rules
- `bingo_three_in_row_points`: required, integer, min:1
- `bingo_full_card_points`: required, integer, min:1

### Files to Create
| File | Purpose |
|------|---------|
| `database/migrations/YYYY_MM_DD_add_bingo_scoring_to_locations_table.php` | Add scoring columns with defaults |
| `app/Http/Requests/UpdateBingoScoreRequest.php` | Validate scoring inputs |

### Files to Modify
| File | Change |
|------|--------|
| `app/Models/Location.php` | Add columns to $fillable and casts() |
| `app/Http/Controllers/Admin/BingoItemController.php` | Add updateScoring() method |
| `routes/web.php` | Add POST route for bingo-scoring |
| `resources/views/admin/bingo-items/index.blade.php` | Add config form section |

### Edge Cases
1. Validatie: punten < 1 → error message
2. Default waardes toegepast op nieuwe locaties via migration
3. Bestaande locaties krijgen default waardes via migration

### Success Criteria
1. Config sectie zichtbaar onderaan bingo items pagina
2. Waardes correct opgeslagen en geladen per locatie
3. Validatie errors correct getoond bij ongeldige input
4. Default waardes (50/100) toegepast op nieuwe en bestaande locaties

---

## Extend: location-url (2025-12-16)

### Overview
URL veld toevoegen aan locaties voor Natuurmonumenten website link. De blauwe header button op de game info pagina moet dynamisch linken naar de locatie-specifieke Natuurmonumenten pagina.

### Task Type
EXTEND

### Scope
- URL veld toevoegen aan Location model
- Admin create/edit formulieren uitbreiden met URL input
- Game info header button dynamisch maken met locatie URL
- Database: nullable kolom (voor bestaande records)
- Formulier: required validatie (voor nieuwe/gewijzigde records)

### Functional Requirements

#### Database
- `url` VARCHAR(255) kolom op locations tabel
- Nullable voor backward compatibility met bestaande records
- Geen default waarde

#### Admin Create/Edit Forms
- URL input veld na distance veld, voor image veld
- Label: "Natuurmonumenten URL"
- Type: url (HTML5 validation)
- Required in formulier
- Validatie: geldige URL format (http/https)

#### Game Info Page
- Header button href wijzigen van hardcoded URL naar `{{ $location->url }}`
- Button text blijft locatie naam (huidige gedrag behouden)
- target="_blank" en rel="noopener noreferrer" behouden

### Testable Requirements

| ID | Description | Category | Test Type | Passes |
|----|-------------|----------|-----------|--------|
| REQ-001 | URL veld toevoegen aan locatie create/edit formulieren in admin panel | core | manual | false |
| REQ-002 | Header button op game info pagina linkt naar locatie URL | core | manual | false |
| REQ-003 | URL veld validatie: required in formulier, geldige URL format | api | automated_api | false |
| REQ-004 | Bestaande locaties zonder URL tonen geen/lege button tot URL ingevuld | edge_case | manual | false |

### Data Models

#### Location Model Extensions
```php
// Fillable
protected $fillable = [..., 'url'];
```

### Validation Rules
- `url`: required, url:http,https, max:255

### Files to Create
| File | Purpose |
|------|---------|
| `database/migrations/YYYY_MM_DD_add_url_to_locations_table.php` | Add nullable url column |

### Files to Modify
| File | Change |
|------|--------|
| `app/Models/Location.php` | Add 'url' to $fillable |
| `app/Http/Requests/StoreLocationRequest.php` | Add url validation rule + Dutch message |
| `app/Http/Requests/UpdateLocationRequest.php` | Add url validation rule + Dutch message |
| `app/Http/Controllers/Admin/LocationController.php` | Add 'url' to safe()->only() in store/update |
| `resources/views/admin/locations/create.blade.php` | Add URL input field |
| `resources/views/admin/locations/edit.blade.php` | Add URL input field |
| `resources/views/games/info.blade.php` | Replace hardcoded URL with {{ $location->url }} |

### Edge Cases
1. Bestaande locaties zonder URL → button toont geen link of fallback
2. Ongeldige URL format → validatie error in formulier
3. Lege URL bij submit → required validation error

### Success Criteria
1. URL veld zichtbaar in admin create/edit formulieren
2. Validatie werkt (required + valid URL format)
3. Game info header button gebruikt dynamische locatie URL
4. Bestaande locaties blijven werken (nullable kolom)
