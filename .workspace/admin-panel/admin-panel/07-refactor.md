# Refactor Log - admin-panel

---

## Extend: search-filter

### Refactor Session
- **Date**: 2025-12-15
- **Plan Selected**: Impact-Focused (10 improvements)
- **Rollback Hash**: `f2ae45f80f0c472f1332e046622d681bc0c795df`

---

## Applied Improvements

### Security Improvements (S1-S3)

#### S1: SQL Injection Prevention
- **File**: [LocationController.php](app/Http/Controllers/Admin/LocationController.php#L27-L28)
- **Change**: Escape LIKE wildcards (`%`, `_`, `\`) before use in search query
- **Risk Mitigated**: SQL injection via malicious search patterns

#### S2: DoS Prevention - per_page Validation
- **Files**:
  - [LocationController.php](app/Http/Controllers/Admin/LocationController.php#L23)
  - [BingoItemController.php](app/Http/Controllers/Admin/BingoItemController.php#L23)
  - [RouteStopController.php](app/Http/Controllers/Admin/RouteStopController.php#L22)
- **Change**: Clamp per_page parameter between 5-100 to prevent memory exhaustion
- **Risk Mitigated**: DoS via extreme pagination values

#### S3: Rate Limiting
- **File**: [routes/web.php](routes/web.php#L75)
- **Change**: Added `throttle:120,1` middleware to admin routes
- **Risk Mitigated**: Brute force attacks on admin panel

---

### DRY Improvements (D1-D2)

#### D1: AdminPaginationTrait
- **New File**: [AdminPaginationTrait.php](app/Http/Controllers/Admin/Traits/AdminPaginationTrait.php)
- **Purpose**: Centralized perPage validation logic
- **Used By**: LocationController, BingoItemController, RouteStopController
- **Method**: `getPerPage(int $default = 15): int`

#### D2: HandlesFileUploads Trait
- **New File**: [HandlesFileUploads.php](app/Http/Controllers/Admin/Traits/HandlesFileUploads.php)
- **Purpose**: Centralized file upload/delete operations
- **Used By**: LocationController, BingoItemController, RouteStopController
- **Methods**:
  - `storeUploadedFile(UploadedFile $file, string $directory): string|false`
  - `deleteStoredFile(?string $path): void`
  - `handleFileUpload(Request $request, string $fieldName, string $directory, ?string $existingPath = null): array`
  - `handleFileRemoval(Request $request, string $removeFieldName, ?string $existingPath): bool`

---

### Test Fix (Pre-existing Bug)

#### RouteStopControllerTest
- **File**: [RouteStopControllerTest.php](tests/Feature/Admin/RouteStopControllerTest.php)
- **Issue**: Test did not include required `image` field after validation was updated
- **Fix**: Added `Storage::fake('public')` and `UploadedFile::fake()->image()` to test

---

## Test Results

```
Tests: 78 passed (194 assertions)
Duration: 56.39s
```

All admin feature tests pass after refactoring.

---

## Files Changed

### New Files (2)
- `app/Http/Controllers/Admin/Traits/AdminPaginationTrait.php`
- `app/Http/Controllers/Admin/Traits/HandlesFileUploads.php`

### Modified Files (5)
- `app/Http/Controllers/Admin/LocationController.php`
- `app/Http/Controllers/Admin/BingoItemController.php`
- `app/Http/Controllers/Admin/RouteStopController.php`
- `routes/web.php`
- `tests/Feature/Admin/RouteStopControllerTest.php`

---

## Benefits

1. **Security Hardened**: SQL injection, DoS, and brute force protections added
2. **Reduced Duplication**: ~40 lines of duplicated code consolidated into traits
3. **Improved Maintainability**: Single point of change for pagination/upload logic
4. **Better Error Handling**: Consistent file upload error responses
5. **Future-Proof**: Traits can be easily extended for new admin controllers

---

## Extend: game-modes

### Refactor Session
- **Date**: 2025-12-16
- **Plan Selected**: Impact-Focused (10 improvements)
- **Rollback Hash**: `df55b11f78c1bc0e03035bac8930ac89c8069f6f`

---

### Applied Improvements

#### Quality Improvements (Q1-Q3)

##### Q1: Rule::in() for Validation
- **Files**:
  - [StoreLocationRequest.php](app/Http/Requests/StoreLocationRequest.php#L26)
  - [UpdateLocationRequest.php](app/Http/Requests/UpdateLocationRequest.php#L31)
- **Change**: Replaced hardcoded `Rule::in(['bingo', 'vragen'])` with `Rule::in(GameMode::ALL_MODES)`
- **Benefit**: Single source of truth for valid game modes

##### Q2: Consistent validated() Usage
- **File**: [LocationController.php](app/Http/Controllers/Admin/LocationController.php)
- **Change**: Replaced `safe()->only()` + `input()` mix with consistent `validated()` method
- **Benefit**: Clearer, more consistent data access pattern

##### Q3: Model Accessor Documentation
- **File**: [Location.php](app/Models/Location.php#L55-L114)
- **Change**: Added docblocks to all game mode accessors explaining their purpose
- **Benefit**: Better code documentation and IDE support

---

#### Performance Improvements (P1)

##### P1: whereNotNull Guard
- **File**: [Location.php](app/Models/Location.php#L129)
- **Change**: Added `whereNotNull('game_modes')` to `scopeWithValidGameModes`
- **Benefit**: Skips corrupted/null data before JSON operations, improves query efficiency

---

#### DRY Improvements (D1)

##### D1: Factory Constants
- **File**: [LocationFactory.php](database/factories/LocationFactory.php)
- **Change**: Replaced hardcoded `'bingo'`, `'vragen'` strings with `GameMode::BINGO`, `GameMode::VRAGEN`, `GameMode::ALL_MODES`
- **Benefit**: Consistent constant usage, easier refactoring

---

#### Code Quality (C1)

##### C1: Simplified Accessor Logic
- **File**: [Location.php](app/Models/Location.php#L110-L114)
- **Change**: Simplified `getHasIncompleteActiveModeAttribute()` from multi-line if-else to single-line boolean expression
- **Before**:
  ```php
  if (($this->has_bingo_mode && !$this->is_bingo_mode_valid)
      || ($this->has_vragen_mode && !$this->is_vragen_mode_valid)) {
      return true;
  }
  return false;
  ```
- **After**:
  ```php
  return ($this->has_bingo_mode && !$this->is_bingo_mode_valid)
      || ($this->has_vragen_mode && !$this->is_vragen_mode_valid);
  ```

---

### Test Results

```
Game-modes specific tests: 3 passed
- GM-REQ-001: location has game_modes JSON field ✓
- GM-REQ-002: bingo mode requires min 9 bingo items ✓
- GM-REQ-003: vragen mode requires min 1 question ✓
```

**Note**: 6 pre-existing test failures detected (unrelated to refactor):
- GM-REQ-004: Test expects "Spelmodi" but view shows "Beschikbare spellen" (text mismatch)
- URL-related tests: Missing required `url` field in test data (from location-url extend)

---

### Files Changed

#### Modified Files (4)
- `app/Http/Requests/StoreLocationRequest.php` - Added Rule::in() with GameMode constant
- `app/Http/Requests/UpdateLocationRequest.php` - Added Rule::in() with GameMode constant
- `app/Http/Controllers/Admin/LocationController.php` - Consistent validated() usage
- `app/Models/Location.php` - Docblocks, whereNotNull, simplified accessor
- `database/factories/LocationFactory.php` - GameMode constants

---

### Benefits

1. **Single Source of Truth**: All game mode values now reference `GameMode::ALL_MODES`
2. **Consistent Patterns**: Uniform `validated()` usage in controllers
3. **Better Documentation**: Accessor docblocks clarify game mode logic
4. **Improved Performance**: `whereNotNull` guard prevents unnecessary JSON processing
5. **Cleaner Code**: Simplified boolean expressions, removed redundant if-else
