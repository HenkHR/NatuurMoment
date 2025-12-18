# 🌿 NatuurMoment

An interactive group game that guides players through nature areas using their phones. Players complete bingo challenges by taking photos and answer multiple-choice questions about the location, competing for the highest score.

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-4E56A6?style=flat-square&logo=livewire&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.4-38BDF8?style=flat-square&logo=tailwindcss&logoColor=white)

---

## 📋 Table of Contents

1. [Features](#-features)
2. [Technology Stack](#-technology-stack)
3. [Entity Relationship Diagram (ERD)](#-entity-relationship-diagram-erd)
4. [Installation](#-installation)
5. [Deployment](#-deployment)
6. [Configuration](#-configuration)
7. [Edge Cases & Special Handling](#-edge-cases--special-handling)
8. [Project Structure](#-project-structure)
9. [Testing](#-testing)

---

<details>
<summary>✨ <b>Features</b></summary>

<br>

- **Game Hosting**: Create games with unique PIN codes for players to join
- **Bingo Mode**: Players capture photos of nature items to complete a 3x3 bingo card
- **Question Mode**: Sequential multiple-choice questions about the location
- **Real-time Leaderboards**: Track player scores and progress in real-time
- **Photo Management**: Host approves/rejects player photos with feedback
- **Admin Panel**: Full CRUD interface for managing locations, bingo items, and questions
- **Game Modes**: Configurable game modes per location (Bingo, Questions, or both)
- **Timer Support**: Optional countdown timer for games
- **Player Feedback**: Post-game feedback collection (rating and age)

</details>

---

<details>
<summary>🛠 <b>Technology Stack</b></summary>

<br>

| Component | Technology | Version |
|-----------|------------|---------|
| Backend Framework | Laravel | ^12.0 |
| Frontend Framework | Livewire | ^3.7 |
| PHP Version | PHP | ^8.2 |
| CSS Framework | Tailwind CSS | ^3.4 |
| JavaScript Framework | Alpine.js | ^3.4 |
| Charts | Chart.js | ^4.4.1 |
| Icons | Blade Icons (Heroicons, Lucide, Solar, Bootstrap) | Various |
| Fonts | Lexend, Figtree | Google/Bunny Fonts |
| Build Tool | Vite | ^7.0 |
| Storage | AWS S3 / Cloudflare R2 | Optional |
| Testing Framework | Pest PHP | ^4.1 |

</details>

---

<details>
<summary>🗄 <b>Entity Relationship Diagram (ERD)</b></summary>

<br>

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                           TEMPLATE LAYER                                                     │
│                                    (Admin-managed location templates)                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────┐
│           locations             │
├─────────────────────────────────┤
│ PK  id                          │
│     name                        │
│     description                 │
│     image_path                  │
│     province                    │
│     distance                    │
│     url                         │
│     game_modes (JSON)           │
│     bingo_three_in_row_points   │
│     bingo_full_card_points      │
│     created_at                  │
│     updated_at                  │
└─────────────────────────────────┘
           │
           │ 1:N
           ├──────────────────────────────────────────┐
           │                                          │
           ▼                                          ▼
┌─────────────────────────────────┐    ┌─────────────────────────────────┐
│    location_bingo_items         │    │    location_route_stops         │
├─────────────────────────────────┤    ├─────────────────────────────────┤
│ PK  id                          │    │ PK  id                          │
│ FK  location_id                 │    │ FK  location_id                 │
│     label                       │    │     name                        │
│     points                      │    │     question_text               │
│     icon                        │    │     option_a/b/c/d              │
│     fact                        │    │     correct_option (ENUM)       │
│     created_at                  │    │     points                      │
│     updated_at                  │    │     sequence                    │
└─────────────────────────────────┘    │     image_path                  │
                                       │     created_at                  │
                                       │     updated_at                  │
                                       └─────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                           INSTANCE LAYER                                                     │
│                                     (Runtime game instances & data)                                          │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

                                       ┌─────────────────────────────────┐
                                       │            games                │
                                       ├─────────────────────────────────┤
                                       │ PK  id                          │
              locations.id ◄───────────│ FK  location_id                 │
                                       │     pin (UNIQUE)                │
                                       │     status (ENUM)               │
                                       │     host_token                  │
                                       │     timer_enabled               │
                                       │     timer_duration_minutes      │
                                       │     timer_ends_at               │
                                       │     started_at / finished_at    │
                                       │     created_at / updated_at     │
                                       └─────────────────────────────────┘
                                                      │
                    ┌─────────────────────────────────┼─────────────────────────────────┐
                    │ 1:N                             │ 1:N                             │ 1:N
                    ▼                                 ▼                                 ▼
┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────────────┐
│         game_players            │  │         bingo_items             │  │         route_stops             │
├─────────────────────────────────┤  ├─────────────────────────────────┤  ├─────────────────────────────────┤
│ PK  id                          │  │ PK  id                          │  │ PK  id                          │
│ FK  game_id                     │  │ FK  game_id                     │  │ FK  game_id                     │
│     name                        │  │     label                       │  │     name                        │
│     token (UNIQUE)              │  │     points                      │  │     question_text               │
│     score                       │  │     position                    │  │     option_a/b/c/d              │
│     feedback_rating             │  │     icon_path                   │  │     correct_option (ENUM)       │
│     feedback_age                │  │     created_at                  │  │     points                      │
│     created_at                  │  │     updated_at                  │  │     sequence                    │
│     updated_at                  │  └─────────────────────────────────┘  │     image_path                  │
└─────────────────────────────────┘                 │                     │     created_at                  │
           │                                        │                     │     updated_at                  │
           │ 1:N                                    │ 1:N                 └─────────────────────────────────┘
           │                                        │                                    │
           ▼                                        ▼                                    │ 1:N
    ┌─────────────────────────────────┐      ┌─────────────────────────────────┐        │
    │      route_stop_answers         │      │           photos                │        │
    ├─────────────────────────────────┤      ├─────────────────────────────────┤        │
    │ PK  id                          │      │ PK  id                          │        │
    │ FK  game_player_id              │      │ FK  game_id                     │        │
    │ FK  route_stop_id ◄─────────────│──────│ FK  game_player_id              │        │
    │     chosen_option (ENUM)        │      │ FK  bingo_item_id ◄─────────────│────────┘
    │     is_correct                  │      │     path                        │
    │     score_awarded               │      │     status (ENUM)               │
    │     answered_at                 │      │     taken_at                    │
    │     created_at                  │      │     created_at                  │
    │     updated_at                  │      │     updated_at                  │
    │                                 │      └─────────────────────────────────┘
    │ UNIQUE(game_player_id,          │
    │        route_stop_id)           │
    └─────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                           SYSTEM LAYER (Admin users)                                         │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────┐
│            users                │
├─────────────────────────────────┤
│ PK  id                          │
│     name                        │
│     email (UNIQUE)              │
│     password                    │
│     is_admin                    │
│     admin_per_page              │
│     created_at                  │
│     updated_at                  │
└─────────────────────────────────┘
```

### Key Relationships

| Parent Table | Child Table | Relationship | Foreign Key |
|--------------|-------------|--------------|-------------|
| `locations` | `location_bingo_items` | 1:N | `location_id` |
| `locations` | `location_route_stops` | 1:N | `location_id` |
| `locations` | `games` | 1:N | `location_id` |
| `games` | `game_players` | 1:N | `game_id` |
| `games` | `bingo_items` | 1:N | `game_id` |
| `games` | `route_stops` | 1:N | `game_id` |
| `game_players` | `photos` | 1:N | `game_player_id` |
| `game_players` | `route_stop_answers` | 1:N | `game_player_id` |
| `bingo_items` | `photos` | 1:N | `bingo_item_id` |
| `route_stops` | `route_stop_answers` | 1:N | `route_stop_id` |

### ENUM Values

| Table | Column | Values |
|-------|--------|--------|
| `games` | `status` | `lobby`, `started`, `finished` |
| `photos` | `status` | `pending`, `approved`, `rejected` |
| `location_route_stops` / `route_stops` | `correct_option` | `A`, `B`, `C`, `D` |
| `route_stop_answers` | `chosen_option` | `A`, `B`, `C`, `D` |

</details>

---

<details>
<summary>🚀 <b>Installation</b></summary>

<br>

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (for development) or MySQL/PostgreSQL (for production)

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd NatuurMoment
```

### Step 2: Install Dependencies

```bash
composer install
npm install
```

### Step 3: Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### Step 4: Database Setup

```bash
php artisan migrate
php artisan db:seed  # Optional: seed with sample data
```

### Step 5: Build Assets

```bash
npm run build       # Production build
# OR
npm run dev         # Development with hot reload
```

### Step 6: Start the Development Server

```bash
php artisan serve
# OR use the dev script (includes queue worker and Vite)
composer run dev
```

The application should now be running at `http://localhost:8000`

### Step 7: Create Admin User (Optional)

```bash
php artisan db:seed --class=DatabaseSeeder
# Default admin: admin@example.com / password
```

> ⚠️ **Security**: Change the default admin password immediately after first login!

</details>

---

<details>
<summary>🌐 <b>Deployment</b></summary>

<br>

### Laravel Cloud (Recommended)

This project was developed and deployed using **Laravel Cloud**. Laravel Cloud provides a seamless deployment experience for Laravel applications.

#### Deploying to Laravel Cloud

1. **Create a Laravel Cloud account** at [cloud.laravel.com](https://cloud.laravel.com)

2. **Connect your repository** - Link your GitHub repository to Laravel Cloud

3. **Create a new application** - Select your repository and branch

4. **Configure environment variables** - Add the following in the Laravel Cloud dashboard:
   - `APP_KEY` (generate with `php artisan key:generate --show`)
   - `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_BUCKET` (for photo storage)

5. **Configure build settings**:
   - Build command: `npm install && npm run build`
   - Laravel Cloud automatically runs `composer install` and `php artisan migrate`

6. **Deploy** - Push to your branch or trigger a manual deployment

Laravel Cloud handles SSL certificates, queue workers, and automatic deployments on push.

---

### Manual Deployment

#### Production Requirements

- PHP 8.2+ with extensions (openssl, pdo, mbstring, tokenizer, xml, ctype, json, fileinfo)
- MySQL 5.7+ or PostgreSQL 10+
- Web server (Nginx or Apache) with mod_rewrite
- SSL certificate (HTTPS required for photo uploads)

#### Deployment Steps

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

</details>

---

<details>
<summary>⚙️ <b>Configuration</b></summary>

<br>

### Key Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_ENV` | Environment | `production` |
| `APP_DEBUG` | Debug mode (false in production!) | `false` |
| `APP_URL` | Application URL | `https://natuurmoment.example.com` |
| `DB_CONNECTION` | Database driver | `mysql` |
| `AWS_ACCESS_KEY_ID` | AWS/R2 access key (optional) | `your_access_key` |
| `AWS_SECRET_ACCESS_KEY` | AWS/R2 secret key (optional) | `your_secret_key` |
| `AWS_BUCKET` | S3/R2 bucket name (optional) | `natuurmoment-photos` |

### File Storage

- **Local Storage**: Photos stored in `storage/app/public/photos` by default
- **Cloud Storage**: Configure AWS credentials in `.env` to use S3/R2
- The `Photo` model automatically falls back to local storage if cloud is unavailable

</details>

---

<details>
<summary>⚠️ <b>Edge Cases & Special Handling</b></summary>

<br>

### 1. Sequential Question Unlocking

Players must answer questions in sequence. Question N+1 is only unlocked after question N is answered.

- **Solution**: `RouteStop::isUnlockedFor()` checks if all previous questions are answered
- **Direct URL access**: Prevented by validation in `PlayerRouteQuestion` component
- **Browser refresh**: State persisted in database, players continue from current question

### 2. Duplicate Answer Prevention

Players cannot answer the same question twice.

- **Solution**: Database unique constraint on `[game_player_id, route_stop_id]`
- **Race condition**: Database constraint catches duplicate submissions
- **UI protection**: Submit button disabled after first answer

### 3. Photo Approval Workflow

Host must approve photos before they count toward bingo completion.

- **Solution**: Photos have `status` field: `pending`, `approved`, `rejected`
- **Completion check**: Only `approved` photos count toward 9-photo requirement

### 4. Game Mode Validation

Locations must have sufficient content for enabled game modes.

- **Bingo Mode**: Requires at least 9 bingo items
- **Question Mode**: Requires at least 1 question
- **Validation**: Locations without valid game modes hidden from home page

### 5. PIN Collision Prevention

Game PINs must be unique.

- **Solution**: `Game::generatePin()` uses do-while loop to ensure uniqueness
- **Database constraint**: Unique index on `pin` column

### 6. Photo Storage Fallback

Cloud storage may be unavailable.

- **Solution**: `Photo::getUrlAttribute()` automatically falls back to local storage
- **Error handling**: Exceptions during cloud storage checks are caught and logged

</details>

---

<details>
<summary>📁 <b>Project Structure</b></summary>

<br>

```
NatuurMoment/
├── app/
│   ├── Constants/          # Game mode constants
│   ├── Http/
│   │   ├── Controllers/    # REST controllers
│   │   ├── Middleware/     # Custom middleware (IsAdmin)
│   │   └── Requests/       # Form request validation
│   ├── Livewire/           # Livewire components
│   │   ├── CreateGame.php
│   │   ├── HostGame.php
│   │   ├── HostLobby.php
│   │   ├── JoinGame.php
│   │   ├── PlayerPhotoCapture.php
│   │   ├── PlayerRouteQuestion.php
│   │   └── ...
│   ├── Models/             # Eloquent models
│   └── Rules/              # Custom validation rules
├── config/                 # Configuration files
├── database/
│   ├── migrations/         # Database migrations
│   ├── seeders/            # Database seeders
│   └── factories/          # Model factories
├── public/                 # Public assets
├── resources/
│   ├── css/                # Tailwind CSS
│   ├── js/                 # JavaScript/Alpine.js
│   └── views/              # Blade templates
│       ├── admin/          # Admin panel views
│       └── livewire/       # Livewire component views
├── routes/                 # Route definitions
├── storage/                # File storage
└── tests/                  # Pest PHP tests
```

</details>

---

<details>
<summary>🧪 <b>Testing</b></summary>

<br>

The project uses **Pest PHP** for testing.

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/Admin/LocationTest.php

# Run with coverage
php artisan test --coverage
```

### Test Coverage

- **Feature Tests**: Admin panel CRUD operations, authentication, game flow
- **Unit Tests**: Model relationships, helper methods, validation rules
- **Livewire Tests**: Component interactions, form submissions, real-time updates

</details>

---

<details>
<summary>📝 <b>TODO</b></summary>

<br>

- [ ] Add functionality for host to play the game with the players
- [ ] Let hosts create accounts so they can create their own routes/locations

</details>

---

## 📄 License

This project is licensed under the MIT License.

---

<p align="center">Made with ❤️ for nature enthusiasts</p>
