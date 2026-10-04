# Inventory (measured 2026-10-04, commit f140dca)

## Size

| Area | Measured | luvo, for scale |
|---|---|---|
| `app/` PHP | **6,494 LOC across 101 files** | 5,678 / 102 |
| Vue components | **59 `.vue` files, 6,978 LOC** | 51 / 5,911 |
| Blade | **53 files** | 39 |
| Sass | **12,250 LOC** | 8,740 |
| Routes | **144** (116 api, 28 web) | 111 |
| Tests | **4 files, 72 LOC** — example stubs, effectively none | 71 LOC |

## `app/` breakdown

| Dir | Files | LOC |
|---|---|---|
| `Http/Controllers/Api` | 22 | 2,935 |
| `Http/Controllers` | 14 | 879 |
| `Models` | 21 | 728 |
| `Http/Requests` | 10 | 414 |
| `Observers` | 5 | 363 |
| `Filters/Image/Template` | 7 | 298 |
| `Providers` | 5 | 207 |
| `Http/Middleware` | 7 | 157 |
| `Helpers` | 2 | 128 |
| `Http/Kernel.php` | 1 | 80 |
| `Console/Commands` | 1 | 59 |
| `Filters/Image` | 1 | 50 |
| `Exceptions/Handler.php` | 1 | 49 |
| `Console/Kernel.php` | 1 | 42 |
| `Services` | 1 | 28 |

Models: Base, Contact, Discourse, DiscourseDocument, DiscourseImage, Grid,
GridElement, GridLayout, HomeImage, Job, JobDocument, JobImage, News, Profile,
ProfileImage, Project, ProjectDocument, ProjectImage, Team, TeamDocument,
TeamImage.

**No raw SQL anywhere** (`DB::raw` / `->raw(` → 0 hits).
**Carbon appears in exactly one file** (`app/Models/News.php`).

## Legacy skeleton artefacts

The app runs Laravel 11 on the **Laravel 8-era skeleton**:

- `bootstrap/app.php` — old style, manually binds `App\Http\Kernel`,
  `App\Console\Kernel`, `App\Exceptions\Handler`
- `app/Http/Kernel.php` — 7 middleware classes + 11 route-middleware aliases
- `app/Providers/` — App, Auth, Broadcast, Event, Route
- `app/Exceptions/Handler.php`
- **`app/User.php`** — not `app/Models/User.php`. Namespace `App\`, not
  `App\Models\`. Oldest file in the tree (Oct 2022).

Middleware to carry over: `TrustProxies`, `CheckForMaintenanceMode`,
`TrimStrings`, `EncryptCookies`, `VerifyCsrfToken`, `Authenticate` (`auth`),
`RedirectIfAuthenticated` (`guest`).

## Dead code and orphans to delete

**`app/Filters/Image/Template/`** — 4 of the 7 classes implement Intervention
Image **v2**'s `FilterInterface`, removed in v3:

| Class | API | Wired into `config/image-cache.php`? | References elsewhere |
|---|---|---|---|
| `Cache.php` | v2 | no | **0** |
| `Large.php` | v2 | no | **0** |
| `Small.php` | v2 | no | **0** |
| `Thumbnail.php` | v2 | no | **0** |
| `Tiny.php` | **v2** | **yes** (`'tiny' =>`) | no URL emits it |
| `Home.php` | v3 | yes | live, see `05-image-pipeline.md` |
| `Project.php` | v3 | yes | no URL emits it |

`Tiny.php` is the trap: it is registered in config but uses the v2 API, so it
is already a latent fatal and will become a hard one on Intervention v4.
Nothing requests `/img/tiny/`, so it has never fired.

**Orphan configs** (present, referenced nowhere in `app/`, `resources/`,
`routes/`):

- `config/dompdf.php` — dompdf is **not in `composer.lock` at all**. Delete.
  (luvo had the identical orphan.)
- `config/media.php`, `config/content.php`, `config/image.php` — 0 `config()`
  reads. Verify against blades before deleting.
- `config/jwt.php` — goes with the Sanctum switch.

Kept, in use: `settings.php` (2 reads), `seo.php` (5 reads), `image-cache.php`,
`scout.php` (read by the package, not by app code).

**Unused npm packages**: `cleave.js` (`v-cleave` used 0 times),
`vuejs-datepicker` (0 imports), `nth-check` (a transitive pin, not a direct
dependency), `popper.js` (deprecated upstream in favour of `@popperjs/core`).

## Frontend structure

- `resources/js/frontend/` — public site. Plain JS + jQuery + Bootstrap 4,
  13 modules + 4 vendored libs (`fancybox`, `lazysizes`, `scrollTo`,
  `swiper`). **No Vue.** Out of scope apart from the Vite entry points.
- `resources/js/backend/` — the Vue 2 SPA. Everything in scope lives here.
  - `app.js` (227 LOC) — **~175 of it is JWT token machinery**; see
    `02-backend-laravel13.md`
  - `routes.js` — vue-router 3, 30 named routes
  - `store.js` — Vuex, 2 state fields (`isLoggedIn`, `hasChanges`)
  - `filters.js` — one `Vue.filter('truncate')`
  - `mixins/` — `progress.js`, `utils.js`, `images/{crop,listing}.js`
  - `config/` — `dz-file.js`, `dz-image.js`, `tinyconfig.js`
  - `layout/` — `Page.vue`, `PageHeader.vue`
  - `components/` — 57 `.vue` files

## Sass / build

Three entry trees: `resources/sass/{backend,frontend,frontend-busu}`.
`webpack.mix.js` produces **6 bundles** (luvo had 4). Only the two backend
bundles go through `mix()` in blade; the frontend ones are referenced with
plain `asset()` despite being `.version()`-ed — see `03-frontend-vue3.md`.
