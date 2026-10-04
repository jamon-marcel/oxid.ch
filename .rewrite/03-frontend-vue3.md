# Frontend: Vue 2.7 → Vue 3

*Done 2026-10-04. This is the plan; the outcome differs in several places.
The first port (`15c4102`) followed this table, then the admin was
rewritten in luvo's shape the same day. What the admin uses now:*

| Plan | Outcome |
|---|---|
| Options API + mixins | `<script setup>` + composables (`f4ac39b`, `7b55d5e`) |
| `vue2-dropzone` → Dropzone v6 wrapper | own `components/ui/Uploader.vue`, per-file progress (`5f77c53`, `946fb60`) |
| `vuedraggable@^4` | `sortablejs` via `components/ui/SortableList.vue` (`ac6a435`) |
| `@kyvg/vue3-notification` | own `lib/notify.js` + `components/ui/Notifications.vue` (`5f77c53`) |
| `vue-the-mask` → a Vue 3 replacement | `dateMask()` in `lib/utils.js` (`maska` in between) |
| `moment` → `dayjs` if needed | no date library |
| `$parent` converted opportunistically | none left |
| `Create`/`Edit`/`Form`/`Index` per entity | `views/<entity>/{Index,Form}.vue`; one `views/images/Index.vue` for the four image libraries |
| feather CSS-background icons → Phosphor | `@phosphor-icons/vue` components for actions; a few CSS icons stay (`09-admin-ui.md`) |
| TinyMCE → Tiptap | as planned, `components/ui/editor/` |
| `vue-advanced-cropper` ^2, `vue-router` ^4, Vuex deleted | as planned |
| Mix → Vite | as planned; the admin and the public site share `vite.config.js` |
| Vendored public libs left alone | moved to npm, later removed or upgraded (`08-frontend-js.md`) |

The router has 33 named routes; the dashboard is gone (the admin lands on
the news list).

Scope is `resources/js/backend/` (the admin SPA). The public site
(`resources/js/frontend/`) is plain JS + jQuery + Bootstrap 4 and stays as it
is, apart from its Vite entry points.

## The code itself is clean

Scanned all 59 components. **Zero hits** for every expensive Vue 2 pattern:

| Pattern | Hits |
|---|---|
| `$listeners` | 0 |
| `$children` | 0 |
| `$scopedSlots` | 0 |
| `slot=` / `slot-scope` | 0 |
| event bus (`$on(` / `$off(` / `$root.$emit`) | 0 |
| `$set(` / `$delete(` | 0 |
| `.sync` modifier | 0 |
| functional components | 0 |

What *does* need touching:

- **`$parent` — 32 hits across 15 `.vue` files.** This is the one place oxid
  is dirtier than luvo, which had a single `$parent.$parent` chain. It still
  *works* in Vue 3, so it is not a blocker, but it is brittle and it is
  concentrated in exactly the components being rewritten anyway (the upload
  and grid components). Convert to `emit` opportunistically where a file is
  already being touched; do not make a separate pass of it.

  | File | Hits |
  |---|---|
  | `components/global/images/Actions.vue` | 4 |
  | `components/projects/upload/ImageUpload.vue` | 4 |
  | `components/global/files/Actions.vue` | 3 |
  | `components/global/files/Listing.vue` | 3 |
  | `components/global/upload/MultiImageUpload.vue` | 3 |
  | `components/projects/upload/FileUpload.vue` | 3 |
  | `components/team/files/Listing.vue` | 3 |
  | `components/global/upload/ImageUpload.vue` | 2 |
  | `components/{global/files/Upload,global/images/Upload,global/input/Text}.vue` | 1 each |
  | `components/projects/grid/{ButtonAdd,ButtonAddArticle,Media,Selector}.vue` | 1 each |

- **1 global filter** — `filters.js`, `Vue.filter('truncate')`. Vue 3 removed
  filters. One template pipe uses it. Convert to a method or a plain import.
- **28 files declare `mixins:`** — ~~keep them~~. **Revised 2026-10-04:**
  they become composables, as in luvo (`<script setup>` throughout).

## Dependency migration table

