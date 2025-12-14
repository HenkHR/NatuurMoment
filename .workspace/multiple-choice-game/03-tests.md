# Test Plan - Multiple Choice Game

Generated: 2025-12-14 20:58:48
Requirements: 14 testable (1 for Part 01)

## Requirements Test Matrix

| REQ-ID | Description | Test Type | Automated | Manual Steps | Part |
|--------|-------------|-----------|-----------|--------------|------|
| REQ-001 | Sequential unlock (vraag N+1 na N beantwoord) | automated_ui | ✓ | ✓ | 02 |
| REQ-002 | Antwoord selecteren en submitten | automated_ui | ✓ | ✓ | 02 |
| REQ-003 | Fout antwoord definitief - geen retry | automated_api | ✓ | ✓ | 02 |
| REQ-004 | Goed antwoord kent punten toe | automated_api | ✓ | ✓ | 02 |
| REQ-005 | Vragen kopiëren bij game start | automated_api | ✓ | ✗ | 01 |
| REQ-006 | Feedback indicator (groen/rood) | manual | ✗ | ✓ | 02 |
| REQ-007 | Auto volgende vraag (2s delay) | manual | ✗ | ✓ | 02 |
| REQ-008 | Alleen ingevulde opties tonen | automated_ui | ✓ | ✓ | 02 |
| REQ-009 | Vragen-tab verbergen als geen vragen | manual | ✗ | ✓ | 03 |
| REQ-010 | Vrij switchen tussen tabs | manual | ✗ | ✓ | 03 |
| REQ-011 | Redirect naar bingo na vragen | automated_ui | ✗ | ✓ | 03 |
| REQ-012 | Redirect naar tussenstand na alles | automated_ui | ✗ | ✓ | 03 |
| REQ-013 | Host ziet % vragen beantwoord | manual | ✗ | ✓ | 03 |
| REQ-014 | Duplicate answer prevention | automated_api | ✓ | ✓ | 02 |

---

## Part 01: Foundation Tests

### REQ-005: Vragen kopiëren bij game start
**Category:** core
**Test Type:** automated_api
**Status:** ✓ Implemented

**Test Files:**
- `tests/Feature/Livewire/HostLobbyTest.php`
- `tests/Unit/Models/RouteStopTest.php`

**Automated Tests:**

```php
// tests/Feature/Livewire/HostLobbyTest.php

// REQ-005: Vragen worden gekopieerd van LocationRouteStop naar RouteStop bij game start
it('REQ-005: copies route stops from location to game on start', function () {
    // Creates LocationRouteStops, starts game, verifies RouteStops created
    // Verifies: count, name, question_text, correct_option, points, sequence
});

// REQ-005: Route stops are not duplicated on multiple start attempts
it('REQ-005: does not duplicate route stops if already generated', function () {
    // Pre-creates RouteStop, starts game, verifies no duplicates
});
```

**Unit Tests:**

```php
// tests/Unit/Models/RouteStopTest.php

// getAvailableOptions() tests
it('getAvailableOptions returns only A and B when C and D are null');
it('getAvailableOptions returns A, B, C when D is null');
it('getAvailableOptions returns all 4 options when all are set');

// isAnsweredBy() tests
it('isAnsweredBy returns false when player has not answered');
it('isAnsweredBy returns true when player has answered');
it('isAnsweredBy returns false when different player has answered');

// Relationship tests
it('game has routeStops relationship');
it('game player has routeStopAnswers relationship');
```

**Manual Test Steps (fallback):**
1. Open admin panel, create Location with route stops
2. Create new game for that location
3. Add 1+ players to lobby
4. Start game
5. Check database: route_stops table has records with game_id matching game
6. Verify sequence, question_text, options copied correctly

**Expected Result:**
- RouteStop records exist with correct game_id
- Sequence matches original LocationRouteStop sequence
- All question data copied (name, question_text, options, correct_option, points)

---

## Run Tests

```bash
# Run all Part 01 tests
php artisan test tests/Unit/Models/RouteStopTest.php tests/Feature/Livewire/HostLobbyTest.php --filter="REQ-005"

# Run only unit tests
php artisan test tests/Unit/Models/RouteStopTest.php

# Run only feature tests
php artisan test tests/Feature/Livewire/HostLobbyTest.php
```

---

## Part 02: Player Game Tests
**Status:** ✓ Implemented
**Generated:** 2025-12-14 21:14:28

### Test Files

- `tests/Unit/Models/RouteStopTest.php` - Unit tests for sequential unlock methods
- `tests/Feature/Livewire/PlayerRouteQuestionTest.php` - Feature tests for component

