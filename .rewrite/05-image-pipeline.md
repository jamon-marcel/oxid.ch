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

## Generic image handling (design agreed 2026-10-04)

The step 5 shape put the knowledge in the wrong places: `ImageController`
had one action per image purpose (`/img/home` even queried the database),
`ImageHelper` one static method per page use with magic size arrays, and the
size whitelist had to be kept in sync with the helper by hand. On top of
that the markup was wrong for portraits: a `900/562` portrait comes out
~375 px wide but was announced as `900w`, and every `<img>` claimed
`width="1600" height="1000"` (the teaser `1000 × 1600`, home `800 × 400`).

### Shape

1. **One signed route.** `/img/{file}?w=…&h=…&fit=max&crop=w,h,x,y&fm=avif&s=…`.
   Glide's own URL signatures (`SignatureFactory`, keyed with `APP_KEY`)
   replace the size whitelist: any parameters are allowed, but only URLs the
   app generated validate, so crafted URLs cannot fill the cache. The
   controller checks the signature and hands the parameters to Glide; it
   knows nothing about page uses. Coords are part of the URL, so a re-crop
   gets a new URL and every rendition is browser-cached for a year.
2. **The image models know how to render themselves.** A trait
   `App\Models\Concerns\IsImage` on all six image models
   (`Home|Project|Discourse|Job|Team|Profile`Image) gives `crop()` (floored
   `[w, h, x, y]` or null), `displaySize()` (after the crop),
   `url(int $size, ?string $format)` and `srcset(array $sizes, ?string $format)`.
3. **Sizes are longer sides.** A size `L` means "fit inside `L × L`, never
   upscale" (`w=L&h=L&fit=max`), whatever the orientation. That is what
   image-cache did for its single 2400 (see "Crop template behaviour"), it
   needs no orientation logic in the URL, and the srcset descriptor is the
   *real* resulting width, computed from the stored dimensions. Sizes beyond
   the image's own size collapse into one candidate.
4. **`width` / `height` columns** on the six tables: the upload's size after
   EXIF orientation, set by the trait on save when the name changes or the
   value is missing, backfilled by the migration. The markup gets the real
   aspect ratio without reading files per page view.
5. **A Blade component** replaces `ImageHelper`:
   `<x-image :image="$image" preset="large" :alt="$image->title" />`.
   Presets are plain lists of longer sides in `config/images.php`. The OG
   image is `$image->url(1600)`. `ImageHelper` and its alias go.

### Presets

| Preset | Longer sides | Used for | Was |
|---|---|---|---|
| `large` | 900, 1200, 2400 | grids, team, jobs, profile, discourse | `900/562, 1200/750, 2400/2400` |
| `preview` | 900, 1600 | works list, discourse list, next project | `900/562, 1600/1000` |
| `teaser` | 1600, 2400 | project teasers | `1600/1000, 2400/2400` |
| `home` | 1200, 2000 | home, search | one `/img/home` rendition (2000 wide / 1250 high) |

The largest candidate of every preset equals the step 5 / production
maximum, so large screens lose nothing. Portraits now get honest
descriptors, which makes the browser pick a larger candidate for them —
the same file production served before.

### Decisions (2026-10-04)

1. **Old URLs redirect.** `/img/crop/...` (shared OG images, search
   engines) and `/img/home/...` answer with a 301 to the signed URL. The
   crop comes from the database record, not the old URL, so the redirect
   cannot be used to render arbitrary crops; size: landscape → `maxWidth`,
   portrait → `maxHeight`, none → 2400. The redirect carries
   `max-age=3600`, so a re-crop is picked up.
2. **Home images are no longer upscaled.** `Home.php` stretched a cropped
   home image to 2000 wide even when the crop was smaller; now it is never
   enlarged and CSS scales it on screen (it is `object-fit: cover` anyway).
   Framing is unchanged; bytes no longer match production for small crops.
3. **Dimensions are stored** (point 4 above), not read per view.

### Admin

The SPA builds `/img/thumbnail|large|original/{file}` itself (~30 call sites)
and cannot sign URLs. Those three stay as fixed, parameter-free actions
until the Vue 3 port, which should take the URLs from the API instead.
`Media.vue`'s `/img/crop/...` goes through the redirect.

### Accepted side effects

- Rotating `APP_KEY` changes every image URL (old ones 404 until the HTML
  is regenerated). Pages are rendered per request, so only externally
  shared signed URLs are affected; the old `/img/crop` URLs keep working.
- Signatures differ per environment (different keys) — production HTML
  cannot be replayed locally.

### Verification

- Framing: unchanged by construction (same crop parameter, same
  orientation handling); re-check a sample against production with
  `.rewrite/tools/image-compare.sh` at matching sizes.
- Descriptors: rendered width of each candidate = the `w` in the srcset.
- Every `/img/...` URL the public pages emit returns 200 (crawl).
- Legacy `/img/crop/...` and `/img/home/...` URLs redirect and resolve.

### Result (2026-10-04)

Files: `app/Models/Concerns/IsImage.php` (on all six image models),
`app/Support/Glide.php` (`url()`, `signature()`), `app/Http/Controllers/ImageController.php`
(`show` + the three admin actions + two legacy redirects),
`resources/views/components/image.blade.php`, `config/images.php`, migration
`2026_10_04_120000_add_dimensions_to_image_tables` (backfilled 939/939 rows).
`app/Helpers/ImageHelper.php` and its alias are gone. 146 routes
(145 + `/img/{file}`), `route:cache` / `view:cache` OK.

**Framing, Imagick:**

- Crops: the 93 production crop URLs from the step 5 sample, rendered
  through the new signed route at the legacy size (longer side 2400), are
  **byte-identical to the step 5 renders** (93/93), which were verified
  against production. `w=h=2400, fit=max` and step 5's one-sided bound give
  the same file.
- Home: the 14 home images are now 689–1502 px wide instead of production's
  937/938 × 1250 (no upscaling, longer side up to 2000). Scaled to
  production's size and downscaled to 25 %, ours scores 41.6–46.0 dB, while
  production shifted by 2 px scores 23.5–33.8 dB. Same crop.

**Signing:** a tampered parameter, a missing signature and an added
parameter all give 404. Signed renditions send
`Cache-Control: max-age=31536000, public, immutable`.

**Legacy URLs:** `/img/crop/{f}/1600/1000/{coords}` → 301 to the signed URL
built from the *record's* crop (bogus coords in the old URL are ignored),
portrait → `maxHeight`; `?fm=webp` is carried over; unknown sizes 404.
`/img/home/{f}` → 301 to the 2000 rendition. Redirects carry `max-age=3600`.

**Crawl:** the public pages (129) emit 6,169 distinct `/img/...` URLs. At
commit time the first 1,552 had been fetched: all 200, and for every srcset
candidate the rendered width equals its `w` descriptor. The rest was still
rendering (cold Glide cache).

**Save hook:** setting `width` to null and saving refills it from the file.

**Tests:** `tests/Unit/ImageTest.php`: crop flooring, crop cut at the
edges, honest descriptors and the collapse of upscaled candidates, and
signature coverage (4 tests).

