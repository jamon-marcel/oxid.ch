<?php

namespace App\Services\Search;

/**
 * An inverted index over weighted fields, and the scoring that queries it.
 *
 * Documents are plain arrays — key => [[text, weight], ...] — so the index
 * can be built, cached and tested without the database.
 */
class SearchIndex
{
  // Match scores, per query token against one indexed token
  public const EXACT = 1.0;
  public const PREFIX = 0.8;
  public const STEM_PREFIX = 0.7;
  public const ONE_TYPO = 0.6;
  public const INFIX = 0.5;
  public const TYPO_PREFIX = 0.45;
  public const TWO_TYPOS = 0.4;

  // German inflection endings dropped for STEM_PREFIX, longest first:
  // "schule" → "schul" finds "schulhaus", "wohnen" → "wohn" finds "wohnung"
  public const ENDINGS = ['en', 'er', 'e', 'n', 's'];
  public const STEM_MIN = 4;

  // Shortest query token that may match by infix or with typos
  public const INFIX_MIN = 3;
  public const ONE_TYPO_MIN = 4;
  public const TYPO_PREFIX_MIN = 5;
  public const TWO_TYPOS_MIN = 7;

  /**
   * @param array<string, array<string, float>> $postings variant => [document key => best field weight]
   */
  public function __construct(protected array $postings = [])
  {
  }

  /**
   * @param array<string, array<int, array{0: ?string, 1: float|int}>> $documents key => [[text, weight], ...]
   */
  public static function build(array $documents): self
  {
    $postings = [];

    foreach ($documents as $key => $fields) {
      foreach ($fields as [$text, $weight]) {
        foreach (Tokenizer::tokens($text) as $token) {
          foreach (Tokenizer::variants($token) as $variant) {
            $postings[$variant][$key] = max($postings[$variant][$key] ?? 0, $weight);
          }
        }
      }
    }

    return new self($postings);
  }

  /**
   * Document keys matching the query, best first.
   *
   * A document scores Σ over query tokens of (best match × field weight),
   * times the share of query tokens it matched. When some documents match
   * every query token, the ones that don't are dropped.
   *
   * @return array<string, float> key => score
   */
  public function search(string $query): array
  {
    $terms = Tokenizer::tokens($query);
    if (! $terms) {
      return [];
    }

    $perTerm = array_map(fn (string $term) => $this->scoreTerm($term), $terms);

    $scores = [];
    $hits = [];
    foreach ($perTerm as $documents) {
      foreach ($documents as $key => $score) {
        $scores[$key] = ($scores[$key] ?? 0) + $score;
        $hits[$key] = ($hits[$key] ?? 0) + 1;
      }
    }

    $all = count($terms);
    if (in_array($all, $hits, true)) {
      $scores = array_filter($scores, fn ($key) => $hits[$key] === $all, ARRAY_FILTER_USE_KEY);
    }

    foreach ($scores as $key => $score) {
      $scores[$key] = $score * $hits[$key] / $all;
    }

    // Highest score first; ties keep index order (stable sort)
    uksort($scores, fn ($a, $b) => $scores[$b] <=> $scores[$a]);

    return $scores;
  }

  /**
   * Best (match × weight) per document for one query token.
   *
   * @return array<string, float>
   */
  protected function scoreTerm(string $term): array
  {
    $best = [];

    foreach (Tokenizer::variants($term) as $query) {
      foreach ($this->postings as $token => $documents) {
        $match = self::match($query, (string) $token);
        if ($match === 0.0) {
          continue;
        }
        foreach ($documents as $key => $weight) {
          $best[$key] = max($best[$key] ?? 0, $match * $weight);
        }
      }
    }

    return $best;
  }

  /**
   * How well one query token matches one indexed token, 0 to 1.
   */
  public static function match(string $query, string $token): float
  {
    if ($query === $token) {
      return self::EXACT;
    }
    if (str_starts_with($token, $query)) {
      return self::PREFIX;
    }

    $length = strlen($query);

    // Numbers (years, mostly) match exactly or by prefix, never fuzzily:
    // 2018 is not a typo for 2019.
    if (ctype_digit($query)) {
      return 0.0;
    }

    $best = 0.0;
    if ($length >= self::INFIX_MIN && str_contains($token, $query)) {
      $best = self::INFIX;
    }

    $stem = self::stem($query);
    if ($stem !== null && str_starts_with($token, $stem)) {
      $best = max($best, self::STEM_PREFIX);
    }

    if ($length >= self::ONE_TYPO_MIN && abs($length - strlen($token)) <= 2) {
      $distance = levenshtein($query, $token);
      if ($distance === 1) {
        $best = max($best, self::ONE_TYPO);
      } elseif ($distance === 2 && $length >= self::TWO_TYPOS_MIN) {
        $best = max($best, self::TWO_TYPOS);
      }
    }

    // A typo at the start of a longer word: "hollz" → "holzbau". From 5
    // chars only: at 4, "haus" would reach every "haupt…"
    if ($best < self::TYPO_PREFIX && $length >= self::TYPO_PREFIX_MIN && strlen($token) > $length) {
      foreach ([$length - 1, $length, $length + 1] as $cut) {
        if (levenshtein($query, substr($token, 0, $cut)) === 1) {
          $best = self::TYPO_PREFIX;
          break;
        }
      }
    }

    return $best;
  }

  /**
   * The query token without its inflection ending, or null if too short.
   */
  public static function stem(string $query): ?string
  {
    foreach (self::ENDINGS as $ending) {
      if (str_ends_with($query, $ending) && strlen($query) - strlen($ending) >= self::STEM_MIN) {
        return substr($query, 0, -strlen($ending));
      }
    }

    return null;
  }

  /**
   * @return array<string, array<string, float>>
   */
  public function postings(): array
  {
    return $this->postings;
  }
}
