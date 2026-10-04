# Progress

Nothing started. Survey only, done 2026-10-04 against `f140dca` on `master`
(clean tree).

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
| Delete dead code: 6 filter classes, `dompdf`/`media`/`content` configs | — | |
| **One commit:** Laravel 13, PHP ^8.3, drop image-cache, add Glide + Intervention 4 | — | |
| Glide routes, `ImageSupport`, requested sizes + WebP/AVIF, `ImageHelper` → `<picture>` | — | |
| Slim skeleton, `app/User.php` → `app/Models/User.php` | — | |
| Search phase 1: drop Algolia, Scout `collection` driver | — | |
| JWT → Sanctum | — | |
| Form-request validation messages (L12+ wants strings) | — | |
| Search phase 2: own scoring search + unit tests, drop Scout | — | |

The Laravel 13 bump and the image-cache → Glide dependency swap **must be
the same commit** — Composer will not resolve anything on Laravel 11. See
`02-backend-laravel13.md`, "Why step 4 must be one commit".

### To verify at the end of the backend phase

- Same 144 routes (116 api, 28 web).
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
- Glide cache dir writable; not backed up.
- After go-live, `storage/app/public/cache/` (old image-cache output) can go.

## Next after this project

QA automation, as luvo did afterwards: a checklist with stable ids, PHPUnit
feature tests, Playwright E2E, and a visual comparison against production.
See `07-qa-automation-prompt.md` and `08-test-plan.md` in the luvo repo —
both are reusable here with the entity names swapped.
