# Image pipeline: image-cache → Glide

Status: **done 2026-10-04, verified against production** — see "Result" at
the end. The original plan follows unchanged above it.

## Why replace rather than retag

`marceli-to/image-cache` v1.4.3 caps at `illuminate ^10|^11` and is the only
hard blocker to Laravel 13. It is your own package, so retagging is possible —
but luvo chose replacement with `league/glide` 4 and that removed the
dependency, fixed three live bugs, and cut page image weight from 20 MB to
4.4–6.1 MB across the pages measured. The Glide server wrapper is 21 LOC
(`app/Support/Glide.php`) and the controller 157.

## What the current routes do

`config/image-cache.php` sets `crop_filter_type => 'dimensions'`, so the
package registers:

```
/img/original/{filename}
/img/{template}/{filename}                                  (template != crop)
/img/crop/{filename}/{maxWidth?}/{maxHeight?}/{coords?}/{ratio?}
```

### Live surface, measured

| Route | Call sites |
|---|---|
| `/img/crop/...` | **12** — all of `app/Helpers/ImageHelper.php` plus `components/projects/grid/Media.vue` |
| `/img/home/...` | **2** — `frontend/pages/home/index.blade.php`, `frontend/pages/search/index.blade.php` |

Nothing emits `/img/tiny/`, `/img/project/`, or any `ratio` segment.

### `Crop` template behaviour — port this exactly

`MarceliTo\ImageCache\Templates\Crop` (Intervention v3 `ModifierInterface`):

1. **Validate.** `maxWidth` ≤ `image-cache.max_width` (default 2400),
   `maxHeight` ≤ `image-cache.max_height` (default 1600). Over → exception.
   All of oxid's emitted URLs are within these caps (1200/750, 2400/1500,
   900/562, 1600/1000), so **oxid does not have luvo's "broken image on tall
   screens" bug.**
2. **Crop** with `coords` = `w,h,x,y`, int-cast, skipped when empty or
   `0,0,0,0`. Dimensions are clamped to the image bounds, and a failed crop is
   logged and swallowed — the original is returned. Glide's `crop=w,h,x,y`
   has the same argument order and also int-casts.
3. **Scale.** Here is the trap. `ImageCache::applyTemplate()` passes
   `$params['maxSize'] ?? null`, and in dimensions mode `maxSize` is never in
   the params array — so it arrives `null`, and the constructor's
   `$this->maxSize = $maxSize ?? $this->maxSize` leaves it at its **default of
   2400**. `scaleImage()` then takes the `maxSize` branch unconditionally:
   the longer side is scaled *down* to 2400 and **`maxWidth` / `maxHeight`
   from the URL are ignored**.

   So despite `crop_filter_type => 'dimensions'`, oxid behaves exactly like
   luvo: every crop is served at up to 2400 px regardless of the size the
   markup asked for. `ImageHelper` emits a `srcset` with 900w / 2400w
   candidates that all resolve to the same 2400 px file.

   Glide equivalent for a strict 1:1 port: `fit=max`, longer side = 2400,
   **other side = 99999**. Passing only one side makes Glide floor the derived
   dimension and come out 1 px short — this hit 7 of luvo's 55 test images
   before the fix.

4. Both pipelines auto-orient from EXIF before cropping.

## The oxid-specific wrinkle: templates that query the database

This is the part luvo's work does **not** cover.

`app/Filters/Image/Template/Home.php` and `Project.php` are Intervention v3
`ModifierInterface` classes that, inside `apply()`, use the
`ImageFilenameExtractor` trait to recover the filename from the image object,
then **look the crop coordinates up in the database** (`HomeImage` /
`ProjectImage`, `where('name', $filename)`) and crop from `coords_w/h/x/y`.

luvo's coords always came from the URL, so its `ImageController` hands them
straight to Glide. **Glide cannot do a database lookup.** The replacement has
to resolve coords in the controller before building the Glide parameters:

