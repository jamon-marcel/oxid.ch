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
   * Words dropped from queries: in nearly every text, so as a query token
   * they only add noise and, as a required term, drop good matches.
   * The index keeps them (see queryTokens()).
   */
  public const STOPWORDS = [
    // German
    'der', 'die', 'das', 'den', 'dem', 'des', 'ein', 'eine', 'einer', 'einen', 'einem', 'eines',
    'und', 'oder', 'aber', 'sowie', 'als', 'wie', 'auch', 'noch', 'nur', 'so', 'dass',
    'in', 'im', 'ins', 'an', 'am', 'ans', 'auf', 'aus', 'bei', 'beim', 'mit', 'nach', 'von', 'vom',
    'zu', 'zum', 'zur', 'für', 'fuer', 'über', 'ueber', 'um', 'unter', 'vor', 'durch', 'gegen', 'ohne', 'bis',
    'ist', 'sind', 'war', 'wird', 'werden', 'wurde', 'hat', 'haben',
    'er', 'sie', 'es', 'wir', 'ihr', 'sich', 'sein', 'seine', 'ihre', 'nicht',
    // English
    'the', 'a', 'an', 'and', 'or', 'of', 'to', 'for', 'on', 'at', 'by', 'with', 'from', 'is', 'are',
  ];

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
   * Query tokens without stopwords. A query of stopwords only keeps them,
   * so searching for "das" still finds something.
   *
   * @return string[]
   */
  public static function queryTokens(?string $query): array
  {
    $tokens = self::tokens($query);
    $words = array_values(array_diff($tokens, self::STOPWORDS));

    return $words ?: $tokens;
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
