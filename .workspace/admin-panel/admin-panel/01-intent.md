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
