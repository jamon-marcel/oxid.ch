<?php
namespace App\Http\Controllers;
use App\Models\DiscourseImage;
use App\Models\HomeImage;
use App\Models\JobImage;
use App\Models\ProfileImage;
use App\Models\ProjectImage;
use App\Models\TeamImage;
use App\Support\Glide;
use App\Support\ImageSupport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use League\Glide\Server;
use League\Glide\Signatures\SignatureException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves uploads through Glide:
 *
 *   /img/{file}?...&s=...       signed, built by Glide::url() — the site
 *   /img/original/{file}        admin
 *   /img/thumbnail/{file}       admin
 *   /img/large/{file}           admin
 *   /img/crop/{file}/...        legacy, 301 to the signed URL
 *   /img/home/{file}            legacy, 301 to the signed URL
 *
 * Every rendition accepts fm=avif|webp when the driver can write it.
 */
class ImageController extends Controller
{
  public const FORMAT_QUALITY = ['jpg' => 75, 'png' => 90, 'gif' => 90, 'webp' => 80, 'avif' => 70];

  /**
   * The width/height pairs the old /img/crop URLs used.
   */
  protected const LEGACY_SIZES = [[900, 562], [1200, 750], [1600, 1000], [2400, 1500], [2400, 2400]];

  protected const MODELS = [ProjectImage::class, DiscourseImage::class, HomeImage::class, TeamImage::class, JobImage::class, ProfileImage::class];

  protected Server $server;

  public function __construct()
  {
    $this->server = Glide::server();
  }

  /**
   * Renders exactly what the signed parameters ask for.
   */
  public function show(Request $request, string $filename): Response
  {
    $this->source($filename);

    try {
      Glide::signature()->validateRequest('img/' . $filename, $request->query());
    }
    catch (SignatureException) {
      abort(404);
    }

    // The crop is part of the URL, so a re-crop gets a new one
    return $this->respond($filename, $request->except('s'), 31536000, ['immutable']);
  }

  public function original(string $filename): BinaryFileResponse
  {
    return response()->file($this->source($filename), $this->cacheHeaders(3600));
  }

  /**
   * 300 x 300, cropped to fill (image-cache "thumbnail").
   */
  public function thumbnail(Request $request, string $filename): Response
  {
    $this->source($filename);

    return $this->respond($filename, ['w' => 300, 'h' => 300, 'fit' => 'crop', 'fm' => $request->query('fm')], 3600);
  }

  /**
   * Landscape scaled down to 1600 wide, portrait to 900 high
   * (image-cache "large").
   */
  public function large(Request $request, string $filename): Response
  {
    [$width, $height] = ImageSupport::dimensions($this->source($filename));
    $params = $height > $width ? ['w' => 99999, 'h' => 900] : ['w' => 1600, 'h' => 99999];

    return $this->respond($filename, $params + ['fit' => 'max', 'fm' => $request->query('fm')], 3600);
  }

  /**
   * /img/crop/{file}/{maxWidth?}/{maxHeight?}/{coords?}: landscape was
   * scaled to maxWidth, portrait to maxHeight. The crop comes from the
   * record, not the URL, so this cannot render arbitrary crops.
   */
  public function legacyCrop(Request $request, string $filename, ?string $maxWidth = null, ?string $maxHeight = null): RedirectResponse
  {
    $path = $this->source($filename);
    $size = $maxWidth === null ? [2400, 2400] : [(int) $maxWidth, (int) $maxHeight];
    abort_unless(in_array($size, self::LEGACY_SIZES, true), 404);

    $image = $this->find($filename);
    [$width, $height] = $image?->displaySize() ?? ImageSupport::dimensions($path);
    $size = $height > $width ? $size[1] : $size[0];

    return $this->redirect(Glide::url($filename, $size, $image?->crop(), $this->format($request)));
  }

  public function legacyHome(Request $request, string $filename): RedirectResponse
  {
    $image = HomeImage::where('name', $filename)->first();
    abort_unless($image, 404);

    return $this->redirect($image->url(2000, $this->format($request)));
  }

  protected function respond(string $filename, array $params, int $maxAge, array $cacheDirectives = []): Response
  {
    $format = in_array($params['fm'] ?? null, ImageSupport::modernFormats(), true) ? $params['fm'] : $this->sourceFormat($filename);
    $image = $this->render($filename, $params, $format);

    // The encoder failed twice: the upload's own format for now, cached
    // briefly so the modern format is tried again soon
    if ($image === null && $format !== $this->sourceFormat($filename)) {
      Log::warning("Broken {$format} rendition of {$filename}, served as {$this->sourceFormat($filename)}", $params);
      $format = $this->sourceFormat($filename);
      $image = $this->render($filename, $params, $format);
      [$maxAge, $cacheDirectives] = [300, []];
    }

    abort_if($image === null, 500, 'Image could not be rendered');

    return response($image, 200, [
      'Content-Type' => 'image/' . ($format === 'jpg' ? 'jpeg' : $format),
    ] + $this->cacheHeaders($maxAge, $cacheDirectives));
  }

  /**
   * The rendition, or null if it is undecodable twice. A broken one is
   * deleted from the cache, so the next request renders it again. Seen
   * with Imagick's AVIF encoder: a bare 16-byte ftyp box, or a HEIF
   * container without the image.
   */
  protected function render(string $filename, array $params, string $format): ?string
  {
    $params['fm'] = $format;
    $params['q'] = self::FORMAT_QUALITY[$format];

    for ($attempt = 1; $attempt <= 2; $attempt++) {
      $cachedPath = $this->make($filename, $params);
      $image = $this->server->getCache()->read($cachedPath);
      if (@getimagesizefromstring($image)) {
        return $image;
      }
      $this->server->getCache()->delete($cachedPath);
    }

    return null;
  }

  /**
   * Renders into the Glide cache (unless cached) and returns the cache path.
   */
  protected function make(string $filename, array $params): string
  {
    return $this->server->makeImage('uploads/' . $filename, $params);
  }

  /**
   * Short-lived, so a re-crop reaches anyone who followed it before.
   */
  protected function redirect(string $url): RedirectResponse
  {
    return redirect($url, 301, $this->cacheHeaders(3600));
  }

  protected function find(string $filename): ?object
  {
    foreach (self::MODELS as $model) {
      if ($image = $model::where('name', $filename)->first()) {
        return $image;
      }
    }

    return null;
  }

  protected function format(Request $request): ?string
  {
    return in_array($request->query('fm'), ImageSupport::modernFormats(), true) ? $request->query('fm') : null;
  }

  /**
   * The fallback format is the upload's own, as with image-cache: a PNG
   * stays a PNG (it may have transparency).
   */
  protected function sourceFormat(string $filename): string
  {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    return match ($extension) {
      'png' => 'png',
      'gif' => 'gif',
      default => 'jpg',
    };
  }

  /**
   * Absolute path of an upload image; 404 for anything else.
   */
  protected function source(string $filename): string
  {
    $path = storage_path('app/public/uploads/' . $filename);
    abort_unless(
      $filename === basename($filename)
        && preg_match('/\.(jpe?g|png|gif)$/i', $filename)
        && is_file($path),
      404
    );

    return $path;
  }

  protected function cacheHeaders(int $maxAge, array $directives = []): array
  {
    return ['Cache-Control' => implode(', ', ['max-age=' . $maxAge, 'public', ...$directives])];
  }
}
