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

#### REQ-001: Search Bar Live Filtering
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/locations
2. Typ "Noord" in de search bar (zonder knop te klikken)
3. Wacht 400ms

**Expected Result:**
- Pagina refresht automatisch na stoppen met typen
- Alleen locaties met "Noord" in de naam of province worden getoond
- Search input behoudt focus en cursor positie

---

#### REQ-002: Regio Filter Dropdown
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/locations
2. Selecteer "Gelderland" in de regio dropdown

**Expected Result:**
- Pagina refresht direct bij selectie (geen knop nodig)
- Alleen locaties in Gelderland worden getoond
- Dropdown bevat alle 12 provincies uit config/provinces.php
- Search bar zoekt nu alleen op naam (niet meer op regio)

---

#### REQ-006: Filters Behouden State
**Category:** ui
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/locations
2. Typ "Veluwe" in search bar (wacht op refresh)
3. Selecteer een regio in dropdown
4. Klik op pagina 2 in pagination
5. Bekijk URL

**Expected Result:**
- URL bevat `search=Veluwe&regio=...&page=2`
- Filters blijven ingevuld in de form
- Focus blijft behouden bij typing

---

#### REQ-007: Lege Resultaten Melding
**Category:** edge_case
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/locations
2. Typ een niet-bestaande naam: "XYZNonExistent"
3. Wacht op refresh

**Expected Result:**
- Melding: "Geen locaties gevonden voor deze filters"
- "Probeer andere zoektermen of filters" hint wordt getoond
- Search input behoudt focus om direct te kunnen corrigeren

---

#### Live Filter UX Tests
**Category:** ux
**Test Type:** manual

**Test Steps:**
1. Typ snel meerdere karakters in search bar
2. Wis de search bar volledig

**Expected Result:**
- Debouncing: pagina refresht pas 400ms na stoppen met typen
- Focus blijft behouden ook bij lege search
- Smooth typing ervaring zonder onderbrekingen

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

---

## Extend: game-modes Tests (2025-12-15)

### Requirements Test Matrix

| REQ-ID | Description | Test Type | Automated | Manual |
|--------|-------------|-----------|-----------|--------|
| GM-REQ-001 | Location has game_modes JSON field | automated_unit | ✓ | - |
| GM-REQ-002 | Bingo mode requires min 9 bingo items | automated_unit | ✓ | - |
| GM-REQ-003 | Vragen mode requires min 1 question | automated_unit | ✓ | - |
| GM-REQ-004 | Edit/create shows toggle switches | automated_ui | ✓ | ✓ |
| GM-REQ-005 | Toggle shows status indicator with count | automated_ui | ✓ | ✓ |
| GM-REQ-006 | New location has all modes OFF | automated_unit | ✓ | - |
| GM-REQ-007 | Table shows red text for counts under minimum | automated_ui | ✓ | ✓ |
| GM-REQ-008 | Table shows warning badge when incomplete modes | automated_ui | ✓ | ✓ |
| GM-REQ-009 | Location without valid modes not visible on home | automated_integration | ✓ | ✓ |
| GM-REQ-010 | Bingo selects random 9 items if > 9 available | - | - | - |

### Manual Tests

#### GM-REQ-004: Toggle Switches on Create/Edit Pages
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/locations/create
2. Bekijk het "Spelmodi" gedeelte

**Expected Result:**
- "Bingo modus" toggle is zichtbaar en UIT
- "Vragen modus" toggle is zichtbaar en UIT
- Tekst toont minimum vereisten (9 items / 1 vraag)

---

#### GM-REQ-005: Status Indicator on Edit Page
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Maak een locatie met bingo modus AAN en 5 bingo items
2. Navigeer naar edit page

**Expected Result:**
- Bingo toggle toont ⚠️ indicator
- Counter toont "5/9 items"
- Oranje styling voor onvoldoende content

---

#### GM-REQ-007/008: Red Counts & Warning Badge on Index
**Category:** ui
**Test Type:** manual

**Test Steps:**
1. Maak een locatie met bingo modus AAN en slechts 3 bingo items
2. Navigeer naar /admin/locations

**Expected Result:**
- Bingo items count "3" is rood gekleurd
- ⚠️ badge verschijnt na locatienaam
- Tooltip toont "Actieve spelmodus heeft onvoldoende content"

---

#### GM-REQ-009: Home Page Filtering
**Category:** integration
**Test Type:** manual

