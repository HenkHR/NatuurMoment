# Feature: Multiple Choice Game

> Multiple choice vragen spel voor spelers met sequential unlocking - admin kan vragen aanmaken, spelers beantwoorden ze tijdens het spel.

## Quick Reference

| Item | Value |
|------|-------|
| **Status** | `PARTIALLY VERIFIED` |
| **Last Updated** | 2025-12-14 |
| **Requirements** | 9/14 passing (Part 01+02 verified) |
| **Parts** | 3 total (2 verified, 1 pending) |

---

## Overview

This feature implements a multiple choice quiz game integrated with the existing NatuurMoment bingo gameplay. When a host starts a game, questions are copied from location templates to game instances. Players answer questions sequentially - unlocking question N+1 only after answering question N.

### Core Functionality
- Questions copied from LocationRouteStop (template) to RouteStop (instance) at game start
- Sequential unlock mechanism for question progression
- Feedback display (correct/incorrect) with auto-advance
- Integration with bingo and leaderboard flows

---

## How to Use

### Database Models

| Model | Table | Key Relationships |
|-------|-------|-------------------|
| RouteStop | route_stops | belongsTo Game, hasMany RouteStopAnswers |
| RouteStopAnswer | route_stop_answers | belongsTo GamePlayer, belongsTo RouteStop |

### Public Interfaces

```php
// RouteStop model
use App\Models\RouteStop;

// Check if player has answered
$routeStop->isAnsweredBy($gamePlayerId); // bool

// Get available options (filters null options)
$routeStop->getAvailableOptions(); // ['A' => 'text', 'B' => 'text', ...]

// Sequential unlock (Part 02)
RouteStop::getNextUnlocked($gameId, $playerId); // ?RouteStop - next unlocked question
$routeStop->isUnlockedFor($playerId); // bool - check if question is unlocked

// Relationships
$game->routeStops;                  // Collection of RouteStop
$player->routeStopAnswers;          // Collection of RouteStopAnswer
```

### Livewire Component (Part 02)

```php
// Use in blade view
@livewire('player-route-question', ['gameId' => $gameId, 'playerToken' => $playerToken])

// The component handles:
// - Sequential unlock logic
// - Answer submission with validation
// - Feedback display (correct/incorrect)
// - Auto-advance after 2 seconds
// - Score calculation
```

### Game Start Integration

```php
// In HostLobby::startGame()
// Route stops are automatically generated alongside bingo items
$this->generateRouteStops($game);
```

---

## Current State

### Pipeline Status

| Phase | Status | Date | Notes |
|-------|--------|------|-------|
| Part 01: Foundation | ✓ Verified | 2025-12-14 | REQ-005 passes |
| Part 02: Player Game | ✓ Verified | 2025-12-14 | 8/8 requirements pass |
| Part 03: Integration | — | - | Pending implementation |
| Refined | — | - | N/A |
| Refactored | — | - | N/A |

### Requirements Status

| REQ-ID | Description | Status |
|--------|-------------|--------|
| REQ-001 | Sequential unlock | ✓ Verified |
| REQ-002 | Antwoord selecteren/submitten | ✓ Verified |
| REQ-003 | Fout antwoord definitief | ✓ Verified |
| REQ-004 | Punten toekennen | ✓ Verified |
| REQ-005 | Vragen kopiëren bij game start | ✓ Verified |
| REQ-006 | Feedback indicator | ✓ Verified |
| REQ-007 | Auto volgende vraag | ✓ Verified |
| REQ-008 | Alleen ingevulde opties | ✓ Verified |
| REQ-009 | Tab verbergen | Pending (Part 03) |
| REQ-010 | Tab switchen | Pending (Part 03) |
| REQ-011 | Redirect naar bingo | Pending (Part 03) |
| REQ-012 | Redirect naar tussenstand | Pending (Part 03) |
| REQ-013 | Host ziet % beantwoord | Pending (Part 03) |
| REQ-014 | Duplicate prevention | ✓ Verified |

### Current Files

| File | Purpose |
|------|---------|
| [app/Models/RouteStop.php](../../app/Models/RouteStop.php) | Game instance question model with sequential unlock methods |
| [app/Models/RouteStopAnswer.php](../../app/Models/RouteStopAnswer.php) | Player answer tracking |
| [app/Livewire/HostLobby.php](../../app/Livewire/HostLobby.php) | Game start with route stop generation |
| [app/Livewire/PlayerRouteQuestion.php](../../app/Livewire/PlayerRouteQuestion.php) | Quiz gameplay component (Part 02) |
| [resources/views/livewire/player-route-question.blade.php](../../resources/views/livewire/player-route-question.blade.php) | Quiz UI with feedback (Part 02) |
| [resources/views/player/route.blade.php](../../resources/views/player/route.blade.php) | Player route page (integrates component) |

---

## Technical Details

### Architecture Decisions

| Decision | Rationale |
|----------|-----------|
| Template→Instance Pattern | Separates location configuration from game state, allows per-game customization |
| Helper methods on model | `isAnsweredBy()` and `getAvailableOptions()` encapsulate common query logic |
| Database unique constraint | `[game_player_id, route_stop_id]` prevents duplicate answers at DB level |

### Database Schema

```sql
-- route_stops table
CREATE TABLE route_stops (
    id BIGINT PRIMARY KEY,
    game_id BIGINT REFERENCES games(id),
    name VARCHAR(255),
    question_text TEXT,
    option_a VARCHAR(255),
    option_b VARCHAR(255),
    option_c VARCHAR(255) NULL,
    option_d VARCHAR(255) NULL,
    correct_option ENUM('A', 'B', 'C', 'D'),
    points INT DEFAULT 1,
    sequence INT,
    INDEX (game_id, sequence)
);

-- route_stop_answers table
CREATE TABLE route_stop_answers (
    id BIGINT PRIMARY KEY,
    game_player_id BIGINT REFERENCES game_players(id),
    route_stop_id BIGINT REFERENCES route_stops(id),
    chosen_option ENUM('A', 'B', 'C', 'D'),
    is_correct BOOLEAN DEFAULT FALSE,
    score_awarded INT,
    answered_at TIMESTAMP,
    UNIQUE (game_player_id, route_stop_id)
);
```

### Known Limitations

- `image_path` exists in `location_route_stops` but not in `route_stops` - images not copied to game instance
- No retry mechanism for incorrect answers (by design per REQ-003)

---

## Change History

| Date | Phase | Summary | Details |
|------|-------|---------|---------|
| 2025-12-14 | Verify | Part 01 + Part 02 verified | 9/14 requirements pass (code review) |
| 2025-12-14 | Code | Part 02: Player Game | 3 files created, 3 files modified |
| 2025-12-14 | Code | Part 01: Foundation | 2 models created, 3 files modified |

---

## Related Documents

| Document | Purpose |
|----------|---------|
| [01-intent.md](01-intent.md) | Requirements & intent |
| [01-research.md](01-research.md) | Research findings |
| [01-architecture.md](01-architecture.md) | Architecture blueprint |
| [02-implementation.md](02-implementation.md) | Implementation log |
| [03-tests.md](03-tests.md) | Test plan |

---
*Generated by /2-code skill. Last updated: 2025-12-14 21:23:13*
