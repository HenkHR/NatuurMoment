# Alpine.js Architecture - NatuurMoment

## Probleem

Alpine.js kan niet twee keer worden geïnitialiseerd. Als dit wel gebeurt, breken Livewire directives (`wire:click`, `wire:model`, etc.).

## Root Cause

Dit project heeft **TWEE types layouts**:

### 1. Livewire Layouts (Alpine komt van Livewire)

| Layout | Pad | Gebruikt Livewire |
|--------|-----|-------------------|
| Lobby | `resources/views/layouts/lobby.blade.php` | JA |
| Player views | `resources/views/player/*.blade.php` | JA |
| Host views | `resources/views/host/*.blade.php` | JA |

**Voor deze layouts:** Alpine wordt automatisch geladen door `@livewireScripts`.
**NIET handmatig Alpine starten!**

### 2. Non-Livewire Layouts (Alpine komt van app.js)

| Layout | Pad | Gebruikt Livewire |
|--------|-----|-------------------|
| Admin | `resources/views/components/layouts/admin.blade.php` | NEE |
| Guest | `resources/views/layouts/guest.blade.php` | NEE |
| App | `resources/views/layouts/app.blade.php` | NEE |

**Voor deze layouts:** Alpine wordt geladen via `resources/js/app.js`.
**GEEN `@livewireScripts` toevoegen!**

---

## Oplossing: Defensive Initialization

`resources/js/app.js` heeft nu defensive checks:

```javascript
if (window.Alpine) {
    // Al gestart door Livewire - skip
    console.info('[Alpine.js] Already initialized by Livewire. Skipping manual start.');
} else {
    // Veilig om handmatig te starten
    window.Alpine = Alpine;
    Alpine.start();
}
```

Dit voorkomt dubbele initialisatie en ondersteunt beide contexten.

---

## Regels voor Developers

### Nieuwe Layout Toevoegen

**Als je Livewire gebruikt:**
1. Voeg `@livewireStyles` toe in `<head>`
2. Voeg `@livewireScripts` toe voor `</body>`
3. Voeg warning comment toe (zie lobby.blade.php)
4. **NIET** handmatig Alpine starten

**Als je GEEN Livewire gebruikt:**
1. Voeg `@vite(['resources/css/app.css', 'resources/js/app.js'])` toe
2. Voeg warning comment toe (zie admin.blade.php)
3. **GEEN** `@livewireScripts` toevoegen

### Layout Converteren naar Livewire

Als je Livewire wilt toevoegen aan een non-Livewire layout:

1. Voeg `@livewireStyles` toe in `<head>`
2. Voeg `@livewireScripts` toe voor `</body>`
3. Update deze documentatie
4. Test alle Alpine componenten in beide contexten

---

## Debugging

### Symptomen van Dubbele Initialisatie

- `wire:click` werkt niet
- Livewire componenten reageren niet op events
- Console toont "Alpine.js already started" errors
- Alpine directives werken in admin maar niet in lobby (of andersom)

### Hoe te Debuggen

1. Open browser console
2. Zoek naar Alpine.js initialisatie berichten:
   - `[Alpine.js] Already initialized by Livewire. Skipping manual start.`
   - `[Alpine.js] Initializing from app.js (non-Livewire context)`

3. Check welke layout de pagina gebruikt
4. Verifieer dat `@livewireScripts` alleen in Livewire layouts staat

### Handmatige Test

```javascript
// In browser console
console.log(window.Alpine);        // Moet bestaan
console.log(window.Alpine.version); // Moet versienummer tonen
console.log(window.Livewire);       // Bestaat op Livewire pagina's, undefined op anderen
```

---

## Migratie Historie

| Datum | Wijziging |
|-------|-----------|
| 2025-12-15 | Defensive initialization toegevoegd om dubbele-init bug te fixen |

**Vorig gedrag:** `app.js` riep blind `Alpine.start()` aan, conflicteerde met Livewire

**Huidig gedrag:** `app.js` checkt of Alpine al bestaat voor het starten

---

## Referenties

- [Alpine.js Documentatie](https://alpinejs.dev/)
- [Livewire + Alpine Integratie](https://livewire.laravel.com/docs/alpine)
- NatuurMoment Issue: Dubbele Alpine initialisatie breekt wire:click (2025-12-15)