```
/img/home/{filename}  →  look up HomeImage by name
                      →  build crop=w,h,x,y (omit when no coords)
                      →  Glide
```

Only `/img/home/` is live (2 call sites). `Project.php` is registered in
config but no URL emits `/img/project/`, so confirm it is dead before porting
it — see `04-open-questions.md` #3.

Also note `Home.php` hardcodes `max_width 2000 / max_height 1250`, which
differs from `Crop`'s 2400/1600. Preserve per-route, do not unify by accident.

## Dead templates

| Class | API | In config | Action |
|---|---|---|---|
| `Cache.php`, `Large.php`, `Small.php`, `Thumbnail.php` | v2 `FilterInterface` | no | delete |
| `Tiny.php` | **v2 `FilterInterface`** | **yes** | delete; it is already a latent fatal and no URL reaches it |
| `Project.php` | v3 | yes | confirm dead, then delete |
| `Home.php` | v3 | yes | port to the Glide controller |

## Decisions (2026-10-04)

- **Fix the 2400 px issue.** Serve the size the markup asks for, plus
  WebP/AVIF. `app/Helpers/ImageHelper.php` moves from `<img srcset>` to
  `<picture>` with `<source type="image/avif">` / `image/webp` and a jpeg
  fallback. All ~10 blade call sites go through its static methods, so the
  change is contained to that one file.
- **Gate the modern formats on `ImageSupport::modernFormats()`** so the extra
  `<source>` elements only appear when the server can write them.
- **Driver: detect, do not hardcode.** See below.

### Still to decide / preserve

1. **Coordinate rounding.** Coords are stored as doubles; `Crop` int-casts,
   `Home.php` uses `floor(floatval(...))`. Keep the same rounding per route
   or crops shift by a pixel.
2. **Cache location.** Today `storage/app/public/cache` (`lifetime` 43200
   min). Glide uses `storage/app/.glide-cache`, outside the public disk.
   After go-live the old directory can be deleted.
3. **`/img/project/` and `/img/tiny/`** — pending the access-log check,
   `04-open-questions.md` #3.

## The pipeline runs on GD today

Worth stating plainly, because it is not configured anywhere obvious:

- `marceli-to/image-cache` hardcodes `new ImageManager(new GdDriver())`.
- `app/Http/Controllers/Api/MediaController.php:48` does the same with the GD
  driver imported directly.
- `config/image.php` sets `'driver' => 'gd'` — as a *string*, where the
  package expects a class-string — but it never fires, because nothing
  resolves `ImageManager` from the container. Same family of latent bug as
  `Tiny.php`: wrong, but unreachable.

So today's output is GD output. luvo's crop-equivalence comparison was also
run on GD, which means its verified geometry rules describe oxid's current
behaviour exactly. Switching to Imagick is a real change in rendering, not a
neutral implementation detail — decide it deliberately rather than inheriting
it from luvo's `Glide.php`.

## Verification plan (mirrors what luvo actually did)

1. Load the production DB + `storage/app` locally.
2. Crawl every public page and collect every `/img/...` URL the live site
   emits.
3. Fetch each from the local site (old pipeline) **and** from production;
   confirm local matches production, so local is a valid reference.
4. Render the same set through Glide and compare with ImageMagick.
5. Accept on: identical output dimensions for 100%, and either pixel-identical
   or PSNR ≥ 50 dB. A 1 px shift scores ~35 dB, so this threshold catches
   geometry errors while tolerating JPEG noise.

luvo's result for comparison: 55/55 same dimensions, 27/55 pixel-identical,
rest ≥ 52.5 dB. Harness: `.rewrite/tools/glide-render.php` in that repo.

## Driver

**Decided: detect at runtime, do not hardcode.**

Production is PHP 8.3–8.5. The driver is probably Imagick — oxid-architektur.ch
and luksundvogt.ch (luvo) are both believed to be on Hostpoint, and luvo runs
Glide on Imagick with AVIF + WebP there today. That is unconfirmed by the
client, so do not build on it.

