# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - 2025-12-02

### Added
- Admin panel voor beheer van locaties, bingo items, vragen en games
- IsAdmin middleware voor admin-only route bescherming
- User model uitgebreid met `is_admin` boolean veld
- Volledige CRUD voor locaties met cascade delete naar bingo items en route stops
- Volledige CRUD voor bingo items (genest onder locaties met shallow routing)
- Volledige CRUD voor route stops/vragen (genest onder locaties met shallow routing)
- Read/delete functionaliteit voor games (games worden door spelers aangemaakt)
- Admin navigatie link in hoofdnavigatie (alleen zichtbaar voor admins)
- Dutch validation messages voor alle form requests
- Admin seeder voor test admin gebruiker (admin@example.com)
- Eloquent models: Location, LocationBingoItem, LocationRouteStop, Game
- Model factories voor alle nieuwe models
- 38 geautomatiseerde tests voor admin panel functionaliteit
