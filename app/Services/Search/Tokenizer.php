<?php

namespace App\Services\Search;

use Illuminate\Support\Str;

/**
 * Turns text into searchable tokens. Pure functions, no state.
 */
class Tokenizer
{
  /**
   * Tokens shorter than this are dropped.
   */
  public const MIN_LENGTH = 2;

  /**
   * Split text (HTML allowed) into unique lowercase tokens.
   *
   * @return string[]
   */
  public static function tokens(?string $text): array
  {
    $text = html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $parts = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY);

    return array_values(array_unique(array_filter(
      $parts,
      fn (string $token) => mb_strlen($token) >= self::MIN_LENGTH
    )));
  }

  /**
   * ASCII spellings of a lowercase token. Umlauts get both the German
   * transliteration and the bare vowel, so "zürich", "zuerich" and
   * "zurich" meet: "zürich" → ["zuerich", "zurich"].
   *
   * @return string[]
   */
  public static function variants(string $token): array
  {
    return array_values(array_unique([
      Str::ascii($token, 'de'),
      Str::ascii($token),
    ]));
  }
}