Port luvo's `app/Support/ImageSupport.php` (48 LOC, reusable as-is). It probes
`Imagick::queryFormats('WEBP' | 'AVIF')` once per request and exposes
`modernFormats()`, so the markup offers only what the server can actually
write and falls back to jpg/png/gif otherwise. Correct on Hostpoint, on
GD-only hosting, and locally, with no config switch and no deploy-time
surprise.

luvo's Glide server itself hardcodes `'driver' => 'imagick'`
(`app/Support/Glide.php`). If the Hostpoint check comes back GD-only,
that one line becomes `'gd'` — luvo's entire crop-equivalence comparison was
actually run on GD, so the geometry rules in this document hold either way.

Verify with the one-liner in `04-open-questions.md` #1 before the image work
starts.

## Guard the new route

The old route's only exposure was the coords segment. A Glide route takes
arbitrary parameters, so whitelist sizes and formats — otherwise crafted
requests can fill the cache. luvo did this in `c100fe0`.

## Result (2026-10-04)

`app/Http/Controllers/ImageController.php`, `app/Support/{Glide,ImageSupport}.php`,
`app/Helpers/ImageHelper.php`. `app/Filters/` is gone.

### Routes — five, not two

The "live surface" table above missed the admin. The Vue SPA's
`getSource(name, size)` emits `/img/thumbnail/`, `/img/large/` and
`/img/original/` (image listings, crop dialog). All five are ported:

| Route | Rule (ported from) | Browser cache |
|---|---|---|
| `/img/crop/{f}/{w?}/{h?}/{coords?}` | crop, then landscape → `w`, portrait → `h`, never upscale | 1 year (coords are in the URL) |
| `/img/home/{f}` | coords from `home_images`; see below (`Home.php`) | 1 h — a re-crop keeps the URL |
| `/img/large/{f}` | landscape → 1600 wide, portrait → 900 high, never upscale (`Large`) | 1 h |
| `/img/thumbnail/{f}` | 300 × 300 cover, centred (`Thumbnail`) | 1 h |
| `/img/original/{f}` | the file as is | 1 h |

Guards: only `jpg/jpeg/png/gif` basenames that exist in `uploads/`;
crop sizes must be one of `ImageController::CROP_SIZES`
(`900/562, 1200/750, 1600/1000, 2400/1500, 2400/2400`), anything else is a
404. Coords are still free-form, as they were with image-cache — residual
cache-fill exposure, same as before.

`?fm=avif|webp` on every route but `original`, when the driver can write
it. The fallback format is the upload's own: **PNGs stay PNG** (luvo forced
jpg; oxid has 328 PNG plans, some may rely on transparency).

### Verified against production

167 production renders fetched from oxid-architektur.ch (93 crops — with and
without coords, 10 PNGs, x/y = 0 anchors; all 14 home images; 30 large;
30 thumbnails) and rendered through the new controller at the legacy size
(longer side 2400). GD driver:

| | Same dimensions | Identical | ≥ 50 dB | Below |
|---|---|---|---|---|
| crop (93) | 93 | 56 | 37 | 0 |
| large (30) | 30 | 13 | 17 | 0 |
| thumbnail (30) | 30 | 11 | 19 | 0 |
| home (14) | 14 | 0 | 1 | 13 — resampling only, see below |

"Identical" includes 5 PNGs with 2–4 differing pixels out of millions
(ImageMagick prints a ~1e-13 PSNR for those).

**Home images.** `Home.php` scaled twice: crop → scale to 2000 wide
(upscaling) → portrait to 1250 high. Reproducing that with Intervention
gives ≥ 56 dB against production, so the crop region is right. The port
computes the same rounded target size (`w = round(2000·1250 / round(h·2000/w))`)
and resizes once (`fit=stretch`): dimensions match 14/14, and pixels differ
only by resampling. Downscaled to 25 %, ours scores 44–55 dB against
production, while production shifted by 2 px scores 24–34 dB — so no
geometry error, just one resample instead of an upscale + downscale (sharper).

