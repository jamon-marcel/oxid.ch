<?php

namespace Tests\Unit;

use App\Support\Seo;
use PHPUnit\Framework\TestCase;

class SeoTextTest extends TestCase
{
    public function test_editor_html_becomes_one_line_of_plain_text(): void
    {
        $html = '<p>Gr&uuml;nzug&nbsp;und <a href="#">Hang</a><br />neu</p><p>Zweiter Absatz</p>';

        $this->assertSame('Grünzug und Hang neu Zweiter Absatz', Seo::text($html));
    }

    public function test_long_text_is_cut_at_a_word(): void
    {
        $text = Seo::text(str_repeat('Holzbau ', 40), 50);

        $this->assertSame('Holzbau Holzbau Holzbau Holzbau Holzbau Holzbau…', $text);
    }

    public function test_empty_input_is_an_empty_string(): void
    {
        $this->assertSame('', Seo::text(null));
    }
}