**Test Steps:**
1. Maak locatie A met bingo modus AAN + 10 bingo items (valide)
2. Maak locatie B met bingo modus AAN + 5 bingo items (invalide)
3. Maak locatie C zonder actieve modi
4. Navigeer naar homepage (/)

**Expected Result:**
- Locatie A is zichtbaar
- Locatie B is NIET zichtbaar
- Locatie C is NIET zichtbaar

---

### Automated Tests

#### Location Controller Tests (12 nieuwe tests)
```bash
./vendor/bin/pest tests/Feature/Admin/LocationControllerTest.php --filter="GM-REQ"
```

| Test | REQ-ID | Description |
|------|--------|-------------|
| GM-REQ-001: location has game_modes JSON field | GM-REQ-001 | JSON array cast werkt |
| GM-REQ-002: bingo mode requires min 9 bingo items to be valid | GM-REQ-002 | Validatie logica accessor |
| GM-REQ-003: vragen mode requires min 1 question to be valid | GM-REQ-003 | Validatie logica accessor |
| GM-REQ-004: edit page shows toggle switches for game modes | GM-REQ-004 | UI elements aanwezig |
| GM-REQ-005: toggle shows status indicator with count | GM-REQ-005 | Count indicator in view |
| GM-REQ-006: new location has all modes OFF by default | GM-REQ-006 | Factory default empty array |
| GM-REQ-007: index table shows red styling for counts under minimum | GM-REQ-007 | Red CSS class applied |
| GM-REQ-008: index table shows warning badge when incomplete active modes | GM-REQ-008 | Warning badge title present |
| GM-REQ-006: admin can save location with game modes | GM-REQ-006 | Store/update game_modes |
| location has_valid_game_mode returns true when at least one mode is valid | - | Accessor logica |
| location has_incomplete_active_mode returns true when enabled mode has insufficient content | - | Accessor logica |
| location scopeWithValidGameModes filters correctly | GM-REQ-009 | Scope filtering |

### Run All Game-Modes Tests
```bash
./vendor/bin/pest tests/Feature/Admin/LocationControllerTest.php --filter="GM-REQ"
```

**Expected result:** 12 new tests passed (total ~62 tests in admin suite)

### Pre-Deployment Checklist (game-modes)

- [ ] Run migration: `php artisan migrate`
- [ ] Run tests: `./vendor/bin/pest tests/Feature/Admin/LocationControllerTest.php --filter="GM-REQ"`
- [ ] Verify: Edit page shows toggles with status indicators
- [ ] Verify: Index page shows red counts and warning badges
- [ ] Verify: Homepage filters out locations without valid modes

---

## Extend: settings-page Tests (2025-12-15)

### Requirements Test Matrix

| REQ-ID | Description | Test Type | Automated | Manual |
|--------|-------------|-----------|-----------|--------|
| SET-001 | Settings page accessible via /settings | manual | - | ✓ |
| SET-002 | Per-page dropdown shows 10, 15, 25, 50, 100 | manual | - | ✓ |
| SET-003 | Per-page preference persists after save | manual | - | ✓ |
| SET-004 | Admin index pages use saved preference | manual | - | ✓ |
| SET-005 | Navigation shows "Instellingen" link | manual | - | ✓ |

### Manual Tests

#### SET-001: Settings Page Access
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Log in als admin
2. Klik op je naam in de navbar
3. Klik op "Instellingen"

**Expected Result:**
- URL is `/settings`
- Pagina toont "Instellingen" als titel
- Eerste sectie is "Admin voorkeuren"
- Overige secties: Profile, Wachtwoord, Account verwijderen

---

#### SET-002/003: Per-Page Preference
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Ga naar /settings
2. Selecteer "25 items" in de per-page dropdown
3. Klik "Opslaan"
4. Refresh de pagina

**Expected Result:**
- Melding "Opgeslagen." verschijnt tijdelijk
- Na refresh is "25 items" nog steeds geselecteerd
- Preference is opgeslagen in database

---

#### SET-004: Admin Index Uses Preference
**Category:** integration
**Test Type:** manual

**Test Steps:**
1. Ga naar /settings en stel "50 items" in
2. Navigeer naar /admin/locations
3. Bekijk de pagination

**Expected Result:**
- Maximaal 50 locaties per pagina worden getoond
- Pagination past zich aan op basis van nieuwe per-page waarde

