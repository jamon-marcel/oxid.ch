# Public site JS: modernisation

Scope: `resources/js/frontend/` — the public site. Added 2026-10-04 at the
client's request; earlier drafts of `00-estimate.md` listed this as explicitly
out of scope.

This is **independent of the Laravel 13 / Vue 3 work**. It shares only the
Vite migration, which has to happen either way. It can run before, after, or
in parallel, and it can be abandoned halfway without leaving anything broken.

## Current state, measured

| File | LOC | Notes |
|---|---|---|
| `vendor/swiper.js` | **8,708** | Swiper **5.3.8**, vendored, not an npm dep |
| `maps.js` | 209 | separate bundle, Google Maps JS API |
| `modules/project.js` | 156 | |
| `modules/history.js` | 146 | |
| `modules/imagescroll.js` | 121 | |
| `modules/overlay.js` | 78 | |
| `modules/menu.js` | 74 | |
| `modules/filter.js` | 70 | |
| `modules/swiper.js` | 68 | wraps the vendored Swiper |
| `modules/collapsible.js` | 62 | |
| `modules/fancybox.js` | 53 | **dead** |
| `modules/dropdown.js` | 36 | |
| `modules/contact.js` | 29 | |
| `bootstrap.js` | 27 | jQuery + axios globals |
| `app.js` | 24 | require list |
| `vendor/fancybox.js` | 12 (minified) | fancyBox **3.5.7**, **dead** |
| `vendor/lazysizes.js` | 1 (minified) | lazysizes 5.1.2 |
| `vendor/scrollTo.js` | 6 (minified) | jQuery.scrollTo |

**Own code excluding vendored libraries: ~1,150 LOC.** That is the real size
of this job. The 8.7k-line Swiper copy is not code anyone maintains.

Built output today: `app.js` **296 KB**, `maps.js` 2.1 KB.

## The code is in better shape than its age suggests

Every module follows the same revealing-module IIFE:

```js
var Menu = (function() {
  var selectors = { menuBtn: '.js-menu-btn', ... };
  var classes   = { visible: 'is-visible', ... };
  var _initialize = function() { _bind(); };
  var _bind = function() { $(selectors.body).on('click', selectors.menuBtn, ...); };
  return { init: _initialize };
})();
$(function() { Menu.init(); });
```

Consistent conventions throughout: selectors and CSS class names hoisted into
named objects, `js-` prefixed hooks kept separate from styling classes, event
delegation from `body` rather than direct binding, no inline handlers.

**That matters more than the `var`s and the jQuery.** The structure is sound,
so this is a mechanical translation, not a redesign. A module converts in
20–40 minutes, and the `selectors`/`classes` objects survive unchanged.

Two further pieces of good news:

- **No inline JavaScript in any blade template.** Nothing depends on
  `window.$` or `window.axios` from outside the bundle, so the globals can go
  without hunting for external callers.
- **No build-order coupling.** `app.js` is a flat require list; modules do not
  reference each other (except `modules/swiper.js` → `vendor/swiper.js`).

## Step 1 — delete dead weight (do this first, it is free)

| Item | Evidence |
|---|---|
| `modules/fancybox.js` | not in `app.js`'s require list |
| `vendor/fancybox.js` | fancyBox 3.5.7, same |
| `@fancyapps/ui` ^5.0.14 (npm) | never imported |
| `in-view` ^0.6.1 (npm) | never imported |
| `axios` on the public site | loaded in `bootstrap.js`, **zero requests made anywhere in `resources/js/frontend/`** |

Verified: `grep -c fancybox public/assets/js/app.js` → **0**. It is not even
in the shipped bundle. No `data-fancybox` attribute in any blade, no
references in the frontend Sass.

Note while deleting: fancyBox 3 is GPLv3-or-commercial, and `@fancyapps/ui` 5
is likewise commercial-licensed for commercial use. Neither is in use, so
removing them also removes a licensing question nobody needs to answer.

Dropping axios alone is a meaningful chunk of the 296 KB bundle for a
dependency that is never called.

## Step 2 — the target style

Three changes travel together, because each one touches the same lines:
`var` → `let`/`const` + ES modules, `js-` hook classes → `data-` attributes,
and jQuery → vanilla.

### `js-*` → `data-*`, but **not** `is-*` / `has-*`

