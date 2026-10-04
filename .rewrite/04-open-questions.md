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

## 3. `/img/project/` and `/img/tiny/` — ANSWERED 2026-10-04: not in use. Delete.

Delete `app/Filters/Image/Template/Project.php` and `Tiny.php`, and their
entries in `config/image-cache.php`. Only `home` survives of the three app
templates, and it moves into the Glide controller
(`05-image-pipeline.md`).

That takes `app/Filters/` down to `Home.php` plus the
`ImageFilenameExtractor` trait it uses — and once `Home.php` becomes
controller-side coord resolution, the whole `app/Filters/` tree goes.

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

## 6. Orphan configs — ANSWERED 2026-10-04: dead code. Delete.

| File | Action |
|---|---|
| `config/dompdf.php` | delete — dompdf is not in `composer.lock` at all |
| `config/media.php` | delete — describes a `storage/app/public/media/` layout the app does not use |
| `config/content.php` | delete — `content_keys` referenced nowhere |
| `config/image.php` | **keep** — it is `intervention/image-laravel`'s own config, not an orphan |

`CLAUDE.md` claims "Custom content settings in `config/content.php`". That
line is stale; remove it in the same commit so the docs and the tree agree.

Finding recorded under `config/image.php`, kept here because it explains a
latent bug: it sets `'driver' => 'gd'` as a *string* where the package
expects a class-string. It never fires, because nothing resolves
`ImageManager` from the container — see `05-image-pipeline.md`.

## 7. Algolia — ANSWERED 2026-10-04: drop it, build our own

Superseded by `07-search.md`. The index-settings export question is moot:
Algolia goes away entirely.

Short version — 119 searchable records behind a plain GET form, no
instant-search, no facets, no client-side Algolia. Phase 1 switches Scout to
its `collection` driver (one line, reversible). Phase 2 is an own scoring
search bringing back typo tolerance, relevance ranking and prefix matching,
plus compound-word handling that suits German better than the stock Algolia
config. ~+1 day over keeping Algolia.

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
| 3 | `/img/project/` and `/img/tiny/` dead? | **yes — delete both** |
| 4 | TinyMCE 8 or Tiptap? | **Tiptap** |
| 5 | What consumes `busu.css`? | **another site — keep, preserve the output path** |
| 6 | Orphan configs safe to delete? | **yes — `dompdf`, `media`, `content`. Keep `image.php` (package config).** |
| 7 | Algolia index config in code or dashboard? | **moot — Algolia dropped, see `07-search.md`** |
| 8 | Deploy / rollback? | **SSH + git pull, commit the Vite build** |
| 9 | Sequenced? | assumed yes |
| 10 | Snapshot taken? | **yes** — DB and `storage/`, 2026-10-04 |

**Decided 2026-10-04:** JWT → Sanctum, as part of this project. See
`02-backend-laravel13.md`. Note that luvo is not precedent for the migration
itself — it has been on Sanctum since its initial commit and never used JWT.
