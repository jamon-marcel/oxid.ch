# Search: Algolia → own implementation

Decided 2026-10-04. Supersedes `04-open-questions.md` #7, which asked how to
preserve Algolia's index settings through the upgrade. That question is moot
if Algolia goes.

## What the search actually is

| | |
|---|---|
| Searchable records | **136** — 56 `Project`, 80 `Discourse` (production, 2026-10-04; the first survey measured 119 on a stale local copy) |
| UI | plain `GET` form, full page reload, server-rendered list |
| Instant search / autocomplete / facets / pagination | none |
| Client-side Algolia | **none** — no search JS in `resources/js/frontend/` |
| Entry point | `app/Http/Controllers/SearchController.php:49-50` |
| Corpus size | ~100 KB of text (avg description 564 chars) |

Algolia is doing three useful things at this scale: **typo tolerance,
relevance ranking, prefix matching**. Everything else it offers is unused.

## Correction to an earlier claim

An earlier draft of `02-backend-laravel13.md` called the Algolia client
v3 → v4 step "a full client rewrite" and budgeted 0.5 day. That was wrong.
Scout selects its engine from whichever client class is installed:

```php
// vendor/laravel/scout/src/EngineManager.php:44
? $this->configureAlgolia4Driver()
: $this->configureAlgolia3Driver();
```

Scout 10.14 already ships `Algolia4Engine`, and no app code touches the
Algolia client directly — only `->search()`. Staying on Algolia would have
been close to a package bump. The decision to drop it is therefore about
proportionality and the four issues below, **not** about avoiding migration
cost.

## Why drop it anyway

1. **Dev and production share the same indices.** `SCOUT_PREFIX` is unset and
   index names are hardcoded (`Project::searchableAs()` → `'projects'`,
   `Discourse` → `'discourse'`). `scout:import` locally writes to production.
   `queue => false`, so every local admin save pushes too.
2. **Unpublished content is in a third-party index.** Neither model defines
   `shouldBeSearchable()`, so every row is indexed regardless of `publish`;
   filtering happens at query time in `SearchController`.
3. **No declared record shape.** Neither model defines
   `toSearchableArray()`, so the full `toArray()` goes to Algolia — every
   column, both locales, timestamps. Adding a column silently changes what
   is sent.
4. **An external API call on the critical path** of a public page, and on
   every admin save.

## Staging

### Phase 1 — drop Algolia, keep Scout (small, reversible)

`SCOUT_DRIVER=collection`. Scout 10 already ships `CollectionEngine`, which
substring-matches in PHP:

```php
// CollectionEngine.php:140
if (Str::contains(Str::lower($value), Str::lower($builder->query)))
```

`SearchController` does not change — `->search(...)->where('publish','1')
->get()` keeps working, the collection engine supports where clauses.

**Not the `database` driver.** It builds SQL `LIKE` against real columns, and
the translatable fields are JSON: `{"de": "Holzwohnschiff …", "en": null}`.
`LIKE '%de%'` would match the JSON key on every row. The collection engine
reads PHP accessor values, so it sees the translated string.

Also in this phase: define `toSearchableArray()` explicitly on both models
(title, title_short, location, description, info for `Project`), which fixes
issue 3 above regardless of engine.

Removes: `algolia/algoliasearch-client-php` from `composer.json`, the
`algolia` block in `config/scout.php`, `ALGOLIA_APP_ID` / `ALGOLIA_SECRET`.

**What is lost after phase 1:** typo tolerance, relevance ranking, prefix
matching. Plain substring only. This is a real regression and phase 2 is what
pays it back.

#### Phase 1 — done 2026-10-04, measured against production

Done right after the Laravel 13 bump instead of as backend step 8: Guzzle 8
broke the Algolia v3 client, so keyword search was a 500 on the branch.

Searchable fields: `Project` — title, title_short, location, **year,
year_works**, description, info. `Discourse` — heading, **date**, title,
description_short, description, info. HTML is stripped and entities decoded,
so tags like `strong` don't match. The year fields were not in the
original plan; they were added because a search for a year should find that
year's projects and events.

Production is **oxid-architektur.ch** (still on Algolia). The same 15
queries, run against it and against local with the same data, compared by
result type + title:

| Query | Prod | Local | Both | Reading |
|---|---|---|---|---|
| holz | 48 | 46 | 45 | ≈ same |
| umbau, basel, zürich, wohn | 11 / 7 / 56 / 47 | 11 / 5 / 45 / 50 | 10 / 5 / 44 / 46 | close |
| wohnschiff | 0 | 1 | 0 | **local better** — compound infix |
| ausstellung | 0 | 2 | 0 | local better (Algolia index likely stale) |
| haus, wohnen, schule | 65 / 30 / 14 | 35 / 14 / 2 | 22 / 14 / 1 | Algolia's typo-tolerant prefix (`schule` ≈ `Schul·haus`) |
| zurich, hollz | 56 / 48 | 0 / 0 | 0 | no umlaut folding, no typos |
| holz bau | 35 | 0 | 0 | **multi-word**: collection engine needs the literal phrase |
| 2019, 2021 | 47 / 96 | 8 / 17 | 8 / 17 | prod matches `created_at`/`updated_at` — noise, not a loss |