Two different kinds of class are in play and only one should move.

| Kind | Examples | Count | Styled in Sass? | Action |
|---|---|---|---|---|
| **Behaviour hooks** | `js-menu-btn`, `js-clpsbl-body`, `js-filter-item` | 29 distinct | **no — verified zero** | → `data-` attributes |
| **State classes** | `is-active`, `is-visible`, `is-open`, `is-expanded`, `has-menu` | 10 distinct | **yes** (`is-active` in 4 files, `is-visible` in 5, …) | **keep as classes** |

The state classes are how CSS reacts to behaviour. Turning those into data
attributes would mean rewriting the matching Sass selectors too, for no gain.
Only the query hooks move.

Worth recording: **no `js-` class is styled anywhere in
`resources/sass/frontend`.** The convention has been observed perfectly. That
makes the migration safe — nothing visual can break from removing them — but
it also means the current `js-` prefix is already doing its job, so this is a
structural improvement rather than a bug fix.

(One loose end: `is-parent` is toggled by `modules/menu.js` but matches
nothing in the Sass. Either dead or styled under a different selector —
check when converting that module.)

### Naming

Module name as the attribute, part as the value:

| Before | After |
|---|---|
| `.js-clpsbl` | `[data-collapsible="root"]` |
| `.js-clpsbl-body` | `[data-collapsible="body"]` |
| `.js-clpsbl-btn` | `[data-collapsible="btn"]` |
| `.js-menu-btn` | `[data-menu="btn"]` |
| `.js-menu-parent` | `[data-menu="parent"]` |

29 class names collapse to roughly 9 attribute names. Grouping by module
makes the ownership obvious in the template, which a flat `data-menu-btn`
does not.

### Worked example — `collapsible.js`, the pilot

Current (62 LOC, jQuery, IIFE):

```js
var Collapsible = (function() {
  var selectors = { body: 'body', wrapper: '.js-clpsbl', content: '.js-clpsbl-body', btn: '.js-clpsbl-btn' };
  var classes = { expanded: 'is-expanded' };
  var _toggle = function(el) {
    var wrapper = el.parents(selectors.wrapper);
    if (!wrapper.hasClass(classes.expanded)) {
      var distance = wrapper.offset().top - 20;
      $.scrollTo(distance, 400);
    }
    wrapper.toggleClass(classes.expanded);
    wrapper.find(selectors.content).toggle();
  };
  ...
})();
$(function() { Collapsible.init(); });
```

Target:

```js
const SEL = {
  root: '[data-collapsible="root"]',
  body: '[data-collapsible="body"]',
  btn:  '[data-collapsible="btn"]',
};
const EXPANDED = 'is-expanded';   // styled in Sass — stays a class

function toggle(btn) {
  const root = btn.closest(SEL.root);
  if (!root) return;

  if (!root.classList.contains(EXPANDED)) {
    const top = root.getBoundingClientRect().top + window.scrollY - 20;
    window.scrollTo({ top, behavior: 'smooth' });
  }

  const expanded = root.classList.toggle(EXPANDED);
  root.querySelectorAll(SEL.body).forEach((el) => { el.hidden = !expanded; });
  btn.setAttribute('aria-expanded', String(expanded));
}

export function init() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest(SEL.btn);
    if (btn) toggle(btn);
  });
}
```

What that one file demonstrates, and why it is the right pilot:

- `var` → `const`, IIFE → ES module exports
- `.js-clpsbl*` → `[data-collapsible="…"]`, `is-expanded` left alone
- `parents()` → `closest()`, `hasClass`/`toggleClass` → `classList`
- `offset().top` → `getBoundingClientRect().top + scrollY`
- `$.scrollTo(…, 400)` → `window.scrollTo({ behavior: 'smooth' })`, which
  retires `vendor/scrollTo.js`
- `.toggle()` → `el.hidden` — **gotcha 1 below**
- `aria-expanded`, which the jQuery version never set. The conversion is a
  natural moment to add the obvious ARIA state; do it where it is free, do
  not turn this into an accessibility project.

### `[hidden]` needs one line of Sass

`el.hidden` only works if nothing overrides it. Any rule setting
`display: block` on a collapsible body beats the browser default, so add this
once to the reset and the whole `show`/`hide`/`toggle` category is solved:

