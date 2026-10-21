<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Discourse;
use App\Models\Project;
use App\Support\Seo;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * /sitemap.xml and /llms.txt. robots.txt is a static file written by
 * `php artisan seo:robots` (routes/console.php).
 */
class SeoController extends Controller
{
    /**
     * The fixed pages: route name => [link text, llms.txt description].
     */
    protected const PAGES = [
        'page.home' => ['Startseite', 'Aktuelles'],
        'page.works.authors' => ['Werkliste nach Autorenschaft', null],
        'page.works.year' => ['Werkliste nach Jahr', null],
        'page.works.program' => ['Werkliste nach Programm', 'Wohnen, Gewerbe/Büro, Hybrid, Öffentlich'],
        'page.works.state' => ['Werkliste nach Status', 'Gebaut, Planung, Wettbewerb'],
        'page.discourse' => ['Diskurs', 'Veranstaltungen, Publikationen und Recherche'],
        'page.discourse.events' => ['Veranstaltungen', null],
        'page.discourse.publications' => ['Publikationen', null],
        'page.discourse.research' => ['Recherche', null],
        'page.office.team' => ['Team', null],
        'page.office.profile' => ['Profil', null],
        'page.office.jobs' => ['Jobs', 'Offene Stellen'],
        'page.history' => ['Geschichte', 'Von Burkhalter Sumi Architekten (1984) zu Oxid Architektur (2020)'],
        'page.contact' => ['Kontakt', null],
    ];

    public function sitemap(): Response
    {
        $urls = collect(array_keys(self::PAGES))->map(fn ($name) => ['loc' => route($name), 'lastmod' => null])
            ->concat($this->projects()->map(fn ($p) => ['loc' => $this->projectUrl($p), 'lastmod' => $p->updated_at]))
            ->concat($this->discourse()->map(fn ($d) => ['loc' => $this->discourseUrl($d), 'lastmod' => $d->updated_at]));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n    <loc>".e($url['loc'])."</loc>\n";
            if ($url['lastmod']) {
                $xml .= '    <lastmod>'.$url['lastmod']->toAtomString()."</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }
        $xml .= "</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Markdown summary of the site for LLMs (https://llmstxt.org).
     */
    public function llms(): Response
    {
        $org = config('seo.organization');

        $lines = [
            '# Oxid Architektur',
            '',
            '> '.config('seo.about'),
            '',
            "- Kontakt: {$org['name']}, {$org['street']}, {$org['zip']} {$org['city']}, {$org['phone']}, {$org['email']}",
            '',
            '## Website',
            '',
            ...collect(self::PAGES)->map(fn ($page, $name) => '- ['.$page[0].']('.route($name).')'.($page[1] ? ': '.$page[1] : ''))->values(),
            '',
            '## Projekte',
            '',
            ...$this->projects()->map(fn ($p) => $this->item("{$p->title_short}, {$p->location}", $this->projectUrl($p), Seo::text($p->description, 200))),
            '',
            '## Diskurs',
            '',
            ...$this->discourse()->map(fn ($d) => $this->item($d->title, $this->discourseUrl($d), implode(': ', array_filter([
                implode(', ', array_filter([$d->heading, $d->date])),
                Seo::text($d->description_short, 200),
            ])))),
            '',
            '## Optional',
            '',
            '- [Sitemap]('.url('sitemap.xml').')',
            '',
        ];

        return response(implode("\n", $lines), 200, ['Content-Type' => 'text/markdown; charset=UTF-8']);
    }

    /**
     * Projects with a detail page (the ones the works list links to).
     */
    protected function projects(): Collection
    {
        return Project::hasDetail()->orderBy('order')->get();
    }

    protected function discourse(): Collection
    {
        return Discourse::published()->orderBy('order')->get();
    }

    protected function projectUrl(Project $project): string
    {
        return route('page.project', ['project' => $project->id, 'slug' => AppHelper::slug($project->title_short)]);
    }

    protected function discourseUrl(Discourse $discourse): string
    {
        return route('page.discourse.detail', ['discourse' => $discourse->id, 'slug' => AppHelper::slug($discourse->title)]);
    }

    protected function item(string $title, string $url, string $text): string
    {
        $title = str_replace(['[', ']'], ['(', ')'], Seo::text($title, 200));

        return "- [{$title}]({$url})".($text !== '' ? ": {$text}" : '');
    }
}