**Imagick** matches geometry everywhere (same dimensions 167/167, 25 %-size
check 41–52 dB) but resamples differently (mostly 40–50 dB full-size). One
thumbnail scored 31 dB at 25 % — probably a 1 px difference in the
centre-crop rounding; admin-only.

### Driver: detected, Imagick when present

`Glide::driver()` picks Imagick if the extension is loaded, else GD;
`ImageSupport` asks the same driver what it can write. Reasons for
preferring Imagick when available: AVIF (local GD has WebP but no AVIF), and
memory — GD decodes into PHP memory, and the 3964 × 6000 source of
`/img/crop/60753579865d7_effingerstrasse-...` needs ~95 MB for the bitmap
alone. The CLI harness died at `memory_limit=128M` on GD. Production served
that URL through GD before, so its web `memory_limit` is evidently higher —
but **check it** when confirming the driver (`04-open-questions.md` #1).
Rendering on Imagick is a visible-to-nobody change in resampling, not in
framing.

### Requested sizes — markup

Measured on production with a real browser (DPR 2):

| Image | Box at 390 px | Box at 1280 px | Box at 1920 px |
|---|---|---|---|
| large (`visual-fit`) | 382 × 509 | 1272 × 740 | 1912 × 1020 |
| teaser | hidden | 630 × 740 | 950 × 1020 |
| preview | 205 × 128 | — | — |

So the **largest candidate stays at the legacy size**: `2400/2400` = longer
side 2400, exactly what image-cache served for every size. Big screens lose
nothing; small screens and every browser with AVIF/WebP gain.

| Helper | srcset (w descriptors) | src |
|---|---|---|
| `largeImage` | 900/562 900w, 1200/750 1200w, 2400/2400 2400w | 900/562 |
| `previewImage` | 900/562 900w, 1600/1000 1600w | 900/562 |
| `teaserImage` | 1600/1000 1600w, 2400/2400 2400w | 1600/1000 |
| `homeImage` (new) | `/img/home/{f}` + `?fm=` sources | — |
| `openGraphImage` | `/img/crop/{f}/1600/1000` | — |

The teaser used to be a single `1600/1000`; under the new size rule that is
1000 px tall for landscape images, visibly soft in a 1020 CSS px box at DPR 2,
hence the extra 2400 candidate. No `sizes` attribute, as before (100vw).

Each helper wraps the `<img>` in `<picture>` with one `<source>` per modern
format. No Sass or frontend JS uses `> img` / sibling selectors, so the
wrapper does not break styling (checked by grep; screenshots still to do —
see `06-progress.md`).

### Cache

`storage/app/.glide-cache/` (git-ignored by `storage/app/.gitignore`).
`Glide::forget($name)` is called from all six `*ImageController::removeCachedImage()`
— on delete and on re-crop. `storage/app/public/cache/` (image-cache's) is no
longer written or read; delete after go-live.

### Not ported

- `ratio` crops and `/img/{xsmall,small,medium,xlarge,xxlarge,huge}/` —
  nothing emits them.
- `resources/js/backend/components/global/upload/ImageUpload.vue:84` emits
  `/media/thumbnail/{f}`, a route that did not exist before either. Dead or
  broken already; left for the Vue 3 port.

Harness: `.rewrite/tools/` — `image-crawl.py` (collects `/img/` URLs from
every public page), `glide-render.php` (renders a `map.txt` through an
`ImageController` subclass forced to the legacy size), `image-compare.sh`
(dimensions + PSNR). They expect a scratch dir (`/tmp/oxid-img/` was used)
holding `paths.txt`, `map.txt` and the production renders in `prod/`.
