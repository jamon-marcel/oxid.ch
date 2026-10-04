# Estimate

Scope: **minimum**. Get onto Laravel 13, Vue 3 and Sanctum with identical
behaviour. Nothing else.

## Explicitly in scope

- Laravel 11 → 13, PHP 8.2 → 8.3 (host offers up to 8.5), Carbon 2 → 3
- Legacy L8 skeleton → slim L11+ skeleton (`bootstrap/app.php`)
- **JWT → Sanctum** (decided 2026-10-04)
- `marceli-to/image-cache` → `league/glide`
- Algolia client v3 → v4, Scout 10 → 11
- Vue 2.7 → Vue 3, vue-router 3 → 4, drop Vuex
- Laravel Mix → Vite
- Replace every Vue 2-only package
- **TinyMCE → Tiptap** (decided; TinyMCE 5 is EOL with unpatched XSS)
- **Serve requested image sizes + WebP/AVIF** (decided), not a strict 1:1 port

## Explicitly OUT of scope

- Composition API / `<script setup>` rewrite — keep Options API + mixins
- Test suite — there is none; QA stays manual click-through
- Any design or UX change
- The **public site** frontend: plain JS + jQuery + Bootstrap 4, no Vue.
  Untouched apart from the Vite entry points.
- The 12.2k LOC of Sass, and its `@import` → `@use` migration. Untouched.
- Bootstrap 4 → 5 (EOL, but public-site only and a separate project)

## Numbers

| Phase | Days |
|---|---|
| Backend L11 → L13 + Sanctum + Glide + Algolia | **3.5 – 4.5** |
| Frontend Vue 3 + Vite + Tiptap | **7 – 8.5** |
| **Total** | **10.5 – 13** |

Add review and click-through QA → **~3 weeks calendar** if reviewed as we go.

Up from the 9–12 first written on 2026-10-04: the answers to questions 2 and
4 both chose the better outcome over the smaller diff. Worth it, but it is
+1.5 days and should not arrive as a surprise later.

### Backend breakdown

| Task | Days |
|---|---|
| Dependency bump + conflict resolution | 0.5 |
| Glide replacement + verification against production images | 0.75 – 1 |
| Requested sizes + WebP/AVIF: rewrite `ImageHelper` to `<picture>`, runtime format detection (Q2) | 0.5 |
| Skeleton migration (Kernel → `bootstrap/app.php`, providers, handler, `app/User.php` → `app/Models/`) | 0.5 |
| JWT → Sanctum (backend half) | 0.5 |
| Algolia v3 → v4 + Scout 11, re-verify both indices | 0.5 |
| PHP 8.3, Carbon 3, `config/` diffs, form-request validation messages | 0.5 |
| Smoke test every route + CRUD path | 0.5 |

### Frontend breakdown

| Task | Days |
|---|---|
| Vite setup, `@vite` in blade, aliases, sass, 6 bundles | 0.5 – 0.75 |
| `app.js` rewrite: Vue 3 bootstrap, router 4, drop vuex/moment/vue-axios | 0.25 |
| Sanctum auth in the SPA (replaces ~200 LOC of token machinery) | 0.5 |
| **Dropzone replacement** (6 files + 2 configs) | 1 – 1.25 |
| `vuedraggable` 2 → 4 across 13 files | 0.75 |
| **TinyMCE → Tiptap** across 7 files, incl. round-trip verification (Q4) | 1 |
| Cropper + the project image listing | 0.5 |
| **`projects/grid/` page builder** (6 components, `$parent`-coupled) | 0.75 – 1 |
| Remaining ~40 components: filters, emits, `$parent`, mechanical fixes | 1.25 |
| Click-through QA of all 30 admin screens | 1 |

## Where the delta from luvo comes from

luvo's comparable figure was 7–9 days for 11→13 + Vue 2→3. oxid is +2 to +3:

| Item | Days | Why |
|---|---|---|
| JWT → Sanctum | +1 | luvo never had JWT; no precedent to copy |
| Algolia v4 | +0.5 | luvo has no search |
| `projects/grid/` builder | +0.75 – 1 | no equivalent in luvo |
| Dropzone: 6 files vs 2 | +0.5 | |
| `vuedraggable`: 13 files vs 9 | +0.25 | |
| 6 build bundles vs 4 | +0.25 | |
| Glide port with DB-lookup coords | +0.25 | `Home.php` needs controller-side resolution |
| **No `vue-feather-icons`** | **−0.25** | luvo's 24-file icon swap does not apply |

Offsetting that: the Glide pipeline, the Vite config, the slim skeleton and
the "port one entity, then replicate" approach are all proven over there, so
the parts oxid shares with luvo should run faster than they did the first time.

## What could move these numbers

1. ~~**PHP 8.3+ on hosting.**~~ **Cleared 2026-10-04** — production runs
   8.3 up to 8.5. This was the one gate that could have stopped the project.
2. **Zero test coverage.** Every verification is manual. That is why the QA
   day is not compressible — and why the Sanctum switch, which can fail
   silently in ways a click-through will not catch, is the riskiest item here.
3. **Dropzone.** The only piece with no migration path. Six files, three of
   them `$parent`-coupled to their hosts, and the upload flow has to be
   re-tested against the image and file endpoints.
4. **Tiptap round-trip.** luvo verified 197 stored values before switching.
   oxid has 6 editor-bearing forms; if the stored HTML turns out to be
   messier than luvo's, the round-trip pass is where that surfaces, and it
   is the kind of thing that turns 1 day into 2.
