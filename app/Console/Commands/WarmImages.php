<?php

namespace App\Console\Commands;

use App\Models\HomeImage;
use App\Support\ImageSupport;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Crawls the public pages in-process and requests every signed /img/...
 * URL they emit, so the Glide cache holds them before visitors ask (a cold
 * render takes 0.3–1.3 s). Cached renditions are only read and checked,
 * so a second run is quick. Run after a deploy or a cache wipe.
 */
class WarmImages extends Command
{
  protected $signature = 'images:warm {--dry-run : Only crawl and count the images}';

  protected $description = 'Render every image the public pages use into the Glide cache';

  /**
   * Not pages: assets, files, the admin.
   */
  protected const SKIP = ['/img/', '/build/', '/assets/', '/storage/', '/admin', '/api/', '/sanctum/'];

  public function handle(Kernel $kernel): int
  {
    // The crawl would otherwise write a session file per request
    config(['session.driver' => 'array']);

    $host = parse_url(config('app.url'), PHP_URL_HOST);
    $queue = ['/'];
    $pages = ['/' => true];
    $images = [];

    while ($page = array_shift($queue)) {
      $response = $this->request($kernel, $page);
      if (! $response->isOk() || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
        continue;
      }
      $html = $response->getContent();

      preg_match_all('/href="([^"#]+)/', $html, $links);
      foreach ($links[1] as $link) {
        $link = $this->page(html_entity_decode($link), $host);
        if ($link !== null && ! isset($pages[$link])) {
          $pages[$link] = true;
          $queue[] = $link;
        }
      }

      preg_match_all('#/img/[^\s"\'?]+\?[^\s"\']*?s=[0-9a-f]+#', $html, $urls);
      foreach ($urls[0] as $url) {
        $images[html_entity_decode($url)] = true;
      }
    }

    // The homepage, the search page and the admin login each show one random
    // home image, so the crawl sees only some of them: add them all
    foreach ($this->homeImageUrls() as $url) {
      $images[$url] = true;
    }

    $this->info(count($pages) . ' pages, ' . count($images) . ' images');
    if ($this->option('dry-run')) {
      return self::SUCCESS;
    }

    $failed = [];
    $this->withProgressBar(array_keys($images), function (string $url) use ($kernel, &$failed) {
      $status = $this->request($kernel, $url)->getStatusCode();
      if ($status !== 200) {
        $failed[] = "{$status} {$url}";
      }
    });
    $this->newLine();

    foreach ($failed as $line) {
      $this->error($line);
    }

    return $failed ? self::FAILURE : self::SUCCESS;
  }

  /**
   * Every rendition of every published home image: the "home" preset
   * (homepage, search) also covers the login background's sizes.
   *
   * @return list<string>
   */
  protected function homeImageUrls(): array
  {
    $formats = [...ImageSupport::modernFormats(), null];
    $urls = [];
    foreach (HomeImage::published()->get() as $image) {
      foreach (config('images.presets.home') as $size) {
        foreach ($formats as $format) {
          $url = parse_url($image->url($size, $format));
          $urls[] = $url['path'] . (isset($url['query']) ? '?' . $url['query'] : '');
        }
      }
    }

    return $urls;
  }

  protected function request(Kernel $kernel, string $uri): Response
  {
    // On the app URL, so the links the pages emit carry its host
    $request = Request::create(rtrim(config('app.url'), '/') . $uri);
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);

    return $response;
  }

  /**
   * Path (and query) of a link to a public page on this site, else null.
   * Relative links (only in rich text) point at pages linked elsewhere too.
   */
  protected function page(string $link, ?string $host): ?string
  {
    $url = parse_url($link);
    if ($url === false || isset($url['host']) && $url['host'] !== $host) {
      return null;
    }

    $path = $url['path'] ?? '';
    if (! str_starts_with($path, '/')
      || pathinfo($path, PATHINFO_EXTENSION) !== ''
      || array_filter(self::SKIP, fn ($prefix) => str_starts_with($path, $prefix))) {
      return null;
    }

    return $path . (isset($url['query']) ? '?' . $url['query'] : '');
  }
}
