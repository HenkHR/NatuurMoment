# Implementation Log - Admin Panel

Generated: 2025-12-02 14:29:04

## Files Created

### Migration
- `database/migrations/2025_12_02_141343_add_is_admin_to_users_table.php` - Adds is_admin boolean to users table

### Middleware
- `app/Http/Middleware/IsAdmin.php` - Admin authorization middleware with 403 abort

### Models
- `app/Models/Location.php` - Location model with hasMany relationships and cascade delete logic
- `app/Models/LocationBingoItem.php` - Bingo item model with belongsTo Location
- `app/Models/LocationRouteStop.php` - Route stop model with belongsTo Location
- `app/Models/Game.php` - Game model with belongsTo Location

### Controllers
- `app/Http/Controllers/Admin/LocationController.php` - Full CRUD with game restriction on delete
- `app/Http/Controllers/Admin/BingoItemController.php` - Full CRUD nested under location (shallow)
- `app/Http/Controllers/Admin/RouteStopController.php` - Full CRUD nested under location (shallow)
- `app/Http/Controllers/Admin/GameController.php` - Index, show, destroy only

### Form Requests
- `app/Http/Requests/StoreLocationRequest.php` - Location create validation
- `app/Http/Requests/UpdateLocationRequest.php` - Location update validation with unique ignore
- `app/Http/Requests/StoreBingoItemRequest.php` - Bingo item create validation
- `app/Http/Requests/UpdateBingoItemRequest.php` - Bingo item update validation
- `app/Http/Requests/StoreRouteStopRequest.php` - Route stop create validation
- `app/Http/Requests/UpdateRouteStopRequest.php` - Route stop update validation

### Views
- `resources/views/components/admin/layout.blade.php` - Admin layout component with navigation
- `resources/views/admin/locations/index.blade.php` - Locations list with counts and modals
- `resources/views/admin/locations/create.blade.php` - Create location form
- `resources/views/admin/locations/edit.blade.php` - Edit location form
- `resources/views/admin/bingo-items/index.blade.php` - Bingo items list per location
- `resources/views/admin/bingo-items/create.blade.php` - Create bingo item form
- `resources/views/admin/bingo-items/edit.blade.php` - Edit bingo item form
- `resources/views/admin/route-stops/index.blade.php` - Route stops list per location
- `resources/views/admin/route-stops/create.blade.php` - Create route stop form
- `resources/views/admin/route-stops/edit.blade.php` - Edit route stop form
- `resources/views/admin/games/index.blade.php` - Games list with status badges
- `resources/views/admin/games/show.blade.php` - Game details view

### Factories
- `database/factories/LocationFactory.php` - Location factory
- `database/factories/LocationBingoItemFactory.php` - Bingo item factory
- `database/factories/LocationRouteStopFactory.php` - Route stop factory
- `database/factories/GameFactory.php` - Game factory with state modifiers

### Tests
- `tests/Feature/Admin/IsAdminMiddlewareTest.php` - 5 middleware tests
- `tests/Feature/Admin/LocationControllerTest.php` - 10 location CRUD tests
- `tests/Feature/Admin/BingoItemControllerTest.php` - 8 bingo item CRUD tests
- `tests/Feature/Admin/RouteStopControllerTest.php` - 9 route stop CRUD tests
- `tests/Feature/Admin/GameControllerTest.php` - 6 game read/delete tests

## Files Modified

- `app/Models/User.php` - Added is_admin to $fillable and casts()
- `bootstrap/app.php` - Registered 'admin' middleware alias
- `routes/web.php` - Added admin route group with resource routes
- `database/seeders/DatabaseSeeder.php` - Added admin user seeder
- `resources/views/layouts/navigation.blade.php` - Added admin nav link for admins

## Architectural Decisions

1. **Shallow Nesting for Resources**: Used `->shallow()` on nested resources to create shorter URLs for edit/update/destroy operations while keeping index/create/store under location context.

2. **Cascade Delete Strategy**:
   - Locations cascade delete bingo items and route stops via model boot method
   - Games use restrict pattern - location cannot be deleted if games exist

3. **Admin Layout Component**: Created as Blade component (`x-admin.layout`) extending app layout with admin-specific navigation and flash messages.

4. **Dutch Validation Messages**: All form request classes include custom Dutch validation messages per user preferences.

5. **Middleware Registration**: Used Laravel 12's new middleware alias pattern in bootstrap/app.php.

## Deviations from Plan

- None - Implementation followed the architecture document closely.

## Post-Implementation Changes (2025-12-03)

