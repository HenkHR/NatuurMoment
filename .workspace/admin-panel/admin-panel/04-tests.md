# Test Plan - Admin Panel

Generated: 2025-12-02 14:29:04

## Manual Tests (User Executes)

### Visual Regression Tests

**Instructions:**

1. **Admin Navigation Visibility**
   - Log in als admin gebruiker (admin@example.com)
   - Verify: "Admin" link is zichtbaar in de navigatie
   - Log in als normale gebruiker (test@example.com)
   - Verify: "Admin" link is NIET zichtbaar

2. **Admin Panel Layout**
   - Navigeer naar /admin/locations
   - Verify: Admin navigatie toont "Locaties" en "Games" links
   - Verify: Flash messages verschijnen correct (groen voor success, rood voor error)
   - Test op mobiel formaat: Verify responsieve navigatie werkt

3. **Tabel Styling**
   - Navigeer naar /admin/locations
   - Verify: Tabel heeft correcte header styling (grijze achtergrond)
   - Verify: Rijen hebben hover effect
   - Verify: Acties rechts uitgelijnd

4. **Delete Modal**
   - Klik op "Verwijder" knop bij een locatie
   - Verify: Modal verschijnt met bevestigingsvraag
   - Verify: "Annuleren" sluit modal
   - Verify: Modal is gecentreerd en leesbaar

### User Flow Testing

**1. Locatie CRUD Flow**
   - Step 1: Ga naar /admin/locations
   - Step 2: Klik "Nieuwe locatie"
   - Step 3: Vul naam "Test Locatie" en beschrijving in
   - Step 4: Klik "Opslaan"
   - Expected: Redirect naar index met success message
   - Step 5: Klik "Bewerk" bij de nieuwe locatie
   - Step 6: Wijzig naam naar "Bijgewerkte Test Locatie"
   - Step 7: Klik "Bijwerken"
   - Expected: Redirect naar index met success message
   - Step 8: Klik "Verwijder", bevestig in modal
   - Expected: Locatie verwijderd met success message

**2. Bingo Item CRUD Flow**
   - Step 1: Maak eerst een locatie aan
   - Step 2: Klik op "X items" link in de locatie rij
   - Step 3: Klik "Nieuw bingo item"
   - Step 4: Vul label "Eekhoorn", punten "5"
   - Step 5: Klik "Opslaan"
   - Expected: Item verschijnt in lijst
   - Step 6: Bewerk en verwijder het item
   - Expected: Beide acties werken correct

**3. Vragen CRUD Flow**
   - Step 1: Navigeer naar vragen via locatie
   - Step 2: Maak nieuwe vraag aan met:
     - Naam: "Vraag 1"
     - Vraagtekst: "Wat is de kleur van gras?"
     - Antwoord A: "Rood"
     - Antwoord B: "Groen"
     - Correct antwoord: B
     - Punten: 5
   - Step 3: Klik "Opslaan"
   - Expected: Vraag verschijnt met correct antwoord "B"
   - Step 4: Test bewerken en verwijderen

**4. Games Overzicht Flow**
   - Step 1: Ga naar /admin/games
   - Verify: Lijst toont alle games met PIN, locatie, status
   - Step 2: Klik op "Details" bij een game
   - Verify: Game details pagina toont alle informatie
   - Step 3: Verwijder een game
   - Expected: Game verdwijnt uit lijst

**5. Edge Case: Locatie met Games**
   - Step 1: Zorg dat er een game gekoppeld is aan een locatie
   - Step 2: Probeer de locatie te verwijderen
   - Expected: Error message "Kan locatie niet verwijderen: er zijn nog games gekoppeld"

**6. Validatie Tests**
   - Test: Lege locatie naam
   - Expected: "Naam is verplicht" error
   - Test: Duplicate locatie naam
   - Expected: "Deze locatie naam bestaat al" error
   - Test: Punten = 0 bij bingo item
   - Expected: "Punten moet minimaal 1 zijn" error
   - Test: Ongeldig correct antwoord (bijv. "E")
   - Expected: "Correct antwoord moet A, B, C of D zijn" error

