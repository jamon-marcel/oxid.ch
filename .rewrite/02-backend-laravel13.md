# Backend: Laravel 11 → 13

## Why this is not optional

`composer update` **cannot run today**:

```
Root composer.json requires laravel/framework ^11.0, found laravel/framework
[v11.0.0, ..., v11.57.0] but these were not loaded, because they are affected
by security advisories
```

No release in the entire 11.x line is patched. Fixes land only in ≥ 12.69.0 /
≥ 13.30.0:

| Severity | Issue | Fixed in |
|---|---|---|
| high | CRLF injection in the default `email` validation rule (CVE-2026-48019) | 12.60.0 / 13.10.0 |
| medium | Temporary signed URL path confusion | 12.61.1 / 13.12.0 |
| low | XSS in debug page information (CVE-2026-102279) | 12.69.0 / 13.30.0 |

`composer audit` reports **45 advisories across 13 packages** in total. Every
one of the other 12 is transitive (`league/commonmark` 2.6.1 with 8 high,
`guzzlehttp/guzzle` 7.9.2 with 1 high, `symfony/http-foundation` and
`symfony/mime` 7.2.x with 1 high each, plus `symfony/{routing,mailer,yaml}`,
`guzzlehttp/psr7`, `league/flysystem`, `psy/psysh`, `phpunit/phpunit`) and
clears automatically with the framework bump.

## Target

`laravel/framework` **v13.34.0** was latest at survey time. Requires **PHP ^8.3**.
`composer.json` declares `^8.2` today; local CLI is 8.4.18.

