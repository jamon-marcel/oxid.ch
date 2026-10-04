# Oxid Architektur

Website of Oxid Architektur — **https://oxid-architektur.ch**.

A Laravel 13 app with a public site (Blade, Sass, vanilla ES modules) and a
Vue 3 admin at `/admin` that edits the content through a JSON API.

- **Public site:** projects with per-project grid layouts, works list
  (`/werkliste`), discourse, team, profile, jobs, history, contact, search.
- **Admin:** news, projects, discourse, team, jobs, profile, contact, home images;
  image upload with cropping, drag-and-drop ordering, rich text (Tiptap).
- **Images:** uploads are rendered on demand by Glide via signed
  `/img/...` URLs, served as AVIF/WebP/JPEG `<picture>` sources.
- **Search:** own scoring search (`app/Services/Search/`), no external
  service.

## Requirements

- PHP 8.3+ (Imagick recommended, GD works), Composer
- MySQL 5.7+ / MariaDB 10.11
- Node 20+ and npm (only to build assets)

## Setup

```bash
composer install
npm install
cp /path/to/existing/.env .env   # there is no .env.example
php artisan key:generate   # only if the .env has no APP_KEY
php artisan migrate
php artisan storage:link
npm run dev             # or: npm run build
```

`.env` essentials: `DB_CONNECTION=mysql` (the framework default is
SQLite), the DB credentials, and an `APP_URL` that matches the origin
exactly — the admin's Sanctum cookie auth depends on it.

Admin users are created with `php artisan tinker`; there is no sign-up.

## Commands

| Command | What it does |
|---|---|
| `npm run dev` | Vite dev server (writes `public/hot`; delete it if the server dies) |
| `npm run build` | Builds public site + admin into `public/build`, plus `public/assets/css/busu.css` |
| `php artisan test` | PHPUnit (unit + feature; admin API tests run on in-memory SQLite) |
| `./vendor/bin/pint` | Code style |
| `php artisan images:warm` | Renders every image the public pages use into the Glide cache (`--dry-run` only counts) |
| `php artisan optimize` | Config, route and view cache |

`public/build/` and `busu.css` are committed — run `npm run build` and
commit the output together with the change that needs it. `busu.css` is
used by another site; keep its path.

## Deploy (Hostpoint)

1. `npm run build` locally, commit, push.
2. On the server: `git pull`, then
   `composer install --no-dev --optimize-autoloader`.
3. `php artisan migrate --force`
4. `php artisan optimize`
5. `php artisan images:warm` (the first run takes a while).

`storage/` and `storage/app/.glide-cache` must be writable. Full deploy
notes, including the `.env` changes for the 2026 rework, are in
`.rewrite/06-progress.md` ("Deploy notes").

## Further reading

- `CLAUDE.md` — architecture: routing and auth, images, search, admin SPA,
  public JS conventions.
- `.rewrite/` — the 2026 rework (Laravel 11 → 13, Vue 2 → 3, Mix → Vite,
  JWT → Sanctum, Algolia → own search). `06-progress.md` is the logbook.
