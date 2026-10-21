<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Helpers for the <head> meta tags, the sitemap and llms.txt.
 */
class Seo
{
    /**
     * Plain text from editor HTML: tags stripped, entities decoded,
     * whitespace collapsed, cut at a word boundary.
     */
    public static function text(?string $html, int $limit = 160): string
    {
        $text = html_entity_decode(strip_tags(str_replace(['<br>', '<br />', '</p>'], ' ', (string) $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', $text)));

        return Str::limit($text, $limit, '…', preserveWords: true);
    }

    /**
     * Crawlers are welcome on production only.
     */
    public static function indexable(): bool
    {
        return app()->isProduction();
    }

    /**
     * schema.org Organization as JSON-LD, for the homepage.
     */
    public static function organization(): string
    {
        $org = config('seo.organization');

        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $org['name'],
            'url' => url('/'),
            'logo' => asset('android-chrome-512x512.png'),
            'email' => $org['email'],
            'telephone' => $org['phone'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $org['street'],
                'postalCode' => $org['zip'],
                'addressLocality' => $org['city'],
                'addressCountry' => $org['country'],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    }
}