So, beyond the plan's list (typos, ranking, prefix), phase 1 also loses
**umlaut folding** and **multi-word queries**. Both are covered by the phase 2
design (normalisation, per-token scoring); add test cases for them there.
Branch-only regression, never deployed: phase 2 lands before go-live.

### Phase 2 — own scoring search

Bring back all three. At 119 records this is comfortably tractable.

**Measured, not assumed:** a full fuzzy scan over ~9.5k tokens (119 records ×
~80 tokens) with a 2-term query, length-gated `levenshtein()`, runs in
**~5 ms** on this machine. Performance is not a design constraint; correctness
and German-language behaviour are.

#### Index

Build an inverted index, cache it, rebuild on model save:

```
token → [ {record, field, weight} ]
```

Weighted fields, heaviest first:

| Field | Weight |
|---|---|
| `title`, `title_short` | 10 |
| `location` | 5 |
| `year`, `year_works`, `date` | 3 |
| `description` | 2 |
| `info` | 1 |

Tokenisation: `strip_tags` → lowercase → split on non-letters → drop tokens
< 2 chars. Store both the raw token and a normalised form (see German notes).

#### Scoring

For each query token against each indexed token, best match wins:

| Match | Score |
|---|---|
| exact | 1.0 |
| prefix (`str_starts_with`) | 0.8 |
| infix — indexed token *contains* query token | 0.5 |
| `levenshtein` ≤ 1, token ≥ 4 chars | 0.6 |
| `levenshtein` ≤ 2, token ≥ 7 chars | 0.4 |
| phonetic match (see below) | 0.3 |

Record score = Σ (match score × field weight), then multiply by the fraction
of query tokens matched, so a record hitting both terms outranks one hitting
one term loudly. Sort descending.

Gate the `levenshtein()` calls on `abs(strlen($a) - strlen($b)) <= 2` — that
is what keeps the scan at 5 ms rather than 50.

#### German specifics — the part worth getting right

- **Umlauts.** Normalise `ä/ö/ü/ß` → `ae/oe/ue/ss` *and* index the bare
  `a/o/u` form, so "Zurich", "Zürich" and "Zuerich" all match.
- **Compounds.** The real German problem: "Wohnschiff" should find
  "Holzwohnschiff". This is exactly what the **infix** rule above is for, and
  it is something Algolia handles poorly out of the box. Expect the own
  implementation to be *better* here, not worse.
- **Phonetics.** PHP's `metaphone()` / `soundex()` are English-tuned. The
  German equivalent is **Kölner Phonetik** (Cologne phonetics), ~40 LOC to
  implement. Optional; add it only if the typo tolerance from `levenshtein`
  proves insufficient against real queries.

#### Shape

One `SearchService` with two public methods (`search(string $q)`, and an
index-rebuild hook), called from `SearchController`. With Algolia gone and a
single call site, Scout's engine abstraction is buying nothing — so phase 2
can drop the `Searchable` trait and the Scout dependency entirely, which also
removes its model observers from every save.

Keep Scout through phase 1 only so that phase 1 stays a one-line revert.

#### Effort

**1 – 1.5 days** including a tuning pass. Budget the tuning: collect 20–30
real queries (the search page is a `GET`, so they are in the access logs) and
check the ranking against them. That is what separates a scoring search that
feels right from one that merely runs.

#### Testing

This is the one piece of this whole project that is genuinely unit-testable
without a browser — pure functions over fixed input. Write tests for it even
though the rest of the codebase has none: tokenisation, umlaut normalisation,
compound matching, and a ranking assertion per real query.

## Effort vs keeping Algolia

| | Days |
|---|---|
| Keep Algolia (package bump + the 4 fixes above) | ~0.5 |
| Phase 1 only | ~0.25 |
| Phase 1 + 2 | ~1.5 |

So the own-search path costs roughly **+1 day** over keeping Algolia. In
exchange: no external dependency on a public page's critical path, no
third-party copy of unpublished content, no dev/prod index collision, and
compound-word matching that suits German better than the default Algolia
config does.

## Not an option here

Self-hosted Meilisearch or Typesense (both supported by Scout) need a
long-running process. Production is Hostpoint shared hosting, which will not
provide one. The realistic choice is Algolia or an in-process implementation.