### Fixes Applied

1. **CSRF Token Fix in Tests**
   - Modified `tests/TestCase.php` to disable CSRF middleware in tests
   - Fixed 19 failing tests due to HTTP 419 errors

2. **Correct Option Validation**
   - Added closure validation to `StoreRouteStopRequest.php` and `UpdateRouteStopRequest.php`
   - Prevents selecting C/D as correct answer when those options are empty

3. **Icon Upload for Bingo Items**
   - Changed icon field from text to image upload
   - Updated `StoreBingoItemRequest.php` and `UpdateBingoItemRequest.php` with `File::image()->max(2 * 1024)` validation
   - Updated `BingoItemController.php` with file upload handling and Storage facade
   - Updated create/edit/index views with file input, preview, and remove functionality
   - Created `storage/app/public/bingo-icons/` directory

### UI/Navigation Changes

1. **Removed Games Tab**
   - Removed Games link from admin layout navigation
   - Removed Games column from locations index table
   - Removed games warning from location delete modal

2. **Dashboard Redirect**
   - `/dashboard` now redirects to `/admin/locations`
   - Removed Dashboard nav link - only "Locaties" visible in navigation
   - Removed secondary admin navigation (header + tabs)

3. **Simplified Navigation**
   - Users land directly on locaties page after login
   - Single "Locaties" link in main navigation

## Sequential Thinking Insights

- Decided to handle cascade deletes in Location model boot method rather than updating migrations for better flexibility
- Used withCount() for eager loading relationship counts on location index for better N+1 prevention
- Created Game factory with state modifiers (lobby(), started(), finished()) for better test readability
- Placed admin layout in components/admin/ folder instead of admin/ for proper Blade component resolution

---

## Extend: search-filter (2025-12-15)

### Overview
Search bar, regio filter en pagination toevoegen aan admin panel pagina's.

### Files Created

#### Blade Components
- `resources/views/components/admin/search-bar.blade.php` - Herbruikbare search input met icon
- `resources/views/components/admin/regio-filter.blade.php` - Regio dropdown met provinces uit config
- `resources/views/components/admin/empty-state.blade.php` - Lege resultaten melding component

### Files Modified

#### Controllers
- `app/Http/Controllers/Admin/LocationController.php`
  - Added `->when()` filters for search (name + province LIKE) and regio (exact match)
  - Smart search: when regio dropdown selected, search only filters on name
  - Added `->withQueryString()` for pagination state preservation
  - Pass `$provinces` from config and `$hasFilters` boolean to view

- `app/Http/Controllers/Admin/BingoItemController.php`
  - Added `->withQueryString()` to pagination

- `app/Http/Controllers/Admin/RouteStopController.php`
  - Added `->withQueryString()` to pagination

#### Views
- `resources/views/admin/locations/index.blade.php`
  - Live filtering: auto-submit on search input (debounced 400ms) and regio dropdown change
  - Search input retains focus after page refresh via sessionStorage
  - Removed manual "Zoek" and "Wis filters" buttons - replaced with live filtering UX
  - Updated empty state to use `<x-admin.empty-state>` component with context-aware message

#### Tests
- `tests/Feature/Admin/LocationControllerTest.php` - Added 8 new tests for REQ-001 to REQ-008
- `tests/Feature/Admin/BingoItemControllerTest.php` - Added 2 pagination tests for REQ-004
- `tests/Feature/Admin/RouteStopControllerTest.php` - Added 2 pagination tests for REQ-005

### Architectural Decisions

1. **Inline when() filters**: Chose simple inline `->when()` clauses in controller instead of service layer (YAGNI - dit is simpele filtering, geen complexe business logic)

2. **Herbruikbare Blade Components**: Created reusable components for search-bar, regio-filter, and empty-state to enable future reuse across other admin pages

3. **Live filtering with vanilla JavaScript**: Used debounced form submission (400ms) for search input and immediate submit for dropdown - provides smooth UX without full page reloads feeling

4. **Focus preservation**: Used sessionStorage to remember if user was typing in search, maintaining focus after page refresh for uninterrupted typing experience

5. **Smart search context**: When regio filter is active, search only filters on name (not province) - prevents redundant filtering

6. **withQueryString()**: Applied to all paginated results to preserve filter state across pages

7. **Config-driven provinces**: Reused existing `config/provinces.php` for dropdown options (consistency with home page)

### Deviations from Research Plan

- Skipped AJAX partial rendering (from research) - form auto-submit with debounce provides similar UX
- Skipped service layer abstraction (from quality agent) - inline when() is simpler and adequate
- Removed "Zoek" and "Wis filters" buttons (were in initial implementation) - live filtering made them unnecessary

