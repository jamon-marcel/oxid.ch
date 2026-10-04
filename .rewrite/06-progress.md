# Progress

Survey done 2026-10-04 against `f140dca` on `master` (clean tree).
All of it — backend, admin, public site JS — was done on 2026-10-04
(`3960590` … `3699b16`). Everything is pushed to
`origin/rework/laravel-13-vue-3`; nothing is deployed yet.

The sections below are written in the order the work happened, and
some later steps replaced earlier ones the same day (Dropzone → own
Uploader, vuedraggable → SortableJS, CSS icons → Phosphor components,
Options API → `<script setup>`). Where an older note no longer describes
the code, it says so. For the code as it is now, read `CLAUDE.md`.

Production: **https://oxid-architektur.ch** (www.oxid.ch is a different, static page).

Branch: **`rework/laravel-13-vue-3`**, cut from `f140dca` on 2026-10-04.

## Before the first commit

- [x] `04-open-questions.md` #1 — **production is PHP 8.3 up to 8.5.**
      Gate cleared 2026-10-04. The driver question (Imagick vs GD) stays open
      but does not block: the pipeline detects formats at runtime.
- [ ] Run the driver one-liner from `04-open-questions.md` #1 on the server,
      and record the result here. Also check the **CLI** PHP version, not
      just the web one.
- [x] ~~Grep production access logs for `/img/project/` and `/img/tiny/`~~
      — moot: answered "not in use" (`04-open-questions.md` #3), deleted in
      `0afa861`. Both now 404. A log grep would only confirm it.
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
- [x] Dev/prod database mismatch: production is **MariaDB 10.11**, local
      is **MySQL 5.7** (EOL). Kept as it is: the whole rework ran against
      the local MySQL 5.7 without a problem. The admin API tests run on
      in-memory SQLite (see "Admin API tests").
- [x] Record the current state for comparison: route list (144 at
      `f140dca`), the `/img/...` URLs the public pages emit (step 5 crawl,
      6,187 URLs), and screenshots (Mix build vs Vite build, then local vs
      production in "Final QA").

## Where things stand (handover, 2026-10-04 evening)

- **Backend: done.** Laravel 13, slim skeleton, config trimmed, Sanctum,
  own search, upload validation, generic image handling (`ffac092`),
  public controllers (`c195334`), `images:warm`.
- **Admin: done.** Vue 3 + Vite in luvo's shape (`<script setup>`,
  composables, `lib/`, `components/ui`), Tiptap, own Uploader with
  per-file progress, own notifications, one Lightbox, one Card, toggles,
  SortableJS, Phosphor icons, 1px lines, lighter type, lands on the news
  list.
- **Public site JS: done** (`08-frontend-js.md`): no jQuery, ES modules,
  `data-` hooks, Swiper 14.
- **Tests:** 118 (`php artisan test`): 84 admin API feature tests on
  in-memory SQLite, 7 public-page and 2 rendition tests against the local
  MySQL copy, 19 search and 4 image unit tests, 2 examples.
- **Open:**
  1. Production facts to collect on Hostpoint: web PHP `upload_max_filesize`
     / `post_max_size` / `memory_limit`, Imagick loaded? (decides larger
     uploads and render memory), CLI PHP version, search queries from the
     access logs (ranking tuning, `07-search.md`).
  2. The deploy itself — see "Deploy notes". Production needs
     `php artisan migrate` (two new migrations).
  3. After go-live: watch `laravel.log` for "Broken … rendition"; delete
     `storage/app/public/cache/`; the 120 unreferenced uploads are a
     cleanup candidate.
- **QA scripts:** Playwright in `~/oxid-qa` (outside the repo; see
  "Public site JS"). The admin QA scripts of the earlier rounds lived in
  `/tmp/pw` and are gone. The user works in the same local admin: tests
  must clean up after themselves, never restore whole tables.

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
| Search phase 2: own scoring search + unit tests, drop Scout | ✅ done — 16 unit tests (19 since the stopword change, `33b32da`); 15 queries vs production in `07-search.md`, every phase 1 loss recovered; queries 2–9 ms; index flushed on save. Ranking tuning against real queries still open (needs the access logs) | `bd0eacb` |
| Generic image handling: signed `/img/{file}`, `IsImage` trait, `<x-image>`, stored dimensions, legacy redirects | ✅ done — crops byte-identical to step 5 (93/93), home framing same crop; descriptors = rendered width and status 200 for the first 1,552 of 6,169 emitted URLs at commit time; the full crawl later (Final QA): 6,135/6,135. See `05-image-pipeline.md`, "Generic image handling" | `ffac092` |
| Public controllers: unpublished pages 404, `/werkliste` 301, no `BaseController` | ✅ done — see "Public controllers" | `c195334` |
| German `validation.php`; `images:warm` replaces `images:clear` | ✅ done | `29edfc0` |

The Laravel 13 bump and the image-cache → Glide dependency swap **must be
the same commit** — Composer will not resolve anything on Laravel 11. See
`02-backend-laravel13.md`, "Why step 4 must be one commit".

### Deferred: generic image handling

