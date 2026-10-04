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

## 2. Image sizes: strict port, or fix the 2400 px issue?

Today every crop is served at up to 2400 px regardless of what the markup
requests — `ImageHelper` emits a `srcset` whose 900w and 2400w candidates
resolve to the same file (`05-image-pipeline.md`, rule 3).

Fixing it means serving the requested size plus WebP/AVIF. luvo did exactly
this and went from 20 MB to 4.4–6.1 MB across the pages measured. It changes
what visitors download and what the pages look like at the margins, so it is
a client decision, not a silent port.

**Recommendation: fix it.** It is the single largest user-visible win in this
whole project and the marginal cost is low once Glide is in.

## 3. Is `/img/project/` really dead?

`Project.php` is registered in `config/image-cache.php` but no URL in
`app/`, `resources/views/` or `resources/js/` emits `/img/project/`. Before
deleting it: is anything outside this repo — a newsletter template, an export,
a third-party integration — hitting that route? Check the access logs.

Same question for `/img/tiny/`, though that one is a v2-API class that would
already be failing if anything called it.

## 4. TinyMCE: upgrade to 8, or replace with Tiptap?

TinyMCE 5 is EOL since April 2023 with unpatched XSS. It has to move either
way. Upgrading to 8 is the smaller diff; replacing with Tiptap is what luvo
did, deletes 7.5 MB of assets, and has a verified round-trip script to reuse
— but the toolbar gets rebuilt and editors notice.

Minimum scope says TinyMCE 8. Worth an explicit decision because it is the
difference between ~0.25 and ~1 day, and because it affects the people who
use the admin daily.

## 5. What consumes `busu.css`?

`resources/sass/frontend-busu/` is a 1,569-line Sass tree compiled to
`public/assets/css/busu.css`, and **nothing in this repo references it**.
Either something outside the repo serves that built file, or it is dead.

Do not drop it from the Vite config on assumption — if an external consumer
exists, the output path and filename have to be preserved exactly.

## 6. Orphan configs

`config/dompdf.php` is safe to delete — dompdf is not in `composer.lock` at
all. But `config/media.php`, `config/content.php` and `config/image.php` have
zero `config()` reads in the codebase. Confirm nothing reads them dynamically
before removing.

## 7. Algolia: re-index, or preserve the live indices?

Scout 10 → 11 plus the Algolia client v3 → v4 rewrite touches `Project` and
`Discourse`. Does the live index configuration (ranking, facets, synonyms)
live in the Algolia dashboard or in code? If the dashboard, a careless
re-index can wipe tuning that is not in version control. Export it first.

## 8. Deployment and rollback

What does deploy look like today, and what is the rollback if the upgraded
build misbehaves?

Specifically: built assets are currently committed (`public/assets/**`), so
Vite output has to be committed too, or the deploy has to grow a build step.
`vendor/` is gitignored, so the server needs `composer install` on the release
that switches to Laravel 13 — with a PHP 8.3+ CLI.

luvo's answer: SSH + `git pull`, commit the build output, `composer install
--no-dev`, `php artisan optimize:clear`. If oxid deploys the same way, say so
and the plan carries over.

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
| 2 | Fix the 2400 px issue? | _unanswered_ |
| 3 | `/img/project/` dead? | _unanswered_ |
| 4 | TinyMCE 8 or Tiptap? | _unanswered_ |
| 5 | What consumes `busu.css`? | _unanswered_ |
| 6 | Orphan configs safe to delete? | _unanswered_ |
| 7 | Algolia index config in code or dashboard? | _unanswered_ |
| 8 | Deploy / rollback? | _unanswered_ |
| 9 | Sequenced? | assumed yes |
| 10 | Snapshot taken? | not yet |

**Decided 2026-10-04:** JWT → Sanctum, as part of this project. See
`02-backend-laravel13.md`. Note that luvo is not precedent for the migration
itself — it has been on Sanctum since its initial commit and never used JWT.
