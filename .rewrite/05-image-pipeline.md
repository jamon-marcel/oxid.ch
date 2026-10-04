# Image pipeline: image-cache → Glide

Status: **planned, not yet verified against oxid's data.** luvo did the
equivalent evaluation and it came out clean (see below). The oxid port has one
structural difference that luvo's work does not cover, flagged below.

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