```scss
[hidden] { display: none !important; }
```

That is the entire Sass footprint of this project, provided the `[hidden]`
convention is used consistently rather than mixed with a `.is-hidden` class.

### Blade cost

29 hooks, **110 occurrences across 28 blade files**, plus 49 in the JS.

**Do this in the same pass as the jQuery conversion, not as a follow-up.**
Each module's selector object is being rewritten anyway, and the blade side
is a mechanical find/replace per hook. Done later it means touching all 28
templates and all 9 modules a second time, and running the QA twice.

Budget **+0.5 day** on top of the jQuery work when combined. Roughly double
that if split.

### Optional: `data-module` auto-init

With data attributes in place, `app.js`'s flat require list could become:

```js
const MODULES = { menu: () => import('./modules/menu.js'), ... };
document.querySelectorAll('[data-module]').forEach((el) => {
  MODULES[el.dataset.module]?.().then((m) => m.init(el));
});
```

Tidier — modules initialise only where their markup exists, instead of every
module binding a delegated listener on every page.

**But be honest about the payoff: there is not much.** The current modules
already delegate from `body`, so running them everywhere is harmless, and at
~1,150 LOC total the code-splitting this enables saves a few kilobytes.
Worth doing if it falls out naturally; not worth engineering toward.

## Step 2b — jQuery → vanilla

### The API surface, counted

| Method | Uses | Vanilla equivalent |
|---|---|---|
| `.addClass` / `.removeClass` / `.toggleClass` / `.hasClass` | 90 / 63 / 26 / 32 | `classList.*` — direct |
| `.find` | 86 | `querySelectorAll` |
| `.css` | 82 | `style.setProperty` — **see gotchas** |
| `.on` / `.off` | 78 / 36 | `addEventListener` + `closest()` for delegation |
| `.attr` | 67 | `getAttribute` / `setAttribute` |
| `.eq` / `.index` | 57 / 20 | array index on `[...els]` |
| `.trigger` | 47 | `dispatchEvent(new CustomEvent(...))` — **see gotchas** |
| `.children` / `.next` / `.prev` / `.parents` | 38 / 17 / 6 / 10 | `children` / `nextElementSibling` / `previousElementSibling` / `closest` |
| `.is` / `.filter` | 35 / 14 | `matches()` / `Array.filter` |
| `.each` | 32 | `forEach` |
| `.hide` / `.show` / `.toggle` | 22 / 11 / 10 | **see gotchas** |
| `.remove` / `.append` / `.html` | 20 / 20 / 12 | `remove()` / `append()` / `innerHTML` |
| `.animate` | 16 | **see gotchas** |
| `.offset` / `.scrollTop` / `.outerHeight` | 10 / 6 / 6 | `getBoundingClientRect()` / `scrollY` / `offsetHeight` |
| `.data` | 17 | `dataset` |

Most of that list is a one-to-one substitution. The delegation idiom
converts uniformly:

```js
// before
$(selectors.body).on('click', selectors.menuBtn, function(){ _toggle($(this)); });

// after
document.body.addEventListener('click', (e) => {
  const btn = e.target.closest(selectors.menuBtn);
  if (btn) _toggle(btn);
});
```

### The four gotchas

These are the only parts that need judgement rather than translation.

**1. `.show()` / `.hide()` / `.toggle()` (43 uses) set inline `display`.**
The vanilla replacement is a class or the `hidden` attribute — which means a
corresponding rule in the Sass. This is the one place the JS work reaches
into the 12.2k LOC of stylesheets. Decide one convention up front (a single
`.is-hidden` utility, or `[hidden]`) and apply it everywhere; do not mix.

**2. `.animate()` (16 uses).** Map to CSS transitions where the animation is
a simple A→B (most will be), and to the Web Animations API (`el.animate()`)
where it is not. Scroll animations should become
`window.scrollTo({ top, behavior: 'smooth' })`, which also retires
`vendor/scrollTo.js`.

**3. `.trigger()` (47 uses).** jQuery's custom events are not DOM events —
`$(el).trigger('foo')` is only visible to jQuery listeners. Each one has to
become a real `CustomEvent`, and both sides converted together. Because 47 is
a lot, map every trigger/listener pair **before** starting, not as you go.

