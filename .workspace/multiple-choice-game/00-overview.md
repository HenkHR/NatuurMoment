# Feature: Multiple Choice Game

> Multiple choice vragen spel voor spelers met sequential unlocking - admin kan vragen aanmaken, spelers beantwoorden ze tijdens het spel.

## Quick Reference

| Item | Value |
|------|-------|
| **Status** | `REFINED` |
| **Last Updated** | 2025-12-15 |
| **Requirements** | 14/14 passing |
| **Parts** | 3 total (all verified) |

---

## Overview

This feature implements a multiple choice quiz game integrated with the existing NatuurMoment bingo gameplay. When a host starts a game, questions are copied from location templates to game instances. Players answer questions sequentially - unlocking question N+1 only after answering question N.

### Core Functionality
- Questions copied from LocationRouteStop (template) to RouteStop (instance) at game start
- Image support for questions (copied from template)
- Sequential unlock mechanism for question progression
- Inline feedback on answered options (green/red styling) with auto-advance
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
| Part 03: Integration | ✓ Verified | 2025-12-14 | 5/5 requirements pass (4 fixes applied) |
| Refined | ✓ | 2025-12-15 | UX improvements: image support, inline feedback, progress text |
| Refactored | — | - | N/A |

### Requirements Status

| REQ-ID | Description | Status |
|--------|-------------|--------|
| REQ-001 | Sequential unlock | ✓ Verified |
| REQ-002 | Antwoord selecteren/submitten | ✓ Verified |
| REQ-003 | Fout antwoord definitief | ✓ Verified |
| REQ-004 | Punten toekennen | ✓ Verified |
| REQ-005 | Vragen kopiëren bij game start | ✓ Verified |
| REQ-006 | Feedback indicator | ✓ Verified (fix: Alpine entangle) |
| REQ-007 | Auto volgende vraag | ✓ Verified |
| REQ-008 | Alleen ingevulde opties | ✓ Verified |
| REQ-009 | Tab verbergen | ✓ Verified (fix: leaderboard conditional) |
| REQ-010 | Tab switchen | ✓ Verified |
| REQ-011 | Redirect naar bingo | ✓ Verified (fix: x-init auto-redirect) |
| REQ-012 | Redirect naar tussenstand | ✓ Verified (fix: checkAutoEnd logic) |
| REQ-013 | Host ziet % beantwoord | ✓ Verified |
| REQ-014 | Duplicate prevention | ✓ Verified |

### Current Files

| File | Purpose |
|------|---------|
| [app/Models/RouteStop.php](../../app/Models/RouteStop.php) | Game instance question model with sequential unlock methods |
| [app/Models/RouteStopAnswer.php](../../app/Models/RouteStopAnswer.php) | Player answer tracking |
| [app/Models/GamePlayer.php](../../app/Models/GamePlayer.php) | Player model with completion methods |
| [app/Livewire/HostLobby.php](../../app/Livewire/HostLobby.php) | Game start with route stop generation |
| [app/Livewire/HostGame.php](../../app/Livewire/HostGame.php) | Host dashboard with question progress + checkAutoEnd |
| [app/Livewire/PlayerRouteQuestion.php](../../app/Livewire/PlayerRouteQuestion.php) | Quiz gameplay component with redirect logic |
| [app/Livewire/PlayerLeaderboard.php](../../app/Livewire/PlayerLeaderboard.php) | Leaderboard with completion-aware nav |
| [resources/views/livewire/player-route-question.blade.php](../../resources/views/livewire/player-route-question.blade.php) | Quiz UI with feedback |
| [resources/views/livewire/player-leaderboard.blade.php](../../resources/views/livewire/player-leaderboard.blade.php) | Leaderboard with conditional nav |
| [resources/views/player/route.blade.php](../../resources/views/player/route.blade.php) | Player route page |
| [resources/views/player/bingo.blade.php](../../resources/views/player/bingo.blade.php) | Player bingo page with conditional route tab |

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
    image_path VARCHAR(255) NULL,
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

- No retry mechanism for incorrect answers (by design per REQ-003)

---

## Change History

| Date | Phase | Summary | Details |
|------|-------|---------|---------|
| 2025-12-15 | Refine | UX improvements | Image support, inline answer feedback, progress text, button state |
| 2025-12-14 | Verify | All parts verified | 14/14 requirements pass, 4 fixes applied |
| 2025-12-14 | Code | Part 03: Integration | 5 files modified (tab visibility, redirects, host progress) |
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
| [04-refine.md](04-refine.md) | Refinement history |

---
*Generated by /3-verify skill. Last updated: 2025-12-15*