Go **straight to 13, not via 12.** luvo asked this exact question
(`04-open-questions.md` #5 there), went direct, and the two-major jump in its
riskiest dependency needed zero code changes. Every oxid dependency has a 13
release — verified below.

## Dependency audit — verified against Packagist 2026-10-04

Every one of the 16 direct dependencies is out of date. None is abandoned.

| Package | Locked | Latest | L13-ready | Notes |
|---|---|---|---|---|
| `laravel/framework` | v11.44.2 | **v13.34.0** | ✅ | needs PHP ^8.3 |
| `php-open-source-saver/jwt-auth` | v2.8.2 | **v2.9.3** | ✅ `illuminate ^12\|^13` | being removed — see below |
| `laravel/scout` | v10.14.0 | **v11.8.0** | ✅ `illuminate ...^13.0` | |
| `algolia/algoliasearch-client-php` | 3.4.2 | — | n/a | **being removed** — see `07-search.md` |
| `intervention/image` | 3.11.2 | **4.3.3** | ✅ | |
| `intervention/image-laravel` | 1.5.5 | **4.1.1** | ✅ | big renumber, v3 API already in use |
| `laravel/tinker` | v2.10.1 | **v3.0.2** | ✅ | major |
| `spatie/laravel-translatable` | 6.11.4 | **6.14.1** | ✅ | minor, painless |
| `laravel/helpers` | v1.7.2 | v1.8.3 | ✅ | minor |
| `phpunit/phpunit` | 10.5.45 | **13.4.0** | ✅ | 3 majors; also has its own high advisory |
| `laravel/pint` | v1.21.2 | v1.32.1 | ✅ | dev |
| `laravel/sail` | v1.41.0 | v1.68.0 | ✅ | dev |
| `nunomaduro/collision` | v8.5.0 | v8.9.5 | ✅ | dev |
| `mockery/mockery` | 1.6.12 | 1.6.15 | ✅ | dev |
| `spatie/laravel-ignition` | 2.9.1 | 2.12.0 | ✅ | dev |
| **`marceli-to/image-cache`** | v1.4.3 | v1.4.5 | ❌ **caps at `illuminate ^10\|^11`** | **blocker — being removed** |

### The one blocker, and why we delete rather than fix it

`marceli-to/image-cache` v1.4.3 requires `illuminate/* ^10.0|^11.0`. It is your
own package, so bumping and retagging is an option — luvo faced exactly this
and chose instead to **replace it with `league/glide`**, which removes the
dependency entirely and fixes three live image bugs in the same move. That work
is done and verified over there; see `05-image-pipeline.md` for what ports
directly and the one place oxid differs.

### Algolia is being dropped

**Decided 2026-10-04: remove Algolia, implement search in-process.**
See `07-search.md` for the reasoning, the design and the staging.

Correcting an earlier draft of this file, which called the client v3 → v4
step "a full client rewrite" and budgeted 0.5 day: that was wrong. Scout
picks its engine from whichever client class is installed
(`EngineManager.php:44`), Scout 10.14 already ships `Algolia4Engine`, and no
app code touches the client directly. Staying would have been close to a
package bump. The decision rests on proportionality — 119 searchable records
behind a plain GET form — and on four concrete issues listed in `07-search.md`,
not on migration cost.

Consequences here:

- Drop `algolia/algoliasearch-client-php` from `composer.json`.
- Drop the `algolia` block from `config/scout.php`, and
  `ALGOLIA_APP_ID` / `ALGOLIA_SECRET` from `.env`.
- `laravel/scout` 10 → 11 still happens in phase 1 (the `collection` driver
  lives in Scout), then Scout itself goes in phase 2.
- The index-settings export question (`04-open-questions.md` #7) is moot.

## JWT → Sanctum

**Decided 2026-10-04.** Note for the record: luvo is *not* precedent for the
migration itself — it has used Sanctum since its initial commit and never had
JWT. What luvo does give us is the target shape (`statefulApi()`, the Sanctum 4
config, a ~30-line axios module) and one hard-won warning, below.

### Surface being removed

| File | What |
|---|---|
| `app/Http/Controllers/AuthController.php` | 82 LOC: `login`, `me`, `logout`, `refresh` |
| `app/User.php` | `implements JWTSubject`, `getJWTIdentifier`, `getJWTCustomClaims` |
| `config/jwt.php` | delete |
| `config/auth.php` | guard `api` → `'driver' => 'jwt'` |
| `app/Http/Middleware/Authenticate.php` | |
| `routes/api.php` | `auth:api` on 2 route groups; `prefix => 'auth'` group with 4 routes |
| `resources/js/backend/app.js` | **~175 of its 227 LOC** — see `03-frontend-vue3.md` |

### Target shape

1. Move `app/User.php` → `app/Models/User.php`, namespace `App\` → `App\Models\`,
   drop `JWTSubject`, add `HasApiTokens` only if token auth is actually needed
   (for a same-origin SPA it is not).
2. `config/auth.php`: the `api` guard goes away; the admin SPA uses the `web`
   session guard via Sanctum's stateful middleware.
3. `bootstrap/app.php`: `$middleware->statefulApi();`
4. `routes/api.php`: `auth:api` → `auth:sanctum`. The `auth/refresh` route
   disappears entirely — sessions do not need refreshing.
5. `AuthController`: `login` becomes a session login after a
   `/sanctum/csrf-cookie` call; `me` stays; `logout` invalidates the session;
   `refresh` is deleted.
6. Env: `SANCTUM_STATEFUL_DOMAINS`, `SESSION_DOMAIN`.

The admin is served same-origin at `/admin`, so cookie auth is the natural fit
and nothing needs CORS.

### The warning from luvo

`c4ba6a6` — *"Use Sanctum 4 config; old one pointed at the deleted
VerifyCsrfToken"*. Publishing or copying a stale Sanctum config that references
a middleware class the slim-skeleton migration has removed makes **every API
call 500**, with nothing obviously wrong in the stack trace. Generate the
Sanctum config fresh against the installed version; do not port the old one.

### Sequencing

Do the Sanctum switch **after** the framework bump and the skeleton migration,
not with them. It touches `bootstrap/app.php`, `config/auth.php` and the SPA
bootstrap simultaneously, and you want the other two already known-good.

## Structural item: slim skeleton

Migrate to `Application::configure()->withRouting()->withMiddleware()
->withExceptions()`. The middleware is thin (7 classes, 157 LOC) so this is
roughly half a day, and it de-risks future upgrades instead of betting on how
long backwards compatibility survives. luvo's `bootstrap/app.php` is a good
template; drop its `DetectRequestLocale` and multilingual bits.

## Step plan

Revised 2026-10-04 after a consistency review. Two things changed:
the order (see "Why step 4 must be one commit"), and stale entries left over
from before questions 3, 6 and 7 were answered.

1. ~~Verify PHP 8.3+ on production.~~ **Done** — 8.3 up to 8.5.
2. Snapshot the production DB + `storage/` as baseline and rollback point.
3. **Delete dead code.** No Composer involved, so this works on Laravel 11:
   - `app/Filters/Image/Template/{Cache,Large,Small,Thumbnail,Tiny,Project}.php`
     and the `tiny` / `project` entries in `config/image-cache.php`
   - `config/dompdf.php`, `config/media.php`, `config/content.php`
   - the stale `config/content.php` line in `CLAUDE.md`
   - **keep** `config/image.php` — it is `intervention/image-laravel`'s config
4. **One Composer change, in one commit:** `php: ^8.3`,
   `laravel/framework: ^13.0`, drop `marceli-to/image-cache`, add
   `league/glide: ^4.1`, `intervention/image: ^4.0`,
   `intervention/image-laravel: ^4.1`, bump the rest per the table. Resolve
   the fallout. Image routes are down between this commit and step 5;
   acceptable on a branch.
5. **Glide routes:** `ImageController` + `app/Support/{Glide,ImageSupport}.php`
   from luvo, controller-side coord lookup for `/img/home/`, `ImageHelper` →
   `<picture>` with requested sizes + WebP/AVIF. Verify against production
   per `05-image-pipeline.md`. Then delete the remaining `app/Filters/` tree.
6. Migrate the skeleton to `bootstrap/app.php` + `bootstrap/providers.php`,
   lean `config/app.php`. Move `app/User.php` → `app/Models/User.php` and
   update the provider model in `config/auth.php`.
7. Diff `config/*` against a fresh L13 skeleton. Custom configs to preserve:
   `seo.php`, `settings.php`. Package configs to keep: `image.php`,
   `scout.php` (until search phase 2). `image-cache.php` went in step 4.
8. **Search phase 1** (`07-search.md`): `SCOUT_DRIVER=collection` — change the
   default in `config/scout.php`, not just `.env` — drop the Algolia client,
   add explicit `toSearchableArray()`. On Laravel 13 Composer works normally,
   so this stays a separate, revertable commit.
9. **JWT → Sanctum** per above. Fresh Sanctum config, not a ported one.
10. Carbon 2 → 3: one call site in `app/` (`Models/News.php`), but check
    vendor fallout.
11. Validation messages: L12+ requires strings, not arrays. luvo hit 500s here
    (`30baa00`) and fixed it with a `BaseFormRequest` preserving the same 422
    shape. You have **10 form requests, 414 LOC** — check all of them, and keep
    the response shape identical or the SPA's error handling breaks.
12. Smoke test: all 144 routes, login, every admin CRUD path.
13. **Search phase 2**: own scoring search, drop Scout. Last, because it is the
    one backend item that is a new feature rather than an upgrade.

### Why step 4 must be one commit

Composer currently refuses to resolve **anything** while `composer.json`
requires `laravel/framework ^11` — every 11.x release is blocked by security
advisories. Verified with a dry run on 2026-10-04: even a partial
`composer require league/glide intervention/image ...` that leaves Laravel
locked fails with the same advisory error.

So the image-cache → Glide dependency swap cannot precede the framework bump.
luvo did both together for the same reason (`c6bca43`, "Upgrade to Laravel
13, replace image-cache with Glide dependency"), then added the Glide routes
in a follow-up (`c100fe0`).

The alternatives — `policy.advisories.ignore-id` to unblock Laravel 11
temporarily, or retagging `marceli-to/image-cache` — both exist only to
manufacture an intermediate state nobody will deploy. Not worth it.

Note that the Glide packages themselves would be fine on 11
(`intervention/image-laravel` 4.1.1 allows `illuminate ^8…^13`); the block is
purely Composer's advisory policy.
