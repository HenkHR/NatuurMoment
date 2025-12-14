# Multiple Choice Game - Architecture

## Selected Approach
**PRAGMATIC BALANCE**

Balance between speed and quality, follows existing patterns with enough abstraction for testability and maintainability.

## Philosophy
- Follow existing codebase patterns (Template→Instance, token auth, polling)
- Use query scopes for testable sequential unlock logic
- Database constraints for data integrity
- Single Livewire component for simplicity
- Extend existing HostGame rather than create new host view

## Design Overview

```
┌─────────────────────────────────────────────────────────────┐
│                      GAME START (HostLobby)                 │
│  generateBingoItems() + generateRouteStops()                │
└────────────────┬───────────────────────────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────────────────────────┐
│              TEMPLATE → INSTANCE COPY                      │
│  LocationRouteStop::replicate() → RouteStop (with game_id) │
│  Preserves: sequence, question_text, options, points       │
└────────────────┬───────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│                   PLAYER FLOW COMPONENT                      │
│                  PlayerRouteQuestion                         │
│  - Loads unlocked question via scope                        │
│  - Displays options A/B/C/D (only non-null)                 │
│  - Submits answer → RouteStopAnswer                         │
│  - Shows feedback (green/red) for 2s                        │
│  - Auto-advances or redirects                                │
└─────────────────┬────────────────────────────────────────────┘
                  │
                  ▼
┌──────────────────────────────────────────────────────────────┐
│              SEQUENTIAL UNLOCK LOGIC                         │
│  RouteStop::unlocked($gamePlayerId) scope                   │
│  - Question N+1 unlocked IF RouteStopAnswer exists for N    │
│  - Returns null if all answered (trigger redirect)          │
└──────────────────┬───────────────────────────────────────────┘
                   │
                   ▼
┌──────────────────────────────────────────────────────────────┐
│               ANSWER SUBMISSION                              │
│  - Validate: not duplicate (unique DB index)                │
│  - Check correct_option == chosen_option                    │
│  - Score: is_correct ? points : 0                           │
│  - Update GamePlayer.score via increment()                  │
│  - Dispatch feedback event to Alpine.js                     │
└──────────────────┬───────────────────────────────────────────┘
                   │
                   ▼
┌──────────────────────────────────────────────────────────────┐
│              COMPLETION & REDIRECT LOGIC                     │
│  - All questions answered? → redirect to bingo              │
│  - Bingo done (9 photos)? → redirect to leaderboard         │
└──────────────────────────────────────────────────────────────┘
                   │
                   ▼
┌──────────────────────────────────────────────────────────────┐
│               HOST DASHBOARD UPDATES                         │
│  HostGame component (existing, extended)                    │
│  - Add route stop progress: "Vragen: X%" per player         │
│  - Aggregate: withCount('routeStopAnswers')                 │
│  - Poll every 5s with wire:poll.5s.visible                  │
└──────────────────────────────────────────────────────────────┘
```

## Files to Create

| File | Purpose | Dependencies |
|------|---------|--------------|
| `app/Models/RouteStop.php` | Game question instance model | Game, RouteStopAnswer |
| `app/Models/RouteStopAnswer.php` | Player answer tracking | GamePlayer, RouteStop |
| `app/Livewire/PlayerRouteQuestion.php` | Quiz component | RouteStop, RouteStopAnswer |
| `resources/views/livewire/player-route-question.blade.php` | Quiz UI | Alpine.js, Tailwind |
| `tests/Unit/Models/RouteStopTest.php` | Unit tests for unlock scope | PHPUnit |
| `tests/Feature/Livewire/PlayerRouteQuestionTest.php` | Feature tests | PHPUnit, Livewire |

## Files to Modify

| File | Change | Lines |
|------|--------|-------|
| `app/Livewire/HostLobby.php` | Add generateRouteStops() call | ~147 |
| `app/Livewire/HostGame.php` | Add route stop progress to loadPlayers() | ~172-183 |
| `app/Models/Game.php` | Add routeStops() relationship | ~33 |
| `app/Models/GamePlayer.php` | Add routeStopAnswers() relationship | ~25 |
| `resources/views/livewire/host-game.blade.php` | Display "Vragen: X%" | player list |
| `resources/views/player/route.blade.php` | Replace TODO with component | ~32-38 |
| `resources/views/components/player-navigation.blade.php` | Conditional tab visibility | route tab |
| `routes/web.php` | Add player.questions route | ~27 |

## Implementation Sequence

### Phase 1: Foundation
1. Create RouteStop model
2. Create RouteStopAnswer model
3. Add relationships to Game and GamePlayer
4. Add generateRouteStops() to HostLobby

### Phase 2: Player Game
1. Add unlocked() scope to RouteStop
2. Create PlayerRouteQuestion component
3. Create player-route-question.blade.php view
4. Connect component in route.blade.php

### Phase 3: Integration
1. Add route stop progress to HostGame
2. Update host-game.blade.php display
3. Add completion redirect logic
4. Implement tab visibility
5. Register route

## Critical Considerations

| Aspect | Approach |
|--------|----------|
| **Security** | #[Locked] on gameId, playerToken; validate player belongs to game |
| **Duplicate Prevention** | Unique DB constraint + catch QueryException code 23000 |
| **Sequential Unlock** | Query scope checking answered count vs sequence |
| **Score Update** | Use `increment()` for atomic update |
| **Feedback UX** | Alpine.js timer for 2s display, dispatch from Livewire |
| **Performance** | Use `withCount()` for aggregates, eager load relationships |
| **Testing** | Unit tests for scope, feature tests for submission flow |

## Estimated Complexity

- **Files to create**: 6
- **Files to modify**: 8
- **Estimated time**: 6-8 hours
- **Testing effort**: Medium
