<?php

namespace Tests\Unit\Search;

use App\Services\Search\Tokenizer;
use PHPUnit\Framework\TestCase;

class TokenizerTest extends TestCase
{
  public function test_it_strips_html_and_entities_and_lowercases(): void
  {
    $this->assertSame(
      ['holzbau', 'in', 'zürich', 'basel'],
      Tokenizer::tokens('<p><strong>Holzbau</strong> in Zürich&nbsp;&amp; Basel</p>')
    );
  }

  public function test_it_drops_short_tokens_and_duplicates(): void
  {
    $this->assertSame(['ab', 'holz'], Tokenizer::tokens('a ab Holz holz – x'));
  }

  public function test_it_keeps_numbers(): void
  {
    $this->assertSame(['13', '06', '2018'], Tokenizer::tokens('13.06.2018'));
  }

  public function test_queries_drop_stopwords(): void
  {
    $this->assertSame(['schulhaus'], Tokenizer::queryTokens('Das Schulhaus'));
    $this->assertSame(['wohnen', 'stadt'], Tokenizer::queryTokens('Wohnen in der Stadt'));
    $this->assertSame(['house', 'zürich'], Tokenizer::queryTokens('the house for Zürich'));
  }

  public function test_a_query_of_stopwords_only_keeps_them(): void
  {
    $this->assertSame(['das'], Tokenizer::queryTokens('das'));
    $this->assertSame(['in', 'der'], Tokenizer::queryTokens('in der'));
  }

  public function test_umlauts_get_the_transliterated_and_the_bare_spelling(): void
  {
    $this->assertSame(['zuerich', 'zurich'], Tokenizer::variants('zürich'));
    $this->assertSame(['strasse'], Tokenizer::variants('straße'));
    $this->assertSame(['holz'], Tokenizer::variants('holz'));
  }
}
