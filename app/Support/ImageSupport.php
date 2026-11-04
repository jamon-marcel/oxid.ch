<?php

namespace App\Support;

use Imagick;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Throwable;

class ImageSupport
{
	protected static ?array $supportedFormats = null;

	/**
	 * Formats the Glide driver can write on this server.
	 */
	public static function supportedFormats(): array
	{
		if (self::$supportedFormats !== null) {
			return self::$supportedFormats;
		}

		$formats = ['jpg', 'jpeg', 'png', 'gif'];

		if (Glide::driver() === 'imagick') {
			if (Imagick::queryFormats('WEBP')) {
				$formats[] = 'webp';
			}
			if (Imagick::queryFormats('AVIF')) {
				$formats[] = 'avif';
			}
		}
		else {
			$gd = function_exists('gd_info') ? gd_info() : [];
			if (! empty($gd['WebP Support'])) {
				$formats[] = 'webp';
			}
			if (! empty($gd['AVIF Support'])) {
				$formats[] = 'avif';
			}
		}

		return self::$supportedFormats = $formats;
	}

	/**
	 * Modern formats offered next to the JPEG/PNG original, best first,
	 * limited to what this server can write.
	 *
	 * @return array<int, string>
	 */
	public static function modernFormats(): array
	{
		return array_values(array_filter(['avif', 'webp'], fn (string $format) => self::supports($format)));
	}

	public static function supports(string $format): bool
	{
		return in_array(strtolower($format), self::supportedFormats(), true);
	}

	/**
	 * Width and height as displayed, i.e. after EXIF auto-orientation, or
	 * null when the file cannot be read.
	 *
	 * @return array{0: int, 1: int}|null
	 */
	public static function dimensions(string $path): ?array
	{
		$size = is_file($path) ? @getimagesize($path) : false;
		if (! $size) {
			return null;
		}

		$orientation = function_exists('exif_read_data') ? (@exif_read_data($path)['Orientation'] ?? 1) : 1;

		return $orientation >= 5 ? [$size[1], $size[0]] : [$size[0], $size[1]];
	}

	/**
	 * Scales an uploaded JPEG/PNG down in place so its longer side is at
	 * most images.upload_max, keeping its format (JPEG at quality 90). EXIF
	 * rotation is applied to the pixels. Returns whether the file changed.
	 *
	 * Imagick only: GD needs ~4 bytes per pixel in PHP memory (a 20,000 px
	 * upload would not fit), so without Imagick the upload stays as it is.
	 */
	public static function downscale(string $path): bool
	{
		$max = (int) config('images.upload_max');
		$size = self::dimensions($path);
		if (! $max || ! $size || max($size) <= $max || Glide::driver() !== 'imagick') {
			return false;
		}

		try {
			Imagick::setResourceLimit(Imagick::RESOURCETYPE_THREAD, 1);
			$image = ImageManager::usingDriver(ImagickDriver::class)->decodePath($path);
			$image->scaleDown($max, $max);
			$image->save($path, quality: 90);

			return true;
		}
		catch (Throwable $e) {
			Log::warning('Upload not scaled down: ' . basename($path) . ': ' . $e->getMessage());

			return false;
		}
	}
}