| Package | Current | Move to | Files | Effort |
|---|---|---|---|---|
| **`vue2-dropzone`** | ^3.6.0 | **no Vue 3 port — rewrite** | **6** | **highest risk** |
| `vuedraggable` | ^2.24.3 | `vuedraggable@^4` | **13** | slot API changed |
| `tinymce` + `@tinymce/tinymce-vue` | 5.10.9 / ^3.2.8 | **removed — Tiptap 3** | 7 | decided; see below |
| `vue-advanced-cropper` | ^0.16.5 | `^2` (has Vue 3 support) | 2 | easy |
| `vue-router` | ^3.6.5 | `^4` | `routes.js`, 30 named routes | mechanical |
| `vue-notification` | ^1.3.20 | `@kyvg/vue3-notification` | `app.js` | drop-in |
| `vuex` | ^3.6.2 | **delete** | `store.js` | 2 fields; see below |
| `vue-moment` + `moment` | ^4.1.0 | **delete** — use `dayjs` if needed | `app.js` | free |
| `vue-axios` + `vue-axios-interceptors` | — | **delete** — plain axios | `app.js` | free |
| `vue-the-mask` | ^0.11.1 | needs a Vue 3 replacement | 1 (`home/news/Form.vue`) | small |
| `cleave.js` | ^1.6.0 | **delete** — `v-cleave` used 0 times | — | free |
| `vuejs-datepicker` | ^1.6.2 | **delete** — unused | — | free |
| `popper.js` | ^1.16.1 | **deprecated upstream** → `@popperjs/core` | — | public site only |
| `laravel-mix` | ^6.0.49 | **Vite** | `webpack.mix.js`, blade | 0.5–0.75 day |

**Icons: corrected 2026-10-04.** An earlier version of this file said "no
`vue-feather-icons`, only one inline `<svg>`" and dropped the icon swap. The
package really isn't used, but the admin uses **Feather SVG files as CSS
background images**: 18 icon classes across 25 components. They're replaced
with Phosphor (light) during this port — see `09-admin-ui.md`.

### Exact file lists

**`vue2-dropzone`** (6): `components/global/files/Upload.vue`,
`components/global/images/Upload.vue`,
`components/global/upload/ImageUpload.vue`,
`components/global/upload/MultiImageUpload.vue`,
`components/projects/upload/FileUpload.vue`,
`components/projects/upload/ImageUpload.vue`
— plus the two configs `config/dz-file.js`, `config/dz-image.js`.

luvo chose a thin wrapper around **Dropzone v6** to keep the UX, and it worked.
Three of these six are `$parent`-coupled to their hosts, so do the emit
conversion in the same pass rather than preserving the coupling.

**`vuedraggable`** (13): `components/discourses/Form.vue`,
`components/discourses/Index.vue`, `components/global/upload/MultiImageUpload.vue`,
`components/home/news/Index.vue`, `components/jobs/Index.vue`,
`components/jobs/images/Index.vue`, `components/profile/images/Index.vue`,
`components/projects/Index.vue`, `components/projects/grid/Index.vue`,
`components/projects/upload/FileUpload.vue`,
`components/projects/upload/ImageUpload.vue`, `components/team/images/Index.vue`,
`components/team/team/Index.vue`

**TinyMCE** (7): `config/tinyconfig.js`, `components/contact/Form.vue`,
`components/discourses/Form.vue`, `components/home/news/Form.vue`,
`components/jobs/Form.vue`, `components/profile/text/Form.vue`,
`components/projects/Form.vue`

**`vue-advanced-cropper`** (2): `components/projects/images/Listing.vue`,
`components/projects/upload/ImageUpload.vue`

### TinyMCE → Tiptap (decided 2026-10-04)

TinyMCE 5 went EOL in April 2023 and carries unpatched XSS (iframe handling,
external SVG via object/embed). It renders admin-authored content on the
public site, so it is the one genuinely user-facing security item in this
survey. Replacing rather than upgrading was chosen — see
`04-open-questions.md` #4.

Port from luvo (`3bf1067`): `components/ui/editor/` — `Editor`, `Toolbar`,
`LinkDialog`, `smallText`. Its link dialog offers URL / E-Mail / Telefon /
Datei, the last picking from uploaded files; check which of those oxid's
`config/tinyconfig.js` actually needs before copying it wholesale.

**Do the round-trip verification before switching, not after.** Export every
stored rich-text value, run it through Tiptap, diff visible text, links,
headings and lists. luvo did this across 197 values
(`.rewrite/tools/tiptap-roundtrip.mjs`, needs `@tiptap/html` + `happy-dom`).
The 6 editor-bearing forms here are contact, discourses, home/news, jobs,
profile/text and projects.

Expected side effect, same as luvo: saving strips Word/Outlook paste junk, so
pasted inline fonts (Segoe UI, 12pt) disappear and those texts revert to the
site font. An improvement, but visible — tell the editors before go-live.

### Vuex

Two fields: `isLoggedIn` and `hasChanges`. `isLoggedIn` is derived from
`localStorage.getItem('token')` and **disappears with the Sanctum switch** —
session auth means the server is the source of truth. `hasChanges` is a single
dirty flag. Neither needs a store; a small reactive module or `provide`/`inject`
covers both. Delete Vuex.

## `app.js` is mostly auth, not bootstrap

