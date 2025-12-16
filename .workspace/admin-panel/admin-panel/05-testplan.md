# Admin Panel - Testplan Nieuwe Functionaliteiten

## Overzicht
Dit testplan beschrijft de handmatige tests voor de nieuw toegevoegde functionaliteiten in het admin panel.

---

## 1. Locatie Formulieren

### 1.1 Nieuwe Locatie Aanmaken
**Route:** `/admin/locations/create`

| # | Test | Verwacht Resultaat | Status |
|---|------|-------------------|--------|
| 1.1.1 | Open het formulier | Velden: Naam, Beschrijving, Provincie/Regio, Duur (minuten), Locatie afbeelding worden getoond | ☐ |
| 1.1.2 | Vul alleen Naam in, submit | Validatiefout voor Province en Duration | ☐ |
| 1.1.3 | Vul alle verplichte velden in (Naam, Provincie, Duur), submit | Locatie wordt aangemaakt, redirect naar index met succesbericht | ☐ |
| 1.1.4 | Upload een geldige afbeelding (< 2MB, jpeg/png) | Afbeelding wordt opgeslagen in `storage/app/public/location-images/` | ☐ |
| 1.1.5 | Upload een te grote afbeelding (> 2MB) | Validatiefout: "max 2MB" | ☐ |
| 1.1.6 | Upload een ongeldig bestandstype (.pdf) | Validatiefout: "mimes" | ☐ |
| 1.1.7 | Duur veld accepteert alleen positieve getallen | min="1" werkt correct | ☐ |

### 1.2 Locatie Bewerken
**Route:** `/admin/locations/{id}/edit`

| # | Test | Verwacht Resultaat | Status |
|---|------|-------------------|--------|
| 1.2.1 | Open bestaande locatie | Alle velden zijn voorgevuld met huidige waarden | ☐ |
| 1.2.2 | Wijzig Provincie, submit | Locatie wordt bijgewerkt, redirect met succesbericht | ☐ |
| 1.2.3 | Locatie met afbeelding: huidige afbeelding wordt getoond | Preview van afbeelding zichtbaar | ☐ |
| 1.2.4 | Vink "Verwijder huidige afbeelding" aan, submit | Afbeelding wordt verwijderd uit storage en database | ☐ |
| 1.2.5 | Upload nieuwe afbeelding (locatie had al één) | Oude afbeelding wordt verwijderd, nieuwe opgeslagen | ☐ |
| 1.2.6 | Klik op "Annuleren" | Redirect naar locaties index zonder wijzigingen | ☐ |

---

## 2. Bingo Items

### 2.1 Nieuw Bingo Item Aanmaken
**Route:** `/admin/locations/{location}/bingo-items/create`

| # | Test | Verwacht Resultaat | Status |
|---|------|-------------------|--------|
| 2.1.1 | Open het formulier | Velden: Label, Punten, Feitje/Weetje, Icon afbeelding worden getoond | ☐ |
| 2.1.2 | Feitje veld toont placeholder tekst | "Bijv: Wist je dat een eekhoorn..." is zichtbaar | ☐ |
| 2.1.3 | Vul Label en Punten in, laat Feitje leeg, submit | Bingo item wordt aangemaakt (feitje is optioneel) | ☐ |
| 2.1.4 | Vul Label, Punten en Feitje in, submit | Bingo item met feitje wordt aangemaakt | ☐ |
| 2.1.5 | Helptext onder Feitje veld | "Een leuk feitje dat getoond wordt wanneer dit item gevonden is" | ☐ |

### 2.2 Bingo Item Bewerken
**Route:** `/admin/bingo-items/{id}/edit`

| # | Test | Verwacht Resultaat | Status |
|---|------|-------------------|--------|
| 2.2.1 | Open bestaand bingo item met feitje | Feitje veld is voorgevuld | ☐ |
| 2.2.2 | Wijzig feitje tekst, submit | Feitje wordt bijgewerkt | ☐ |
| 2.2.3 | Verwijder feitje (leeg maken), submit | Feitje wordt op null gezet | ☐ |
| 2.2.4 | "Terug naar bingo items" knop | Correct styled, linkt naar juiste locatie | ☐ |

