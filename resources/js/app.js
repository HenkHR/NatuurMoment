import './bootstrap';

import Alpine from 'alpinejs';

/**
 * ALPINE.JS ARCHITECTURE - DEFENSIVE INITIALIZATION
 *
 * Dit project heeft TWEE types layouts:
 *
 * 1. LIVEWIRE layouts (lobby.blade.php, player/*.blade.php, host/*.blade.php):
 *    - Gebruiken @livewireScripts die AUTOMATISCH Alpine.js laadt en start
 *    - Alpine komt van Livewire, NIET van dit bestand
 *
 * 2. NON-LIVEWIRE layouts (admin.blade.php, guest.blade.php, app.blade.php):
 *    - Gebruiken GEEN Livewire
 *    - Alpine komt van dit bestand (app.js)
 *
 * PROBLEEM: Als Alpine dubbel wordt gestart, breken wire:click en andere
 * Livewire directives. Dit was een CRITICAL bug (2025-12-15).
 *
 * OPLOSSING: Defensive check - alleen Alpine starten als het nog niet bestaat.
 *
 * @see docs/ALPINE_ARCHITECTURE.md voor volledige documentatie
 */

// Defensive check: Only initialize if Alpine hasn't been started yet
if (window.Alpine) {
    // Alpine already exists (loaded by Livewire's @livewireScripts)
    console.info('[Alpine.js] Already initialized by Livewire. Skipping manual start.');
} else {
    // No Alpine instance exists - safe to initialize for non-Livewire pages
    console.info('[Alpine.js] Initializing from app.js (non-Livewire context)');

    window.Alpine = Alpine;

    try {
        Alpine.start();
        console.info('[Alpine.js] Successfully started');
    } catch (error) {
        console.error('[Alpine.js] Failed to start:', error);

        // Graceful fallback: ensure window.Alpine exists even if start fails
        if (!window.Alpine) {
            window.Alpine = Alpine;
        }
    }
}
