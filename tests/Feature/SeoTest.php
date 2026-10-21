<?php

namespace Tests\Feature;

use App\Models\Discourse;
use App\Models\Project;
use Tests\TestCase;

/**
 * Meta tags, sitemap.xml and llms.txt. Reads existing rows from the local
 * database; writes nothing.
 */
class SeoTest extends TestCase
{
    public function test_pages_carry_meta_tags_and_are_noindex_outside_production(): void
    {
        $html = $this->get('/kontakt')->assertOk()->getContent();

        $this->assertStringContainsString('<title>Kontakt - Oxid Architektur GmbH</title>', $html);
        $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow">', $html);
        $this->assertStringContainsString('<link rel="canonical" href="'.url('/kontakt').'">', $html);
        $this->assertStringContainsString('<meta property="og:image" content="'.asset('assets/img/oxid-architektur-open-graph.jpg').'">', $html);
        $this->assertFileExists(public_path('assets/img/oxid-architektur-open-graph.jpg'));
    }

    public function test_production_is_indexable_except_the_search(): void
    {
        $this->app['env'] = 'production';

        $this->get('/kontakt')->assertSee('<meta name="robots" content="index, follow">', false);
        $this->get('/suche/haus')->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    public function test_project_has_a_canonical_url_with_slug_and_a_plain_description(): void
    {
        $project = Project::hasDetail()->whereNotNull('description')->firstOrFail();

        $html = $this->get('/projekt/'.$project->id)->assertOk()->getContent();

        preg_match('/<meta name="description" content="([^"]*)">/', $html, $match);
        $description = html_entity_decode($match[1]);
        $this->assertLessThanOrEqual(161, mb_strlen($description));
        $this->assertStringNotContainsString('&', $description);
        $this->assertMatchesRegularExpression('#<link rel="canonical" href="'.preg_quote(url('/projekt/'.$project->id), '#').'/[a-z0-9-]+">#', $html);
    }

    public function test_og_image_is_a_signed_1200_by_630_rendition(): void
    {
        $project = Project::hasDetail()->orderBy('order')->firstOrFail();
        $html = $this->get('/projekt/'.$project->id)->getContent();

        preg_match('/<meta property="og:image" content="([^"]*)">/', $html, $match);
        $url = html_entity_decode($match[1]);
        $this->assertStringContainsString('w=1200&h=630&fit=crop', $url);

        $image = $this->get(str_replace(url('/'), '', $url))->assertOk()->getContent();
        $this->assertSame([1200, 630], array_slice(getimagesizefromstring($image), 0, 2));
    }

    public function test_homepage_has_organization_json_ld(): void
    {
        $html = $this->get('/')->getContent();

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $match);
        $data = json_decode($match[1], true);
        $this->assertSame('Organization', $data['@type']);
        $this->assertSame('Zürich', $data['address']['addressLocality']);
    }

    public function test_sitemap_lists_pages_projects_and_discourse(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = simplexml_load_string($response->getContent());
        $this->assertSame(
            14 + Project::hasDetail()->count() + Discourse::published()->count(),
            count($xml->url)
        );
        $this->assertStringContainsString('<loc>'.url('/kontakt').'</loc>', $response->getContent());
    }

    public function test_llms_txt_is_markdown_with_projects_and_discourse(): void
    {
        $response = $this->get('/llms.txt')->assertOk()->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $text = $response->getContent();

        $this->assertStringStartsWith("# Oxid Architektur\n\n> ", $text);
        $this->assertStringContainsString("\n## Projekte\n", $text);
        $this->assertStringContainsString("\n## Diskurs\n", $text);
        $this->assertStringContainsString(url('sitemap.xml'), $text);
    }
}