### 2.3 Bingo Items Index
**Route:** `/admin/locations/{location}/bingo-items`

| # | Test | Verwacht Resultaat | Status |
|---|------|-------------------|--------|
| 2.3.1 | Tabel headers | Kolommen: Label, Punten, Feitje, Icon, (acties) | ☐ |
| 2.3.2 | Bingo item met feitje | Feitje tekst wordt getoond (max 2 regels, afgekapt) | ☐ |
| 2.3.3 | Bingo item zonder feitje | "-" wordt getoond in Feitje kolom | ☐ |
| 2.3.4 | Hover over afgekapte feitje | Title attribute toont volledige tekst | ☐ |
| 2.3.5 | Mobile view (< 768px) | Feitje wordt getoond onder punten in card layout | ☐ |

---

## 3. Route Stops (Vragen)

### 3.1 Vraag Bewerken
**Route:** `/admin/route-stops/{id}/edit`

| # | Test | Verwacht Resultaat | Status |
|---|------|-------------------|--------|
| 3.1.1 | "Terug naar vragen" knop styling | Consistent met andere pagina's (sky-blue button met pijl icon) | ☐ |
| 3.1.2 | Klik op "Terug naar vragen" | Redirect naar `/admin/locations/{location}/route-stops` | ☐ |
| 3.1.3 | "Annuleren" knop | Redirect naar vragen index van juiste locatie | ☐ |

---

## 4. Database Integriteit

| # | Test | Verwacht Resultaat | Status |
|---|------|-------------------|--------|
| 4.1 | Check `locations` tabel na aanmaken | `province`, `duration`, `image_path` kolommen correct gevuld | ☐ |
| 4.2 | Check `location_bingo_items` tabel | `fact` kolom correct gevuld of NULL | ☐ |
| 4.3 | Verwijder locatie met afbeelding | Afbeelding wordt uit storage verwijderd | ☐ |

---

## 5. Storage & Bestandsbeheer

| # | Test | Verwacht Resultaat | Status |
|---|------|-------------------|--------|
| 5.1 | Locatie afbeeldingen worden opgeslagen in | `storage/app/public/location-images/` | ☐ |
| 5.2 | Bingo item icons worden opgeslagen in | `storage/app/public/bingo-icons/` | ☐ |
| 5.3 | Storage symlink bestaat | `php artisan storage:link` is uitgevoerd | ☐ |
| 5.4 | Afbeeldingen zijn publiek toegankelijk | `/storage/location-images/{filename}` laadt correct | ☐ |

---

## 6. Validatie Berichten (Nederlands)

| # | Veld | Verwachte Foutmelding | Status |
|---|------|----------------------|--------|
| 6.1 | Locatie naam (leeg) | "Naam is verplicht." | ☐ |
| 6.2 | Locatie naam (duplicate) | "Deze locatie naam bestaat al." | ☐ |
| 6.3 | Province (leeg) | Laravel standaard validatiefout | ☐ |
| 6.4 | Duration (0 of negatief) | Laravel standaard validatiefout | ☐ |
| 6.5 | Image (te groot) | "max 2MB" fout | ☐ |

---

## 7. Cross-browser Testing

| Browser | Versie | Status |
|---------|--------|--------|
| Chrome | Latest | ☐ |
| Firefox | Latest | ☐ |
| Safari | Latest | ☐ |
| Edge | Latest | ☐ |

---

## 8. Responsive Design

| Viewport | Test | Status |
|----------|------|--------|
| Desktop (1920x1080) | Alle formulieren correct weergegeven | ☐ |
| Tablet (768x1024) | Formulieren schalen correct | ☐ |
| Mobile (375x667) | Bingo items tonen card layout | ☐ |

---

## Testcommando's

```bash
# Run alle tests
php artisan test

# Run specifieke feature tests
php artisan test --filter=LocationTest
php artisan test --filter=BingoItemTest

# Check database migraties
php artisan migrate:status

# Verify storage link
ls -la public/storage
```

---

## Bevindingen

| Datum | Test # | Bevinding | Ernst | Opgelost |
|-------|--------|-----------|-------|----------|
| | | | | |

---

## Sign-off

- [ ] Alle tests doorlopen
- [ ] Kritieke issues opgelost
- [ ] Ready for production