`resources/js/backend/app.js` is 227 LOC, and **roughly 175 of it is JWT token
machinery**: a refresh-subscriber queue, `isRefreshing` guard, `_retry` flags,
blacklisted-token detection, a response interceptor that transparently refreshes
and replays failed requests, and a `router.beforeEach` that round-trips
`POST /api/auth/me` on **every** protected navigation.

With the Sanctum switch this collapses to roughly:

- `createApp(App)` instead of `new Vue({ el: '#app' })`
- `createRouter({ history: createWebHistory(), routes })`
- an axios instance with `withCredentials: true`, a `/sanctum/csrf-cookie`
  call before login, and **one** response interceptor: on 401, redirect to
  login. No refresh, no queue, no replay.
- a route guard that checks a session flag, not a token
- `@kyvg/vue3-notification` in place of `vue-notification`
- drop: Vuex, vue-moment, vue-axios, vue-axios-interceptors

**Do the Vue 3 port and the Sanctum switch as two commits, not one.** They both
rewrite this file; interleaving them makes a bisect useless, and this is the
file with no test coverage and the most ways to fail silently.

## Vite migration

`webpack.mix.js` produces **6 bundles**:

| Source | Output | `.version()` |
|---|---|---|
| `resources/js/backend/app.js` | `public/assets/backend/js` | no |
| `resources/sass/backend/app.scss` | `public/assets/backend/css` | no |
| `resources/js/frontend/app.js` | `public/assets/js` | yes |
| `resources/js/frontend/maps.js` | `public/assets/js` | yes |
| `resources/sass/frontend/app.scss` | `public/assets/css` | yes |
| `resources/sass/frontend-busu/busu.scss` | `public/assets/css` | yes |

Carry over: the `@` → `resources/js/backend/` alias, `processCssUrls: false`,
and `sassOptions.outputStyle: 'compressed'`.

luvo's `vite.config.js` is a direct template, including
`css.preprocessorOptions.scss.silenceDeprecations: ['import']` — you have
**12,250 lines of Sass still on `@import`**, and migrating that to
`@use`/`@forward` is a separate refactor, not part of this one.

### Two findings in the current build

1. **The public site's cache-busting does not work.** Four bundles are
   `.version()`-ed and `public/mix-manifest.json` has the `?id=` hashes, but
   `resources/views/frontend/partials/{head,footer}.blade.php` reference them
   with plain `asset()`, not `mix()`. The hash is never emitted, so visitors
   can get stale CSS/JS after a deploy. Vite's manifest fixes this by
   construction — but note it as a *behaviour change*: caching that silently
   did nothing will start working.
2. **`busu.css` is built and referenced nowhere.** `resources/sass/frontend-busu/`
   is a 1,569-line Sass tree compiled to `public/assets/css/busu.css`, and
   nothing in `resources/views/`, `routes/`, `app/` or `public/` links to it.
   Last touched in two commits both titled "frontend". Either something
   outside this repo consumes the built file, or it is dead. See
   `04-open-questions.md` #5 — do **not** drop it from the Vite config on
   assumption.

Blade `mix()` calls to convert: exactly **2**, both in
`resources/views/backend/app.blade.php` (lines 7 and 14). The frontend ones
switch from `asset()` to `@vite([...])`.

The SPA mounts on `<div id="app">` in `resources/views/backend/app.blade.php:11`.

Leave `resources/js/frontend/vendor/` (fancybox, lazysizes, scrollTo, swiper)
alone; make sure Vite does not try to optimise the vendored copies.

If `08-frontend-js.md` step 1 runs first — it is pure deletion — then
`vendor/fancybox.js` and `modules/fancybox.js` are already gone and Vite has
less to carry. Worth doing in that order.

## Shortcut: the near-duplicates

The CRUD screens follow a rigid pattern — `Create.vue` / `Edit.vue` /
`Form.vue` / `Index.vue` per entity across contact, discourses, jobs,
home/news, profile/text, projects, team/team. The `Create`/`Edit` pairs are
thin stubs. **Port one entity end to end, review it, then replicate.**

The exception is `components/projects/grid/` (ButtonAdd, ButtonAddArticle,
Index, Media, Row, Selector) — a page-builder over the `Grid`, `GridLayout`
and `GridElement` models, with `vuedraggable`, `$parent` coupling and a
cropper. luvo has no equivalent. Budget it separately and port it last, when
the patterns from everything else are established.

## Admin screens to click through for QA

30 named routes: dashboard · login/logout · news (index/create/edit) ·
home images · projects (index/create/edit) · **project grids** · discourses
(index/create/edit) · team (index/create/edit) · team images · jobs
(index/create/edit) · job images · profile (index/create/edit) · profile
images · contact (index/create/edit)

Per screen: list, create, edit, delete, reorder (draggable), file upload,
image upload + crop, TinyMCE content, validation errors.
