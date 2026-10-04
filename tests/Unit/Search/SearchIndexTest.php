<?php

namespace Tests\Unit\Search;

use App\Services\Search\SearchIndex;
use PHPUnit\Framework\TestCase;

class SearchIndexTest extends TestCase
{
  protected SearchIndex $index;

  protected function setUp(): void
  {
    // Shaped like the real records: [text, weight] per field
    $this->index = SearchIndex::build([
      'project:1' => [['Holzwohnschiff auf dem Wannenholz', 10], ['Zürich', 5], ['2017', 3], ['<p>Ein Wohnhaus aus Holz.</p>', 2]],
      'project:2' => [['Pile Up Giesshübel', 10], ['Zürich', 5], ['2013', 3], ['Aufstockung in Holzbauweise', 2]],
      'project:3' => [['Murgareal', 10], ['Frauenfeld', 5], ['2017', 3], ['Umbau einer Fabrik zu Wohnungen', 2]],
      'project:4' => [['Schulhaus Leutschenbach', 10], ['Zürich', 5], ['2019', 3], ['Neubau Schule', 2]],
      'discourse:1' => [['Stadt aus Holz – Wüest Partner', 10], ['Vortrag', 5], ['13.06.2018', 3]],
      'discourse:2' => [['Basler Bautage', 10], ['Ausstellung', 5], ['2019', 3], ['Holz und Bau in Basel', 2]],
    ]);
  }

  protected function keys(string $query): array
  {
    return array_keys($this->index->search($query));
  }

  public function test_match_scores(): void
  {
    $this->assertSame(SearchIndex::EXACT, SearchIndex::match('holz', 'holz'));
    $this->assertSame(SearchIndex::PREFIX, SearchIndex::match('schul', 'schulhaus'));
    $this->assertSame(SearchIndex::INFIX, SearchIndex::match('wohnschiff', 'holzwohnschiff'));
    $this->assertSame(SearchIndex::ONE_TYPO, SearchIndex::match('hollz', 'holz'));
    $this->assertSame(SearchIndex::ONE_TYPO, SearchIndex::match('ausstelung', 'ausstellung'));
    $this->assertSame(SearchIndex::TWO_TYPOS, SearchIndex::match('austelung', 'ausstellung'));
    $this->assertSame(0.0, SearchIndex::match('holzz', 'hoolz'), 'two typos need 7+ chars');
    $this->assertSame(0.0, SearchIndex::match('hlz', 'holz'), 'no typos below 4 chars');
    $this->assertSame(0.0, SearchIndex::match('bas', 'bau'));
  }

  public function test_german_endings_are_stemmed_for_prefix_matching(): void
  {
    $this->assertSame('schul', SearchIndex::stem('schule'));
    $this->assertSame('wohn', SearchIndex::stem('wohnen'));
    $this->assertNull(SearchIndex::stem('bau'));
    $this->assertNull(SearchIndex::stem('baue'), 'stem would be under 4 chars');
    $this->assertSame(SearchIndex::STEM_PREFIX, SearchIndex::match('schule', 'schulhaus'));
    $this->assertSame(['project:4'], $this->keys('schule'));
  }

  public function test_a_typo_at_the_start_of_a_longer_word(): void
  {
    $this->assertSame(SearchIndex::TYPO_PREFIX, SearchIndex::match('hollz', 'holzbauweise'));
    $this->assertSame(0.0, SearchIndex::match('hollz', 'holm'));
    $this->assertSame(0.0, SearchIndex::match('haus', 'hauptbahnhof'), 'not below 5 chars');
    $this->assertContains('project:2', $this->keys('hollz'));
  }

  public function test_numbers_never_match_fuzzily(): void
  {
    $this->assertSame(0.0, SearchIndex::match('2018', '2019'));
    $this->assertSame(['project:4', 'discourse:2'], $this->keys('2019'));
  }

  public function test_compounds_match_by_infix(): void
  {
    $this->assertSame(['project:1'], $this->keys('wohnschiff'));
  }

  public function test_umlauts_fold_every_way(): void
  {
    $expected = ['project:1', 'project:2', 'project:4'];
    foreach (['zürich', 'zurich', 'zuerich', 'Zürich'] as $query) {
      $this->assertEqualsCanonicalizing($expected, $this->keys($query), $query);
    }
  }

  public function test_typos_are_tolerated(): void
  {
    $this->assertContains('project:1', $this->keys('hollz'));
    $this->assertContains('discourse:2', $this->keys('ausstelung'));
  }

  public function test_prefix_finds_the_longer_word(): void
  {
    $this->assertSame(['project:4'], $this->keys('schul'));
  }

  public function test_a_title_hit_outranks_a_description_hit(): void
  {
    $keys = $this->keys('holz');
    $this->assertSame('discourse:1', $keys[0], 'exact title word');
    $this->assertLessThan(array_search('project:2', $keys), array_search('project:1', $keys));
  }

  public function test_multi_word_queries_require_every_word_when_possible(): void
  {
    // discourse:2 has both words, project:2 both inside "Holzbauweise";
    // the holz-only records drop out
    $this->assertSame(['discourse:2', 'project:2'], $this->keys('holz bau'));
  }

  public function test_multi_word_queries_fall_back_to_partial_matches(): void
  {
    // Nothing has both; partial matches remain, ranked
    $this->assertSame(['project:3'], $this->keys('murgareal xylophon'));
  }

  public function test_empty_and_unmatched_queries(): void
  {
    $this->assertSame([], $this->keys(''));
    $this->assertSame([], $this->keys('a'));
    $this->assertSame([], $this->keys('xylophon'));
  }
}
