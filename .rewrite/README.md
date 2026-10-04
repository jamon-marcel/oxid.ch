# Rewrite notes — Laravel 11→13, Vue 2→3, JWT→Sanctum

Survey done **2026-10-04** against commit `f140dca` (branch `master`, clean tree).
Written so the next session can skip re-deriving all of this.

Modelled on the same survey done for **luvo.ch** (`.rewrite/` in
`github.com/marceli-to/luvo`), which went 11→13 + Vue 2→3 in Sept/Oct 2026.
Where a decision was already made and verified there, this set says so and
reuses it rather than re-litigating.

## Files here

| File | Contents |
|---|---|
| `00-estimate.md` | The headline numbers and what "minimum scope" means |
| `01-inventory.md` | What's actually in the codebase, measured |
| `02-backend-laravel13.md` | Dependency audit, blockers, JWT→Sanctum, step plan |
| `03-frontend-vue3.md` | Package-by-package migration table, step plan |
| `04-open-questions.md` | What must be answered before starting |
| `05-image-pipeline.md` | image-cache → Glide, the oxid-specific wrinkle |
| `06-progress.md` | What is done, verified, and left to do |

QA automation (luvo's `07-` / `08-`) is deliberately not here yet. luvo wrote
those *after* the upgrade landed. Same order applies.

## The 30-second version

- **10.5–13 working days** of focused work. Backend 3.5–4.5, frontend 7–8.5.
  luvo's comparable number was 7–9; the delta is JWT→Sanctum, Algolia v4,
  the project grid builder, Tiptap, and a frontend that is ~15% bigger.
- **Laravel 11 is EOL with unpatched CVEs.** `composer update` refuses to run
  at all today. This is not a nice-to-have.
- Backend is in decent shape: every third-party dep already has a Laravel 13
  release, including `jwt-auth` and `scout`. No external blocker.
- Vue 2 code is clean in the ways that matter (no `$listeners`, no event bus,
  no `slot-scope`, no `.sync`). The cost is replacing dead Vue 2 packages,
  not fixing your components.
- Biggest single risk: **`vue2-dropzone` has no Vue 3 port** — 6 files, full
  rewrite. luvo hit the same wall with only 2 files.
- ~~Hard gate: PHP 8.3+.~~ **Cleared** — production runs 8.3 up to 8.5.
  The image driver (Imagick vs GD) is still unconfirmed, but the pipeline
  detects it at runtime, so it does not gate anything.

## Ground rule for scope

Stay on Options API. Keep the mixins. No test suite. No design changes.
Do **not** rewrite into `<script setup>` / composables while in there.

Two deliberate exceptions, decided 2026-10-04 (`04-open-questions.md`):
TinyMCE is **replaced with Tiptap** rather than upgraded, and the image
pipeline **serves requested sizes + WebP/AVIF** rather than porting today's
behaviour 1:1. Together +1.5 days, both chosen as the better outcome over
the smaller diff.

Worth knowing: luvo set this same rule, then broke it afterwards
(`fc4583f`: 6.9k → 2.2k LOC, 639 KB → 216 KB bundle) and replaced TinyMCE with
Tiptap on top. That was a second pass on a working Vue 3 app, not a wider
first pass. Keep the same separation here.