### REQ-001: Sequential unlock (vraag N+1 na N beantwoord)
**Category:** core
**Test Type:** automated_ui
**Status:** ✓ Implemented

**Unit Tests:**
```php
// tests/Unit/Models/RouteStopTest.php
it('REQ-001: getNextUnlocked returns first question for new player');
it('REQ-001: getNextUnlocked returns second question after first answered');
it('REQ-001: getNextUnlocked returns null when all questions answered');
it('REQ-001: isUnlockedFor returns true for first question');
it('REQ-001: isUnlockedFor returns false for second question when first not answered');
it('REQ-001: isUnlockedFor returns true for second question when first answered');
```

**Feature Tests:**
```php
// tests/Feature/Livewire/PlayerRouteQuestionTest.php
it('REQ-001: shows first question to new player');
it('REQ-001: shows second question after first answered');
```

**Manual Test Steps:**
1. Open player route page
2. Verify first question is displayed
3. Answer first question
4. Verify second question appears automatically
5. Try to access question 3 directly (should be locked)

---

### REQ-002: Antwoord selecteren en submitten
**Category:** core
**Test Type:** automated_ui
**Status:** ✓ Implemented

**Feature Tests:**
```php
it('REQ-002: player can select an answer option');
it('REQ-002: player can submit an answer');
```

**Manual Test Steps:**
1. Click on an answer option (radio button)
2. Verify option is highlighted
3. Click "Bevestig antwoord" button
4. Verify answer is submitted

---

### REQ-003: Fout antwoord definitief - geen retry
**Category:** core
**Test Type:** automated_api
**Status:** ✓ Implemented

**Feature Tests:**
```php
it('REQ-003: wrong answer is final - no retry');
```

**Manual Test Steps:**
1. Select wrong answer
2. Submit answer
3. Verify red feedback indicator appears
4. Verify question auto-advances (no retry option)

---

### REQ-004: Goed antwoord kent punten toe
**Category:** core
**Test Type:** automated_api
**Status:** ✓ Implemented

**Feature Tests:**
```php
it('REQ-004: correct answer awards points to player');
it('REQ-004: wrong answer awards zero points');
```

**Manual Test Steps:**
1. Note current score
2. Answer question correctly
3. Verify green feedback shows points awarded
4. Verify score increased in leaderboard

---

### REQ-006: Feedback indicator (groen/rood)
**Category:** ui
**Test Type:** manual
**Status:** Ready for manual testing

**Manual Test Steps:**
1. Answer question correctly
2. Verify green indicator with checkmark appears
3. Verify message shows "Correct! +X punten"
4. Answer question incorrectly
5. Verify red indicator with X appears
6. Verify message shows "Helaas, dat is niet het goede antwoord"

---

### REQ-007: Auto volgende vraag (2s delay)
**Category:** ui
**Test Type:** manual
**Status:** Ready for manual testing

**Manual Test Steps:**
1. Answer a question
2. Verify feedback indicator appears
3. Wait 2 seconds
4. Verify feedback disappears automatically
5. Verify next question is displayed (or completion message)

---

### REQ-008: Alleen ingevulde opties tonen
**Category:** ui
**Test Type:** automated_ui
**Status:** ✓ Implemented

**Feature Tests:**
```php
it('REQ-008: only shows available options (not null)');
```

**Manual Test Steps:**
1. Create question with only 2 options (A and B)
2. Open player route page
3. Verify only options A and B are shown

---

### REQ-014: Duplicate answer prevention
**Category:** edge_case
**Test Type:** automated_api
**Status:** ✓ Implemented

**Feature Tests:**
```php
it('REQ-014: prevents duplicate answer submission');
```

**Manual Test Steps:**
1. Answer a question
2. Try to answer same question via browser manipulation
3. Verify error message "Je hebt deze vraag al beantwoord"

---

### Security Tests

**Feature Tests:**
```php
it('blocks access with invalid player token');
it('blocks access when game is not started');
```

---

### Run Part 02 Tests

```bash
# Run all Part 02 tests
php artisan test tests/Unit/Models/RouteStopTest.php tests/Feature/Livewire/PlayerRouteQuestionTest.php

# Run only unit tests
php artisan test tests/Unit/Models/RouteStopTest.php --filter="REQ-001"

# Run only feature tests
php artisan test tests/Feature/Livewire/PlayerRouteQuestionTest.php
```

---

## Part 03: Integration Tests (Pending)

Tests to implement when Part 03 is developed:
- REQ-009: Tab visibility logic
- REQ-010: Tab switching
- REQ-011: Bingo redirect
- REQ-012: Leaderboard redirect
- REQ-013: Host progress display

---
*Generated by /2-code skill*
