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
