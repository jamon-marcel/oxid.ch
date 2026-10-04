# Admin UI refresh

Added 2026-10-04 at the client's request. A *slight* refresh of the admin's
look and feel, not a redesign:

1. splash screen
2. menu — smaller type, real group headers
3. icons → Phosphor
4. thinner borders and lines throughout

This breaks the "no design changes" rule in `README.md` on purpose; it is
listed there as an exception. Scope is `resources/sass/backend/` and the
admin templates. The public site is untouched.

## Correction: icons were not in the plan

`03-frontend-vue3.md` said "No `vue-feather-icons` … only one inline `<svg>`
in the whole admin" and took an icon swap off the list. Both statements are
true and the conclusion was wrong. The admin doesn't use the npm package —
it uses **Feather SVG files as CSS background images**:

```scss
.icon-eye { background-image: url($url-icons + 'eye.svg'); }
```

```html
<svg … stroke-width="2" … class="feather feather-eye">
```

So there *are* Feather icons throughout, just not where the package grep
looked. They need replacing.

## 1. Splash screen

**What it is today.** The login screen (`components/auth/LoginComponent.vue`)
sits on `.container-auth` (`layout/_container.scss`), which draws
`public/assets/backend/img/splash.jpg` full-bleed with `background-size:
cover`. The image is 1600×1067 JPEG, 265 KB, dated January 2020. On top: an
`<h1>Login</h1>`, two fields, a button.

There is **no loading splash** before Vue mounts. `backend/app.blade.php`
renders an empty `<div id="app">`, so the screen is blank until the bundle
has loaded and executed.

**Proposal:**

- **Login screen:** a new image, or none (a flat colour with the Oxid
  wordmark from `PageHeader.vue`, already inline as SVG). Serve it as AVIF/WebP
  with a JPEG fallback, sized for the viewport, rather than a single 265 KB
  JPEG. Card styled with the new 1px borders and type scale.
- ~~**Pre-mount splash (optional):**~~ *Dropped 2026-10-04 — not wanted.* a few lines of static HTML + CSS inside
  `<div id="app">` in `backend/app.blade.php` — wordmark, centred, on the
  brand background. Vue replaces it on mount. No JS, no flash of blank page.
  After the Vue 3 port the admin bundle should shrink (luvo's went from
  639 KB to 216 KB, though most of that came from its later `<script setup>`
  rewrite), so this mostly matters on slow connections.

**Open: the image itself.** A new photograph, a project image from the site,
or no image at all is a design decision — see "Open questions" below.

**Fix in passing:** `LoginComponent.vue` sets `loginError = true` on a failed
login and never displays it. Wrong credentials currently fail silently. The
login is rewritten for Sanctum anyway (`02-backend-laravel13.md`), so show the
error there — it costs nothing in that pass.

## 2. Menu

**What it is today.** An off-canvas panel sliding in from the right
(`navigation/_site.scss`, markup in `layout/PageHeader.vue`): 360 px wide,
`$color-dark` background. **Every item is bold at `$fs-lg`**, which is
24 px on mobile and 28–36 px on larger screens.

The structure already has groups:

```
Home      → News, Bilder
Projekte
Diskurs
Team      → Mitarbeiter, Bilder
Jobs      → Inserate, Bilder
Profil    → Text, Bilder
Kontakt
Logout
```

But the group labels (`Home`, `Team`, `Jobs`, `Profil`) are `<span>`s styled
identically to the links. The only difference is `padding-bottom: 0`. So the
hierarchy exists in the markup and is invisible on screen; four "Bilder"
entries in large bold type look like siblings of "Projekte".

**Proposal:**

| Element | Today | Proposed |
|---|---|---|
| Links | bold, `$fs-lg` (24–36 px) | regular, `$fs-sm` (15–16 px), medium-grey → white on hover/active |
| Group headers | same as links | **uppercase, `$fs-xxs` (10–12 px), letter-spacing ~0.08em, muted**, extra space above |
| Ungrouped items (`Projekte`, `Diskurs`, `Kontakt`) | same as links | links, same as grouped links |
| Active item | colour only | colour + a 1px left marker, matching the new line weight |
| Logout | link with icon | separated at the bottom by a 1px rule, Phosphor `SignOut` |

The panel gets narrower to match the smaller type — around 280 px instead
of 360.

**No markup change needed for the groups.** Restyle the existing `<span>`s
to `li > span`. One template change: give the panel's `<ul>` a class, so the
group-header rule doesn't depend on element nesting.