### Requirements Status

| REQ-ID | Description | Status |
|--------|-------------|--------|
| REQ-001 | Search bar filtert op regio | Implemented |
| REQ-002 | Regio filter dropdown | Implemented |
| REQ-003 | Locaties pagination 15/page | Implemented |
| REQ-004 | Bingo items pagination 15/page | Implemented |
| REQ-005 | Vragen pagination 15/page | Implemented |
| REQ-006 | Filters behouden state | Implemented |
| REQ-007 | Lege resultaten melding | Implemented |
| REQ-008 | Pagination toont totaal pages | Implemented |

---

## Extend: settings-page (2025-12-15)

### Overview
Settings pagina met per-page voorkeur voor admin panel. De profile pagina is hernoemd naar settings met een nieuwe admin voorkeuren sectie.

### Files Created

#### Migration
- `database/migrations/2025_12_15_160000_add_admin_per_page_to_users_table.php` - Adds admin_per_page column (default 15)

### Files Modified

#### Models
- `app/Models/User.php`
  - Added `admin_per_page` to $fillable
  - Added `'admin_per_page' => 'integer'` to casts()

#### Routes
- `routes/web.php`
  - Renamed `/profile` to `/settings`
  - Renamed `profile.edit` to `settings.edit`
  - Renamed `profile.update` to `settings.update`
  - Added new `settings.preferences` route (PATCH)
  - Renamed `profile.destroy` to `settings.destroy`

#### Controllers
- `app/Http/Controllers/ProfileController.php`
  - Updated redirect in `update()` to `settings.edit`
  - Added `updatePreferences()` method for saving admin_per_page

- `app/Http/Controllers/Admin/LocationController.php`
  - Changed default per_page from hardcoded 15 to `auth()->user()->admin_per_page ?? 15`
  - Pass `$perPage` to view for dropdown selection

- `app/Http/Controllers/Admin/BingoItemController.php`
  - Same per_page change as LocationController

- `app/Http/Controllers/Admin/RouteStopController.php`
  - Same per_page change as RouteStopController

#### Views
- `resources/views/profile/edit.blade.php`
  - Changed title from "Profile" to "Instellingen"
  - Added new "Admin voorkeuren" section at top with per-page dropdown
  - Dropdown options: 10, 15, 25, 50, 100 items

- `resources/views/profile/partials/update-profile-information-form.blade.php`
  - Updated form action from `profile.update` to `settings.update`

- `resources/views/profile/partials/delete-user-form.blade.php`
  - Updated form action from `profile.destroy` to `settings.destroy`

- `resources/views/layouts/admin-navigation.blade.php`
  - Changed "Profile" link to "Instellingen"
  - Updated href from `profile.edit` to `settings.edit`

- `resources/views/layouts/navigation.blade.php`
  - Same changes as admin-navigation.blade.php

- `resources/views/admin/locations/index.blade.php`
  - Removed per-page dropdown from filter bar
  - Updated dropdown to use `$perPage` variable instead of `request('per_page', 15)`

- `resources/views/admin/bingo-items/index.blade.php`
  - Removed per-page dropdown completely (now managed via settings)

- `resources/views/admin/route-stops/index.blade.php`
  - Removed per-page dropdown completely (now managed via settings)

#### JavaScript
- `resources/js/app.js`
  - Added Alpine.js import and initialization (was missing, causing dropdown to not work)

### Architectural Decisions

1. **User preference storage**: Stored admin_per_page in users table instead of session - persists across devices and sessions

2. **Centralized settings**: Moved per-page preference from individual pages to central settings page - cleaner UX, less clutter on index pages

3. **Fallback default**: Controllers use `auth()->user()->admin_per_page ?? 15` - handles both null values and unauthenticated edge cases

4. **Route renaming**: Changed profile to settings to better reflect expanded functionality beyond just profile info

5. **Alpine.js fix**: Added proper Alpine.js import in app.js - was previously commented out assuming Livewire would load it, but admin panel doesn't use Livewire

### Deviations from Previous Implementation

- Removed per-page dropdown from all admin index pages (was added in search-filter extend)
- Per-page selection now only available in settings page

---

## Extend: game-modes (2025-12-15)

### Overview
Game modes systeem met toggle switches per locatie, validatie per modus (bingo: min 9 items, vragen: min 1 vraag), en filtering op homepage.

### Files Created