---

#### SET-005: Navigation Link
**Category:** ui
**Test Type:** manual

**Test Steps:**
1. Log in als admin
2. Klik op je naam in de navbar (desktop en mobiel)

**Expected Result:**
- Dropdown toont "Instellingen" (niet "Profile")
- Link navigeert naar /settings

---

### Pre-Deployment Checklist (settings-page)

- [ ] Run migration: `php artisan migrate`
- [ ] Verify: Alpine.js dropdown werkt in navbar
- [ ] Verify: Settings pagina toont per-page dropdown
- [ ] Verify: Opslaan werkt en toont success message
- [ ] Verify: Admin index pages respecteren preference
- [ ] Rebuild assets: `npm run dev` of `npm run build`

---

## Extend: survey-statistieken Tests (2025-12-15)

### Requirements Test Matrix

| REQ-ID | Description | Test Type | Automated | Manual |
|--------|-------------|-----------|-----------|--------|
| SS-REQ-001 | Feedback formulier toont 1-5 sterren | manual | - | ✓ |
| SS-REQ-002 | Statistieken dashboard beschikbaar | automated_ui | ✓ | - |
| SS-REQ-003 | 4 stat cards tonen correct | automated_ui | ✓ | ✓ |
| SS-REQ-004 | Leeftijdsverdeling staafdiagram | manual | - | ✓ |
| SS-REQ-005 | Tevredenheid per leeftijd grafiek | manual | - | ✓ |
| SS-REQ-006 | Trends lijndiagram met AJAX filter | automated_api | ✓ | ✓ |
| SS-REQ-007 | Rating per locatie horizontaal | manual | - | ✓ |
| SS-REQ-008 | Rating opgeslagen als 1-5 | automated_unit | ✓ | - |
| SS-REQ-009 | Leeftijd in 5 groepen | automated_unit | ✓ | - |
| SS-REQ-010 | Aggregatie queries correct | automated_unit | ✓ | - |
| SS-REQ-011 | Lege staat message | automated_ui | ✓ | ✓ |
| SS-REQ-012 | Grafieken met 0 responses | manual | - | ✓ |

### Manual Tests

#### SS-REQ-001: Sterren Rating Formulier
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Start een game en speel tot het einde
2. Na afloop verschijnt het feedback formulier
3. Bekijk de rating vraag

**Expected Result:**
- 5 ster-iconen worden getoond (niet 10 knoppen)
- Sterren zijn klikbaar en worden geel bij selectie
- Hover effect toont potentiele selectie
- "X/5 sterren" indicator verschijnt na selectie

---

#### SS-REQ-003: Stat Cards Visueel
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Zorg dat er feedback data is (speel een game en geef feedback)
2. Log in als admin
3. Navigeer naar /admin/statistics

**Expected Result:**
- 4 stat cards worden getoond in een rij
- "Totaal Responses" toont het totaal aantal feedback entries
- "Gemiddelde Rating" toont gemiddelde met sterren visualisatie
- "Deze Maand" toont responses van huidige maand
- "Meest Actieve Locatie" toont locatienaam met aantal

---

#### SS-REQ-004/005/007: Grafiek Visualisatie
**Category:** ui
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/statistics met feedback data
2. Bekijk alle 4 grafieken

**Expected Result:**
- Leeftijdsverdeling: staafdiagram met 5 categorieën (≤12, 13-15, 16-18, 19-21, 22+)
- Tevredenheid per leeftijd: staafdiagram met gemiddelde rating per leeftijdscategorie
- Trends: lijndiagram met rating over tijd
- Rating per locatie: horizontaal staafdiagram met locaties

---

#### SS-REQ-006: Trends AJAX Filter
**Category:** core
**Test Type:** manual

**Test Steps:**
1. Navigeer naar /admin/statistics
2. Klik op de period dropdown bij "Trends Over Tijd"
3. Selecteer "Per Week"
4. Selecteer "Per Jaar"

**Expected Result:**
- Grafiek update zonder page refresh
- Labels veranderen naar week/maand/jaar format
- Data punten veranderen op basis van aggregatie periode

---

#### SS-REQ-011: Lege Staat
**Category:** edge_case
**Test Type:** manual

**Test Steps:**
1. Zorg dat er geen feedback data is (verse database)
2. Navigeer naar /admin/statistics

