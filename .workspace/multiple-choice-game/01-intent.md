# Multiple Choice Game - Intent

## Overview
Multiple choice vragen spel voor spelers met sequential unlocking. Admin kan al vragen aanmaken (LocationRouteStop), nu moet player-facing game gebouwd worden waar spelers vragen beantwoorden. Vragen unlocken sequentieel - vraag N+1 pas beschikbaar na beantwoorden vraag N.

## Task Type
FEATURE

## Complexity Score
94/100 (HIGH)

## Game Flow
1. Host start game → vragen worden gekopieerd van LocationRouteStop → RouteStop
2. Speler opent vragen-tab → ziet eerste vraag
3. Speler beantwoordt vraag → feedback (groen/rood) → auto volgende vraag
4. Alle vragen beantwoord → redirect naar bingo
5. Bingo ook klaar (9 foto's ingediend) → redirect naar tussenstand

## Testable Requirements

### Core Requirements
| ID | Description | Category | Test Type | Passes |
|----|-------------|----------|-----------|--------|
| REQ-001 | Speler kan alleen vraag N+1 zien als vraag N beantwoord is (sequential unlock) | core | automated_ui | true |
| REQ-002 | Speler kan een antwoord selecteren (A/B/C/D) en submitten | core | automated_ui | true |
| REQ-003 | Fout antwoord is definitief - geen retry mogelijk, 0 punten | core | automated_api | true |
| REQ-004 | Goed antwoord kent punten toe aan speler (opgeteld bij totaalscore) | core | automated_api | true |
| REQ-005 | Vragen worden gekopieerd van LocationRouteStop naar RouteStop bij game start | core | automated_api | true |

### UI Requirements
| ID | Description | Category | Test Type | Passes |
|----|-------------|----------|-----------|--------|
| REQ-006 | Na beantwoorden toont feedback: goed (groen) of fout (rood) indicator | ui | manual | true |
| REQ-007 | Na feedback automatisch volgende vraag tonen (2s delay) | ui | automated_ui | true |
| REQ-008 | Alleen ingevulde antwoordopties tonen (2-4 opties) | ui | manual | true |
| REQ-009 | Vragen-tab verbergen als locatie geen vragen heeft | ui | manual | false |
| REQ-010 | Speler kan vrij switchen tussen bingo en vragen tabs | ui | manual | false |

### Integration Requirements
| ID | Description | Category | Test Type | Passes |
|----|-------------|----------|-----------|--------|
| REQ-011 | Na alle vragen beantwoord → redirect naar bingo kaart | integration | automated_ui | false |
| REQ-012 | Na bingo voltooid (9 foto's ingediend) + vragen klaar → redirect naar tussenstand | integration | automated_ui | false |
| REQ-013 | Host ziet per speler % vragen beantwoord in dashboard | integration | manual | false |

### Edge Cases
| ID | Description | Category | Test Type | Passes |
|----|-------------|----------|-----------|--------|
| REQ-014 | Speler kan niet 2x dezelfde vraag beantwoorden (duplicate prevention) | edge_case | automated_api | true |

---

## Part 01: Foundation
**Status:** ✓ verified (2025-12-14)

### Scope
- RouteStop model met relationships
- RouteStopAnswer model met relationships
- Game::routeStops() relationship
- GamePlayer::routeStopAnswers() relationship
- HostLobby::generateRouteStops() method

### Requirements Covered
- REQ-005: Vragen kopiëren bij game start

### Success Criteria
- [ ] RouteStop model aangemaakt met game(), answers() relationships
- [ ] RouteStopAnswer model aangemaakt met gamePlayer(), routeStop() relationships
- [ ] Unique constraint op [game_player_id, route_stop_id]
- [ ] generateRouteStops() kopieert LocationRouteStop → RouteStop via replicate()
- [ ] Game start genereert RouteStops naast BingoItems

### Dependencies
None (foundation layer)

---

## Part 02: Player Game
**Status:** ✓ verified (2025-12-14)

### Scope
- PlayerRouteQuestion Livewire component
- player-route-question.blade.php view
- Sequential unlock via query scope
- Answer submission met score calculation
- Feedback UI (groen/rood) met 2s delay
- Auto-advance naar volgende vraag

### Requirements Covered
- REQ-001: Sequential unlock
- REQ-002: Antwoord selecteren en submitten
- REQ-003: Fout antwoord definitief
- REQ-004: Punten toekennen
- REQ-006: Feedback indicator
- REQ-007: Auto volgende vraag
- REQ-008: Alleen ingevulde opties tonen
- REQ-014: Duplicate prevention

### Success Criteria
- [x] PlayerRouteQuestion component met #[Locked] properties
- [x] unlocked() query scope op RouteStop model
- [x] submitAnswer() met correctness check en score update
- [x] wire:loading op submit button
- [x] Alpine.js feedback timer (2s)
- [x] Vraag display met 2-4 antwoordopties

### Dependencies
- Part 01 (models must exist)

---

## Part 03: Integration
**Status:** ○ pending

### Scope
- HostGame dashboard: % vragen beantwoord per speler
- Redirect logic: vragen klaar → bingo, alles klaar → tussenstand
- Vragen-tab visibility (verbergen als geen vragen)
- Route registratie voor player questions

### Requirements Covered
- REQ-009: Tab verbergen als geen vragen
- REQ-010: Vrij switchen tussen tabs
- REQ-011: Redirect naar bingo
- REQ-012: Redirect naar tussenstand
- REQ-013: Host ziet % beantwoord

### Success Criteria
- [ ] HostGame toont "Vragen: X%" per speler
- [ ] PlayerRouteQuestion redirect naar bingo na laatste vraag
- [ ] Completion check: bingo + vragen → tussenstand
- [ ] Vragen-tab conditionally rendered
- [ ] Route `/player/questions/{game}` geregistreerd

### Dependencies
- Part 01 (models)
- Part 02 (component)
