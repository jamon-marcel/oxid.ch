# Open questions — answer before starting

## 1. PHP version on production — ANSWERED 2026-10-04

**Production runs PHP 8.3, up to 8.5. Gate cleared.** Laravel 13 (`^8.3`) and
`jwt-auth` v2.9.3 (`^8.3`) are both satisfied.

Target 8.3 or 8.4 for the `composer.json` floor, not 8.5 — no dependency needs
8.5 and it narrows the hosting options for no gain.

### Still open: the image driver

Both oxid-architektur.ch and luksundvogt.ch (luvo) are believed to be on
**Hostpoint** — unconfirmed by the client. If that holds it is good news:
luvo runs Glide on Imagick with AVIF + WebP in production today, so the same
stack is proven on the same provider.

Treat it as a strong indication, not a fact. Two things make it low-risk
either way:

1. Confirm with one command on the server:
   ```
   php -r 'echo PHP_VERSION, PHP_EOL;
           echo class_exists("Imagick") ? "imagick: yes" : "imagick: NO", PHP_EOL;
           if (class_exists("Imagick")) {
             echo "webp: ", Imagick::queryFormats("WEBP") ? "yes":"no", PHP_EOL;
             echo "avif: ", Imagick::queryFormats("AVIF") ? "yes":"no", PHP_EOL;
           }
           echo extension_loaded("gd") ? "gd: yes" : "gd: NO", PHP_EOL;'
   ```
2. **Do not hardcode the answer.** Port luvo's `app/Support/ImageSupport.php`,
   which probes `Imagick::queryFormats()` at runtime and offers only the
   modern formats the server can actually write, falling back to jpg/png/gif.
   That makes the pipeline correct on Hostpoint, on GD-only hosting, and on a
   dev machine, without a config switch. 48 LOC, reusable as-is.

## 2. Image sizes — ANSWERED 2026-10-04: fix it

Serve the size the markup actually asks for, plus WebP/AVIF. Same decision
luvo made, which took it from 20 MB to 4.4–6.1 MB across the pages measured.

Scope this adds: `app/Helpers/ImageHelper.php` currently emits `<img srcset>`
strings. Modern formats need `<picture>` with `<source type="image/avif">` /
`image/webp` and a jpeg fallback. All ~10 blade call sites go through the
static helpers, so the markup change is contained to that one file.