## 3. Icons → Phosphor

**Inventory:**

- 43 SVG files in `public/assets/backend/img/icons/`, all Feather,
  stroke-width 2
- **28 referenced, 15 unreferenced**: `arrow-right`, `bookmark`, `calendar`,
  `check`, `chevron-down-grey`, `chevron-down-white`, `columns`, `delete`,
  `grid-1-1`, `grid-1-2`, `grid-1`, `grid-2-1`, `grid-2-2`, `users`, `x`
- 18 icon classes in templates, across **25 `.vue` files**:

| Class | Uses | Phosphor |
|---|---|---|
| `icon-mini` (size modifier, 18 px) | 42 | → `:size="18"` |
| `icon-edit` | 12 | `PhPencil` |
| `icon-trash` | 11 | `PhTrash` |
| `icon-eye` / `icon-eye-off` | 11 / 11 | `PhEye` / `PhEyeSlash` |
| `icon-close-overlay` | 10 | `PhX` |
| `icon-external-link` | 5 | `PhArrowSquareOut` |
| `icon-view` | 4 | `PhMagnifyingGlassPlus` or `PhArrowsOut` — check usage |
| `icon-move` | 4 | `PhDotsSixVertical` (drag handle, as luvo) |
| `icon-sticky` | 2 | `PhPushPin` |
| `icon-crop` | 2 | `PhCrop` |
| `icon-disabled` | 2 | state modifier — check |
| `icon-menu` / `icon-close` | 1 / 1 | `PhList` / `PhX` |
| `icon-logout` | 1 | `PhSignOut` |
| `icon-layout`, `icon-grid`, `icon-grid-list` | 1 each | `PhLayout`, `PhSquaresFour`, `PhRows` |

The Sass side is 18 files in `components/icons/`, one per icon, plus
`config/_icons.scss`.

**Done differently, 2026-10-04:** Phosphor light as SVG files behind the
existing CSS classes, not as components — see `06-progress.md`, "Notes from
the admin UI refresh" (the `progress` mixin works on `event.target`).
Original plan, kept for reference:

