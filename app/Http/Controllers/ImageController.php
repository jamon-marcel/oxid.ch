<?php
namespace App\Http\Controllers;
use App\Models\HomeImage;
use App\Support\Glide;
use App\Support\ImageSupport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Glide\Server;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves /img/... through Glide, keeping the URL shapes of the former
 * marceli-to/image-cache package:
 *
 *   /img/original/{file}
 *   /img/thumbnail/{file}                                  admin
 *   /img/large/{file}                                      admin
 *   /img/home/{file}                                       crop from home_images
 *   /img/crop/{file}/{maxWidth?}/{maxHeight?}/{coords?}   coords = w,h,x,y
 *
 * Every variant except original accepts ?fm=avif|webp.
 */
class ImageController extends Controller
{
  public const MAX_SIZE = 2400;

  /**
   * The width/height pairs the markup asks for. Anything else is a 404, so
   * crafted URLs cannot fill the cache.
   */
  public const CROP_SIZES = [[900, 562], [1200, 750], [1600, 1000], [2400, 1500], [2400, 2400]];

  public const FORMAT_QUALITY = ['jpg' => 75, 'png' => 90, 'gif' => 90, 'webp' => 80, 'avif' => 70];

  protected Server $server;

  public function __construct()
  {
    $this->server = Glide::server();
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

    return $this->respond($filename, ['w' => 300, 'h' => 300, 'fit' => 'crop'], $request, 3600);
  }

  /**
   * Landscape scaled down to 1600 wide, portrait to 900 high
   * (image-cache "large").
   */
  public function large(Request $request, string $filename): Response
  {
    [$width, $height] = $this->orientedSize($this->source($filename));

    return $this->respond($filename, $this->bound($width, $height, 1600, 900) + ['fit' => 'max'], $request, 3600);
  }

  /**
   * Home images: the crop comes from home_images, not the URL. A cropped
   * image is scaled to 2000 wide, or 1250 high when portrait, upscaling if
   * needed; an uncropped one is only scaled down.
   */
  public function home(Request $request, string $filename): Response
  {
    [$width, $height] = $this->orientedSize($this->source($filename));
    $image = HomeImage::where('name', $filename)->first();
    $params = [];

    if ($image && $image->coords_w && $image->coords_h) {
      $crop = array_map(fn ($v) => (int) floor((float) $v), [$image->coords_w, $image->coords_h, $image->coords_x ?? 0, $image->coords_y ?? 0]);
      $params['crop'] = implode(',', $crop);
      [$width, $height] = $crop;

      // image-cache scaled twice (to 2000 wide, then portrait to 1250 high);
      // reproduce its rounded size exactly, but resample only once.
      $scaledHeight = (int) round($height * 2000 / $width);
      $params += $width > $height
        ? ['w' => 2000, 'h' => $scaledHeight]
        : ['w' => (int) round(2000 * 1250 / $scaledHeight), 'h' => 1250];
      $params['fit'] = 'stretch';
    }
    elseif ($width > $height && $width >= 2000) {
      $params += ['w' => 2000, 'h' => 99999, 'fit' => 'max'];
    }
    elseif ($height >= 1250) {
      $params += ['w' => 99999, 'h' => 1250, 'fit' => 'max'];
    }

    // Re-cropping keeps the URL, so no long browser cache here
    return $this->respond($filename, $params, $request, 3600);
  }

  /**
   * Crop to the coords, then scale down to the requested size: landscape to
   * maxWidth, portrait to maxHeight (orientation taken after the crop).
   */
  public function crop(Request $request, string $filename, ?string $maxWidth = null, ?string $maxHeight = null, ?string $coords = null): Response
  {
    $source = $this->source($filename);
    [$maxWidth, $maxHeight] = $this->size($maxWidth, $maxHeight);

    $params = [];
    $crop = $this->coords($coords);

    if ($crop) {
      $params['crop'] = implode(',', $crop);
      [$width, $height] = $crop;
    }
    else {
      [$width, $height] = $this->orientedSize($source);
    }

    // The coords are part of the URL, so a re-crop gets a new one
    return $this->respond($filename, $params + $this->bound($width, $height, $maxWidth, $maxHeight) + ['fit' => 'max'], $request, 31536000);
  }

  /**
   * Bound one side only: width for landscape (and square), height for
   * portrait. The unbounded other side keeps Glide from flooring the
   * derived dimension (2400 x 1600.49 would become 2399 x 1600).
   */
  protected function bound(int $width, int $height, int $maxWidth, int $maxHeight): array
  {
    return $height > $width
      ? ['w' => 99999, 'h' => $maxHeight]
      : ['w' => $maxWidth, 'h' => 99999];
  }

  protected function respond(string $filename, array $params, Request $request, int $maxAge): Response
  {
    $format = strtolower((string) $request->query('fm'));
    $format = in_array($format, ImageSupport::modernFormats(), true) ? $format : $this->sourceFormat($filename);

    $params['fm'] = $format;
    $params['q'] = self::FORMAT_QUALITY[$format];

    $cachedPath = $this->server->makeImage('uploads/' . $filename, $params);

    return response($this->server->getCache()->read($cachedPath), 200, [
      'Content-Type' => 'image/' . ($format === 'jpg' ? 'jpeg' : $format),
    ] + $this->cacheHeaders($maxAge));
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

  /**
   * One of CROP_SIZES; no size at all means the largest.
   */
  protected function size(?string $maxWidth, ?string $maxHeight): array
  {
    if ($maxWidth === null) {
      return [self::MAX_SIZE, self::MAX_SIZE];
    }

    $size = [(int) $maxWidth, (int) $maxHeight];
    abort_unless(ctype_digit($maxWidth) && ctype_digit((string) $maxHeight) && in_array($size, self::CROP_SIZES, true), 404);

    return $size;
  }

  /**
   * w,h,x,y as ints, or null when there is no usable crop. Missing or
   * non-numeric x/y (the admin sends "null") count as 0.
   */
  protected function coords(?string $coords): ?array
  {
    $parts = explode(',', (string) $coords);
    if (count($parts) !== 4) {
      return null;
    }

    $parts = array_map(fn ($v) => is_numeric($v) ? max(0, (int) $v) : 0, $parts);

    return $parts[0] > 0 && $parts[1] > 0 ? $parts : null;
  }

  /**
   * Width and height as displayed, i.e. after EXIF auto-orientation.
   */
  protected function orientedSize(string $path): array
  {
    [$width, $height] = getimagesize($path);
    $orientation = function_exists('exif_read_data') ? (@exif_read_data($path)['Orientation'] ?? 1) : 1;

    return $orientation >= 5 ? [$height, $width] : [$width, $height];
  }

  protected function cacheHeaders(int $maxAge): array
  {
    return ['Cache-Control' => 'max-age=' . $maxAge . ', public'];
  }
}