Pair it with `ImageSupport::modernFormats()` (see #1) so the extra `<source>`
elements only appear when the server can actually write those formats.

## 3. Are `/img/project/` and `/img/tiny/` dead? — STILL OPEN

**What this is about.** `config/image-cache.php` registers twelve named image
templates. A request to `/img/<template>/<filename>` runs that template. Three
of them are oxid's own: `home`, `project`, `tiny`.

Searching `app/`, `resources/views/` and `resources/js/` for emitted URLs
finds `/img/crop/` (12 call sites) and `/img/home/` (2). **Nothing emits
`/img/project/` or `/img/tiny/`.**

**Why it is a question and not just a deletion.** The grep only proves *this
repo* does not link them. A route is reachable by anyone who knows the URL.
If a newsletter template, a PDF export, a partner site or an old cached page
still points at `/img/project/...`, deleting the template turns those into
404s — silently, because nothing here would break.

**How to answer it.** Grep the production access logs for `/img/project/` and
`/img/tiny/` over the last 6–12 months:

```
grep -c '/img/project/' access.log*
grep -c '/img/tiny/'    access.log*
```

Zero hits over a year → delete both with confidence. Any hits → keep
`Project.php` and port it to Glide alongside `Home.php` (it has the same
database-lookup shape).

**Note on `tiny` specifically:** it is registered in config but implements
Intervention **v2**'s `FilterInterface`, removed in v3. If anything had called
it, it would already be erroring. So a non-zero log count for `/img/tiny/`
means "something is requesting a URL that is already broken", not "this
works and must be preserved".

## 4. TinyMCE — ANSWERED 2026-10-04: replace with Tiptap

Not the minimum-scope answer; adds ~0.75 day over a TinyMCE 5 → 8 bump.
Buys: 7.5 MB of self-hosted assets deleted, and off the TinyMCE CVE treadmill
for good.

Port luvo's `components/ui/editor/` (Editor, Toolbar, LinkDialog, smallText)
and, importantly, its **round-trip verification**: export every stored
rich-text value, run it through Tiptap, and diff the visible text, links,
headings and lists. luvo did this across 197 values before switching. Script
is `.rewrite/tools/tiptap-roundtrip.mjs` in that repo (needs `@tiptap/html`
and `happy-dom`).

Expect the same side effect luvo saw: saving a text strips Word/Outlook paste
junk, so pasted inline fonts disappear and those texts revert to the site
font. That is an improvement, but it is a visible change — warn the editors.

## 5. `busu.css` — ANSWERED 2026-10-04: another site consumes it. Keep.

`resources/sass/frontend-busu/` (1,569 LOC) compiles to
`public/assets/css/busu.css` and is served to a different site.

**Consequence for the Vite migration:** the output path and filename must stay
exactly `public/assets/css/busu.css`. Vite's default is hashed filenames under
`public/build/`, so this bundle needs an explicit exception — a fixed
`rollupOptions.output` entry, or keep it as a separate build step. Do not let
it get swept into the manifest with the rest.

Worth confirming with the consuming site whether it also expects the `?id=`
query string that Mix's `.version()` currently produces in
`mix-manifest.json`. Today that hash is only in the manifest; whoever links
the file may or may not be reading it.

## 6. Orphan configs — PARTLY RESOLVED, one still open

**What this is about.** Four config files looked unreferenced. On a closer
pass with `config('x.')`, `Config::get('x.')` and `config()->get('x.')` across
`app/`, `resources/`, `routes/`, `database/` and `bootstrap/`, here is the
real picture:

| File | Reads in app code | Verdict |
|---|---|---|
| `config/dompdf.php` | 0 | **Delete.** dompdf is not in `composer.lock` at all. Pure leftover. |
| `config/content.php` | 0 | **Delete**, with one caveat below. |
| `config/media.php` | 0 | **Delete.** Defines `storage_paths` under `storage/app/public/media/` — a path layout the app does not use; uploads live in `storage/app/public/uploads/`. |
| `config/image.php` | 0 **in app code** | **Keep the file, but see below.** Not an orphan — it is `intervention/image-laravel`'s own config and the package's ServiceProvider reads `config('image.driver')`. |

**The caveat on `content.php`:** `CLAUDE.md` states "Custom content settings
in `config/content.php`", which suggests it mattered once. Its `content_keys`
array is referenced nowhere (`grep content_keys` → 0 hits). It looks genuinely
dead and `CLAUDE.md` looks stale — but since the documentation disagrees with
the code, confirm before deleting, and fix `CLAUDE.md` either way.

**The finding under `config/image.php`** — this one is worth knowing
regardless of the deletion question:

- It sets `'driver' => 'gd'` as a **string**. The package default is a
  class-string (`\Intervention\Image\Drivers\Gd\Driver::class`). The
  string form would not resolve.
- It never fires, because nothing resolves `ImageManager` from the container.
  Both places that use Intervention construct it directly:
  `marceli-to/image-cache` hardcodes `new ImageManager(new GdDriver())`, and
  `app/Http/Controllers/Api/MediaController.php:48` does
  `new ImageManager(new Driver())` with the GD driver imported.

So it is the same family of latent bug as `Tiny.php`: wrong, but unreachable.

**The real takeaway: the entire image pipeline runs on GD today**, hardcoded
in two places, not configured. That is useful for #1 — luvo's crop-equivalence
comparison was also run on GD, so it matches oxid's current behaviour exactly.
Moving to Imagick would be a genuine driver change, and should be a deliberate
decision rather than a side effect of adopting Glide.

## 7. Algolia index settings — STILL OPEN

**What this is about.** An Algolia index has two separate things: the
**records** (pushed from your app) and the **settings** — searchable
attributes, custom ranking, facets, synonyms, typo tolerance, stop words.

Scout pushes records. Settings are configured either in code (and applied on
deploy) or by hand in the Algolia dashboard. **If they were tuned in the
dashboard, they exist nowhere in this repository.**

`config/scout.php` is the place code-side settings would live, and `Searchable`
is on exactly two models: `app/Models/Project.php` and
`app/Models/Discourse.php`.

**Why it matters for this project.** The upgrade bumps Scout 10 → 11 *and*
rewrites against Algolia client v4. Re-indexing is a normal part of that
(`scout:flush` + `scout:import`, or a fresh index). A flush-and-reimport
preserves settings; creating a new index, or certain `setSettings` calls
during the v4 migration, does not. Hand-tuned relevance can be wiped with no
error and no obvious symptom — search just quietly gets worse.

**How to answer it.** Before touching Scout, export the live settings for
both indices from the Algolia dashboard (Index → Configuration → "Export
configuration" produces JSON), and commit them. Then after re-indexing,
diff against that export. If the settings turn out to be code-managed in
`config/scout.php`, say so and this question closes with no action.

## 8. Deployment — ANSWERED 2026-10-04: same as luvo

SSH onto the server, `git pull`, `composer install --no-dev` (PHP 8.3+ CLI),
`php artisan optimize:clear`. Built assets are committed, so **Vite output
gets committed too**, exactly as the Mix output is today.

Implications to carry into the Vite work:

- `public/build/` goes into git; remove `public/assets/backend/**` and the
  Mix-built frontend bundles once nothing references them.
- `busu.css` keeps its current path (#5), so it stays outside the manifest.
- The release that switches to Laravel 13 is the one that needs
  `composer install` with an 8.3+ CLI — confirm which PHP version the
  server's *CLI* runs, not just the web SAPI. On shared hosting these
  routinely differ.
- Rollback is `git checkout <previous sha>` + `composer install`, so the
  pre-upgrade DB snapshot (#10) is the real safety net — migrations are the
  part a git revert will not undo.

## 9. Backend and frontend together, or sequenced?

The estimate assumes **sequential** — backend first, then frontend, so each
half can be smoke-tested against a known-good other half. One branch is
slightly faster but much worse to debug given there are no tests.

The Sanctum switch is the awkward one: it spans both halves. The plan puts it
at the end of the backend phase, with the SPA's auth code adapted in the same
commit, before the Vue 3 port starts.

## 10. Snapshot first

Before anything: a snapshot of the production DB + `storage/` as the test
baseline and rollback point. The image verification in `05-image-pipeline.md`
depends on having real data locally.

---

# Answers

| # | Question | Answer |
|---|---|---|
| 1 | PHP 8.3+ on production? | **yes — 8.3 up to 8.5. Gate cleared.** |
| 1b | Imagick or GD? | likely Hostpoint + Imagick (same as luvo), unconfirmed; detect at runtime regardless |
| 2 | Fix the 2400 px issue? | **yes** |
| 3 | `/img/project/` and `/img/tiny/` dead? | _open — check access logs_ |
| 4 | TinyMCE 8 or Tiptap? | **Tiptap** |
| 5 | What consumes `busu.css`? | **another site — keep, preserve the output path** |
| 6 | Orphan configs safe to delete? | `dompdf` + `media` yes; `content` likely (confirm vs CLAUDE.md); `image.php` is package config, keep |
| 7 | Algolia index config in code or dashboard? | _open — export settings before re-indexing_ |
| 8 | Deploy / rollback? | **SSH + git pull, commit the Vite build** |
| 9 | Sequenced? | assumed yes |
| 10 | Snapshot taken? | not yet |

**Decided 2026-10-04:** JWT → Sanctum, as part of this project. See
`02-backend-laravel13.md`. Note that luvo is not precedent for the migration
itself — it has been on Sanctum since its initial commit and never used JWT.