---

## Automated Tests (Claude Code Executes)

### Middleware Tests (5 tests)
```bash
./vendor/bin/pest tests/Feature/Admin/IsAdminMiddlewareTest.php
```

| Test | Description |
|------|-------------|
| admin routes require authentication | Unauthenticated users redirected to login |
| admin routes require admin user | Non-admin gets 403 |
| admin can access admin routes | Admin gets 200 |
| admin link is visible for admin users | Admin sees "Admin" nav link |
| admin link is hidden for non-admin users | Non-admin doesn't see admin link |

### Location Controller Tests (10 tests)
```bash
./vendor/bin/pest tests/Feature/Admin/LocationControllerTest.php
```

| Test | Description |
|------|-------------|
| admin can view locations index | Index page loads with locations |
| admin can view create location form | Create form renders |
| admin can create a location | POST creates location in DB |
| location name is required | Empty name fails validation |
| location name must be unique | Duplicate name fails validation |
| admin can view edit location form | Edit form loads with data |
| admin can update a location | PUT updates location in DB |
| admin can delete a location without games | DELETE removes location |
| admin cannot delete a location with games | DELETE with games shows error |
| deleting location cascades to bingo items and route stops | Related records deleted |

### Bingo Item Controller Tests (8 tests)
```bash
./vendor/bin/pest tests/Feature/Admin/BingoItemControllerTest.php
```

| Test | Description |
|------|-------------|
| admin can view bingo items index for a location | Index shows location's items |
| admin can view create bingo item form | Create form renders |
| admin can create a bingo item | POST creates bingo item |
| bingo item label is required | Empty label fails validation |
| bingo item points must be at least 1 | Points < 1 fails validation |
| admin can view edit bingo item form | Edit form loads |
| admin can update a bingo item | PUT updates bingo item |
| admin can delete a bingo item | DELETE removes bingo item |

### Route Stop Controller Tests (9 tests)
```bash
./vendor/bin/pest tests/Feature/Admin/RouteStopControllerTest.php
```

| Test | Description |
|------|-------------|
| admin can view route stops index for a location | Index shows location's stops |
| admin can view create route stop form | Create form renders |
| admin can create a route stop | POST creates route stop |
| route stop question_text is required | Empty question fails |
| route stop correct_option must be valid | Invalid option fails |
| route stop option_a and option_b are required | Missing options fail |
| admin can view edit route stop form | Edit form loads |
| admin can update a route stop | PUT updates route stop |
| admin can delete a route stop | DELETE removes route stop |

### Game Controller Tests (6 tests)
```bash
./vendor/bin/pest tests/Feature/Admin/GameControllerTest.php
```

| Test | Description |
|------|-------------|
| admin can view games index | Index page loads with games |
| admin can view game details | Show page renders |
| admin can delete a game | DELETE removes game |
| games index shows correct status badges | Status badges display |
| games index shows location name | Location name visible |
| game show page displays timestamps | Timestamps shown |

### Run All Admin Tests
```bash
./vendor/bin/pest tests/Feature/Admin/
```

**Expected result:** 38 tests passed

## Pre-Deployment Checklist

- [ ] Run migrations: `php artisan migrate`
- [ ] Seed admin user: `php artisan db:seed`
- [ ] Run all tests: `./vendor/bin/pest tests/Feature/Admin/`
- [ ] Build assets: `npm run build`
- [ ] Clear caches: `php artisan config:clear && php artisan view:clear`

---

## Extend: search-filter Tests (2025-12-15)

### Requirements Test Matrix

