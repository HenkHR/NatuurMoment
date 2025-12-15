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
| REQ-001 | Locaties overzicht toont search bar die filtert op regio | core | automated_ui | false |
| REQ-002 | Locaties overzicht toont regio filter dropdown met zelfde opties als home | core | automated_ui | false |
| REQ-003 | Locaties overzicht toont pagination met 15 items per pagina | core | automated_ui | false |
| REQ-004 | Bingo items per locatie toont pagination met 15 items per pagina | core | automated_ui | false |
| REQ-005 | Vragen per locatie toont pagination met 15 items per pagina | ui | automated_ui | false |
| REQ-006 | Filters en pagination behouden state bij navigeren | ui | automated_ui | false |
| REQ-007 | Lege resultaten tonen "geen resultaten" melding | edge_case | automated_ui | false |
| REQ-008 | Pagination toont correcte totaal aantal pagina's | edge_case | automated_unit | false |

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
| REQ-001 | Locatie heeft game_modes JSON veld met enabled modes | core | automated_unit | false |
| REQ-002 | Bingo modus vereist minimaal 9 bingo items | core | automated_unit | false |
| REQ-003 | Vragen modus vereist minimaal 1 vraag | core | automated_unit | false |
| REQ-004 | Edit/create pagina toont toggle switches per game mode | ui | automated_ui | false |
| REQ-005 | Toggle toont status indicator (✓/⚠️) met count | ui | automated_ui | false |
| REQ-006 | Nieuwe locatie heeft alle modes standaard UIT | core | automated_unit | false |
| REQ-007 | Tabel toont rode tekst voor counts onder minimum | ui | automated_ui | false |
| REQ-008 | Tabel toont ⚠️ badge achter naam bij incomplete actieve modes | ui | automated_ui | false |
| REQ-009 | Locatie zonder valide actieve modes niet zichtbaar op home | core | automated_api | false |
| REQ-010 | Bingo selecteert random 9 items als er meer dan 9 zijn | core | automated_unit | false |

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