**4. `.css()` (82 uses).** Mostly reads of computed values and writes of
positioning. Reads → `getComputedStyle`, writes → `style.setProperty` or,
better, a CSS custom property set once and consumed by the stylesheet.
`modules/imagescroll.js`, `history.js` and `project.js` hold most of these
and are the three files to convert last, once the pattern is settled.

### Order of conversion

Easiest to hardest, so the conventions are established before the hard files:

`contact` (29) → `dropdown` (36) → `collapsible` (62) → `menu` (74) →
`filter` (70) → `overlay` (78) → `imagescroll` (121) → `history` (146) →
`project` (156)

`collapsible.js` is the natural pilot: it exercises `toggleClass`, `parents`,
`find`, `offset`, `toggle()` and `$.scrollTo` in 62 lines — four of the five
problem categories in one small file. Convert it, review it, then the rest
follow the pattern.

## Step 3 — Swiper

`vendor/swiper.js` is **Swiper 5.3.8** (2020), vendored rather than an npm
dependency, 8,708 lines. Current is 12.x. Swiper 6+ dropped jQuery-style
internals and ships as ES modules with per-module imports, so only the
features in use get bundled — a large part of the 296 KB.

`modules/swiper.js` is only 68 lines and already does
`import Swiper from '../vendor/swiper.js'`, so the swap is contained. The
work is in the config object and the breakpoint handling
(`window.matchMedia('(min-width:960px)')` with `addListener`, itself
deprecated in favour of `addEventListener('change', …)`).

Do this **after** the jQuery removal, not before — Swiper 5 does not depend
on jQuery, so it is not blocking, and doing it second keeps the two diffs
separate.

## Step 4 — `maps.js`

209 LOC, its own bundle, Google Maps JS API with hardcoded coordinates
(47.371496, 8.544790) and a single jQuery call used only to check whether the
map container exists.

Converting the jQuery out is trivial. The real question is separate and worth
raising with the client: Google Maps JS API is billed per load and sets
third-party cookies. For a static office-location pin, a linked static image
or an embedded OpenStreetMap frame removes both the cost and a consent
obligation. **Out of scope unless asked** — noted because the file has to be
touched anyway.

## Effort

| Step | Days |
|---|---|
| 1. Delete dead code (fancybox ×2, axios, 2 npm deps) | **0.25** |
| 2. Target style: ESM + `let`/`const`, `js-` → `data-`, jQuery → vanilla; 9 modules + `bootstrap.js` + 28 blades | **2.5 – 3** |
| 3. Swiper 5 → 12 | **0.5** |
| 4. `maps.js` de-jQuery | **0.25** |
| 5. Cross-browser + device QA | **0.5** |
| **Total** | **4 – 4.5** |

The QA half-day is not compressible. There are no tests, the modules drive
visible layout behaviour (menus, scroll effects, sliders), and `imagescroll`
and `history` are scroll-position dependent — exactly the category that
breaks subtly on real devices and not in a desktop browser.

### Expected outcome

jQuery (~90 KB min), axios (~35 KB min) and Swiper 5 (8.7k lines) all leave
the bundle; Swiper 12 returns with only the modules in use. The 296 KB
`app.js` should land well under half that. The precise number depends on the
Swiper feature set, so measure rather than promise.

## Sequencing against the main rework

- **Vite migration is shared.** Do it once, in the main project
  (`03-frontend-vue3.md`), and let this work land on top.
- Step 1 can happen immediately — it is deletion, and it shrinks what the
  Vite migration has to carry.
- Steps 2–4 are best done **after** the Laravel 13 work is stable, so a
  public-site regression is unambiguously attributable to this work and not
  to the framework upgrade.
- Nothing here touches the admin SPA, the API or the image pipeline.

## Testing

No tests exist and this work does not justify building a suite. But the
public site is exactly what Playwright is good at, and luvo already proved
the setup (`07-qa-automation-prompt.md` / `08-test-plan.md` in that repo).

A thin smoke suite — each page type loads, no `console.error`, menu opens and
closes, filter narrows the list, slider advances, collapsibles expand —
would be perhaps half a day and would de-risk all of steps 2–4. Worth it if
the QA automation project happens anyway; skip it otherwise and QA by hand.

Either way: **capture before/after screenshots of every public page type at
375 and 1280 px** before starting. That is the baseline, and it costs
minutes.
