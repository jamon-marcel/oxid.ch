<?php

namespace App\Support;

use League\Glide\Server;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Signatures\SignatureInterface;
use League\Glide\Urls\UrlBuilderFactory;

/**
 * The Glide server behind /img/... (ImageController).
 */
class Glide
{
	public static function server(): Server
	{
		// ImageMagick 7.1.2 on Hostpoint segfaults encoding large AVIFs
		// (about 1500 x 2000 and up) on several threads; on one it is stable
		// and no slower (2026-11-04). A segfault is a bare 503, nothing logged.
		if (self::driver() === 'imagick') {
			\Imagick::setResourceLimit(\Imagick::RESOURCETYPE_THREAD, 1);
		}

		return ServerFactory::create([
			'source' => storage_path('app/public'),
			'cache' => storage_path('app/.glide-cache'),
			'driver' => self::driver(),
		]);
	}

	/**
	 * Imagick when the extension is loaded, GD otherwise. The production
	 * driver is unconfirmed, so this is detected rather than configured.
	 */
	public static function driver(): string
	{
		return extension_loaded('imagick') ? 'imagick' : 'gd';
	}

	/**
	 * Signed /img/{file} URL: the upload cropped to w,h,x,y (if given) and
	 * scaled down to fit inside size x size, never up.
	 */
	public static function url(string $filename, int $size, ?array $crop = null, ?string $format = null): string
	{
		$params = ['w' => $size, 'h' => $size, 'fit' => 'max'];
		if ($crop) {
			$params['crop'] = implode(',', $crop);
		}
		if ($format) {
			$params['fm'] = $format;
		}

		return UrlBuilderFactory::create('/img/', self::signKey())->getUrl($filename, $params);
	}

	/**
	 * Signed /img/{file} URL for og:image: the upload cropped to w,h,x,y
	 * (if given), then cropped to fill 1200 x 630.
	 */
	public static function ogUrl(string $filename, ?array $crop = null): string
	{
		$params = ['w' => 1200, 'h' => 630, 'fit' => 'crop'];
		if ($crop) {
			$params['crop'] = implode(',', $crop);
		}

		return UrlBuilderFactory::create('/img/', self::signKey())->getUrl($filename, $params);
	}

	/**
	 * The signature makes sure /img/{file} only renders what url() built.
	 */
	public static function signature(): SignatureInterface
	{
		return SignatureFactory::create(self::signKey());
	}

	/**
	 * Remove every cached rendition of an upload.
	 */
	public static function forget(string $filename): void
	{
		self::server()->deleteCache('uploads/' . $filename);
	}

	protected static function signKey(): string
	{
		return (string) config('app.key');
	}
}
