# Admin Panel - Architecture

## Selected Approach: MINIMAL CHANGES

Philosophy: Kleinste change set, maximum hergebruik van Laravel conventies en bestaande components.

## Design Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    Admin Routes                              │
│  /admin/locations, /admin/games, /admin/bingo-items,        │
│  /admin/route-stops                                          │
└────────────────┬────────────────────────────────────────────┘
                 │
         ┌───────▼────────┐
         │  IsAdmin       │
         │  Middleware    │
         └───────┬────────┘
                 │
    ┌────────────┴────────────┐
    │                         │
┌───▼──────────┐   ┌─────────▼────────┐
│ Resource     │   │ Form Request     │
│ Controllers  │   │ Validation       │
│ (4 classes)  │   │ (6 classes)      │
└───┬──────────┘   └─────────┬────────┘
    │                        │
    │  Uses Models ←─────────┘
    │  (4 classes)
    │
┌───▼──────────────────────────┐
│  Blade Views                 │
│  - index.blade.php (list)    │
│  - create.blade.php (form)   │
│  - edit.blade.php (form)     │
│  Using existing components   │
└──────────────────────────────┘
```

## Files to Create

### Migration
| File | Purpose |
|------|---------|
| `database/migrations/YYYY_MM_DD_add_is_admin_to_users.php` | Add is_admin boolean to users table |

### Middleware
| File | Purpose |
|------|---------|
| `app/Http/Middleware/IsAdmin.php` | Check user is_admin flag, abort 403 if not |

### Models
| File | Purpose |
|------|---------|
| `app/Models/Location.php` | Location model with hasMany relationships |
| `app/Models/LocationBingoItem.php` | Bingo item model with belongsTo Location |
| `app/Models/LocationRouteStop.php` | Route stop model with belongsTo Location |
| `app/Models/Game.php` | Game model with belongsTo Location |

### Controllers
| File | Purpose |
|------|---------|
| `app/Http/Controllers/Admin/LocationController.php` | CRUD for locations |
| `app/Http/Controllers/Admin/BingoItemController.php` | CRUD for bingo items (nested) |
| `app/Http/Controllers/Admin/RouteStopController.php` | CRUD for route stops (nested) |
| `app/Http/Controllers/Admin/GameController.php` | Index, show, destroy for games |

### Form Requests
| File | Purpose |
|------|---------|
| `app/Http/Requests/StoreLocationRequest.php` | Validate location creation |
| `app/Http/Requests/UpdateLocationRequest.php` | Validate location updates |
| `app/Http/Requests/StoreBingoItemRequest.php` | Validate bingo item creation |
| `app/Http/Requests/UpdateBingoItemRequest.php` | Validate bingo item updates |
| `app/Http/Requests/StoreRouteStopRequest.php` | Validate route stop creation |
| `app/Http/Requests/UpdateRouteStopRequest.php` | Validate route stop updates |

### Views
| File | Purpose |
|------|---------|
| `resources/views/admin/layout.blade.php` | Admin layout with navigation |
| `resources/views/admin/locations/index.blade.php` | List all locations |
| `resources/views/admin/locations/create.blade.php` | Create location form |
| `resources/views/admin/locations/edit.blade.php` | Edit location form |
| `resources/views/admin/bingo-items/index.blade.php` | List bingo items per location |
| `resources/views/admin/bingo-items/create.blade.php` | Create bingo item form |
| `resources/views/admin/bingo-items/edit.blade.php` | Edit bingo item form |
| `resources/views/admin/route-stops/index.blade.php` | List route stops per location |
| `resources/views/admin/route-stops/create.blade.php` | Create route stop form |
| `resources/views/admin/route-stops/edit.blade.php` | Edit route stop form |
| `resources/views/admin/games/index.blade.php` | List all games |
| `resources/views/admin/games/show.blade.php` | Show game details |

## Files to Modify

| File | Change |
|------|--------|
| `app/Models/User.php` | Add `is_admin` to $fillable and casts() |
| `bootstrap/app.php` | Register IsAdmin middleware alias |
| `routes/web.php` | Add admin route group |
| `database/seeders/DatabaseSeeder.php` | Create admin user for testing |

## Implementation Sequence

### Phase 1: Foundation
1. Create migration for is_admin field
2. Update User model with is_admin
3. Create IsAdmin middleware
4. Register middleware in bootstrap/app.php
5. Seed admin user

### Phase 2: Core Logic
1. Create Eloquent models with relationships
2. Create Form Request classes
3. Generate resource controllers
4. Define admin routes in web.php
5. Create admin layout view

### Phase 3: Views
1. Build locations views (index/create/edit)
2. Build bingo items views (index/create/edit)
3. Build route stops views (index/create/edit)
4. Build games views (index/show)

## Critical Considerations

| Aspect | Approach |
|--------|----------|
| **Error Handling** | Laravel validation + try-catch for DB errors |
| **State Management** | Session flash messages for feedback |
| **Testing Strategy** | Feature tests for middleware + CRUD |
| **Performance** | Eager loading with `with()`, paginate(15) |
| **Security** | CSRF, middleware auth, mass assignment protection |

## Estimated Effort

- **Files to create**: 29
- **Files to modify**: 4
- **Implementation time**: ~3-4 hours
- **Testing effort**: Low
