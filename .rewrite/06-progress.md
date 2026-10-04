# Progress

Survey done 2026-10-04 against `f140dca` on `master` (clean tree).
Backend steps 3 (dead code), 4 (Laravel 13 + Glide dependency), search
phase 1, 5 (Glide images), 6 (slim skeleton), 7 (config diff) and JWT →
Sanctum and the validation-message check done 2026-10-04.

Production: **https://oxid-architektur.ch** (www.oxid.ch is a different, static page).

Branch: **`rework/laravel-13-vue-3`**, cut from `f140dca` on 2026-10-04.

## Before the first commit

- [x] `04-open-questions.md` #1 — **production is PHP 8.3 up to 8.5.**
      Gate cleared 2026-10-04. The driver question (Imagick vs GD) stays open
      but does not block: the pipeline detects formats at runtime.
- [ ] Run the driver one-liner from `04-open-questions.md` #1 on the server,
      and record the result here. Also check the **CLI** PHP version, not
      just the web one.
- [ ] Grep production access logs for `/img/project/` and `/img/tiny/`
      (`04-open-questions.md` #3).
- [ ] Collect 20–30 real search queries from the production access logs —
      the search page is a `GET`, so `?keyword=` is in there. Needed to tune
      the ranking in `07-search.md` phase 2.
- [x] **Production DB → local `oxid`** (2026-10-04). Dump from Hostpoint
      (MariaDB 10.11.19), imported into local MySQL 5.7.39; all 19 tables
      with data match the dump row for row. Prod and repo have run the same
      51 migrations. Previous local DB (an older snapshot, behind prod on
      every content table) backed up to
      `~/backups/oxid-local-before-prod-2026-10-04/oxid-local.sql`; the prod
      dump is kept beside it as `oxid-prod-2026-10-04.sql`.
- [x] **Production `storage/` → local** (2026-10-04).
      `storage/app/public/uploads`: 1,063 files, 1.8 GB, flat (no subdirs).
      **Every file the DB references is present** — 943 records across the
      6 image tables and `team_documents`, 0 missing.
      **120 files (158 MB: 65 jpg, 54 png, 1 pdf) are referenced nowhere** —
      not by any table, not anywhere else in the dump, not in code. Likely
      leftovers from uploads whose records were never saved or were deleted by
      older code (the current delete endpoints do remove files). Harmless:
      nothing emits their URLs. Not touched; a cleanup candidate after
      go-live, not part of this project.
- [ ] Decide on the dev/prod database mismatch: production is **MariaDB
      10.11**, local is **MySQL 5.7** (EOL). The dump imported cleanly, but
      check Laravel 13's minimum MySQL version before the framework bump.
- [ ] Record the current state for comparison: route list, the `/img/...` URLs
      every public page emits and their byte sizes, and screenshots of the
      public pages at 375 and 1280 px.

## Backend

| Step | Status | Commit |
|---|---|---|
| Delete dead code: 6 filter classes, `dompdf`/`media`/`content` configs | ✅ done — 144 routes, config caches; `home`, `small`, `thumbnail` images 200; `tiny`, `project` 400 | `0afa861` |
| **One commit:** Laravel 13, PHP ^8.3, drop image-cache, add Glide + Intervention 4 | ✅ done — 0 advisories; 141 routes (the 3 image-cache `/img` routes gone); all 16 public pages 200, 404 renders as 404; all 36 read-only admin API GETs 200 with a JWT; upload 200. **Keyword search 500s** — see below | `a809fd2` |
| Search phase 1: drop Algolia, Scout `collection` driver — **moved up from step 8**, it fixed the Guzzle 8 search 500 | ✅ done — 15 queries compared with production, see `07-search.md` | `e9d5dde` |
| Glide routes, `ImageSupport`, requested sizes + WebP/AVIF, `ImageHelper` → `<picture>` | ✅ done — 167 production renders compared, geometry matches 167/167; 5 routes incl. the admin's `large`/`thumbnail`/`original`; full crawl of every emitted URL: see notes | `62c73c4` |
| Slim skeleton, `app/User.php` → `app/Models/User.php` | ✅ done — same 146 routes, same per-route middleware; all 21 public pages 200, 404 renders as 404; 32 admin API GETs 200 with a JWT, `auth/me` + `auth/refresh` OK; 422 shape unchanged; `config:cache` OK. (`route:cache` was recorded as OK here too — wrong, it failed; see step 7) | `5c014f1` |
| Config diff against L13 (was step 7), drop `intervention/image-laravel` | ✅ done — 145 routes (duplicate `/suche` removed); `config:cache` **and `route:cache`** OK; effective config unchanged except `same_site` → `lax` and the cache key prefix; public pages, admin API GETs, throttle headers OK | `5bdc257` |
| JWT → Sanctum, incl. the Vue 2 SPA's auth bootstrap | ✅ done — cookie flow verified with curl and in headless Chromium against the Vue 2 admin: login, 8 list screens, edit + save, upload, session expiry on navigation and on POST, logout; 145 routes, caches OK | `046c9e8` |
| Form-request validation messages (L12+ wants strings) | ✅ checked, **no change needed** — all 10 form requests already return string messages (ran each one's rules + messages through the validator: 17 errors, 0 non-string); 422 shape verified unchanged in the Sanctum run | (docs only) |
| Search phase 2: own scoring search + unit tests, drop Scout | — | |
| **Rethink image handling — more generic** (requested 2026-10-04, see below) | — open, design first | |

The Laravel 13 bump and the image-cache → Glide dependency swap **must be
the same commit** — Composer will not resolve anything on Laravel 11. See
`02-backend-laravel13.md`, "Why step 4 must be one commit".

### Deferred: generic image handling

Requested by the user on 2026-10-04, after step 7: the step 5 result —
`ImageController` (5 per-purpose actions: `original`, `thumbnail`, `large`,
`home`, `crop`) plus `ImageHelper` (one static method per page use:
`largeImage`, `previewImage`, `teaserImage`, `homeImage`, `openGraphImage`) —
is too special-cased and should become **more generic**. Not decided how;
write the design up in `05-image-pipeline.md` first, then implement. Step 5's
verified behaviour (framing matches production 167/167, the URL shapes the
public pages emit) is the regression baseline for it. Do it after the
remaining backend steps, before the frontend port touches the admin's image
screens.

### Notes from step 4

- **Keyword search was broken until search phase 1** (fixed in the next
  commit). Laravel 13 lets Composer pick Guzzle 8, which removed
  `GuzzleHttp\choose_handler()`; the Algolia v3 client still calls it
  (`src/Http/GuzzleHttpClient.php:56`).
- `config.platform.php` is pinned to `8.3.0`, so the lock always resolves
  for the production minimum no matter which PHP runs Composer locally
  (local CLI is 8.4; Symfony 8 would need 8.4). Resolved Symfony 7.4.20.
- PHPUnit is `^12`, not 13 as the dependency table said: 13 needs PHP
  ≥ 8.4.1. Same choice as luvo.
- `intervention/image-laravel` stays per the plan, but nothing uses its
  `Image` facade — the only Intervention caller is `MediaController`, which
  builds its own `ImageManager`. It can go with the skeleton cleanup in
  step 6/7, along with `config/image.php`.
- The six `*ImageController::removeCachedImage()` lost their
  `ImageCache::clearImageCache()` call; the manual sweep of
  `storage/app/public/cache` stays. Step 5 replaces the method body with
  `Glide::server()->deleteCache()`, as luvo does.
- `MediaController`: Intervention 4 `read()` → `decodePath()`.

### Notes from step 5

Details in `05-image-pipeline.md`, "Result".

- **Driver is detected** — Imagick when loaded (AVIF, and no PHP-memory
  bitmap), GD otherwise. Production output so far was GD; Imagick changes
  resampling, not framing. Confirm on Hostpoint, and check the web
  `memory_limit` if it turns out GD-only (a 24 MP source needs ~95 MB).
- **Largest srcset candidate = legacy output** (longer side 2400), measured
  box sizes justify it; smaller screens get 900/1200/1600.
- **Cold renders** take 0.3–1.3 s each (AVIF slowest). The old pipeline had
  the same first-hit cost. Consider an `images:warm` command for the deploy
  (luvo's `Glide.php` mentions one).
- **Full crawl done:** all 6,187 `/img/...` URLs the public pages emit
  (at `62c73c4`) return 200 — 2,054 AVIF, 2,054 WebP, 1,551 JPEG, 528 PNG.
- **Still to do for this step:** screenshot comparison of the public pages
  against production (the `<picture>` wrapper), and the admin image screens
  once the SPA runs again.

### Notes from step 6

- **Deleted:** `app/Http/Kernel.php`, all 7 `app/Http/Middleware/*`,
  `app/Console/Kernel.php`, `app/Exceptions/Handler.php`, the Auth/Broadcast/
  Event/Route service providers, `routes/channels.php`,
  `tests/CreatesApplication.php`, and the legacy `database/seeds/` (a
  pre-namespace copy of `database/seeders/`). Every custom middleware was a
  stock subclass with default settings, except the two below.
- **`bootstrap/app.php`** carries the only two non-defaults:
  `throttle:200,1` prepended to the `api` group, and
  `shouldRenderJsonWhen(api/* or expectsJson)`. The second replaces the old
  `Authenticate` middleware, which turned `AuthenticationException` into a
  401 `UnauthorizedHttpException` — without it, a guest hitting `api/*`
  without `Accept: application/json` would be redirected to a non-existent
  `login` route (500).
- **Observable differences, all on unauthenticated `api/*` calls:** same 401
  and same `{"message":"Unauthenticated."}`, but no `WWW-Authenticate:
  JWTAuth` header (the SPA does not read it), and no `X-RateLimit-*` headers,
  because the framework's middleware priority now runs auth before the
  throttle. With `APP_DEBUG` on, the 401 body no longer carries a trace.
- **New global middleware from the framework defaults:** `HandleCors`
  (framework `cors` config: `api/*`, any origin, no credentials),
  `ValidatePathEncoding`, `InvokeDeferredCallbacks`. `HandleCors` matters for
  the Sanctum step — revisit in step 7's config diff and keep credentials off.
- **`TrustProxies`:** still trusts no proxy, as before. If Hostpoint
  terminates TLS in front of PHP and `https` URLs come out as `http`, this is
  the place (`$middleware->trustProxies(at: ...)`); it was not set before
  either.
- **JWT sessions end once on deploy.** jwt-auth puts a hash of the user model
  class into the token (`prv` claim); `App\User` → `App\Models\User`
  changes it, so existing tokens fail and the SPA falls back to the login
  screen. Harmless, and Sanctum replaces the tokens anyway.
- **`config/app.php`** is down to the non-default keys plus the two custom
  facade aliases (`AppHelper`, `ImageHelper`). The `Image` alias went —
  nothing calls it, and the Intervention package registers it itself.
  Providers are auto-discovered; `bootstrap/providers.php` lists only
  `AppServiceProvider`.
- **Factories/seeders:** autoload switched from `classmap` to PSR-4
  (`Database\Factories`, `Database\Seeders`), and `UserFactory` is now a
  class-based factory. Pre-existing and **not fixed**: the three other
  factories still use the Laravel ≤7 `$factory->define()` syntax, and the
  seeders call `Model::factory()` on models without `HasFactory`, so
  `db:seed` fails. Nothing on production seeds; a QA-automation item.
- `CLAUDE.md` named the image command `app:clear-images`; it is
  `images:clear`. Fixed. The rest of `CLAUDE.md` (Algolia, image-cache,
  Laravel 11) is rewritten at the end of the project.

### Notes from step 7

Method: dump the resolved `config('<file>')` with the file present and with it
moved aside (framework defaults), and diff. Then decide per file.

- **Deleted, defaults are equivalent or the subsystem is unused:** `view`
  (identical), `database` (the `mysql` connection resolves the same; only the
  unused redis/sqlite/pgsql blocks differ), `queue` (nothing is queued),
  `broadcasting` (nothing broadcasts), `hashing` (bcrypt 10 → 12 rounds;
  existing hashes still verify and are rehashed on login), `mail` (the app
  sends **no** mail — no `Mail::` anywhere; the file's L6-era top-level
  `driver`/`host`/`encryption` keys were already ignored since Laravel 11),
  `services` (only stale mailgun/sparkpost/stripe stubs).
- **Republished from L13 (`config:publish --force`), with customisations re-applied:**
  - `cache`: default store **`file`**, not `database`. This one is load-bearing:
    the API's `throttle:200,1` uses the default store, and there is no `cache`
    table, so the L13 default would 500 every API call.
  - `session`: driver `file`; cookie name kept as `oxid_architektur_gmbh_session`
    (the L13 default would produce `oxid_architektur_gmb_h_session`).
    `same_site` is now `lax` (was `null`) — deliberately adopted; it is what
    the Sanctum step needs.
  - `logging`: stack channel `daily`, as before.
  - `filesystems`: the `local` disk root stays **`storage/app`** (L13: `app/private`)
    and `serve` stays off. Load-bearing: every image/document delete endpoint
    and `images:clear` call `Storage::allDirectories('public')` /
    `Storage::delete('public/…')` on the default disk.
- **Left as is:** `auth`, `jwt` (rewritten in the Sanctum step), `scout`
  (goes in search phase 2), `seo`, `settings`, `app` (done in step 6).
  `cors` is not published; the framework default applies (see step 6 notes).
- **`intervention/image-laravel` removed**, with `config/image.php`: nothing used
  its facade or config. `intervention/image` stays (MediaController, Glide).
  `composer audit` clean.
- **`route:cache` failed — pre-existing on `master`, fixed.** `/suche` and
  `/suche/{keyword?}` were both named `page.search.index`. The first route
  went; the second matches `/suche` too, and `route('page.search.index')`
  still yields `/suche`. So production has never been able to cache its
  routes. 146 → 145 routes.
- Pre-existing, not changed: `/suche/{keyword}` ignores the path segment —
  `SearchController` only reads `?keyword=`. Revisit in search phase 2.

### Notes from JWT → Sanctum

- **Backend:** `laravel/sanctum` 4.3.3 in, `php-open-source-saver/jwt-auth`
  out (with `lcobucci/jwt`, `namshi/jose`), `config/jwt.php` deleted.
  `config/sanctum.php` freshly published — not ported (luvo's `c4ba6a6`
  warning); it points at `ValidateCsrfToken`, which exists in L13 as a
  subclass of `PreventRequestForgery`. `config/auth.php` republished from
  L13: default guard `web`, no `api` guard; reset table kept at
  `password_resets` (the table that exists).
- `bootstrap/app.php`: `statefulApi()`. Routes: `auth:api` → `auth:sanctum`;
  `auth/refresh` gone; `auth/login` now **throttled 10/min** (new — JWT login
  had only the global 200/min); `logout`/`me` behind `auth:sanctum` at the
  route instead of the controller constructor. `AuthController`: session
  login with `regenerate()`, logout with `invalidate()` + `regenerateToken()`.
  The `/api/user` route stays.
- **Login response changed:** it returns the user, not
  `{access_token, token_type, expires_in}`. A failed login is still
  `401 {"error":"Unauthorized"}`.
- **SPA (Vue 2, rebuilt):** no tokens or `localStorage` anywhere. The router
  guard asks `POST /api/auth/me`; one interceptor sends 401/419 to the login
  screen (auth calls excluded). Login calls `/sanctum/csrf-cookie` first.
  Logout calls the API. Dropzone (4 configs) sends `X-CSRF-TOKEN` from the
  meta tag.
- **Two pre-existing SPA bugs surfaced and fixed:**
  - `<meta name="csrf-token" value=…>` — `value`, not `content`, so axios had
    been sending `X-CSRF-TOKEN: undefined`. Harmless under JWT; under
    Sanctum it would beat the cookie's `X-XSRF-TOKEN` and 419 every POST.
    The axios default header is gone entirely now (axios reads the cookie);
    the meta tag is fixed for Dropzone.
  - Two axios instances: `bootstrap.js` did `require('axios')` (the CJS
    build) while `app.js` did `import` (ESM), so `window.axios` — used by the
    login/logout components — had no interceptors. Now one instance.
- **419 vs 401:** L13's `PreventRequestForgery` accepts a browser request
  with `Sec-Fetch-Site: same-origin` without a token, so an expired session
  usually shows up as **401**, not 419, even on POST. The interceptor
  handles both.
- **Session expiry** is now inactivity-based (`SESSION_LIFETIME`, 120 min);
  every API call extends it. JWT had a TTL plus silent refresh. Unsaved form
  data is lost on expiry either way.
- **Build:** local `node_modules` was behind `yarn.lock` (axios 1.8.4 vs the
  locked and previously shipped 1.13.5) — `yarn install --frozen-lockfile`
  fixed it, no lockfile change. Only the admin JS bundle was rebuilt (a
  temporary backend-only Mix config); Mix rewrites `mix-manifest.json` with
  only what it built, so the manifest was restored and just the backend
  hash updated. Public bundles untouched.
- **Test residue, cleaned:** a temporary user and two uploaded test PNGs
  were removed. The local news entry 2 got an unchanged save (only
  `updated_at` moved).
- Pre-existing, not changed: a POST to an unknown `api/*` URL returns **405**
  JSON, not the 404 fallback, because `Route::fallback` is GET-only.

### Notes from the validation-message check

- luvo's 500s came from array-valued messages; oxid has none. Nothing to
  port, no `BaseFormRequest` needed.
- Pre-existing, **not changed**: there is no `resources/lang/de/validation.php`
  and `fallback_locale` is `de`, so any rule without a custom message shows
  its raw key (`validation.string`, `validation.email`). Only `required` has
  custom messages. Fix candidates: add a German `validation.php`, or set
  the fallback to `en` — the latter would also change which file answers
  missing `content`/`settings` keys on the public site, so check that first.
- Lang files still live in `resources/lang`; L13 picks that up
  (`app()->langPath()`), moving them to `lang/` is optional.

### To verify at the end of the backend phase

- Same routes as the baseline: 145 since step 7 (146 before minus the
  duplicate `/suche`).
- Every public page 200.
- `api/*` → 401 JSON when unauthenticated; session + XSRF cookies set.
- 404 renders as 404, not 500. (luvo hit a 500 here after its dependency bump
  and fixed it with null-safe menus.)
- Every `/img/...` URL the public pages emit returns 200 with the same framing
  as production — per the method in `05-image-pipeline.md`.
- Admin login, and every CRUD path, against the un-migrated Vue 2 SPA.

## Frontend

| Step | Status | Commit |
|---|---|---|
| Public site on Vite | — | |
| Admin on Vue 3 + Vite | — | |
| Dropzone v6 replacement | — | |
| TinyMCE → Tiptap (incl. round-trip verification) | — | |
| `projects/grid/` page builder | — | |
| Icons → Phosphor light, during the port (`09-admin-ui.md`) | — | |
| Border tokens, 1px lines (`09-admin-ui.md`) | — | |
| Menu: type scale + group headers | — | |
| Login screen / splash | — | |

### To verify at the end of the frontend phase

- Public site pixel-identical to production, no console errors; jQuery
  plugins, lazysizes, fancybox, swiper all load.
- All 30 admin screens load clean; lists, forms, TinyMCE instances, tabs,
  dropzones.
- Save → 200 + notification; reload persists.
- Validation 422 → message shown + field marked, same shape as before.
- Image upload → store → crop (same geometry) → delete.
- File upload → store → delete.
- **Drag-and-drop reorder.** Note from luvo: Sortable uses native HTML5 DnD,
  which browser automation cannot drive reliably. Budget this as a manual
  check — one drag per list type.
- Session expiry: clear cookies, then try to save. With Sanctum this path is
  completely different from the JWT refresh flow it replaces, and it is the
  single most likely thing to be wrong and not noticed.

## Public site JS (separate project — `08-frontend-js.md`)

| Step | Status | Commit |
|---|---|---|
| Delete dead code: fancybox ×2, axios, `@fancyapps/ui`, `in-view` | — | |
| jQuery → vanilla, 9 modules + `bootstrap.js` | — | |
| Swiper 5.3.8 → 12 | — | |
| `maps.js` de-jQuery | — | |

Capture before/after screenshots of every public page type at 375 and
1280 px **before** starting. That is the only baseline this work gets.

## Known issues to carry (found during the survey, pre-existing)

- Public-site cache-busting does not work: four bundles are `.version()`-ed
  but referenced with `asset()` instead of `mix()`, so the `?id=` hash is
  never emitted. Vite fixes this by construction — treat it as a behaviour
  change, not a silent improvement.
- Every image crop is served at up to 2400 px regardless of the requested
  size. See `05-image-pipeline.md` rule 3 and `04-open-questions.md` #2.
- `config/image-cache.php` registers `Tiny.php`, which uses the Intervention
  **v2** API. It is a latent fatal that has never fired because no URL
  reaches it. It will become a hard failure on Intervention v4 if left in.
- `busu.css` is built from a 1,569-line Sass tree that nothing in this repo
  references. Answered: another site consumes it — keep the output path.
- The public site loads **axios and never makes a request with it**, and
  ships a dead fancyBox 3.5.7 that is not even in the compiled bundle.
  See `08-frontend-js.md` step 1.

## Deploy notes

To be filled in once `04-open-questions.md` #8 is answered. Expected shape,
based on luvo:

- `composer install --no-dev` on the server, PHP 8.3+ CLI.
- Vite build output committed (as the Mix output is today), or a build step
  added to the deploy.
- `php artisan optimize:clear`.
- `.env`: remove `ALGOLIA_APP_ID` / `ALGOLIA_SECRET`, and make sure
  `SCOUT_DRIVER` is unset or `collection` (the config default is now
  `collection`).
- `.env`, after step 7 (Laravel 11+ env names; the config files that read the
  old names are gone):
  - **`DB_CONNECTION=mysql` must be set** — the framework default is `sqlite`.
  - `CACHE_DRIVER` → `CACHE_STORE` (default is `file` now, so only needed if
    production uses something else). Same for `FILESYSTEM_DRIVER` →
    `FILESYSTEM_DISK` (default `local`).
  - `QUEUE_CONNECTION=sync` — nothing is queued, but the framework default
    is `database`.
  - Unused, can go: `BROADCAST_DRIVER`, `PUSHER_*`, `MIX_PUSHER_*`,
    `REDIS_*`, `MAIL_*`, and `JWT_SECRET` (since the Sanctum step).
- `.env`, Sanctum: **`APP_URL` must be the exact production origin**
  (`https://oxid-architektur.ch`) — Sanctum treats requests from that host
  as stateful. If the admin is also reached via `www.`, set
  `SANCTUM_STATEFUL_DOMAINS=oxid-architektur.ch,www.oxid-architektur.ch`.
  Leave `SESSION_DOMAIN` unset (host-only cookie) unless both hosts are
  used. Consider `SESSION_SECURE_COOKIE=true`.
- Admins are logged out once by the deploy (JWTs no longer accepted); the
  SPA lands on the login screen.
- `php artisan optimize` (config + route + view cache) works since step 7;
  it never did before because of the duplicate route name.
- Glide cache dir writable; not backed up.
- After go-live, `storage/app/public/cache/` (old image-cache output) can go.
- Optionally warm the Glide cache after deploy (cold renders 0.3–1.3 s each).

## Next after this project

QA automation, as luvo did afterwards: a checklist with stable ids, PHPUnit
feature tests, Playwright E2E, and a visual comparison against production.
See `07-qa-automation-prompt.md` and `08-test-plan.md` in the luvo repo —
both are reusable here with the entity names swapped.