Requested by the user on 2026-10-04, after step 7: the step 5 result —
`ImageController` (5 per-purpose actions: `original`, `thumbnail`, `large`,
`home`, `crop`) plus `ImageHelper` (one static method per page use:
`largeImage`, `previewImage`, `teaserImage`, `homeImage`, `openGraphImage`) —
is too special-cased and should become **more generic**. Designed and done
on 2026-10-04 — see `05-image-pipeline.md`, "Generic image handling". Step 5's
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
- `intervention/image-laravel` stays per the plan (*removed in step 7*),
  but nothing uses its `Image` facade — the only Intervention caller is `MediaController`, which
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
  (luvo's `Glide.php` mentions one). *Added in `29edfc0`.*
- **Full crawl done:** all 6,187 `/img/...` URLs the public pages emit
  (at `62c73c4`) return 200 — 2,054 AVIF, 2,054 WebP, 1,551 JPEG, 528 PNG.
- ~~Still to do for this step: screenshot comparison of the public pages
  against production (the `<picture>` wrapper), and the admin image screens
  once the SPA runs again.~~ Both done in "Final QA".

### Broken AVIF renders (found 2026-10-04, guarded the same day)

During the generic-image crawl some AVIF renditions came back broken with
status 200. Two shapes, both from Imagick's AVIF encoder: a **bare 16-byte
`ftyp` box** (1,549 files) and a **HEIF container whose `pitm` points at no
image** (2 files, 93 and 35 KB). All were written 2026-10-04 11:40–14:21,
during the crawls.

**Not reproducible afterwards:** 0 broken out of 78 fresh AVIF renders —
Herd's PHP-FPM sequential and 8 in parallel, `artisan serve` with
`PHP_CLI_SERVER_WORKERS=4` and `=8` under parallel load, 900 to 2400 px.
The earlier "16 bytes every time" was most likely Glide serving the
already-cached broken file. Root cause unknown (`imagick.set_single_thread`
is on, so not ImageMagick's OpenMP).

**Guard** (`ImageController::render()`): every rendition is checked with
`getimagesizefromstring()` before it is served — that accepts all 7,214
good cache files (JPEG, WebP, PNG, AVIF) and rejects both broken shapes,
and only reads headers. A broken one is deleted and rendered once more;
broken twice, the upload's own format is served with `max-age=300` instead
of a year `immutable`, and a warning is logged ("Broken avif rendition of
…"). So a broken file can't stick in the cache or in a browser.
`tests/Feature/ImageRenditionTest.php` covers both paths (fails against the
old controller). The 1,551 broken files are deleted from the local cache.

**Production:** watch `laravel.log` for "Broken … rendition" after go-live.
If it shows up often, drop `avif` from `ImageSupport::modernFormats()`
(WebP stays). PHP ≥ 8.2 needed for AVIF in `getimagesize` — production is
8.3+.

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
  facade aliases (`AppHelper`, `ImageHelper`; `ImageHelper` went with the
  generic image handling, so only `AppHelper` is left). The `Image` alias went —
  nothing calls it, and the Intervention package registers it itself.
  Providers are auto-discovered; `bootstrap/providers.php` lists only
  `AppServiceProvider`.
- **Factories/seeders:** autoload switched from `classmap` to PSR-4
  (`Database\Factories`, `Database\Seeders`), and `UserFactory` is now a
  class-based factory. Pre-existing and **not fixed**: the three other
  factories still use the Laravel ≤7 `$factory->define()` syntax, and the
  seeders call `Model::factory()` on models without `HasFactory`, so
  `db:seed` fails. Nothing on production seeds; a QA-automation item.
  *Fixed later with the admin API tests (class-based factories).*
- `CLAUDE.md` named the image command `app:clear-images`; it is
  `images:clear`. Fixed. The rest of `CLAUDE.md` (Algolia, image-cache,
  Laravel 11) was rewritten at the end (`cefbaee`). `images:clear` itself
  was removed later for `images:warm` (`29edfc0`).

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
    and `serve` stays off. Load-bearing: every image/document delete
    endpoint (and, at the time, `images:clear`) calls
    `Storage::allDirectories('public')` / `Storage::delete('public/…')` on
    the default disk.
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
- Pre-existing, not changed here: `/suche/{keyword}` ignores the path
  segment — `SearchController` only reads `?keyword=`. *Fixed in search
  phase 2: the path segment searches too.*

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
  meta tag. *(Dropzone is gone since `5f77c53`; the own Uploader posts
  through `lib/http.js` like every other call.)*
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
  **Fixed 2026-10-04:** `resources/lang/de/validation.php`, every rule in
  German, plus attribute names for the admin's fields. In practice the
  admin only marks the failed fields and never shows the text, so this
  matters for API responses, not for what editors see. The form requests'
  own `required` messages are still English ("Title is required!") —
  still true, left as they are.
- Lang files still live in `resources/lang`; L13 picks that up
  (`app()->langPath()`), moving them to `lang/` is optional.

### Notes from the public site on Vite

*Several points here were superseded by the public JS project
(`08-frontend-js.md`): jQuery, `jquery.scrollto` and the axios import are
gone, Swiper is 14, and the two pre-existing bugs are fixed. The admin left
Mix with the Vue 3 port.*

- **Vite 8.3 + `laravel-vite-plugin` 3.2.** Three entries: `frontend/app.scss`,
  `frontend/app.js`, `frontend/maps.js` → `public/build/` with a manifest,
  committed (deploy decision #8). The Blade layouts use `@vite(...)`, so the
  cache-busting that never worked under Mix (`asset()` instead of `mix()`)
  now works — a behaviour change: browsers fetch new CSS/JS after a deploy.
- **Admin stays on Mix until the Vue 3 port.** `webpack.mix.js` now builds
  only `backend/app.js` and `backend/app.scss`; npm scripts are
  `admin:dev` / `admin:watch` / `admin:build`. `mix-manifest.json` holds
  only the admin entries.
- **`busu.css` keeps its path** (`public/assets/css/busu.css`, another site
  loads it) — built by the `sass` CLI in `npm run build` (`build:busu`).
  Output differs from Mix only in vendor prefixes and value notation
  (Mix ran autoprefixer + cssnano); the rules are the same.
- **Vendored UMD files → npm, same versions, pinned exactly:** `swiper`
  5.3.8, `lazysizes` 5.1.2, `jquery.scrollto` 2.1.2. Vite's dev server
  cannot load local CommonJS/UMD. `vendor/fancybox.js` stays (dead code,
  `08-frontend-js.md` step 1).
- `require()` → `import`; `bootstrap.js` still sets the jQuery global the
  modules rely on. Vite entries load as deferred `type="module"` scripts;
  no inline script uses `$`, and every module waits for DOM ready anyway.
- **Sass URLs made absolute:** fonts → `/assets/css/fonts/…`,
  `$url-images`/`$url-icons` → `/assets/img/…` (they were relative to
  `public/assets/css/`; the built CSS now lives in `public/build/assets/`).
  `vite.config.js` sets `publicDir: 'public'` for the dev server only, so
  those URLs resolve there too; in a build it would prefix them with
  `/build/`.
- LightningCSS (Vite 8's CSS minifier) rejects the old-IE `*zoom: 1` hack;
  `css.lightningcss.errorRecovery` drops it. Sass `@import` deprecation
  warnings are silenced, as in luvo — the `@use` migration is separate.
- Removed: Mix's public outputs `public/assets/{js/app.js, js/maps.js,
  css/app.css}`. `public/assets/js/modernizr.min.js` stays (static, linked
  directly).
- **Pre-existing, seen during the check, not changed:** `/geschichte` throws
  `_toggleDropDownItems is not defined` (8×, in the old build too);
  `/werkliste` is a 200 with an empty body (`WorksController@index` is
  empty — the menu links to the sub-pages).
- The public `<meta name="csrf-token">` also uses `value=`, so the public
  site's axios sent `X-CSRF-TOKEN: undefined` — harmless, it never makes a
  request. Axios went in `046a170`; the tag still says `value=` and
  nothing reads it.

### Notes from the admin on Vue 3 + Vite

One commit: a half-ported Vue 2/3 admin can't run, so Dropzone, Tiptap,
vuedraggable and the cropper went in together.

*This was a mechanical port, and most of it was replaced later the same
day:* Options API, mixins, `this.axios` and `$parent` → `<script setup>`
(`f4ac39b`, `7b55d5e`); `@kyvg/vue3-notification`, Dropzone and `maska` →
own code (`5f77c53`); vuedraggable → SortableJS (`ac6a435`). The editor
moved from `components/global/editor/` to `components/ui/editor/`. Still
true: Vite, Tiptap and its round trip, the bugs fixed, the dead code
removed.

- **Build:** the admin joins `vite.config.js` (`@vitejs/plugin-vue`, `@`
  alias). Laravel Mix is gone (`webpack.mix.js`, `mix-manifest.json`, the
  `admin:*` scripts, `public/assets/backend/js` incl. the self-hosted
  TinyMCE, 3.7 MB). Admin bundle 907 KB (Mix: 1,048 KB), mostly
  Tiptap/ProseMirror. `vue` is aliased to its runtime ES build:
  vuedraggable's UMD `require('vue')` pulled in Vue's CJS build and the
  template compiler (−92 KB). axios is now a shared chunk with the public
  site; public pages re-checked after that.
- **Kept, deliberately mechanical:** Options API, the mixins, `this.axios`
  (now `app.config.globalProperties.axios` instead of vue-axios — 109 call
  sites untouched), `$notify` (`@kyvg/vue3-notification`). `$parent` calls
  stay where the child is a direct child (Form → Listing → Actions, grid
  Row → ButtonAdd/Media); none crosses a draggable.
- **Replaced:** Vuex → a 3-line `reactive()` store; `vue-router` 4;
  `vuedraggable` 4 (`#item` slot + `item-key`; images keyed by `name`
  since new uploads have `id: null`); `vue-the-mask` → `maska`;
  `moment` → `utils/date.js` (null-safe — moment showed "Invalid date");
  `Vue.filter('truncate')` deleted with its only user.
- **Tiptap** (`components/global/editor/`, from luvo): bold, superscript,
  link (URL/E-Mail/Telefon; relative `../../projekt/…` links stay as they
  are), "Worttrennung deaktivieren", H1–H3, remove formatting — the TinyMCE
  toolbar's features. Pasting strips `style`/`class` (TinyMCE had
  `paste_as_text`). Round trip: `.rewrite/tools/tiptap-roundtrip.mjs` over
  all 213 stored values (6 tables, both languages): **0 visible
  differences** (text, links, targets, headings, `<br>`, bold, sup, nowrap);
  12 % smaller, the pasted inline fonts go. Tell the editors.
- **Dead code deleted (≈ 770 LOC):** `global/upload/{ImageUpload,
  MultiImageUpload}`, `projects/upload/{ImageUpload,FileUpload}`,
  `projects/grid/ButtonAddArticle`, `config/dz-*.js`, `filters.js`. They
  pointed at `/media/…` and `/image/…` URLs that don't exist. Dropzone is
  left in 2 components instead of 6.
- **Pre-existing bugs found and fixed:**
  - **210 of 263 team members could not be edited**: `role`/`position` are
    `null` in the DB, and `v-model="team.role.de"` threw on render (in Vue
    2 too). The form now fills in `{de: null, en: null}`.
  - `global/images/Listing` and `discourses/images/Listing` render
    `<cropper>` without importing it — cropping on home/team/job/profile/
    discourse images showed nothing. Imported now (crop → save verified).
  - Wrong login credentials failed silently (`loginError` was never
    shown). Shown now.
  - `v-for` + `v-if` on one element (team list) — Vue 3 evaluates `v-if`
    first; split.
- **Test residue:** a broken test PNG made one upload 500 (bad image →
  decoder exception, an unfriendly but pre-existing 500); my script then
  cropped and **deleted a real home image** (`…renggli_luegisland_08.jpg`).
  Restored the same minute: file re-downloaded from production (identical
  size, 8070×5572), DB rows from a dump taken before the run. All test
  tables restored from dumps; temp user deleted.

### Notes from the admin UI refresh

*The CSS-icon decision below lasted until the `<script setup>` rewrite:
action icons are Phosphor components now (`ListActions`), as originally
planned. What stays CSS: the select/button carets, the `grid-*`
pictograms, and a few icons in `public/assets/backend/img/icons/` (21
files). The menu changed twice more: the active page is underlined
(`8c0b175`), group pages sit flush under their header (`8aa6ee4`).*

- **Icons: SVG files, not components — a deliberate change from the plan.**
  The 27 referenced icon files are now Phosphor *light* SVGs with the
  original colours (`currentColor` → black, as it rendered in a CSS
  background), same file names, so every `.icon-*` class and template stays
  as it is. Reason: the `progress` mixin toggles `is-loading` on
  `event.target`; with an inline `<svg>` component inside each link the
  target becomes the svg and the loading state lands on the wrong element
  (24 screens). Same look as luvo's components, no behaviour risk.
  10 unreferenced Feather files deleted. The `grid-*.svg` layout
  pictograms stay: they're custom diagrams, referenced dynamically
  (`'grid-' + key`) — `09-admin-ui.md` wrongly listed them as unused.
  `file.svg` (custom) stays.
- **Borders:** `$border-width: 1px` (+ `$border-color`, `$border`) in
  `config/_global.scss`; 39 hardcoded `2px solid|dashed` lines use it
  (`09-admin-ui.md` counted 54, which included `border-radius: 2px`). CSS
  triangles (6px/20px) untouched; the notification accent bar 5px → 3px.
- **Focus:** inputs keep the blue border on focus plus a 1px ring
  (`box-shadow`), so focus doesn't get fainter with the thinner border.
  Buttons suppress `outline` (`!important`), so they get a
  `:focus-visible` ring.
- **Shadows** softened in the four variables only (lower opacity, similar
  blur). No explicit call sites use the mixin.
- **Menu:** 280px; links regular `$fs-sm`, grey → white; group labels
  (Home, Team, Jobs, Profil) small uppercase headers; logout below a 1px
  rule (wrapped in `.site-nav__footer`). The active marker uses
  `.router-link-active` — the old CSS styled `.is-active`, which
  router-link never sets, so the current page was never highlighted.

### Notes from the `<script setup>` rewrite

- **Why:** `.rewrite/` said "stay on Options API" — written before luvo's own
  rewrite and never updated. Target is luvo's current code; see README.
- **Same API, same markup.** Composables take oxid's endpoints
  (`GET …/get`, `GET edit/{id}`, `POST create`, `POST update/{id}`,
  `GET status/{id}`, `DELETE destroy/{id}`, `POST order`); templates keep
  oxid's CSS classes, so the screens look as before. Action icons are
  Phosphor components now (`ListActions`), as in luvo — the reason for
  keeping CSS icons (`progress` on `event.target`) goes with the mixin;
  saving shows `LoadingIndicator` instead.
- **Validation moves to the API's 422** (Laravel's default shape,
  `errors['title.de']`), with an optional client `validate()` for checks
  the API doesn't make (e.g. a project needs images).
- **`useResourceForm` fills `null` fields from the model defaults** on
  load (luvo's behaviour) — the generic fix for the team `role: null` case.
- Lazy routes: the admin entry chunk is 162 KB (was 907 KB in one file).
- Converted screens live in `views/`; until an entity is converted its old
  `components/` screen keeps running in the new `App` shell (no page
  header / notifications of its own any more).
- **Finished in the second commit:** every screen is `<script setup>`;
  `mixins/`, the Options-API components and the `this.axios` shim are gone.
  Structure as luvo: `views/<entity>/{Index,Form}.vue`,
  `components/{ui,images,files,grid}`, `composables/`, `lib/`.
- **Image handling, reused from luvo:** `ImageManager` (grid/list view with
  drag order, edit overlay with a `#fields` slot for entity flags, cropper
  with a ratio function) replaces four listing variants, the crop/listing
  mixins and four near-identical image pages. Crop ratios unchanged:
  project by orientation (16:10, plans 16:10.67, portrait 12:16), home 3:4,
  the rest 16:10; same default crop box. Images used in the project layout
  still can't be deleted. Admin image URLs go through `lib/images.js`
  (`/img/thumbnail|large|original`), the one place to change when the
  image agent moves the admin to API-supplied URLs.
- **Backend fix:** `store()` of Discourse/Job/Project/Team took a plain
  `Request` (only `update()` used the form request). With client checks the
  only guard, an empty team create was a **500** (`firstname` NOT NULL).
  All four use their `*StoreRequest` now → 422.
- **Validation:** forms with rules the API doesn't make (project: images;
  discourse: title + images; job: info) check all required fields in the
  client too, so every error shows at once as before; the rest rely on the
  API's 422.
- **Removed CSS/SVG:** 14 `icon-*` partials and 12 icon SVGs no longer
  referenced (icons are Phosphor components now); `icon-view`,
  `icon-layout`, `icon-grid-list` and the button/select SVGs stay.
- Verified (headless Chromium, all writes on dumped tables, restored
  after): 7 lists; edit + save on every form (team member 10 with
  `role: null` included); empty create on 6 forms (errors + tab markers);
  project image overlay + cropper, documents tab; discourse list view;
  upload → caption save → crop → publish → delete on all four image pages;
  grid builder add row → pick image → delete image; session expiry on
  navigation and on API call, re-login, logout.

### Notes from the dependency / lightbox pass (user feedback 2026-10-04)

- **Dependencies removed (6):** `@kyvg/vue3-notification` → `lib/notify.js`
  + `components/ui/Notifications.vue` (~40 lines, same CSS classes);
  `dropzone` → own `components/ui/Uploader.vue` (drop zone + picker,
  browser-side type/size/count checks, sequential uploads through `http`, so
  the CSRF and error handling are the app's own); `maska` → `dateMask()` in
  `lib/utils.js` (its only use was the news end date); `@fancyapps/ui`,
  `in-view`, `nth-check` — not imported anywhere. The two Dropzone vendor
  stylesheets went with it; the drop zone shows plain text, no button.
- **Kept, and why:** `vue`, `vue-router`, `axios` (interceptors, XSRF);
  `@tiptap/*` (the editor); `vue-advanced-cropper` (a cropper is real work);
  `vuedraggable` (8 lists; it's unmaintained since 2021 and is the reason
  for the `vue` alias in `vite.config.js` — *replaced by SortableJS later
  the same day*); `@phosphor-icons/vue`. Public
  site: `jquery`, `jquery.scrollto`, `lazysizes`, `swiper` until
  `08-frontend-js.md`.
- **One Lightbox for all overlays** (`components/ui/Lightbox.vue`, native
  `<dialog>`): image edit, crop, file edit, grid image picker, editor link
  dialog (size `small`). Header with title + close, scrolling body, footer
  for the buttons; Escape, focus and backdrop come from `<dialog>`; page
  scroll locked while open. Replaces four overlay styles
  (`upload-overlay-edit/-cropper`, `overlay`, `overlay-asset/-crop`).
- **Cropper fits the window:** `fill` mode gives the cropper exactly the
  height between header and footer (was a fixed `max-height: 700px` plus
  80px padding, so buttons fell off short screens). Checked at 1280×620:
  footer ends at 590px. The edit lightbox caps its image to the window.
- **Menu:** single pages (Projekte, Diskurs, Kontakt) sit at section level
  with the same spacing as group headers; the pages of a group are indented
  under it, as in the old admin. The list scrolls on short windows; logout
  stays below it.
- Admin entry chunk 156 KB.

### Notes from the upload check (2026-10-04)

- **The 8 MB limit was only checked in the browser.** `MediaController::upload`
  validated nothing: any type, any size up to PHP's limits. Now: `mimes`
  (content) **and** `extensions` (name) jpg/jpeg/png/pdf, `max` 8 MB
  (`MediaController::MAX_KB`), German messages; the Uploader shows the
  API's message. `mimes` alone let a JPEG named `.exe` (or a JPEG/PHP
  polyglot named `.php`) through and stored it under that name in the
  public uploads — verified, now 422. Existing uploads: 730 jpg, 328 png,
  5 pdf, nothing else.
- **Orientation without decoding.** The upload decoded the whole image
  with GD just for portrait/landscape — 269 MB for the 65 MP floor plan
  `6221f934848dd_18-grundriss-erdgeschoss-kopie.jpg` (7.7 MB file).
  `ImageSupport::dimensions()` reads the header + EXIF instead; same
  answer as the stored value for all 656 project images.
- **Memory follows pixels, not bytes.** Glide still decodes the whole
  source when it renders. With GD that is ~4 bytes/pixel, so a 16 MB JPEG
  (easily 100+ MP) needs 400 MB+. Raising the limit needs production's
  web `upload_max_filesize` / `post_max_size` / `memory_limit` and whether
  Imagick is loaded (open item above).

### Upload progress (2026-10-04)

The Uploader lists every file of a batch below the drop zone: name,
progress bar with percent (axios `onUploadProgress`), then "fertig" or the
reason it was rejected (browser check or the API's 422). Files go up one
after the other, so order is kept and the server isn't flooded; the zone
counts "Hochladen… 1 / 2" over the files actually sent. Finished rows go
2 s after the batch, rejected ones stay until the next upload. Verified
with throttled upload (two valid, one wrong type, one too large).
Styled like the admin's lists after feedback: 1px rows, Phosphor status
icon (file / check / warning), name left, state right, a 2px progress line
along the row's bottom edge.

### Ja/Nein buttons → toggle (2026-10-04)

`components/ui/Toggle.vue` replaces `RadioButton`: a real checkbox drawn as
a switch (1px line like the inputs, `$color-blue` when on, as the selected
Ja button was; focus ring), with the state ("Ja"/"Nein", or e.g.
"light"/"dark" for the discourse image theme) next to it. Same API, 0/1
in and out (also reads "0"/"1" and booleans). Used for every yes/no field:
publish, project flags, discourse image preview/theme, project image plan,
and the two project image preview flags (were two button-style
checkboxes). The team document language DE/EN stays two buttons — a choice,
not a yes/no.

### One card for images and files (2026-10-04)

`components/ui/Card.vue` + `.card-grid`: square media (image fitted, so
plans aren't cut), footer with a label and/or actions, dimmed when
unpublished, `selectable` makes the whole card a button (hover, focus,
Enter/Space). Used by `ImageManager` (actions), `FileManager` (file icon,
name above the actions) and the grid builder's image picker (name only,
click to choose; names without the 13-character upload prefix, full name
on hover). Replaces `.upload-listing/.upload-item`, the picker's
`grid-image-selector` module and the unused old `.card`/`.post` partials.
Menu: group pages sit flush under their header (no indent).

### Login splash (2026-10-04)

- `admin` and `admin/{any}` are one route now, `admin/{any?}`, a closure
  that passes `HomeImage::published()->inRandomOrder()->first()` to
  `backend/app.blade.php` (one query; `route:cache` still OK).
- The blade writes a small `<style>` for `.container-auth`: a plain JPEG
  `url()` first, then `image-set()` with AVIF/WebP (from
  `ImageSupport::modernFormats()`) and JPEG, all signed URLs from the
  image's `url()`, with its crop. 2000 px, 1200 px at ≤ 1000 px wide.
  **Not a `--splash` custom property** as `09-admin-ui.md` planned: a `var()`
  that turns out invalid (older browser without `image-set()` `type()`)
  falls back to `none`, not to the declaration before it. Two plain
  declarations give the JPEG fallback for free.
- No published image → no `<style>`, `.container-auth` shows
  `$color-light-grey`.
- Card: white, `$border`, the existing shadow.
- Checked: 40 loads hit all 10 published images and none of the 4
  unpublished; all 6 renditions 200 with real sizes (AVIF 157/243 KB, no
  16-byte files); Chromium picks AVIF; wrong-password message still shows;
  `/admin/…` deep links 200; 22 tests pass.
- Not changed: the admin has `body { min-width: 840px }`
  (`$page-min-width`, since the initial commit), so on a phone the login is
  laid out at 840 px and scrolls sideways. The admin is desktop-only by
  design; left alone.

### vuedraggable → SortableJS (2026-10-04)

- `components/ui/SortableList.vue`: `v-model` on the array, `@end` with the
  new order, the `v-for` goes in the slot; children with `.is-draggable`
  are the items (all 7 call sites already had the class). Sortable moves
  the dragged node; `onEnd` puts it back and reorders the array, so Vue
  keeps owning the DOM. Uses `oldDraggableIndex`/`newDraggableIndex`.
- A component rather than the planned composable: the team page renders
  one list per category in a `v-for`, which a single template ref doesn't
  cover. `v-model="groups[categoryId]"` does.
- Same DOM as before (vuedraggable also rendered a wrapping `<div>`), same
  `draggable-ghost` class, so no CSS change.
- `vuedraggable` removed, `sortablejs` ^1.15.7 direct; the `vue` alias in
  `vite.config.js` gone. Admin entry 156 → 118 KB, the chunk holding
  Sortable 95 → 37 KB; no template compiler in the build.
- Checked in Chromium (Playwright's `dragTo` drives Sortable's native DnD
  fine): news 0→1 and back; team images (list view), grid builder (list
  view, project 2) and the largest team group 0→2 and back — UI order, the
  POST, and the order after a reload.
- **Found: `order` is a `TINYINT` (max 127) in all 10 tables.** Team
  category 3 (former members) has 244 rows, so saving its order 500s at
  the 128th row — in production too, since 2020. The rows before it are
  already written (no transaction). Migration
  `2026_10_04_150000_widen_order_columns` makes them `SMALLINT`, same
  default `-1`, NOT NULL; checked by saving all 244 inside a rolled-back
  transaction (200, orders up to 243).
- The failed test run rewrote `order` on 156 category-3 rows locally
  (the old values had duplicates, so dragging back didn't restore them).
  Restored row by row from the 2026-10-04 production dump, guarded by the
  test's `updated_at`, so nothing written outside the test was touched.

### Smaller admin rounds (2026-10-04)

- Menu: close button in the top right corner (`fb8d41d`); the active page
  is underlined instead of marked by a line on the left, and the indented
  group lists no longer scroll sideways (`8c0b175`).
- The admin lands on the news list: login, the header logo and `/admin` go
  to `/admin/home/news`; the empty dashboard view is gone and
  `/admin/dashboard` redirects there (`e2a5b21`).
- Lighter type: regular weight for buttons, labels, tabs, tables and
  notifications; `h2` bold (`adfd57a`).
- Public footer: the Google tag loads only in production (`cbe136c`).

### Final QA (2026-10-04)

Run against `cefbaee` with the local copy of the production DB and uploads.
Scripts in `/tmp/pw` (not in the repo).

**Backend**
- 145 routes; `route:cache` OK.
- 17 public pages 200 (incl. `/suche`, `/suche/beton`), unknown page and
  unknown project 404. The crawl reached 125 pages, all 200.
- `api/*` unauthenticated → 401 `{"message":"Unauthenticated."}`; session
  and XSRF cookies on `/admin` and `/sanctum/csrf-cookie`.
- **Image crawl: every `/img/...` URL the 125 pages emit — 6,135 — is 200,
  decodes, has the requested format (2,045 AVIF, 2,045 WebP, 1,521 JPEG,
  524 PNG), the right `Content-Type`, and its real width equals its srcset
  descriptor (6,135/6,135).** The rendition guard never fired (the only
  three "Broken … rendition" log lines are from its own unit test).
- 24 tests pass.

**Admin** (headless Chromium, temporary user, everything it created deleted
through the UI/API)
- 7 lists load; all 7 forms (news, project, discourse, team, job, profile,
  contact) open and save 200; empty creates show field errors and mark the
  tabs (422 where the client lets the request through).
- Project images: 13 cards, 10 protected by the grid; edit overlay; cropper.
  Discourse images: grid ↔ list view, rows draggable.
- Home/team/job/profile images: upload → caption → crop → publish toggle →
  delete, all 200, counts back to where they were.
- Files: PDF upload on job 1 → save → present after reload, link serves
  `application/pdf` → delete → gone, file removed from disk.
- Drag reorder: news, team group, team images, grid builder (see
  "vuedraggable → SortableJS").
- Session expiry: dropping the session cookie sends both an SPA navigation
  and an API call to `/admin/login`; logout, then a protected route →
  login. Login screen: wrong password message; random published home image.

**Public site vs production** (17 pages × 1280/375, full page,
pixel-compared; production serves JPEG, local AVIF/WebP)
- Pixel-identical or within a few px: team, history, contact, discourse
  detail, profile, jobs, project detail.
- Home and search pick a random image — not comparable by design.
- Lists (`/projekte`, `/diskurs`, `/werkliste/...`) differ by **±1 px per
  image height**: local takes the aspect ratio from the stored crop
  (`width`/`height` attributes), production from the rendered file's
  rounded size. Accumulates to 1–12 px per page. Accepted.
- Same behaviour: jQuery, Swiper, fonts; same JS error on `/geschichte`.
- Content differences are local edits: empty grid rows 584 (project 58)
  and 585 (project 61) are the user's.

**Found in passing, pre-existing, not changed**
- `/werkliste` is a blank 200 on production and locally:
  `WorksController::index()` has been empty since 2020. Nothing links to it.
- `/geschichte` throws `_toggleDropDownItems is not defined` (×8) on
  production and locally. **Fixed 2026-10-04:** see "Public controllers".

**Test damage found and repaired:** the previous session's grid-delete test
(15:24:55 local, the second of commit `91e0f08`) had deleted grid element
1016 — Quellenhof's (project 58) first image — and cleared `is_grid` on
image 930. Both restored from the 2026-10-04 production dump; it was the
only grid element missing from the whole table.

### To verify at the end of the backend phase

*Done, see "Final QA". The last point ran against the Vue 2 SPA in the
Sanctum step, and against the Vue 3 admin in the final QA.*

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
| Public site on Vite | ✅ done — 30 screenshots (15 pages × 1280/375) old Mix build vs Vite build: 27 pixel-identical, 3 differ only by the random home image; menu, map, Swiper, collapsible, lazysizes, scrollTo work; `vite` dev server checked | `cba7799` |
| Admin on Vue 3 + Vite | ✅ done — headless Chromium against the real admin: all 27 screens render without errors; login (incl. wrong-password message), editor, save, drag reorder, Dropzone upload → crop → delete, grid builder (add row, pick image, delete row), session expiry, logout | `15c4102` |
| Dropzone v6 replacement | ✅ done, same commit — thin wrapper `global/upload/Dropzone.vue`; *replaced by the own `ui/Uploader.vue` in `5f77c53`* | `15c4102` |
| TinyMCE → Tiptap (incl. round-trip verification) | ✅ done, same commit — 213 stored values round-trip with 0 visible differences | `15c4102` |
| `projects/grid/` page builder | ✅ ported in the same commit (vuedraggable 4 slot syntax; `$parent` calls are direct parents, kept); *rewritten as `views/projects/Grid.vue` + `components/grid/` in `7b55d5e`* | `15c4102` |
| Icons → Phosphor light, during the port (`09-admin-ui.md`) | ✅ done — as SVG files behind the existing CSS classes, not components (see notes); *action icons became Phosphor components in `7b55d5e`* | `4dfc989` |
| Border tokens, 1px lines (`09-admin-ui.md`) | ✅ done — `$border-width`, 39 lines; softer shadows; focus rings | `4dfc989` |
| Menu: type scale + group headers | ✅ done | `4dfc989` |
| **`<script setup>` + composables, luvo's shape** (scope changed 2026-10-04): foundation + news | ✅ done — `lib/{http,utils,images}`, composables `useResourceForm/useListing/useOrder/useEscape`, `components/ui/*`, `App.vue` + `views/layout/PageHeader`, lazy `router.js` with the session guard; news list/create/edit/order/toggle and server-side validation verified; old screens still run inside the new shell | `f4ac39b` |
| … projects, discourses, team, jobs, profile, contact | ✅ done — all lists and forms; `ImageManager`, `FileManager`, `Uploader`, `useImages/useImageLibrary/useFiles` | `7b55d5e` |
| … image pages, listings, grid builder | ✅ done, same commit — one `views/images/Index.vue` for home/team/jobs/profile (route props); grid builder in `views/projects/Grid.vue` + `components/grid/`. No Options API, mixin or `$parent` left | `7b55d5e` |
| Fewer dependencies, one Lightbox, menu sections | ✅ done — see notes | `5f77c53` |
| Login screen / splash | ✅ done — login error shown (`15c4102`); random published home image as background, white card with 1px border; `splash.jpg` deleted. See notes | `2457ad4` |
| Uploader progress per file; yes/no fields as toggles | ✅ done | `946fb60`, `d787c01` |
| One `Card` for images, files and the grid picker | ✅ done | `8aa6ee4` |
| vuedraggable → SortableJS (`SortableList`), `order` columns → `SMALLINT` | ✅ done | `ac6a435`, `f7b4584` |
| Menu close button, active page underlined, land on news, lighter type | ✅ done — see "Smaller admin rounds" | `fb8d41d`, `8c0b175`, `e2a5b21`, `adfd57a` |

### To verify at the end of the frontend phase

*Written before the port. Done in "Final QA", against what the admin has
now (Tiptap, not TinyMCE; the own Uploader, not Dropzone). Drag and drop
could be automated after all: Playwright's `dragTo` drives SortableJS.*

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

### Public controllers (2026-10-04)

- **Unpublished projects and discourse entries 404 for visitors**; before,
  `/projekt/{id}` and `/diskurs/{id}` rendered them for anyone with the URL.
  A logged-in admin still sees them: the grid builder's "Vorschau" links to
  `/projekt/{id}`, and the admin's Sanctum login is the same `web` session.
  `/projekte` now opens on the first *published* project (it ignored
  `publish`).
- **`/werkliste` 301s to `/werkliste/autorenschaft`** (it was an empty 200).
  The unused `{isSearch?}` segment is gone from that route; the search page
  links with `?search=1`, which is unchanged.
- Rewritten in the shape of `AuthController`: no `BaseController`, no model
  injection, typed returns. Deleted `BaseController`, `DataController` (no
  route), `App\Services\Menu` (now a view composer on
  `menu/items/projects`, memoised with `once()` because the partial renders
  twice on project pages), `works/index.blade.php`, the unused `showInfo`
  variable and `WorksController::year`'s discarded `$data` filter.
- Fewer queries: a project page 15 → 11 (no reload of the bound model, the
  prev/next projects picked from the list already loaded), home/search pick
  the random image in SQL.
- **Verified:** HTML of all 155 public URLs (every project and discourse
  id) before vs after, CSRF token masked: identical except the random home
  image, `/werkliste` (now 301), and `og:image` on 15 projects. That one is
  a fix: it came from the grid row created first, now from the first row in
  display order, which matches the first image on the page (checked on 4).
  `tests/Feature/PublicPagesTest.php`.
- **`/geschichte` JS error fixed.** `history.js` still called
  `_toggleDropDownItems()` from the scroll handler after the function (and
  the history footer dropdown) had been commented out. The throw also
  skipped the `history.replaceState` after it, so the URL hash never
  followed the scroll. Removed the call and the dead code. Verified in
  headless Chromium at 1280/375: no errors; at 1280 scrolling sets
  `#1984/#2000/#2020` and the active footer link; setting the hash scrolls
  to the period on both widths.

### Admin API tests (2026-10-04)

`tests/Feature/Admin/`: 84 feature tests over the JSON API: auth, CRUD +
validation + status + order + delete for every resource, the four image
libraries, project/discourse images, documents, uploads, the grid builder,
settings, and the search-index flush.

- **They run on in-memory SQLite, not the local MySQL copy.**
  `AdminTestCase` switches the default connection in
  `beforeRefreshingDatabase()` (all 53 migrations run on SQLite) and
  asserts the driver before every test, so a misconfiguration fails
  instead of writing to live rows (cf. the grid-delete damage under "Final
  QA"). The local disk is faked as well: the image and document
  controllers delete from every directory under `public/`.
  `MediaUploadTest` is the exception (the uploader writes to
  `storage/app/public/uploads` directly) and deletes what it stores.
- The older tests (`PublicPagesTest`, `ImageRenditionTest`) still read the
  local MySQL DB.
- `phpunit.xml` used `CACHE_DRIVER`/`MAIL_DRIVER`, which Laravel 11+
  ignores, so tests used the file cache and forgot the real search index.
  Renamed them to `CACHE_STORE`/`MAIL_MAILER`.
- Class-based factories for `Project`, `Discourse`, `Team`, `Job` (with
  `HasFactory`) replace the pre-Laravel-8 `$factory->define` files, which
  had stopped loading; the seeders call `::factory()` and work again.
- **Fixed:** deleting a home/team/job library image whose record was
  already gone (another tab, or an upload removed before saving) 500'd on
  `$image->name` of `null` and left the file behind. These controllers now
  use the requested file name, as the profile/project/discourse ones
  already did.
- Checked that the tests catch a regression: breaking `is_grid` cleanup in
  `GridController::destroy` fails a test. Removing the documents cleanup in
  `ProjectObserver` doesn't, because the `ON DELETE CASCADE` foreign keys
  (present in the live MySQL schema too) delete them anyway.
- **Removed:** `Api\ProfileController` saved `images` with a `profile_id`
  that `profile_images` doesn't have. The form never sends `images`
  (profile images are a separate library), so it was dead code. Removed
  along with `Profile::images()` and `ProfileObserver`, which only deleted
  through that relation (profiles have no delete route).
- Found, not changed: deleting a project image that a grid uses would hit the
  `grid_elements` foreign key (500); the admin prevents it by protecting
  `is_grid` images.

## Public site JS (separate project — `08-frontend-js.md`)

| Step | Status | Commit |
|---|---|---|
| Delete dead code: fancyBox (JS **and** Sass), axios | done | `046a170` |
| jQuery → vanilla, 9 modules + `bootstrap.js`, `js-` → `data-` | done | `ccbfa36` |
| `maps.js` de-jQuery | done (with step 2) | `ccbfa36` |
| Swiper 5.3.8 → 14.3 | done | `127b04e` |
| Cross-browser QA (WebKit, Firefox) | done, no real devices | see below |

**Baseline and how it is checked.** Playwright scripts in `~/oxid-qa`
(outside the repo; `playwright`, `pngjs`, `pixelmatch` installed there):

- `shots.js <dir>`: full-page screenshots of 13 page types at 375 and
  1280 px, console errors per page. `cmp.js before after` pixel-diffs them.
- `behave.js <out.json>`: drives every module (menu, sub menu on mobile,
  overlay + Esc, dropdown, scroll buttons and the project counter, project
  teaser hover, filter in the menu and on the works list, collapsibles and
  their scroll, swiper next/prev/loop and resize across 960 px, history hash
  and scroll spy, imprint, map) and logs the resulting classes, scroll
  positions and hash. `diff.js` compares two runs, ignoring `js-*` classes.
- `BUILD=1` serves the pages with the `public/build` assets (Playwright
  rewrites the dev-server tags), so the committed build is tested while the
  shared Vite dev server keeps running.

Baseline taken at `5752cff` (after the `/geschichte` fix).

**Step 1.** fancyBox's Sass was still in the tree
(`sass/frontend/vendor/fancybox/`, 955 lines), imported only in comments —
the plan's "no references in the Sass" was wrong; deleted with the JS.
`@fancyapps/ui` and `in-view` were already gone from `package.json`. Without
axios the public page no longer loads the shared 36 KB axios chunk.

**Step 2, decisions:**

- Hooks are `data-<module>="<part>"`: `collapsible` root/btn/body, `menu`
  root/btn/bar/parent, `overlay` root/btn, `dropdown` root/btn, `filter`
  btn/item/group, `project` grid/prev/next/index, `imagescroll`
  item/prev/next, `imprint` btn/body, `swiper` themed, `data-map`. Filter
  buttons were `data-filter="wood"`, which collides with the hook, so the
  type moved to `data-filter-value`. `data-project-id`,
  `data-project-teaser`, `data-period`, `data-visible-onload` and the
  items' `data-filter-wood` etc. were already data attributes and stay.
- Show/hide is the `hidden` attribute everywhere, backed by
  `[hidden] { display: none !important }` in `_normalize.scss`. The inline
  `style="display: …"` the blades set for the initial state became
  `hidden` too.
- `$.scrollTo` → `window.scrollTo({ behavior: 'smooth' })`; the browser
  picks the duration (was 400/800 ms). Scroll targets are unchanged.
- `project.js` and `imagescroll.js` were the same code; both use
  `lib/sections.js` now. `contact.js` became `imprint.js` (it only toggles
  the imprint). `app.js` imports the modules and calls `init()`; module
  scripts run after parsing, like the old `$(fn)`.
- `aria-expanded` on collapsible buttons (free while rewriting it).
- `maps.js` is an ES module without jQuery; it bails out if
  `window.google` is missing instead of throwing.

**Behaviour changes, deliberate:**

- History scroll spy: `$('a[href!="#"]').removeClass('is-active')` took
  `is-active` off **every** link on the page, so the main menu lost its
  "Geschichte" marker after the first scroll. Now only `#…` links are
  touched (visible in the screenshot diff: "Geschichte" stays underlined).
- Scroll buttons on a project page closed the menu but left
  `html.has-menu` set. They now use the menu's own `close()`, which clears
  all three states (as history already did).
- Menu entries hidden by the filter come back as `display: inline`
  (their CSS default) instead of the `inline-block` jQuery wrote inline.
  Pixel diff: a sub-pixel underline shift on the active entry, nothing else.

**Verified:** `behave.js` before vs after, dev server and `BUILD=1`:
identical (after dropping the duplicate `overlay-info overlay-info` class
from two blades, which `classList` dedupes), 0 console errors. Screenshots:
22 of 26 identical; home and search differ by their random image, history
by the "Geschichte" marker, project by the underline shift. Contact map
renders from the built `maps.js`. Dropdown (works page, 375) and the project
page filter (24 → 36 → 24 entries) checked separately. `php artisan test`
37 passed. Bundle: `app.js` 238.8 KB → **147.6 KB** (gzip 71.0 → 40.0 KB);
Swiper 5 is most of the rest.

**Step 3, Swiper 5.3.8 → 14.3.0** (14 is current; the plan said 12).
Swiper runs on the discourse detail page only, from 960 px up: 42 of 77
published entries have two or more images.

- `import Swiper from 'swiper'` + `Navigation` from `swiper/modules`;
  only those two are bundled. Container class `.swiper-container` →
  `.swiper` (blade).
- The 531-line vendored Swiper 5 CSS is gone; `app.scss` imports the
  package's `swiper/swiper.css` (5.9 KB). Sass emits it as a plain CSS
  import, Vite inlines it at the top of the bundle, so
  `_swiper-custom.scss` (e.g. `.swiper-wrapper { display: block }` below
  960 px) still wins. Checked in the built CSS.
- The theme classes are now also set on `init`; Swiper 5 got there through
  a `transitionEnd` fired by the loop setup.
- Loop mode since Swiper 11 rearranges the real slides instead of cloning
  two extra, so the DOM has 5 slides instead of 7. That is the only
  difference in `behave.js` (dev server and `BUILD=1`).
- `~/oxid-qa/swiper-check.js`: entry 17 (themes 1,0,0,…) switches the
  arrows and close button light → dark and back; entry 5 (two images)
  loops both ways with no loop warning; images fill 1280 px. Screenshot
  diff: anti-aliasing inside the slide image only.
- Changing the dependency left the running Vite dev server with stale
  pre-bundled deps (504 on `.vite/deps/swiper_modules.js`), so the public
  JS did not run through it. Restarted it with `npx vite --force`.

Bundle: `app.js` 147.6 KB → **83.5 KB** (gzip 40.0 → 26.6 KB); 238.8 KB
(71.0 KB gzip) before this project. Public CSS 79.3 → 71.1 KB.

**Step 5, cross-browser QA** (Playwright 1.6x engines, `ENGINE=webkit|firefox`,
built assets): `behave.js` in WebKit and Firefox matches the Chromium run
except for scroll positions a few px apart (font metrics; the scroll targets
still land at `top` 0–1 px). `swiper-check.js` passes in both. 0 page
errors. Firefox warns that `assets/js/modernizr.min.js` (head, pre-existing)
forces layout before the CSS has loaded. Not done: real iOS/Android
devices. The scroll-position modules (`history`, `sections`) only run from
960 px up, so a desktop Safari check by hand is the useful remaining one.

**Found on the way, left alone:** the office footer dropdown
(`menu/footer/office.blade.php`) is inside an HTML comment, so it ships in
the page source but does nothing; converted with the rest, not removed.

## Known issues found during the survey (pre-existing) — all resolved

- Public-site cache-busting did not work: four bundles were `.version()`-ed
  but referenced with `asset()` instead of `mix()`, so the `?id=` hash was
  never emitted. **Fixed** by Vite (`cba7799`) — a behaviour change:
  browsers fetch new CSS/JS after a deploy.
- Every image crop was served at up to 2400 px regardless of the requested
  size. **Fixed** (`62c73c4`, then `ffac092`): requested sizes + AVIF/WebP.
- `config/image-cache.php` registered `Tiny.php`, which used the
  Intervention **v2** API. **Deleted** in `0afa861`, image-cache in `a809fd2`.
- `busu.css` is built from a 1,569-line Sass tree that nothing in this repo
  references. Answered: another site consumes it — output path kept.
- The public site loaded **axios and never made a request with it**, and
  shipped a dead fancyBox 3.5.7. **Fixed** in `046a170`.

## Deploy notes

`04-open-questions.md` #8: SSH + `git pull`, as in luvo. The full
checklist with server-specific notes is in `DEPLOYMENT.md` at the repo
root — gitignored on purpose, so it exists only locally. The committed
summary:

- Built assets are committed: run `npm run build` (public site + admin
  into `public/build/`, plus `public/assets/css/busu.css`) and commit the
  output with the change that needs it. Never commit `public/hot`
  (gitignored). Nothing is built on the server.
- Before the first rework deploy: snapshot the production DB and
  `storage/`; confirm the server's **CLI** PHP is 8.3+.
- On the server:
  ```bash
  git pull
  composer install --no-dev --optimize-autoloader
  php artisan migrate --force
  php artisan optimize:clear
  php artisan optimize
  php artisan images:warm
  ```
- New migrations: `2026_10_04_120000_add_dimensions_to_image_tables`
  (stored image sizes, backfilled from the files) and
  `2026_10_04_150000_widen_order_columns` (`order` TINYINT → SMALLINT;
  fixes reordering the 244 former team members). The other 51, including
  the two from 2026-02-09, already ran on production at `f140dca`.
- `.env` (Laravel 11+ names; the config files that read the old names are
  gone):
  - **`DB_CONNECTION=mysql` must be set** — the framework default is `sqlite`.
  - `QUEUE_CONNECTION=sync` — nothing is queued, but the framework default
    is `database`.
  - `CACHE_DRIVER` → `CACHE_STORE`, `FILESYSTEM_DRIVER` → `FILESYSTEM_DISK`
    (the defaults `file` / `local` are right).
  - Remove: `ALGOLIA_APP_ID`, `ALGOLIA_SECRET`, `SCOUT_DRIVER`,
    `SCOUT_PREFIX`, `JWT_SECRET`, `BROADCAST_DRIVER`, `PUSHER_*`,
    `MIX_PUSHER_*`, `REDIS_*`, `MAIL_*`.
- `.env`, Sanctum: **`APP_URL` must be the exact production origin**
  (`https://oxid-architektur.ch`) — Sanctum treats requests from that host
  as stateful. If the admin is also reached via `www.`, set
  `SANCTUM_STATEFUL_DOMAINS=oxid-architektur.ch,www.oxid-architektur.ch`.
  Leave `SESSION_DOMAIN` unset (host-only cookie) unless both hosts are
  used. Set `SESSION_SECURE_COOKIE=true`.
- Admins are logged out once by the deploy (JWTs no longer accepted); the
  SPA lands on the login screen.
- `php artisan optimize` (config + route + view cache) works since step 7;
  it never did before because of the duplicate route name.
- `storage/` and `storage/app/.glide-cache` writable; the Glide cache is
  not backed up.
- `php artisan images:warm` crawls the public pages in-process and renders
  every image they use into the Glide cache (cold renders 0.3–1.3 s each,
  so the first run takes a while; a warm run is ~20 s locally for 128
  pages / 6,162 URLs). Exits non-zero and lists the URLs that weren't 200.
- After go-live: `storage/app/public/cache/` (image-cache's output) can
  go; watch `laravel.log` for "Broken … rendition".

## Next after this project

QA automation, as luvo did afterwards. Partly done already: PHPUnit
feature tests for the whole admin API and the public pages, and the
Playwright screenshot/behaviour scripts in `~/oxid-qa` (outside the repo).
Still missing: a checklist with stable ids, Playwright E2E for the admin
in the repo, and a repeatable visual comparison against production. See
`07-qa-automation-prompt.md` and `08-test-plan.md` in the luvo repo —
both are reusable here with the entity names swapped.
