# Admin Panel - Architecture

## Extend: survey-statistieken (2025-12-15)

### Selected Approach: PRAGMATIC BALANCE

Philosophy: Balans tussen snelheid en kwaliteit - hergebruik bestaande patterns, testbare code zonder over-engineering.

### Design Overview

```
┌─────────────────────────────────────────────────────────┐
│  Admin Panel Routes (web.php)                           │
│  + GET /admin/statistics                                │
│  + GET /admin/statistics?period=week|month|year (JSON)  │
└────────────────┬────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────┐
│  StatisticsController                                   │
│  - index(): aggregates data, returns view OR JSON       │
│  - Queries: selectRaw, groupByRaw, strftime             │
│  - Returns: stat cards data + chart datasets            │
└────────────────┬────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────┐
│  statistics/index.blade.php                             │
│  - 4 stat cards (responsive 2x2 grid)                   │
│  - 4 Chart.js charts (Alpine.js x-init + $refs)         │
│  - Dropdown filter voor trends (Alpine.js + fetch)      │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  PlayerFeedback Livewire (Modified)                     │
│  - Validatie: 1-5 (was 1-10)                            │
│  - View: 5 star buttons (was 10 number buttons)         │
└─────────────────────────────────────────────────────────┘
```

### Files to Create

| File | Purpose | Dependencies |
|------|---------|--------------|
| `app/Http/Controllers/Admin/StatisticsController.php` | Aggregatie queries, JSON endpoint voor trends | GamePlayer, Game, Location models |
| `resources/views/admin/statistics/index.blade.php` | Dashboard met stat cards en Chart.js grafieken | Chart.js CDN, Alpine.js, admin layout |

### Files to Modify

| File | Change | Reason |
|------|--------|--------|
| `app/Livewire/PlayerFeedback.php` | Validatie 1-10 → 1-5 | Star rating system |
| `resources/views/livewire/player-feedback.blade.php` | 10 buttons → 5 sterren | UI simplificatie |
| `routes/web.php` | Route toevoegen in admin group | Statistics dashboard toegang |
| `resources/views/layouts/admin-navigation.blade.php` | Nav link "Statistieken" | Navigatie |

### Implementation Sequence

**Phase 1: PlayerFeedback Modification**
1. Update validatie in `PlayerFeedback.php`: 1-10 → 1-5
2. Vervang 10 number buttons met 5 star SVG icons in blade view
3. Test feedback submission: verify 1-5 range werkt

**Phase 2: Statistics Controller**
1. Create `StatisticsController.php` met index() method
2. Implement stat card queries (total, avg, month, location)
3. Implement chart data queries (age, satisfaction, trends, location)
4. Add JSON response voor `?period=` query param

**Phase 3: Statistics View**
1. Create blade file met admin layout
2. Add 4 stat cards (responsive grid)
3. Add Chart.js CDN script tag
4. Create 4 charts met Alpine.js x-init
5. Wire up trends dropdown met fetch() + chart.update()

**Phase 4: Integration**
1. Register route in web.php
2. Add navigation link
3. Test responsive design
4. Test AJAX dropdown

### Critical Considerations

| Aspect | Approach |
|--------|----------|
| **Error Handling** | Return empty arrays on DB errors, show user-friendly message in blade |
| **State Management** | Alpine.js x-data for dropdown + chart instances, geen Livewire (read-only) |
| **Testing Strategy** | Unit test controller aggregations, feature test routes, manual chart testing |
| **Performance** | selectRaw voor aggregation (single query per chart), geen N+1 |
| **Security** | Admin middleware reeds in place, validate period param |
| **Empty State** | Check `whereNotNull('feedback_rating')->exists()`, toon friendly message |

### Estimated Complexity

- **Files to create**: 2
- **Files to modify**: 4
- **Complexity**: Medium
- **Testing effort**: Medium (unit + feature + manual)
