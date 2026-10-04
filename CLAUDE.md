# CLAUDE.md

Guidance for Claude Code (claude.ai/code) in this repository.

## What this is

The website of Oxid Architektur (production: **https://oxid-architektur.ch**;
www.oxid.ch is a different, static page). A Laravel 13 app with two faces:

- **Public site:** Blade templates (`resources/views/frontend/`), Sass, and
  vanilla ES modules (`resources/js/frontend/modules/`). Projects with a
  per-project grid layout, the works list (`/werkliste/...`), discourse,
  team, profile, jobs, history, contact, and search.
- **Admin:** a Vue 3 SPA under `/admin` (`resources/js/backend/`), talking
  to the JSON API in `routes/api.php`.

The 2026 rework (Laravel 11 → 13, Vue 2 → 3, Mix → Vite, JWT → Sanctum,
Algolia → own search, image-cache → Glide) is planned and logged in
`.rewrite/`. **`.rewrite/06-progress.md` is the logbook**: decisions, what
was verified and how, known issues, and the deploy notes.

## Stack

- PHP ^8.3 (production on Hostpoint: PHP 8.3–8.5, MariaDB 10.11),
  Laravel 13, Sanctum 4, Glide 4 + Intervention Image 4,
  spatie/laravel-translatable (de/en fields as JSON).
- Vite 8 with `laravel-vite-plugin`; Vue 3.5, vue-router, axios, Tiptap,
  vue-advanced-cropper, SortableJS, Phosphor icons.
- Public JS: no framework; Swiper 14 (discourse detail slider), lazysizes,
  Google Maps JS API on the contact page (`maps.js`, its own entry).

## Commands

```bash
npm run dev          # Vite dev server (writes public/hot — remove it if the server dies)
npm run build        # public site + admin into public/build, plus busu.css
php artisan test     # PHPUnit 12 (tests/Unit, tests/Feature)
./vendor/bin/pint    # code style
php artisan migrate
php artisan optimize # config/route/view cache — works, keep it that way
```

`public/build/` and `public/assets/css/busu.css` are **committed**: run
`npm run build` and commit the output with the change that needs it.
`busu.css` (from `resources/sass/frontend-busu/`) is consumed by another
site; keep its output path.

## Architecture notes

### Routing and auth

- `routes/web.php`: public pages, the `/img/...` image routes, and
  `admin/{any?}`, which serves the SPA shell (`backend/app.blade.php`) and
  picks a random published home image for the login background.
- `routes/api.php`: everything behind `auth:sanctum`, cookie-based
  (stateful SPA): `GET /sanctum/csrf-cookie`, then `POST /api/auth/login`.
  `bootstrap/app.php` renders every `api/*` exception as JSON (401
  included). `APP_URL` must be the exact origin for Sanctum's stateful
  check.

### Images

- Uploads live flat in `storage/app/public/uploads/`. Image models
  (`ProjectImage`, `DiscourseImage`, `HomeImage`, `TeamImage`, `JobImage`,
  `ProfileImage`) use the `App\Models\Concerns\IsImage` trait: crop
  (`coords_w/h/x/y`), stored `width`/`height`, `url($size, $format)`,
  `srcset()`.
- `App\Support\Glide` builds **signed** `/img/{file}?w&h&fit&crop&fm&s`
  URLs; `ImageController` renders them into `storage/app/.glide-cache`
  (Imagick if loaded, else GD). Every rendition is checked before it is
  served; a broken one is re-rendered, then falls back to the upload's
  format (see `06-progress.md`, "Broken AVIF renders").
- Blade: `<x-image :image="$image" preset="large" />` → `<picture>` with
  AVIF/WebP sources where the server can write them
  (`ImageSupport::modernFormats()`). Presets in `config/images.php`.
- The admin uses the fixed `/img/thumbnail|large|original/{file}` routes
  (`resources/js/backend/lib/images.js`). `/img/crop/...` and
  `/img/home/...` are legacy URLs that 301 to signed ones.
- `php artisan images:clear` is a leftover that deletes directories which
  no longer exist; the Glide cache is `storage/app/.glide-cache`.

### Search

Own scoring search in `app/Services/Search/` (`SearchIndex`, `Tokenizer`,
`SearchService`), no Scout/Algolia. The index is flushed when a record is
saved. See `.rewrite/07-search.md`.

### Admin SPA (`resources/js/backend/`)

Built in the shape of the luvo project (github.com/marceli-to/luvo):
`<script setup>` only, no Options API or mixins.

- `lib/`: `http` (axios instance, XSRF, error notifications), `notify`,
  `images`, `utils`.
- `composables/`: `useResourceForm`, `useListing`, `useOrder`,
  `useImages`, `useImageLibrary`, `useFiles`, `useEscape`.
- `components/ui/`: `Card`, `Lightbox` (native `<dialog>`), `Toggle`,
  `Tabs`, `Uploader`, `SortableList` (drag ordering via SortableJS),
  `ListActions`, `Notifications`, Tiptap `editor/`.
- `views/<resource>/{Index,Form}.vue`; `router.js` is lazy-loaded with a
  session guard. Styles: `resources/sass/backend/` (1px `$border` tokens).
- The admin is desktop-only (`$page-min-width: 840px`).

### Public JS (`resources/js/frontend/`)

Each module exports `init()`; `app.js` imports and calls them. Behaviour
hooks are `data-` attributes grouped by module (`[data-collapsible="btn"]`,
`[data-filter="item"]`); `is-*`/`has-*` state classes stay classes because
the Sass styles them. Show/hide with the `hidden` attribute (backed by
`[hidden] { display: none !important }`), scroll with
`window.scrollTo({ behavior: 'smooth' })`. Shared helpers live in `lib/`
(`utils.js`, `sections.js` for the prev/next section scrollers). No jQuery
— don't reintroduce it.

The baseline scripts used for the de-jQuery (Playwright behaviour log and
screenshots) are described in `.rewrite/06-progress.md`, "Public site JS".
After changing a JS dependency, restart the Vite dev server with
`npx vite --force`, or the public pages get 504s on stale pre-bundled deps.

## Local environment

- Served by Laravel Herd (PHP-FPM) at https://oxid.ch.test. The database is
  MAMP MySQL 5.7 over the socket `/Applications/MAMP/tmp/mysql/mysql.sock`
  (production is MariaDB 10.11).
- The local DB and `storage/app/public/uploads` are copies of production
  (2026-10-04). People edit content in the local admin while you work:
  tests must clean up after themselves (create → delete), never restore
  whole tables.
- Admin API tests (`tests/Feature/Admin/`) extend `AdminTestCase`: in-memory
  SQLite + faked local disk, so they may write freely. Put new tests that
  write data there.
- Reorder endpoints rewrite `order` for every row of a list; check
  bulk-writing endpoints inside a rolled-back transaction instead of
  against live rows.