**Approach — copy luvo exactly** (`13647d0`, "Use Phosphor light icons like
strut"):

- `@phosphor-icons/vue`, **weight `light`** throughout. That's the thin-line
  look that matches the 1px borders below.
- Template icons become components: `<PhPencil :size="18" weight="light" />`.
  `icon-mini` disappears as a class and becomes a size prop.
- Icons that genuinely need to stay CSS backgrounds (select carets, the grid
  layout pictograms in `projects/grid/`) become Phosphor **light** SVG files
  in place of the Feather ones, colours kept.
- Delete the 15 unreferenced SVGs, then any further files freed up by moving
  to components. luvo deleted 33.
- Delete `components/icons/_*.scss` as each class stops being used.

**Do this during the Vue 3 port, not after.** All 25 files are open in that
pass anyway, and a Vue 2 → 3 component port that leaves CSS-background icons
behind only to revisit the same templates later is wasted motion. The project
grid layout icons (`grid-1`, `grid-1-2`, `grid-2-1`, …) belong to the page
builder and go with it, last.

## 4. Thinner borders and lines

**Census of `resources/sass/backend`:** 103 border declarations across
24 files. **No border variable exists**; every width is hardcoded.

| Width | Count | What it is |
|---|---|---|
| **2px** | **54** | the lines — inputs, cards, tables, tabs, dividers |
| 1px | 4 | |
| 6px | 3 | CSS triangle in `vendor/_dropzone.scss` (tooltip arrow) |
| 20px | 2 | CSS triangles |
| 8px, 5px, 3px | 1 each | 5px is the left accent bar in `vendor/_notifications.scss` |

**Proposal:**

1. Add tokens to `config/_global.scss` (or a new `config/_borders.scss`):
   ```scss
   $border-width: 1px;
   $border-color: <existing line colour>;
   $border: $border-width solid $border-color;
   ```
2. Replace the 54 × 2px lines and the 4 × 1px lines with the tokens. That makes
   "thinner" one variable, and any later adjustment a one-line change.
3. **Leave the triangles alone.** The 20px, 8px and 6px values are CSS-drawn
   arrows; their "border" is geometry, not a line, and making them thinner
   changes their shape, not their weight.
4. Notifications accent bar (5px): reduce to 2–3px to match the lighter
   overall feel. It's a deliberate accent, so keep it thicker than 1px.
5. Check focus states. If any input uses a 2px border **as** its focus
   indicator, thinning it to 1px weakens keyboard visibility. Replace with an
   `outline` + `outline-offset` focus ring rather than a thicker border, so
   the resting state can be 1px without losing focus visibility. (There are
   currently 5 `outline: none` / `outline: 0` declarations — check each one
   has a replacement.)

Box shadows (`$box-shadow`, `$box-shadow-lg`, used on the open menu and
overlays) are part of the same visual weight. Soften them alongside, or
deliberately leave them — but decide, since 1px lines next to the current
shadows may look unbalanced.

## Sequencing

| Item | When | Why |
|---|---|---|
| Icons | **during** the Vue 3 port | same 25 files, open anyway |
| Login screen + error message | **during** the Sanctum switch | the login is rewritten then |
| Borders, shadows, menu | **after** the Vue 3 port, **before** final QA | Sass-only; one QA pass covers them |

Do the border token step first of the Sass items. It's the widest change
(24 files) and the purely mechanical one, and getting it in early means the
menu and login are styled against the final line weight.

## Effort

| Item | Days |
|---|---|
| Icons: 18 classes across 25 files, CSS-background SVGs, cleanup | 0.5 – 0.75 |
| Menu: type scale, group headers, width, active marker | 0.25 – 0.5 |
| Splash: login restyle, random home image via `--splash`, delete `splash.jpg` | 0.25 – 0.5 |
| Borders: tokens, 58 replacements, focus rings, shadow check | 0.5 |
| Visual pass across all 30 screens | 0.25 |
| **Total** | **1.75 – 2.5** |

The visual pass overlaps with the main project's admin QA day. Do them as
one pass, not two.

## Open questions

| # | Question | Answer (2026-10-04) |
|---|---|---|
| 1 | Login image | **A random published home image** (revised 2026-10-04; replaces "client provides one"). Nothing to deliver, nothing blocking. See below. |
| 2 | Pre-mount splash | **Not wanted** — restyling the login screen is enough. |
| 3 | Box shadows | **Soften them** along with the borders. |
| 4 | Brand colour / typeface | **Unchanged** — Euclid Circular A (Regular + Medium), palette in `config/_colors.scss`. |

### On #3: what "soften" means here

`$box-shadow` and `$box-shadow-lg` are used on the open menu and on
overlays, plus 5 call sites passing explicit `$x $y $blur $spread $color`.
Soften = lower opacity and a smaller spread, keeping the blur, so surfaces
still lift off the page but don't look heavy next to 1px lines. Do it in the
two variables first. Then check the 5 explicit call sites; any that don't
need custom values should switch to the variables.

### On #1: a random home image as the login background

Same idea the public search page already uses (`SearchController:53-58` picks
a random published `HomeImage`). 10 of 14 home images are published in production.

**How:**

- `/admin` is served by `Route::view('admin', 'backend.app')` in
  `routes/web.php`, with no controller. Turn it into a small closure (or a view
  composer) that picks one image:
  `HomeImage::published()->inRandomOrder()->first()`. That's one query,
  instead of the load-all-then-`mt_rand` pattern in `SearchController`.
- In `backend/app.blade.php`, expose it as a CSS custom property on `<body>`:
  `style="--splash: url('/img/home/{{ $splash->name }}')"`.
- `.container-auth` uses `background-image: var(--splash, none)` over the
  brand background colour.

So the Vue login component needs **no API call and no JS**. It works before
authentication, which an API route would have had to allow for anyway.

**Delete `public/assets/backend/img/splash.jpg`** (265 KB, from 2020) once
this is in.

**Details to get right:**

- **Published only.** The login page is reachable without logging in, so
  unpublished images must never appear. `published()` handles it; keep it.
- **Fallback.** With zero published images, `--splash` is unset and the
  brand colour shows. No broken image.
- **Size and format.** `/img/home/` goes through the Glide controller after
  step 5 of the backend plan, with the `Home` crop applied from the
  database. Request a viewport-appropriate size with AVIF/WebP via
  `image-set()`, instead of the 2000×1250 cap the `Home` template uses today.
- **Framing.** The home-image crops are composed for the homepage. As a
  full-viewport `cover` background on a portrait phone, the sides get cut.
  `background-position: center` is the honest default; there's no per-image
  focal point to use.
- **Legibility.** The images vary in brightness, so the login card needs
  its own solid background (white, 1px border, softened shadow), not
  transparency over the photo.
- **Caching.** Pages under `/admin` must not be cached at the HTTP level
  anyway, so each visit gets a fresh pick. The image itself is cached by
  Glide.