**Expected Result:**
- Melding "Geen feedback gegevens" wordt getoond
- Geen grafieken of stat cards zichtbaar
- Uitleg dat statistieken verschijnen na eerste feedback

---

#### SS-REQ-012: Grafieken met Lege Categorieën
**Category:** edge_case
**Test Type:** manual

**Test Steps:**
1. Maak feedback met alleen spelers van 10 jaar oud
2. Navigeer naar /admin/statistics

**Expected Result:**
- Leeftijdsverdeling toont alleen ≤12 categorie met data
- Andere categorieën tonen 0 (geen error)
- Grafieken renderen correct zonder crashes

---

### Automated Tests

#### StatisticsController Tests (17 tests)
```bash
./vendor/bin/pest tests/Feature/Admin/StatisticsControllerTest.php
```

| Test | REQ-ID | Description |
|------|--------|-------------|
| REQ-002: admin can view statistics dashboard | SS-REQ-002 | Dashboard laadt voor admin |
| non-admin cannot view statistics dashboard | - | 403 voor non-admin |
| guest cannot view statistics dashboard | - | Redirect naar login |
| REQ-011: dashboard shows empty state | SS-REQ-011 | Lege staat bij geen data |
| REQ-003: dashboard shows total responses | SS-REQ-003 | Totaal responses correct |
| REQ-003: dashboard shows average rating | SS-REQ-003 | Gemiddelde berekening correct |
| REQ-003: dashboard shows responses this month | SS-REQ-003 | Maand filter werkt |
| REQ-003: dashboard shows most active location | SS-REQ-003 | Meest actieve locatie correct |
| REQ-009: age groups categorized correctly | SS-REQ-009 | 5 leeftijdscategorieën werken |
| REQ-009: age distribution handles empty categories | SS-REQ-009 | 0 voor lege categorieën |
| REQ-010: aggregation calculates satisfaction by age | SS-REQ-010 | AVG per leeftijdsgroep correct |
| REQ-010: rating by location aggregation correct | SS-REQ-010 | AVG per locatie correct |
| REQ-006: trends AJAX endpoint returns JSON | SS-REQ-006 | JSON response format |
| REQ-006: trends AJAX accepts week period | SS-REQ-006 | Week period werkt |
| REQ-006: trends AJAX accepts year period | SS-REQ-006 | Year period werkt |
| REQ-008: players only have ratings 1-5 | SS-REQ-008 | Rating range verificatie |

### Run All Survey-Statistieken Tests
```bash
./vendor/bin/pest tests/Feature/Admin/StatisticsControllerTest.php
```

**Expected result:** 17 tests passed

### Pre-Deployment Checklist (survey-statistieken)

- [x] Run tests: `./vendor/bin/pest tests/Feature/Admin/StatisticsControllerTest.php` ✓ 16 tests passed
- [x] Verify: Feedback formulier toont 5 sterren ✓
- [x] Verify: Admin navigatie toont "Statistieken" link ✓
- [x] Verify: Dashboard toont 4 stat cards ✓
- [x] Verify: 4 Chart.js grafieken renderen correct ✓
- [x] Verify: Trends AJAX filter werkt zonder page refresh ✓
- [x] Verify: Empty state verschijnt bij geen feedback data ✓

### Verification Summary (2025-12-15)

**All requirements PASSED:**
- SS-REQ-001: Sterren rating formulier ✓
- SS-REQ-002: Statistics dashboard accessible ✓ (automated)
- SS-REQ-003: 4 stat cards ✓
- SS-REQ-004: Leeftijdsverdeling staafdiagram ✓
- SS-REQ-005: Tevredenheid per leeftijd ✓
- SS-REQ-006: Trends lijndiagram + AJAX filter ✓
- SS-REQ-007: Rating per locatie horizontaal ✓
- SS-REQ-008-010: Automated tests ✓
- SS-REQ-011: Lege staat message ✓
- SS-REQ-012: Grafieken met 0 responses ✓

**Bug fixes during verification:**
1. CRITICAL: Alpine.js double initialization fixed (broke wire:click on Livewire pages)
   - Added defensive initialization to app.js
   - Created docs/ALPINE_ARCHITECTURE.md
2. Data cleanup: Old 1-10 scale ratings removed
3. Chart.js Y-axis: 0.5 step gridlines with integer labels (1,2,3,4,5)
4. Dropdown styling: Fixed icon overlap in trend period selector