#### Constants
- `app/Constants/GameMode.php` - Business constants (MIN_BINGO_ITEMS = 9, MIN_QUESTIONS = 1)

#### Migration
- `database/migrations/2025_12_15_154359_add_game_modes_to_locations_table.php` - Adds game_modes JSON column

#### Views
- `resources/views/admin/locations/_game-mode-toggles.blade.php` - Herbruikbare partial met toggle switches

### Files Modified

#### Models
- `app/Models/Location.php`
  - Added `game_modes` to $fillable
  - Added `casts(): ['game_modes' => 'array']`
  - Added 6 model accessors:
    - `getHasBingoModeAttribute()` - Check if bingo mode enabled
    - `getHasVragenModeAttribute()` - Check if vragen mode enabled
    - `getIsBingoModeValidAttribute()` - Check if bingo mode valid (enabled + >= 9 items)
    - `getIsVragenModeValidAttribute()` - Check if vragen mode valid (enabled + >= 1 question)
    - `getHasValidGameModeAttribute()` - Check if location has at least one valid mode
    - `getHasIncompleteActiveModeAttribute()` - Check if any enabled mode lacks content
  - Added `scopeWithValidGameModes()` query scope

#### Controllers
- `app/Http/Controllers/Admin/LocationController.php`
  - Store: Default game_modes to empty array
  - Edit: loadCount(['bingoItems', 'routeStops']) for status indicators
  - Update: Handle game_modes from request

- `app/Http/Controllers/HomeController.php`
  - Applied `withValidGameModes()` scope to filter locations (REQ-009)

#### Form Requests
- `app/Http/Requests/StoreLocationRequest.php`
  - Added `'game_modes' => ['nullable', 'array']`
  - Added `'game_modes.*' => ['string', 'in:bingo,vragen']`

- `app/Http/Requests/UpdateLocationRequest.php`
  - Same game_modes validation rules

#### Views
- `resources/views/admin/locations/create.blade.php`
  - Added `@include('admin.locations._game-mode-toggles')` section

- `resources/views/admin/locations/edit.blade.php`
  - Added `@include('admin.locations._game-mode-toggles')` section with status indicators

- `resources/views/admin/locations/index.blade.php`
  - Added warning badge (⚠️) for incomplete active modes
  - Added red styling for counts under minimum (bingo < 9, vragen < 1)
  - Updated both desktop table and mobile cards

#### Factory
- `database/factories/LocationFactory.php`
  - Added `game_modes` to definition (default: [])
  - Added state methods: `withBingoMode()`, `withVragenMode()`, `withAllModes()`

#### Tests
- `tests/Feature/Admin/LocationControllerTest.php`
  - Added 12 new tests for GM-REQ-001 to GM-REQ-010
  - Tests cover: JSON field, validation logic, UI indicators, scope filtering

### Architectural Decisions

1. **Constants Class**: Created `App\Constants\GameMode` for centralized min values - easy to modify and reference across codebase

2. **Model Accessors**: Business logic for mode validation encapsulated in Location model - single source of truth, testable in isolation

3. **Query Scope**: `withValidGameModes()` scope filters locations with at least one valid game mode - reusable, clean controller code

4. **Blade Partial**: Created `_game-mode-toggles.blade.php` for toggle switches - reusable between create/edit views

5. **JSON Column**: Used JSON column with array cast instead of separate table - simpler, all mode data in one place

6. **Defensive Accessors**: Accessors use fallback to relationship count if `_count` attribute not loaded - prevents errors

### Deviations from Research Plan

- Skipped Blade components (from quality agent) - simple partial is sufficient for single use case
- Skipped separate unit test file (from quality agent) - feature tests cover the model accessors
- Used in_array() for mode checks instead of boolean flags in JSON - cleaner for multiple modes

### Requirements Status

| REQ-ID | Description | Status |
|--------|-------------|--------|
| REQ-001 | Location has game_modes JSON field | Implemented |
| REQ-002 | Bingo mode requires min 9 bingo items | Implemented |
| REQ-003 | Vragen mode requires min 1 question | Implemented |
| REQ-004 | Edit/create shows toggle switches | Implemented |
| REQ-005 | Toggle shows status indicator with count | Implemented |
| REQ-006 | New location has all modes OFF | Implemented |
| REQ-007 | Table shows red text for counts under minimum | Implemented |
| REQ-008 | Table shows ⚠️ badge when incomplete active modes | Implemented |
| REQ-009 | Location without valid active modes not visible on home | Implemented |
| REQ-010 | Bingo selects random 9 items if > 9 available | Deferred (game creation logic) |
