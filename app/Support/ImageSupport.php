<?php

namespace App\Support;

use Imagick;

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
}