| REQ-ID | Description | Test Type | Automated | Manual |
|--------|-------------|-----------|-----------|--------|
| REQ-001 | Search bar filtert op regio | automated_ui | ✓ | ✓ |
| REQ-002 | Regio filter dropdown | automated_ui | ✓ | ✓ |
| REQ-003 | Locaties pagination 15/page | automated_ui | ✓ | - |
| REQ-004 | Bingo items pagination 15/page | automated_ui | ✓ | - |
| REQ-005 | Vragen pagination 15/page | automated_ui | ✓ | - |
| REQ-006 | Filters behouden state | automated_ui | ✓ | ✓ |
| REQ-007 | Lege resultaten melding | edge_case | ✓ | ✓ |
| REQ-008 | Pagination totaal pages | automated_unit | ✓ | - |

### Manual Tests

#### REQ-001: Search Bar Filtert op Regio
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/locations
2. Typ "Noord" in de search bar
3. Klik op "Zoek" knop

**Expected Result:**
- Alleen locaties met "Noord" in de province worden getoond
- Locaties met andere provincies zijn verborgen

---

#### REQ-002: Regio Filter Dropdown
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/locations
2. Selecteer "Gelderland" in de regio dropdown
3. Klik op "Zoek" knop

**Expected Result:**
- Alleen locaties in Gelderland worden getoond
- Dropdown bevat alle 12 provincies uit config/provinces.php

---

#### REQ-006: Filters Behouden State
**Category:** ui
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/locations?search=Noord&regio=Utrecht
2. Klik op pagina 2 in pagination
3. Bekijk URL

**Expected Result:**
- URL bevat `search=Noord&regio=Utrecht&page=2`
- Filters blijven ingevuld in de form

---

#### REQ-007: Lege Resultaten Melding
**Category:** edge_case
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/locations
2. Zoek op een niet-bestaande provincie: "NonExistent"
3. Klik op "Zoek"

**Expected Result:**
- Melding: "Geen locaties gevonden voor deze filters"
- "Probeer andere zoektermen of filters" hint wordt getoond
- "Wis filters" knop is zichtbaar

---

### Automated Tests

#### Location Controller Tests (8 nieuwe tests)
```bash
./vendor/bin/pest tests/Feature/Admin/LocationControllerTest.php --filter="REQ-00"
```

| Test | REQ-ID | Description |
|------|--------|-------------|
| REQ-001: locations can be filtered by search on province | REQ-001 | Zoek filtert op province |
| REQ-002: locations index passes provinces config to view | REQ-002 | Provinces uit config in view |
| REQ-003: locations index paginates with 15 items per page | REQ-003 | 15 items per pagina |
| REQ-006: filters and pagination preserve query string | REQ-006 | Query params behouden |
| REQ-007: shows empty state when no locations match filter | REQ-007 | Lege resultaten melding |
| REQ-008: pagination shows correct total pages | REQ-008 | Correcte lastPage |
| locations can be filtered by regio dropdown | - | Regio filter werkt |
| hasFilters is true when search or regio provided | - | hasFilters boolean check |

#### Bingo Item Controller Tests (2 nieuwe tests)
```bash
./vendor/bin/pest tests/Feature/Admin/BingoItemControllerTest.php --filter="REQ-004"
```

| Test | REQ-ID | Description |
|------|--------|-------------|
| REQ-004: bingo items index paginates with 15 items per page | REQ-004 | 15 items per pagina |
| bingo items pagination preserves query string | - | withQueryString werkt |

#### Route Stop Controller Tests (2 nieuwe tests)
```bash
./vendor/bin/pest tests/Feature/Admin/RouteStopControllerTest.php --filter="REQ-005"
```

| Test | REQ-ID | Description |
|------|--------|-------------|
| REQ-005: route stops index paginates with 15 items per page | REQ-005 | 15 items per pagina |
| route stops pagination preserves query string | - | withQueryString werkt |

### Run All Search-Filter Tests
```bash
./vendor/bin/pest tests/Feature/Admin/ --filter="REQ-00"
```

**Expected result:** 12 new tests passed (in addition to existing 38)
