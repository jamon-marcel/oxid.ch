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
- [ ] Export both Algolia indices' settings from the dashboard and commit
      them (`04-open-questions.md` #7).
- [ ] Snapshot the production DB + `storage/` locally, as baseline and
      rollback point. The image verification depends on it.
- [ ] Record the current state for comparison: route list, the `/img/...` URLs
      every public page emits and their byte sizes, and screenshots of the
      public pages at 375 and 1280 px.

## Backend

| Step | Status | Commit |
|---|---|---|
| Delete dead v2 filters + orphan configs | — | |
| Glide replaces `marceli-to/image-cache` | — | |
| Requested sizes + WebP/AVIF, `ImageHelper` → `<picture>` | — | |
| Laravel 13, PHP ^8.3, Carbon 3, Intervention 4 | — | |
| Slim skeleton, `app/User.php` → `app/Models/User.php` | — | |
| Algolia client v4, Scout 11 | — | |
| JWT → Sanctum | — | |
| Form-request validation messages (L12+ wants strings) | — | |

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
  references.

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
